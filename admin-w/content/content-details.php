<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  include_once 'content-functions.php';
  
  $back_link = "content.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
      
  if(isset($_GET['content_id'])) {
    $current_content_id = $_GET['content_id'];
  }
  $languages_array = get_languages();

  $query_content = "SELECT `contents`.*,`contents_types`.`content_type`,`contents_descriptions`.`content_name`, 
                            CONCAT(`users`.`user_firstname`, ' ', `users`.`user_lastname`) as userfullname
                      FROM `contents`
                INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                 LEFT JOIN `users` ON `users`.`user_id` = `contents`.`content_last_modified_by`
                     WHERE `contents`.`content_id` = '$current_content_id' AND `contents_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_content;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_content_type_id = $content_array['content_type_id'];
    if(!isset($cct_id)) $cct_id = $content_content_type_id;
    if(!isset($change_content_type_id)) $change_content_type_id = $content_content_type_id;
    $content_parent_id = $content_array['content_parent_id'];
    $content_hierarchy_level = $content_array['content_hierarchy_level'];
    $content_is_section_header = $content_array['content_is_section_header'];
    $content_show_in_menu = $content_array['content_show_in_menu'];
    $content_show_in_footer = $content_array['content_show_in_footer'];
    $content_name = stripslashes($content_array['content_name']);
    $content_image = $content_array['content_image'];
    if(!empty($content_image)) {
      $content_image_name_exploded = explode(".", $content_image);
      $image_name = $content_image_name_exploded[0];
      $image_exstension = mb_convert_case($content_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $image_thumb_name = $image_name."_thumb.".$image_exstension;
      $content_image_thumb = "/site/images/contents/$image_thumb_name";
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$content_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
    else {
      $content_image_thumb = "/site/images/no_image_172x120.jpg";
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$content_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
    $content_menu_order = $content_array['content_menu_order'];
    $content_is_active = $content_array['content_is_active'];
    $content_target = $content_array['content_target'];
    $content_attribute_1 = $content_array['content_attribute_1'];
    $content_last_modified_by = $content_array['userfullname']; // user_id
    $content_modified_date = $content_array['content_modified_date'];
  }
  //print_r($content_errors);
  
  $page_title = $languages['page_title_content_details'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_pages'];?>"><?=$languages['header_contents'];?></a>
        <span>&raquo;</span>
        <?=$content_name?>
      </div>

      <h1 id="pagetitle"><?=$content_name?></h1>

      <ul class="content_tabs tabs">
        <li><a href="#content_main_tab" ajax-fn="EditContentMainTab"><?=$languages['header_content_main_tab'];?></a></li>
        <li><a href="#content_options_tab" ajax-fn="EditContentOptionsTab"><?=$languages['header_content_options_tab'];?></a></li>
        <li><a href="#content_meta_information_tab" ajax-fn="EditContentMetaTab"><?=$languages['header_content_meta_information_tab'];?></a></li>
        <li><a href="#content_image_tab" class="images" ajax-fn=""><?=$languages['header_content_image_tab'];?></a></li>
        <?php
          if($_SESSION['admin']['user_id'] == 1) {
        ?>
        <li><a href="#content_inlude_blocks_tab" ajax-fn="EditContentIncludeBlocksTab"><?=$languages['header_content_inlude_blocks'];?></a></li>
        <?php
          }
        ?>
      </ul>
      <div class="clearfix"></div>

      <div id="edit_content" class="input_form">
        <div>
          <a href="javascript:void(0);" onClick="EditContentMainTab('#content_main_tab')" class="save_tab button green">
            <i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save_tab'];?>
          </a>
          <a href="<?=$back_link;?>" class="button blue">
            <i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?>
          </a>
          <input type="hidden" name="language_id" id="language_id" value="<?=$current_language_id;?>" />
          <input type="hidden" name="content_id" id="content_id" value="<?=$current_content_id;?>" />
          <input type="hidden" name="request_uri" id="request_uri" value="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" />
          <input type="hidden" id="text_yes" value="<?=$languages['yes'];?>" />
          <input type="hidden" id="text_no" value="<?=$languages['no'];?>" />
          <input type="hidden" id="text_btn_delete" value="<?=$languages['btn_delete'];?>" />
          <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" />
        </div>
        <p class="clearfix"></p>

        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>

        <!--content_main_tab-->
        <div id="content_main_tab" class="content_tab row tab">
          <form method="post" name="edit_content_main_tab" id="edit_content_main_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
            <input type="hidden" name="change_content_type_id" value="<?=$change_content_type_id;?>" />

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <label for="content_type" class="title"><?=$languages['header_content_type'];?></label>
              <select name="content_type_id" id="content_type_id" onchange="document.edit_content.submit()">
                <?php
                  list_content_types_in_select($cct_id);
                ?> 
              </select>
            </div>
            <div class="clearfix"></div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <label for="content_parent" class="title"><?=$languages['header_content_parent'];?></label>
              <input type="hidden" name="current_content_parent_id" value="<?=$content_parent_id;?>" />
              <input type="hidden" name="current_content_hierarchy_level" value="<?=$content_hierarchy_level;?>" />
              <input type="hidden" name="current_content_menu_order" value="<?=$content_menu_order;?>" />
              <select name="content_parent_id_level" id="content_parent_id_level">
                <option value="0|0" level="0"><?=$languages['option_no_content_parent'];?></option>
                <?php list_contents_for_select($parent_id = 0, $path_number = 0, $content_parent_id, $current_content_id); ?> 
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
              $language_menu_name = $row_languages['language_menu_name'];
              
              $language_record = 0;
              $query_content = "SELECT `contents_descriptions`.*
                                  FROM `contents_descriptions`
                                 WHERE `contents_descriptions`.`content_id` = '$current_content_id' AND `contents_descriptions`.`language_id` = '$language_id'";
              //echo "$query_content<br>";
              $result_content = mysqli_query($db_link, $query_content);
              if(!$result_content) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_content) > 0) {
                $content_array = mysqli_fetch_assoc($result_content);
                $content_name = stripslashes($content_array['content_name']);
                $content_menu_text = stripslashes($content_array['content_menu_text']);
                $content_summary = stripslashes($content_array['content_summary']);
                $content_text = stripslashes($content_array['content_text']);
                $content_pretty_url = $content_array['content_pretty_url'];
                $content_desc_is_active = $content_array['content_desc_is_active'];
                $content_redirect_url = $content_text;
                $language_record = 1;
              }
              else {
                $content_name = "";
                $content_menu_text = "";
                $content_summary = "";
                $content_text = "";
                $content_pretty_url = "";
                $content_redirect_url = "";
                $content_desc_is_active = 0;
              }
?>
            <div class="language_tab tab tab_<?=$language_id;?>" data-id="<?=$language_id;?>">
              <input type="hidden" name="has_record_for_language[<?=$language_id;?>]" value="<?=$language_record;?>" />
              <div>
                <label for="content_desc_is_active" class="title"><?=$languages['header_content_desc_is_active'];?></label>
                <?php
                  if($content_desc_is_active == 0) echo '<input type="checkbox" name="content_desc_is_active['.$language_id.']" />';
                  else echo '<input type="checkbox" name="content_desc_is_active['.$language_id.']" checked="checked" />';
                ?>
              </div>
              <div class="clearfix"></div>

              <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
                <label for="content_name" class="title"><?=$languages['header_content_name'];?><span class="red">*</span></label>
                <?php
                  if(isset($content_errors['content_name'])) {
                    echo "<div class='error'>".$content_errors['content_name']."</div>";
                  }
                ?>
                <input type="text" name="content_name[<?=$language_id;?>]" class="content_name" value="<?php if(isset($content_name)) echo $content_name;?>" />
              </div>
              <div class="clearfix"></div>

              <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
                <label for="content_menu_text" class="title"><?=$languages['header_content_menu_text'];?><span class="red">*</span></label>
                <?php
                  if(isset($content_errors['content_menu_text'])) {
                    echo "<div class='error'>".$content_errors['content_menu_text']."</div>";
                  }
                ?>
                <input type="text" name="content_menu_text[<?=$language_id;?>]" class="content_menu_text" value="<?php if(isset($content_menu_text)) echo $content_menu_text;?>" />
              </div>
              <div class="clearfix"></div>

              <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 clearfix">
                <label for="content_pretty_url" class="title"><?=$languages['header_content_pretty_url'];?></label>
                <input type="hidden" name="current_content_pretty_url[<?=$language_id;?>]" value="<?=$content_pretty_url;?>" />
                <input type="text" name="content_pretty_url[<?=$language_id;?>]" class="content_pretty_url" value="<?php if(isset($content_pretty_url)) echo $content_pretty_url;?>" />
                <div class="clearfix"></div>
                <i class="info"><?=$languages['info_content_pretty_url'];?></i>
              </div>
              <div class="clearfix"></div>

              <div>
                <label for="content_summary" class="title"><?=$languages['header_content_summary'];?></label>
                <?php
                  if(isset($content_errors['content_summary']) && $cct_id == 1) {
                    echo "<div class='error'>".$content_errors['content_summary']."</div>";
                  }
                ?>
                <textarea name="content_summary[<?=$language_id;?>]"><?php if(isset($content_summary)) echo $content_summary;?></textarea>
              </div>
              <div class="clearfix"></div>

              <div>
                <label for="content_text" class="title"><?=$languages['header_content_text'];?></label>
                <textarea name="content_text[<?=$language_id;?>]"><?php if(isset($content_text)) echo $content_text;?></textarea>
              </div>
              <div class="clearfix">
                <p>&nbsp;</p>
              </div>
              
            </div>
<?php
            }
          }
?>
          </form>
        </div>
        <!--content_main_tab-->

        <!--content_options_tab-->
        <div id="content_options_tab" class="content_tab row tab">
          <form method="post" name="edit_content_options_tab" id="edit_content_options_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">

            <div>
              <label for="content_show_in_menu" class="title"><?=$languages['header_content_show_in_menu'];?></label>
              <?php
                if($content_show_in_menu == 0) echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" />';
                else echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" checked="checked" />';
              ?>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="content_show_in_footer" class="title"><?=$languages['header_content_show_in_footer'];?></label>
              <?php
                if($content_show_in_footer == 0) echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" />';
                else echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" checked="checked" />';
              ?>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="content_is_active" class="title"><?=$languages['header_content_is_active'];?></label>
              <?php
                if($content_is_active == 0) echo '<input type="checkbox" name="content_is_active" id="content_is_active" />';
                else echo '<input type="checkbox" name="content_is_active" id="content_is_active" checked="checked" />';
              ?>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="content_is_section_header" class="title"><?=$languages['header_content_is_section_header'];?></label>
              <?php
                if($content_is_section_header == 0) echo '<input type="checkbox" name="content_is_section_header" id="content_is_section_header" />';
                else echo '<input type="checkbox" name="content_is_section_header" id="content_is_section_header" checked="checked" />';
              ?>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="content_target" class="title"><?=$languages['header_content_target'];?></label>
              <select name="content_target" id="content_target" style="width: auto;">
                <option value=""><?=$languages['option_no_content_target'];?></option>
                <option value="_blank" <?php if(isset($content_target) && $content_target == "_blank") echo "selected" ;?>><?=$languages['option_content_target_blank'];?></option>
              </select>
              <div class="clearfix"></div>
              <!--<i class="info"><?=$languages['info_content_target_blank'];?></i>-->
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

            <div>
              <p class="title"><?=$languages['header_content_last_modified_by'];?>: <?=$content_last_modified_by;?></p>
              <div class="clearfix"></div>
            </div>

            <div>
              <p class="title"><?=$languages['header_content_last_modified_date'];?>: <?=$content_modified_date;?></p>
              <div class="clearfix"></div>
            </div>

            <div class="clearfix">
              <p>&nbsp;</p>
            </div>

          </form>
        </div>
        <!--content_options_tab-->

        <!--content_meta_information_tab-->
        <div id="content_meta_information_tab" class="content_tab row tab">
          <form method="post" name="edit_content_meta_information_tab" id="edit_content_meta_information_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">

            <ul class="language_tabs tabs">
<?php
            if(!empty($languages_array)) {
              foreach($languages_array as $row_languages) {

                $language_id = $row_languages['language_id'];
                $language_code = $row_languages['language_code'];
                $language_menu_name = $row_languages['language_menu_name'];
?>
                <li>
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
              
              $language_record = 0;
              $query_content = "SELECT `content_meta_title`,`content_meta_keywords`,`content_meta_description`,`content_pretty_url`
                                  FROM `contents_descriptions`
                                 WHERE `contents_descriptions`.`content_id` = '$current_content_id' AND `contents_descriptions`.`language_id` = '$language_id'";
              //echo "$query_content<br>";
              $result_content = mysqli_query($db_link, $query_content);
              if(!$result_content) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_content) > 0) {
                $content_array = mysqli_fetch_assoc($result_content);
                $content_meta_title = stripslashes($content_array['content_meta_title']);
                $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
                $content_meta_description = stripslashes($content_array['content_meta_description']);
                $content_pretty_url = $content_array['content_pretty_url'];
                $language_record = 1;
              }
              else {
                $content_meta_title = "";
                $content_meta_keywords = "";
                $content_meta_description = "";
                $content_pretty_url = "";
              }
?>
            <div class="language_tab tab tab_<?=$language_id;?>" data-id="<?=$language_id;?>">
              <input type="hidden" name="has_record_for_language[<?=$language_id;?>]" value="<?=$language_record;?>" />
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="content_meta_title" class="title"><?=$languages['header_content_meta_title'];?></label>
                <input type="text" name="content_meta_title[<?=$language_id;?>]" class="content_meta_title" onkeyup="CountCharacters(this,'55')" value="<?php if(isset($content_meta_title)) echo $content_meta_title;?>" style="width: 60%;" />
                <span class="info"><b class="info_b"></b></span>
                <span class="warning red" style="display: none;"><b><?=$languages['content_meta_characters_warning'];?></b></span>
                <div class="clearfix"></div>
                <i class="info"><?=$languages['info_content_meta_title'];?></i>
              </div>
              <div class="clearfix"></div>

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="content_meta_keywords" class="title"><?=$languages['header_content_meta_keywords'];?></label>
                <input type="text" name="content_meta_keywords[<?=$language_id;?>]" class="content_meta_keywords" value="<?php if(isset($content_meta_keywords)) echo $content_meta_keywords;?>" style="width: 60%;" />
                <div class="clearfix"></div>
                <i class="info"><?=$languages['info_content_meta_keywords'];?></i>
              </div>
              <div class="clearfix"></div>

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label for="content_meta_description" class="title"><?=$languages['header_content_meta_description'];?></label>
                <textarea name="content_meta_description[<?=$language_id;?>]" class="content_meta_description" onkeyup="CountCharacters(this,'200')" style="width: 60%;"><?=$content_meta_description;?></textarea>
                <span class="info"><b class="info_b"></b></span>
                <span class="warning red" style="display: none;"><b><?=$languages['content_meta_characters_warning'];?></b></span>
                <div class="clearfix"></div>
                <i class="info"><?=$languages['info_content_meta_description'];?></i>
              </div>
              <div class="clearfix"></div>

              <div>
                <label for="check_meta_data" class="title"><?=$languages['header_content_check_meta_data'];?></label>
<?php
                $content_link = DOMAIN."/$language_code/$content_pretty_url";
?>
                <input type="text" name="check_meta_data" value="<?=$content_link;?>" style="width: 60%;">
                &nbsp;&nbsp;&nbsp;
                <a href="http://analyzer.metatags.org/" title="<?=$languages['header_content_check_meta_data'];?>" target="_blank">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/info.gif" class="systemicon" width="16" height="16" alt="<?=$languages['header_content_check_meta_data'];?>" />
                </a>
                <div class="clearfix"></div>
                <i class="info"><?=$languages['info_check_meta_data'];?></i>
              </div>

              <div class="clearfix">
                <p>&nbsp;</p>
              </div>
              
            </div>
<?php
          }
        }
?>

          </form>
        </div>
        <!--content_meta_information_tab-->

        <!--content_image_tab-->
        <div id="content_image_tab" class="content_tab row tab">

          <h2><?=$languages['header_current_image'];?> </h2>
          <p></p>
          <p><i class="info"><?=$languages['info_image']." ".$languages['btn_save'];?></i></p>

          <div id="dropzone" style="padding-bottom: 410px;">
            <div id="current_image">
              <img src="<?=$content_image_thumb;?>" <?=$thumb_image_dimensions;?>>
            </div>
            <p>&nbsp;</p>
            <h2><?=$languages['header_change_image'];?></h2>
          </div>
          <div class="clearfix"></div>

        </div>
        <!--content_image_tab-->

        <!--content_inlude_blocks_tab-->
        <div id="content_inlude_blocks_tab" class="content_tab row tab">
          <form method="post" name="edit_content_inlude_blocks_tab" id="edit_content_inlude_blocks_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">

            <h4 style="margin-bottom: 20px;"><?=$languages['header_content_inlude_blocks'];?></h4>

            <ul class="include_block_tabs tabs">
<?php
            $menu_array = get_content_include_blocks();

            if(!empty($menu_array)) {
              foreach($menu_array as $menu_row) {

                $menu_id = $menu_row['menu_id'];
                $menu_translation_text = stripslashes($menu_row['menu_translation_text']);
?>
              <li><a href="#include_block_<?=$menu_id;?>"><?=$menu_translation_text;?></a></li>
<?php
              }
            }
?>
            </ul>
            <div class="clearfix"></div>
<?php
            if(!empty($menu_array)) {
              foreach($menu_array as $menu_row) {

                $menu_id = $menu_row['menu_id'];
                $menu_translation_text = stripslashes($menu_row['menu_translation_text']);
                $menu_css_id = $menu_row['menu_css_id'];
                $menu_include_block_fn = $menu_row['menu_include_block_fn'];
                $td_category_visibility = "class='hidden'";
                if($menu_css_id == "news-main") $td_category_visibility = "";
                
                $content_inlude_blocks_record_exists = false;
                $blocks_ids_array = array();
                $query = "SELECT `cib_id`,`blocks_ids_string` FROM `contents_inlude_blocks` WHERE `content_id` = '$current_content_id' AND `menu_id` = '$menu_id'";
                $result = mysqli_query($db_link, $query);
                if(!$result) echo mysqli_error($db_link);
                if($cib_count = mysqli_num_rows($result) > 0) {
                  $content_inlude_blocks_record_exists = true;

                  $cib_row = mysqli_fetch_assoc($result);
                  $blocks_ids_array = explode(",", $cib_row['blocks_ids_string']);
                  mysqli_free_result($result);
                }
?>
              
              <!--content_inlude_blocks_tab-->
              <div id="include_block_<?=$menu_id;?>" class="include_block_tab input_form row">
                <input type="hidden" name="content_inlude_blocks_record_exists[<?=$menu_id;?>]" value="<?=$content_inlude_blocks_record_exists;?>">
                <table>
                  <thead>
                    <tr>
                      <th width="5%" title="<?=$languages['title_toggle_checkbox_all'];?>" class="text_left">
                        <input id="selectall" type="checkbox" onclick="SelectAllCheckboxes(this,'multicontent_<?=$menu_id;?>')" />
                      </th>
                      <th width="35%" class="text_left"><?=$languages['header_image'];?></th>
                      <th width="40%" class="text_left"><?=$languages['header_name'];?></th>
                      <th width="20%" class="text_left" <?=$td_category_visibility;?>><?=$languages['header_category'];?></th>
                    </tr>
                  </thead>
                </table>
                <?php 
                  //print_r($blocks_ids_array);
                  if(function_exists($menu_include_block_fn)) echo $menu_include_block_fn($menu_id,$blocks_ids_array);
                  else echo "<div class='alert alert-danger'>функцията: <b>$menu_include_block_fn</b> не съществува</div>";
                ?>
              </div>
<?php
              }
            }
?>
            <div class="clearfix">
              <p>&nbsp;</p>
            </div>

          </form>
        </div>
        <!--content_inlude_blocks_tab-->

        <div>
          <a href="javascript:void(0);" onClick="EditContentMainTab('#content_main_tab')" class="save_tab button green">
            <i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save_tab'];?>
          </a>
          <a href="<?=$back_link;?>" class="button blue">
            <i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?>
          </a>
        </div>
        <div class="clearfix">&nbsp;</div>

        <form action="ajax/upload-images.php" id="filedrop" class="dropzone" style="display: none;">
          <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_tab_success" value="<?=$languages['ajaxmessage_update_tab_success'];?>" >
          <input type="hidden" name="content_id" id="content_id" value="<?=$current_content_id;?>" >
          <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" >
        </form>
        <div class="clearfix"></div>
      </div>

    </div>
  </main>

<?php
  print_html_admin_footer();
?>
  <!-- CK Configuration -->
  <script type="text/javascript" src="/modules/ckeditor/ckeditor/ckeditor.js"></script>
  <!-- CK Configuration -->
  <script type="text/javascript">
    $(document).ready(function() {
      Dropzone.options.filedrop = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            this.removeFile(file);
          });
          this.on("success", function(file, responseImage) {
            if(responseImage == "" || responseImage == " ") {
              
            }
            else {
              $("#current_image").html(responseImage);
              //alert(responseImage);
              //this.removeFile(file);
            }
          });
        }
      };
<?php 
  if($cct_id != 4) {
    if(!empty($languages_array)) {
      foreach($languages_array as $row_languages) {

        $language_id = $row_languages['language_id'];
?>
        CKEDITOR.replace('content_summary[<?=$language_id;?>]');
        CKEDITOR.replace('content_text[<?=$language_id;?>]');
<?php
      }
    }
  }
?>
      // contents tab switcher
      $(".content_tabs li").removeClass("active");
      $(".content_tab").hide();
      $(".content_tabs li:first").addClass("active");
      $(".content_tab:first").show();
      $(".content_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        var ajax_fn = this_link.attr("ajax-fn");
        if(this_link.hasClass("images")) $(".dropzone").show();
        else $(".dropzone").hide();
        $(".save_tab").attr("onClick",""+ajax_fn+"('"+clicked_tab+"')");
        $(".content_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".content_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end contents tab switcher

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
      
      // include blocks tab switcher
      $(".include_block_tabs li").removeClass("active");
      $(".include_block_tab").hide();
      $(".include_block_tabs li:first").addClass("active");
      $(".include_block_tab:first").show();
      $(".include_block_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".include_block_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".include_block_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end include blocks tab switcher
    });
  </script>
</body>
</html>