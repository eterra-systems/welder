<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "content.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }

  $languages_array = get_languages();
  $content_parent_id = 0;
    
  if(isset($_POST['submit_content'])) {
   
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $content_errors = array();
    $all_queries = "";

    $cct_id = $_POST['content_type_id'];
    
    foreach($_POST['content_name'] as $language_id => $content_name) {
      if(empty($content_name)) $content_errors['content_name'][$language_id] = $languages['required_field_error'];
      if(empty($_POST['content_menu_text'][$language_id])) $content_errors['content_menu_text'][$language_id] = $languages['required_field_error'];
    }
    /*
    *  $_POST['content_parent_id_level'] has three parameters - content_id, content_hierarchy_level
    */
    $content_parent_id_level = explode("|", $_POST['content_parent_id_level']);
    $content_parent_id = $content_parent_id_level[0];
    $content_hierarchy_level = $content_parent_id_level[1]+1;
    
    foreach($_POST['content_pretty_url'] as $language_id => $content_pretty_url) {
      
      if(empty($content_pretty_url)) {
        $content_pretty_url = str_replace(array('\\',"'",'?','!','"','.',',','(',')','%',' - ',' '), array('-','','','','','','-','-','-','-','-','-'), mb_convert_case($_POST['content_name'][$language_id], MB_CASE_LOWER, "UTF-8"));
        $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
        if(!$is_pretty_url_unique) {
          $content_pretty_url = $content_pretty_url."-1";
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_errors['content_pretty_url'] = $languages['content_pretty_url_error'];
          }
        }
      }
      else {
        $content_pretty_url = str_replace(array('\\',"'",'?','!','"','.',',','(',')','%',' - ',' '), array('-','','','','','','-','-','-','-','-','-'), mb_convert_case($content_pretty_url, MB_CASE_LOWER, "UTF-8"));
        $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
        if(!$is_pretty_url_unique) {
          $content_pretty_url = $content_pretty_url."-1";
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_errors['content_pretty_url'] = $languages['content_pretty_url_error'];
            $content_pretty_url = $content_pretty_url."-1";
          }
        }
      }
      
      $_POST['content_pretty_url'][$language_id] = $content_pretty_url;
    }

    $content_is_section_header = (isset($_POST['content_is_section_header'])) ? 1 : 0;
    $content_show_in_menu = (isset($_POST['content_show_in_menu'])) ? 1 : 0;
    $content_show_in_footer = (isset($_POST['content_show_in_footer'])) ? 1 : 0;
    $content_is_active = (isset($_POST['content_is_active'])) ? 1 : 0;
      
    $input_name = "content_image";
    $max_image_size = "4194304"; //4MB
    $content_image_set = false;
    if(isset($_FILES[$input_name]) && ($_FILES[$input_name]['error'] != 4)) {
      $content_image_set = true;
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/contents/";
      $image_params = validate_upload_image($input_name, $upload_path, $max_image_size);
      //echo "<pre>";print_r($image_params);exit;
      if(!empty($image_params['error'])) {
        $content_errors[$input_name] = $image_params['error']; // array that may contain extension, size, upload
      }
      else {
        $image_tmp_name = $image_params['image_tmp_name'];
        $image_name = $image_params['image_name'];
        $image_exstension = $image_params['image_exstension'];
        $image_name_full = $image_params['image_name_full'];
      }
    }

    $content_target = $_POST['content_target'];
    $user_id = $_SESSION['admin']['user_id'];

    if(empty($content_errors)) {
      //if there are no form errors we can insert the information

      if($content_parent_id != 0) {

        /*
         * update the parent column `content_has_children` to 1, wich means it has children
         * no matter if it was set to 1 or 0
         */

        $query_update_parent = "UPDATE `contents` SET `content_has_children` = '1' WHERE `content_id` = '$content_parent_id'";
        $all_queries .= $query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $content_menu_order = get_content_lаst_child_order_value($content_parent_id)+1;
      }
      else {
        $content_menu_order = get_content_lаst_child_order_value($content_parent_id)+1;
      }
      
      $content_target = prepare_for_null_row($content_target);
      $content_hierarchy_ids = 0;
      $content_has_children = 0;
      $content_is_home_page = 0;
      $content_collapsed = 1;
      $content_image_name_db = ($content_image_set) ? "'$image_name_full'" : "NULL";
      $content_attribute_1 = "NULL";

      $query_insert_content = "INSERT INTO `contents`(`content_id`, 
                                                      `content_type_id`, 
                                                      `content_parent_id`, 
                                                      `content_hierarchy_ids`, 
                                                      `content_hierarchy_level`, 
                                                      `content_has_children`, 
                                                      `content_is_home_page`, 
                                                      `content_is_section_header`, 
                                                      `content_show_in_menu`, 
                                                      `content_show_in_footer`, 
                                                      `content_collapsed`, 
                                                      `content_image`,   
                                                      `content_menu_order`, 
                                                      `content_is_active`, 
                                                      `content_target`, 
                                                      `content_attribute_1`, 
                                                      `content_last_modified_by`, 
                                                      `content_created_date`, 
                                                      `content_modified_date`) 
                                              VALUES (NULL,
                                                      '$cct_id',
                                                      '$content_parent_id',
                                                      '$content_hierarchy_ids',
                                                      '$content_hierarchy_level',
                                                      '$content_has_children',
                                                      '$content_is_home_page',
                                                      '$content_is_section_header',
                                                      '$content_show_in_menu',
                                                      '$content_show_in_footer',
                                                      '$content_collapsed',
                                                      $content_image_name_db,
                                                      '$content_menu_order',
                                                      '$content_is_active',
                                                      $content_target,
                                                      $content_attribute_1, 
                                                      '$user_id',
                                                      NOW(),
                                                      NOW())";
      $all_queries .= "<br>".$query_insert_content;
      $result_insert_content = mysqli_query($db_link, $query_insert_content);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 1 `contents` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $content_id = mysqli_insert_id($db_link);

      //update the content's `content_hierarchy_ids` after insertion
      $content_hierarchy_ids_list = "";
      if($content_parent_id != 0) {
        $content_hierarchy_ids = get_contents_hierarchy_ids($content_parent_id);
        $content_hierarchy_ids_list .= "$content_hierarchy_ids.$content_id";
      }
      else {
        $content_hierarchy_ids_list = $content_id;
      }

      $query_update_parent = "UPDATE `contents` SET `content_hierarchy_ids` = '$content_hierarchy_ids_list' WHERE `content_id` = '$content_id'";
      $all_queries .= "<br>".$query_update_parent;
      $result_update_parent = mysqli_query($db_link, $query_update_parent);
      if(!$result_update_parent) {
        echo $languages['sql_error_update']." - 2 `contents` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      //update the content's `content_hierarchy_ids` after insertion
      
      foreach($_POST['content_name'] as $language_id => $content_name) {
        
        $content_name = mysqli_real_escape_string($db_link, $content_name);
        $content_menu_text = mysqli_real_escape_string($db_link, $_POST['content_menu_text'][$language_id]);
        $content_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_title'][$language_id]));
        $content_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_description'][$language_id]));
        $content_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_keywords'][$language_id]));
        if($cct_id == 4) {
          //redirect url
          $content_summary = "NULL";
          $content_text = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_redirect_url'][$language_id]));
        }
        else {
          $content_summary = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_summary'][$language_id]));
          $content_text = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_text'][$language_id]));
        }
        $content_pretty_url = $_POST['content_pretty_url'][$language_id];
        $content_desc_is_active = (isset($_POST['content_desc_is_active'][$language_id])) ? 1 : 0;

        $query_insert_descriptions = "INSERT INTO `contents_descriptions`(`content_id`, 
                                                                          `language_id`, 
                                                                          `content_name`, 
                                                                          `content_menu_text`, 
                                                                          `content_meta_title`, 
                                                                          `content_meta_keywords`, 
                                                                          `content_meta_description`, 
                                                                          `content_summary`, 
                                                                          `content_text`, 
                                                                          `content_pretty_url`, 
                                                                          `content_desc_is_active`) 
                                                                  VALUES ('$content_id',
                                                                          '$language_id',
                                                                          '$content_name',
                                                                          '$content_menu_text',
                                                                          $content_meta_title,
                                                                          $content_meta_keywords,
                                                                          $content_meta_description,
                                                                          $content_summary,
                                                                          $content_text,
                                                                          '$content_pretty_url',
                                                                          '$content_desc_is_active')";
        //echo $query_insert_descriptions;
        $all_queries .= "<br>".$query_insert_descriptions;
        $result_insert_descriptions = mysqli_query($db_link, $query_insert_descriptions);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 3 `contents_descriptions` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        
      }

      if($content_image_set) {

        if(is_uploaded_file($image_tmp_name)) {
          move_uploaded_file($image_tmp_name, $upload_path.$image_name_full);
        }
        else {
          echo $languages['image_uploading_error'];
          exit;
        }

        $file = $upload_path.$image_name_full;

        list($width,$height) = getimagesize($file);

        $image = new SimpleImage();
        $image->load($file);

        $image_thumb_name = $image_name."_thumb.".$image_exstension;
        $image_thumb = $upload_path.$image_thumb_name;

        switch($image_exstension) {
          case "gif" : $image_type = 1;
            break;
          case "jpg" : $image_type = 2;
            break;
          case "jpeg" : $image_type = 2;
            break;
          case "png" : $image_type = 3;
            break;
        }

        if($width > $height) {
          $image->resizeToWidth(340);

          $image->save($image_thumb,$image_type);

        }
        else {
          $image->resizeToHeight(211);

          $image->save($image_thumb,$image_type);
        }
        
      } //if($content_image_set)

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
    }//if(empty($content_errors))
      
  }//if(isset($_POST['submit_content']))
  else {
    if(isset($_POST['content_type_id'])) $cct_id = $_POST['content_type_id'];
    else $cct_id = 1; //default content_type is content
  }

  $page_title = $languages['page_title_add_new_content'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_pages'];?>"><?=$languages['header_contents'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_add_new_content'];?>
      </div>
      
      <h1 id="pagetitle"><?=$languages['header_add_new_content'];?></h1>
      
      <ul class="content_tabs tabs">
        <li><a href="#content_main_tab"><?=$languages['header_main_tab'];?></a></li>
        <li><a href="#content_options_tab"><?=$languages['header_options_tab'];?></a></li>
        <li><a href="#content_meta_information_tab"><?=$languages['header_meta_information_tab'];?></a></li>
      </ul>
      <div class="clearfix"></div>
      
      <form method="post" name="edit_content" id="edit_content" class="input_form" enctype="multipart/form-data" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <div class="clearfix margin_bottom">
          <button type="submit" name="submit_content" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        
        <div><i class="info"><?=$languages['text_required_fields'];?></i></div>
       
        <div id="content_main_tab" class="content_tab tab row">
          
          <div class="col-lg-5 col-md-10 col-sm-12 col-xs-12">
            <label for="content_type" class="title"><?=$languages['header_content_type'];?></label>
            <select name="content_type_id" id="content_type_id" onchange="document.edit_content.submit()">
              <?php
                list_content_types_in_select($cct_id);
              ?> 
            </select>
          </div>
          <div class="clearfix"></div>

          <div class="col-lg-5 col-md-10 col-sm-12 col-xs-12">
            <label for="content_parent" class="title"><?=$languages['header_content_parent'];?></label>
            <select name="content_parent_id_level" id="content_parent_id_level">
              <option value="0|0" level="0"><?=$languages['option_no_content_parent'];?></option>
              <?php list_contents_for_select($parent_id = 0, $path_number = 0, $content_parent_id = $content_parent_id, $current_content_id = 0); ?> 
            </select>
          </div>
          <div class="clearfix"></div><p>&nbsp;</p>

          <ul class="language_tabs tabs">
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
              $class_error = (isset($content_errors['content_name'][$language_id]) || isset($content_errors['content_menu_text'][$language_id])) ? ' class="red"' : "";
?>
            <li<?=$class_error;?>>
              <a href=".tab_<?=$language_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?>
              </a>
            </li>
<?php
            }
          }
?>
          </ul>
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
?>
          <div class="language_tab tab tab_<?=$language_id;?>" data-id="<?=$language_id;?>">
            <div>
              <label for="content_desc_is_active" class="title"><?=$languages['header_content_desc_is_active'];?></label>
              <?php
                if(isset($_POST['content_desc_is_active'][$language_id])) {
                  if($_POST['content_desc_is_active'][$language_id] == 0) echo '<input type="checkbox" name="content_desc_is_active['.$language_id.']" />';
                  else echo '<input type="checkbox" name="content_desc_is_active['.$language_id.']" checked="checked" />';
                }
                else echo '<input type="checkbox" name="content_desc_is_active['.$language_id.']" checked="checked" />';
              ?>
            </div>
            <div class="clearfix"></div>
            
            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
              <label for="content_name" class="title"><?=$languages['header_content_name'];?><span class="red">*</span></label>
              <?php
                if(isset($content_errors['content_name'][$language_id])) {
                  echo "<div class='error'>".$content_errors['content_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="content_name[<?=$language_id;?>]" class="content_name" value="<?php if(isset($_POST['content_name'][$language_id])) echo $_POST['content_name'][$language_id];?>" />
            </div>
            <div class="clearfix"></div>

            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
              <label for="content_menu_text" class="title"><?=$languages['header_content_menu_text'];?><span class="red">*</span></label>
              <?php
                if(isset($content_errors['content_menu_text'][$language_id])) {
                  echo "<div class='error'>".$content_errors['content_menu_text'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="content_menu_text[<?=$language_id;?>]" class="content_menu_text" value="<?php if(isset($_POST['content_menu_text'][$language_id])) echo $_POST['content_menu_text'][$language_id];?>" />
            </div>
            <div class="clearfix"></div>

            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
              <label for="content_pretty_url" class="title"><?=$languages['header_content_pretty_url'];?></label>
              <input type="text" name="content_pretty_url[<?=$language_id;?>]" class="content_pretty_url" value="<?php if(isset($_POST['content_pretty_url'][$language_id])) echo $_POST['content_pretty_url'][$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_content_pretty_url'];?></i>
            </div>
            <div class="clearfix"></div>
<?php
        if($cct_id == 4) {
?>
            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 clearfix">
              <label for="content_redirect_url" class="title"><?=$languages['header_content_redirect_url'];?><span class="red">*</span></label>
              <?php
                if(isset($content_errors['content_redirect_url'])) {
                  echo "<div class='error'>".$content_errors['content_redirect_url']."</div>";
                }
              ?>
              <input type="text" name="content_redirect_url" class="content_redirect_url" value="<?php if(isset($_POST['content_redirect_url'][$language_id])) echo $_POST['content_redirect_url'][$language_id];?>" />
              <div class="clearfix">&nbsp;</div>
            </div>
<?php
        }
        else {
?>
            <div>
              <label for="content_summary" class="title"><?=$languages['header_content_summary'];?></label>
              <?php
                if(isset($content_errors['content_summary']) && $cct_id == 1) {
                  echo "<div class='error'>".$content_errors['content_summary']."</div>";
                }
              ?>
              <textarea name="content_summary[<?=$language_id;?>]" id="ckeditor_content_summary_<?=$language_code;?>"><?php if(isset($_POST['content_summary'][$language_id])) echo $_POST['content_summary'][$language_id];?></textarea>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="content_text" class="title"><?=$languages['header_content_text'];?></label>
              <textarea name="content_text[<?=$language_id;?>]" id="ckeditor_content_text_<?=$language_code;?>"><?php if(isset($_POST['content_text'][$language_id])) echo $_POST['content_text'][$language_id];?></textarea>
            </div>
<?php
        }
?>
            <div class="clearfix">
              <p>&nbsp;</p>
            </div>
          </div>
<?php
            }
          }
?>
        </div>
        
        <div id="content_options_tab" class="content_tab tab row">
          
          <div>
            <label for="content_show_in_menu" class="title"><?=$languages['header_content_show_in_menu'];?></label>
            <?php
              if(isset($content_show_in_menu)) {
                if($content_show_in_menu == 0) echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" />';
                else echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_show_in_footer" class="title"><?=$languages['header_content_show_in_footer'];?></label>
            <?php
              if(isset($content_show_in_footer)) {
                if($content_show_in_footer == 0) echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" />';
                else echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_is_active" class="title"><?=$languages['header_content_is_active'];?></label>
            <?php
              if(isset($content_is_active)) {
                if($content_is_active == 0) echo '<input type="checkbox" name="content_is_active" id="content_is_active" />';
                else echo '<input type="checkbox" name="content_is_active" id="content_is_active" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_is_active" id="content_is_active" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_is_section_header" class="title"><?=$languages['header_content_is_section_header'];?></label>
            <?php
              if(isset($content_is_section_header)) {
                if($content_is_section_header == 0) echo '<input type="checkbox" name="content_is_section_header" id="content_is_section_header" />';
                else echo '<input type="checkbox" name="content_is_section_header" id="content_is_section_header" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_is_section_header" id="content_is_section_header" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_image" class="title"><?=$languages['header_add_image'];?></label>
            <?php
              if(isset($content_errors['content_image'])) {
                foreach($content_errors['content_image'] as $error) {
                  echo "<div class='error'>$error</div>";
                }
              }
            ?>
            <p><input type="file" name="content_image" style="width: auto;" /></p>
          </div>
          
          <div>
            <label for="content_target" class="title"><?=$languages['header_content_target'];?></label>
            <select name="content_target" id="content_target" style="width: auto;">
              <option value=""><?=$languages['option_no_content_target'];?></option>
              <option value="_blank" <?php if(isset($content_target) && $content_target == "_blank") echo "selected" ;?>><?=$languages['option_content_target_blank'];?></option>
            </select>
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_content_target_blank'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div class="hidden">
            <label for="content_attribute_1" class="title"><?=$languages['header_extra_attribute_1'];?></label>
            <?php
              if(isset($content_attribute_1)) {
                if($content_attribute_1 == 0) echo '<input type="checkbox" name="content_attribute_1" id="content_attribute_1" />';
                else echo '<input type="checkbox" name="content_attribute_1" id="content_attribute_1" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_attribute_1" id="content_attribute_1" />';
            ?>
          </div>
          
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        
        </div>
        <!--content_options_tab-->

        <!--content_meta_information_tab-->
        <div id="content_meta_information_tab" class="content_tab tab row">
          
          <ul class="language_tabs tabs">
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
?>
              <li><a href=".tab_<?=$language_id;?>"><img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?></a></li>
<?php
            }
          }
?>
          </ul>
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
?>
          <div class="language_tab tab tab_<?=$language_id;?>" data-id="<?=$language_id;?>">
            <input type="hidden" name="language_ids[<?=$language_id;?>]" value="<?=$language_id;?>" />
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label for="content_meta_title" class="title"><?=$languages['header_content_meta_title'];?></label>
              <input type="text" name="content_meta_title[<?=$language_id;?>]" class="content_meta_title" value="<?php if(isset($_POST['content_meta_title'][$language_id])) echo $_POST['content_meta_title'][$language_id];?>" onkeyup="CountCharacters(this,'55')" />
              <span class="info"><b class="info_b"></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages['content_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_content_meta_title'];?></i>
            </div>
            <div class="clearfix"></div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label for="content_meta_keywords" class="title"><?=$languages['header_content_meta_keywords'];?></label>
              <input type="text" name="content_meta_keywords[<?=$language_id;?>]" class="content_meta_keywords" value="<?php if(isset($_POST['content_meta_keywords'][$language_id])) echo $_POST['content_meta_keywords'][$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_content_meta_keywords'];?></i>
            </div>
            <div class="clearfix"></div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label for="content_meta_description" class="title"><?=$languages['header_content_meta_description'];?></label>
              <textarea name="content_meta_description[<?=$language_id;?>]" class="content_meta_description" onkeyup="CountCharacters(this,'200')"><?php if(isset($_POST['content_meta_description'][$language_id])) echo $_POST['content_meta_description'][$language_id];?></textarea>
              <span class="info"><b class="info_b"></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages['content_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_content_meta_description'];?></i>
            </div>
            <div class="clearfix"></div>
          </div>
<?php
          }
        }
?>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        </div>
        <!--content_meta_information_tab-->
        
        <div>
          <button type="submit" name="submit_content" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
  if($cct_id != 4) {
?>
<!-- CK Configuration -->
  <script type="text/javascript" src="/modules/ckeditor/ckeditor/ckeditor.js"></script>
<!-- CK Configuration -->
<?php
  }
?>
  <script type="text/javascript">
    $(document).ready(function() {
<?php 
  if($cct_id != 4) {
    if(!empty($languages_array)) {
      foreach($languages_array as $row_languages) {

        $language_code = $row_languages['language_code'];
?>
    CKEDITOR.replace('ckeditor_content_summary_<?=$language_code;?>');
    CKEDITOR.replace('ckeditor_content_text_<?=$language_code;?>');
<?php
      }
    }
  }
?>
      // tab switcher
      $(".content_tabs li").removeClass("active");
      $(".content_tab").hide();
      $(".content_tabs li:first").addClass("active");
      $(".input_form .content_tab:first").show();
      $(".content_tabs a").click(function(event) {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".content_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".input_form .content_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end tab switcher
      
      // languages tab switcher
      $.each($(".content_tab"), function(){
        var content_tab_id = $(this).attr("id");
        $("#"+content_tab_id+" .language_tabs li").removeClass("active");
        $("#"+content_tab_id+" .language_tab").hide();
        $("#"+content_tab_id+" .language_tabs li:first").addClass("active");
        $("#"+content_tab_id+" .language_tab:first").show();
      });
      $(".language_tabs a").click(function(event) {
        var perant_tab = $(this).closest(".content_tab");
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        perant_tab.find($(".language_tabs li")).removeClass("active");
        this_link.parent().addClass("active");
        perant_tab.find($(".language_tab")).hide();
        perant_tab.find($(clicked_tab)).fadeIn();
        event.preventDefault();
      });
      // end languages tab switcher
    });
  </script>
</body>
</html>