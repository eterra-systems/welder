<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "news.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  /*
   * initiating some variables and arrays
   */
  $news_category_id = 0;
  $news_post_date_month = false;
  $news_post_date_day = false;
  $news_post_date_year = false;
  $news_post_date_hour = false;
  $news_post_date_minute = false;
  $news_post_date_second = false;
  $news_expiry_start_date_month = false;
  $news_expiry_start_date_day = false;
  $news_expiry_start_date_year = false;
  $news_expiry_start_date_hour = false;
  $news_expiry_start_date_minute = false;
  $news_expiry_start_date_second = false;
  $news_expiry_end_date_month = false;
  $news_expiry_end_date_day = false;
  $news_expiry_end_date_year = false;
  $news_expiry_end_date_hour = false;
  $news_expiry_end_date_minute = false;
  $news_expiry_end_date_second = false;
  $category_ids = array();
  
  if(isset($_POST['add_news'])) {
    
    //echo"<pre>";print_r($_POST);echo"</pre>";
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['news_title'] as $language_id => $news_title) {
      if(empty($news_title)) $news_errors[$language_id]['news_title'] = $languages['required_field_error'];
      //if(empty($_POST['news_summary'][$language_id])) $news_errors[$language_id]['news_summary'] = $languages['required_field_error'];
      if(empty($_POST['news_text'][$language_id])) $news_errors[$language_id]['news_text'] = $languages['required_field_error'];
      
      $news_titles_array[$language_id] = $_POST['news_title'][$language_id];
      $news_summaries_array[$language_id] = $_POST['news_summary'][$language_id];
      $news_texts_array[$language_id] = $_POST['news_text'][$language_id];
      
      $news_pretty_url = $_POST['news_pretty_url'][$language_id];
      if(empty($news_pretty_url)) {
        $news_pretty_url = str_replace(array('\\',':',"'",'?','!','"','.',',','(',')','%',' - ',' ','--'), array('-','-','','','','','','-','-','-','-','-','-','-'), mb_convert_case($_POST['news_title'][$language_id], MB_CASE_LOWER, "UTF-8"));
      }
      else {
        $news_pretty_url = str_replace(array('\\',':',"'",'?','!','"','.',',','(',')','%',' - ',' ','--'), array('-','-','','','','','','-','-','-','-','-','-','-'), mb_convert_case($news_pretty_url, MB_CASE_LOWER, "UTF-8"));
      }
      
      $_POST['news_pretty_url'][$language_id] = $news_pretty_url;
    }
    
    if(isset($_POST['categories'])) {
      $news_categories =  $_POST['categories'];
      $category_ids = $news_categories;
    }
    else $news_errors['categories'] = $languages['error_choosen_category'];
    if(isset($_POST['news_categories_names'])) {
      $news_categories_names =  $_POST['news_categories_names'];
    }
    if(isset($_POST['default_category_id'])) {
      $default_category_id =  $_POST['default_category_id'];
    }
    $news_is_active = $_POST['news_is_active'];
    $news_post_date_year = $_POST['news_post_date_year'];
    $news_post_date_month = $_POST['news_post_date_month'];
    $news_post_date_day = $_POST['news_post_date_day'];
    $news_post_date_hour = $_POST['news_post_date_hour'];
    $news_post_date_minute = $_POST['news_post_date_minute'];
    $news_post_date_second = $_POST['news_post_date_second'];
    $news_post_date = "$news_post_date_year-$news_post_date_month-$news_post_date_day $news_post_date_hour:$news_post_date_minute:$news_post_date_second";
    $use_expiry_info = 0;
    if(isset($_POST['use_expiry_info'])) $use_expiry_info = 1;
    if($use_expiry_info == 1) {
      $news_expiry_start_date_year = $_POST['news_expiry_start_date_year'];
      $news_expiry_start_date_month = $_POST['news_expiry_start_date_month'];
      $news_expiry_start_date_day = $_POST['news_expiry_start_date_day'];
      $news_expiry_start_date_hour = $_POST['news_expiry_start_date_hour'];
      $news_expiry_start_date_minute = $_POST['news_expiry_start_date_minute'];
      $news_expiry_start_date_second = $_POST['news_expiry_start_date_second'];
      $news_expiry_end_date_year = $_POST['news_expiry_end_date_year'];
      $news_expiry_end_date_month = $_POST['news_expiry_end_date_month'];
      $news_expiry_end_date_day = $_POST['news_expiry_end_date_day'];
      $news_expiry_end_date_hour = $_POST['news_expiry_end_date_hour'];
      $news_expiry_end_date_minute = $_POST['news_expiry_end_date_minute'];
      $news_expiry_end_date_second = $_POST['news_expiry_end_date_second'];
      $news_start_time = "'$news_expiry_start_date_year-$news_expiry_start_date_month-$news_expiry_start_date_day $news_expiry_start_date_hour:$news_expiry_start_date_minute:$news_expiry_start_date_second'";
      $news_end_time = "'$news_expiry_end_date_year-$news_expiry_end_date_month-$news_expiry_end_date_day $news_expiry_end_date_hour:$news_expiry_end_date_minute:$news_expiry_end_date_second'";
    }
    else {
      $news_expiry_start_date_month = false;
      $news_expiry_start_date_day = false;
      $news_expiry_start_date_year = false;
      $news_expiry_start_date_hour = false;
      $news_expiry_start_date_minute = false;
      $news_expiry_start_date_second = false;
      $news_expiry_end_date_month = false;
      $news_expiry_end_date_day = false;
      $news_expiry_end_date_year = false;
      $news_expiry_end_date_hour = false;
      $news_expiry_end_date_minute = false;
      $news_expiry_end_date_second = false;
      $news_start_time = "NULL";
      $news_end_time = "NULL";
    }
    
    $input_name = "news_image";
    $max_image_size = "8388608"; //8MB
    $news_image_set = false;
    if(isset($_FILES[$input_name]) && ($_FILES[$input_name]['error'] != 4)) {
      $news_image_set = true;
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/news/";
      $image_params = validate_upload_image($input_name, $upload_path, $max_image_size);
      //echo "<pre>";print_r($image_params);exit;
      if(!empty($image_params['error'])) {
        $news_errors[$input_name] = $image_params['error']; // array that may contain extension, size, upload
      }
      else {
        $image_tmp_name = $image_params['image_tmp_name'];
        $image_name = $image_params['image_name'];
        $image_exstension = $image_params['image_exstension'];
        $image_name_full = $image_params['image_name_full'];
      }
    }
    //echo"<pre>";print_r($news_errors);exit;
    
    if(!isset($news_errors)) {
      //if there are no form errors we can insert the information
    
      $news_author_id = $_SESSION['admin']['user_id'];
      $news_extra = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['news_extra']));
      $news_image_name_db = ($news_image_set) ? "'$image_name_full'" : "NULL";

      $query_insert_news = "INSERT INTO `news`(`news_id`, 
                                                `news_category_id`, 
                                                `news_post_date`, 
                                                `news_start_time`, 
                                                `news_end_time`, 
                                                `news_is_active`, 
                                                `news_image`, 
                                                `news_created_date`, 
                                                `news_modified_date`, 
                                                `news_author_id`, 
                                                `news_extra`)
                                        VALUES (NULL,
                                                '$default_category_id',
                                                '$news_post_date',
                                                $news_start_time,
                                                $news_end_time,
                                                '$news_is_active',
                                                $news_image_name_db,
                                                NOW(),
                                                NOW(),
                                                '$news_author_id',
                                                $news_extra)";
      //echo $query_insert_news;exit;
      $all_queries .= "<br>".$query_insert_news;
      $result_insert_news = mysqli_query($db_link, $query_insert_news);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 1 INSERT INTO `news`".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $news_id = mysqli_insert_id($db_link);
      
      foreach($news_titles_array as $language_id => $news_title) {
        
        $news_title_db = mysqli_real_escape_string($db_link, $news_title);
        $news_summary_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $news_summaries_array[$language_id]));
        $news_text_db = mysqli_real_escape_string($db_link, $news_texts_array[$language_id]);
        $news_pretty_url = mysqli_real_escape_string($db_link, $_POST['news_pretty_url'][$language_id]);
        $news_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['news_meta_title'][$language_id]));
        $news_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['news_meta_description'][$language_id]));
        $news_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['news_meta_keywords'][$language_id]));
      
        $query_insert_news_descriptions = "INSERT INTO `news_descriptions`(`news_id`, 
                                                                          `language_id`, 
                                                                          `news_title`, 
                                                                          `news_summary`, 
                                                                          `news_text`,
                                                                          `news_pretty_url`,
                                                                          `news_meta_title`,
                                                                          `news_meta_description`,
                                                                          `news_meta_keywords`)
                                                                  VALUES ('$news_id',
                                                                          '$language_id',
                                                                          '$news_title_db',
                                                                          $news_summary_db,
                                                                          '$news_text_db',
                                                                          '$news_pretty_url',
                                                                          $news_meta_title,
                                                                          $news_meta_description,
                                                                          $news_meta_keywords)";
        $all_queries .= "<br>".$query_insert_news_descriptions;
        $result_insert_news_descriptions = mysqli_query($db_link, $query_insert_news_descriptions);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 2 INSERT INTO `news_descriptions`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      foreach($news_categories as $category_id) {

        $cat_is_default = ($default_category_id == $category_id) ? 1 : 0;
        $news_sort_order = get_news_highest_order_value_for_category($category_id)+1;
        $query_insert_news_to_cat = "INSERT INTO `news_to_news_category`(`news_id`, `news_category_id`,`cat_is_default`,`news_sort_order`) 
                                                                  VALUES ('$news_id','$category_id','$cat_is_default','$news_sort_order')";
        //echo $query_insert_news_to_cat."<br>";
        $all_queries .= "<br>\n".$query_insert_news_to_cat;
        $result_insert_news_to_cat = mysqli_query($db_link, $query_insert_news_to_cat);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 3 insert `news_to_news_category`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
      
      if($news_image_set) {

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

        $image_sidebar_thumb_name = $image_name."_sidebar_thumb.".$image_exstension;
        $image_sidebar_thumb = $upload_path.$image_sidebar_thumb_name;

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

        if($width > 1280) {
          $image->resizeToWidth(1280);
        }

        $image->save($file,$image_type);

        $image->resizeToWidth(750);

        $image->save($image_thumb,$image_type);

        $image->resizeToWidth(100);

        $image->save($image_sidebar_thumb,$image_type);

      }
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
      
    } //if(!isset($news_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages['page_title_add_new_news'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_news'];?>"><?=$languages['header_news'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_add_new_news'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_add_new_news'];?></h1>
<?php
    if($_SESSION['users_rights_add'] == 1) {
?>
      <ul class="news_tabs tabs">
        <li><a href="#news_main_tab" class="main_tab"><?=$languages['header_main_tab'];?></a></li>
        <li <?php if(isset($news_errors['categories'])) echo "class='red error'"?>><a href="#news_categories_tab"><?=$languages['header_categories_tab'];?></a></li>
        <li><a href="#news_meta_information_tab"><?=$languages['header_meta_information_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>
      
      <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        <div class="margin_bottom">
          <button type="submit" name="add_news" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>
        
      <!--news_main_tab-->
      <div id="news_main_tab" class="news_tab tab">
        <div>
          <label for="news_is_active" class="title"><?=$languages['header_news_status'];?><span class="red">*</span></label>
          <select name="news_is_active" id="news_is_active" style="width: auto;">
            <option value="0"><?=$languages['header_news_status_draft'];?></option>
            <option value="1" selected="selected"><?=$languages['header_news_status_published'];?></option>
          </select>
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="news_post_date" class="title"><?=$languages['header_news_post_date'];?></label>
 <?php
          list_date_months_in_select("news_post_date_month",$news_post_date_month);
          list_date_days_in_select("news_post_date_day",$news_post_date_day);
          list_date_years_in_select("news_post_date_year",$news_post_date_year);
          list_date_hours_in_select("news_post_date_hour",$news_post_date_hour);
          list_date_minutes_in_select("news_post_date_minute",$news_post_date_minute);
          list_date_seconds_in_select("news_post_date_second",$news_post_date_second);
 ?>
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="use_expiry_info" class="title"><?=$languages['header_news_expiry_info'];?></label>
          <input type="checkbox" name="use_expiry_info" id="use_expiry_info" onclick="ToggleCollapse('expiry_info');" <?php if(isset($use_expiry_info) && $use_expiry_info == 1) echo 'checked="checked"' ;?>>
        </div>
        <p class="clearfix"></p>
        
        <div id="expiry_info" style="<?php if(isset($use_expiry_info) && $use_expiry_info == 1) echo 'display: block;'; else echo "display: none;"; ;?>">
          <div>
            <label for="news_expiry_start_date" class="title"><?=$languages['header_news_expiry_start_date'];?></label>
<?php
            list_date_months_in_select("news_expiry_start_date_month",$news_expiry_start_date_month);
            list_date_days_in_select("news_expiry_start_date_day",$news_expiry_start_date_day);
            list_date_years_in_select("news_expiry_start_date_year",$news_expiry_start_date_year);
            list_date_hours_in_select("news_expiry_start_date_hour",$news_expiry_start_date_hour);
            list_date_minutes_in_select("news_expiry_start_date_minute",$news_expiry_start_date_minute);
            list_date_seconds_in_select("news_expiry_start_date_second",$news_expiry_start_date_second);
 ?>        
          </div>
          <div>
            <label for="news_expiry_end_date" class="title"><?=$languages['header_news_expiry_end_date'];?></label>
<?php
            list_date_months_in_select("news_expiry_end_date_month",$news_expiry_end_date_month);
            list_date_days_in_select("news_expiry_end_date_day",$news_expiry_end_date_day);
            list_date_years_in_select("news_expiry_end_date_year",$news_expiry_end_date_year);
            list_date_hours_in_select("news_expiry_end_date_hour",$news_expiry_end_date_hour);
            list_date_minutes_in_select("news_expiry_end_date_minute",$news_expiry_end_date_minute);
            list_date_seconds_in_select("news_expiry_end_date_second",$news_expiry_end_date_second);
 ?>        
          </div>
        </div>
        <p class="clearfix"></p>
        
        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12 hidden">
          <label for="news_extra" class="title"><?=$languages['header_news_extra'];?></label>
          <input type="text" name="news_extra" id="news_extra" value="<?php if(isset($_POST['news_extra'])) echo $_POST['news_extra'];?>">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="news_image" class="title"><?=$languages['header_add_image'];?> (680x270px)</label>
          <?php
            if(isset($news_errors['news_image'])) {
              echo "<div class='error'>".$news_errors['news_image']."</div>";
            }
          ?>
          <p><input type="file" name="news_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
            $class_error = (isset($news_errors[$language_id])) ? ' class="red"' : "";
?>
            <li<?=$class_error;?>>
              <a href="#<?=$language_code;?>">
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
        foreach($languages_array as $key => $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          $language_menu_name = $row_languages['language_menu_name'];
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <label for="news_title" class="title"><?=$languages['header_news_title'];?><span class="red">*</span></label>
            <?php
              if(isset($news_errors[$language_id]['news_title'])) {
                echo "<div class='error'>".$news_errors[$language_id]['news_title']."</div>";
              }
            ?>
            <input type="text" name="news_title[<?=$language_id;?>]" class="news_title" value="<?php if(isset($_POST['news_title'][$language_id])) echo $_POST['news_title'][$language_id];?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="news_summary" class="title"><?=$languages['header_news_summary'];?></label>
            <?php
              if(isset($news_errors[$language_id]['news_summary'])) {
                echo "<div class='error'>".$news_errors[$language_id]['news_summary']."</div>";
              }
            ?>
            <textarea name="news_summary[<?=$language_id;?>]" id="ckeditor_news_summary_<?=$language_code;?>" class="default_text"><?php if(isset($_POST['news_summary'][$language_id])) echo $_POST['news_summary'][$language_id];?></textarea>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>

          <div>
            <label for="news_text" class="title"><?=$languages['header_news_text'];?><span class="red">*</span></label>
            <?php
              if(isset($news_errors[$language_id]['news_text'])) {
                echo "<div class='error'>".$news_errors[$language_id]['news_text']."</div>";
              }
            ?>
            <textarea name="news_text[<?=$language_id;?>]" id="ckeditor_news_text_<?=$language_code;?>" class="default_text"><?php if(isset($_POST['news_text'][$language_id])) echo $_POST['news_text'][$language_id];?></textarea>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        </div>
<?php
        } //foreach($languages_array)
      } //if(!empty($languages_array))
?>
      </div>
      <!--news_main_tab-->
      
      <!--news_meta_information_tab-->
      <div id="news_meta_information_tab" class="news_tab tab row">

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
            <label for="news_meta_title" class="title"><?=$languages['header_meta_title'];?></label>
            <input type="text" name="news_meta_title[<?=$language_id;?>]" class="news_meta_title" onkeyup="CountCharacters(this,'55')" value="<?php if(isset($_POST['news_meta_title'][$language_id])) echo $_POST['news_meta_title'][$language_id];?>" />
            <span class="info"><b class="info_b"></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages['meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_meta_title'];?></i>
          </div>
          <div class="clearfix"></div>

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="news_meta_keywords" class="title"><?=$languages['header_meta_keywords'];?></label>
            <input type="text" name="news_meta_keywords[<?=$language_id;?>]" class="news_meta_keywords" value="<?php if(isset($_POST['news_meta_keywords'][$language_id])) echo $_POST['news_meta_keywords'][$language_id];?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_meta_keywords'];?></i>
          </div>
          <div class="clearfix"></div>

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="news_meta_description" class="title"><?=$languages['header_meta_description'];?></label>
            <textarea name="news_meta_description[<?=$language_id;?>]" class="news_meta_description" onkeyup="CountCharacters(this,'200')"/><?php if(isset($_POST['news_meta_description'][$language_id])) echo $_POST['news_meta_description'][$language_id];?></textarea>
            <span class="info"><b class="info_b"></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages['meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_meta_description'];?></i>
          </div>
          <div class="clearfix"></div>

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="news_pretty_url" class="title"><?=$languages['header_pretty_url'];?></label>
            <input type="text" name="news_pretty_url[<?=$language_id;?>]" class="news_pretty_url" value="<?php if(isset($_POST['news_pretty_url'][$language_id])) echo $_POST['news_pretty_url'][$language_id];?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_pretty_url'];?></i>
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
      <!--news_meta_information_tab-->
        
      <!--news_tab-->
      <div id="news_categories_tab" class="news_tab tab">

        <div>
          <label for="news_categories" class="title"><?=$languages['header_news_categories'];?><span class="red">*</span></label>
          <div class="tree">
            <ul>
              <?php list_news_categories_with_checkboxes($cat_parent_id = 0, $category_ids); ?>
            </ul>
          </div>
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="news_categories" class="title"><?=$languages['header_default_category'];?></label>
          <?php
            if(isset($news_errors['categories'])) {
              echo "<div class='error'>".$news_errors['categories']."</div>";
            }
          ?>
          <select name="default_category_id" id="default_category_id" style="width: auto;">
<?php
          if(isset($news_categories)) {
            foreach($news_categories as $category_id) {

              $news_cat_name = $news_categories_names[$category_id];
              $selected = ($default_category_id == $category_id) ? 'selected="selected"' : "";
?>
              <option value="<?=$category_id;?>" <?=$selected;?>><?=$news_cat_name;?></option>
<?php
            }
          }   
?>
          </select>
        </div>
        <div id="ajax_result" class="clearfix">&nbsp;</div>

      </div>
      <!--news_tab-->
      
      <div>
        <button type="submit" name="add_news" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
        <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
      </div>
      <div class="clearfix"></div>
        
    </form>
<?php
    }
    else {
?>
      <div class="alert alert-danger"><h2><?=$languages['text_no_add_rights'];?></h2></div> 
<?php
    }
?>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="/modules/ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {
              
          $language_code = $row_languages['language_code'];
?>
          CKEDITOR.replace('ckeditor_news_summary_<?=$language_code;?>');
          CKEDITOR.replace('ckeditor_news_text_<?=$language_code;?>');
<?php
        }
      }
?>
      // news tab switcher
      $(".news_tabs li").removeClass("active");
      $(".news_tab").hide();
      $(".news_tabs li:first").addClass("active");
      $(".news_tab:first").show();
      $(".news_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        var ajax_fn = this_link.attr("ajax-fn");
        if(this_link.hasClass("images")) {
          $("#dropzoneGallery").show();
          $("#dropzoneDefaultImg").hide();
        }
        else if(this_link.hasClass("main_tab")) {
          $("#dropzoneGallery").show();
          $("#dropzoneDefaultImg").hide();
        }
        else {
          $("#dropzoneDefaultImg").hide();
          $("#dropzoneGallery").hide();
        }
        $(".save_news_tab").attr("onClick",""+ajax_fn+"('"+clicked_tab+"')");
        $(".news_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".news_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end news tab switcher
     
      // languages tab switcher
      $.each($(".news_tab"), function(){
        var news_tab_id = $(this).attr("id");
        $("#"+news_tab_id+" .language_tabs li").removeClass("active");
        $("#"+news_tab_id+" .language_tab").hide();
        $("#"+news_tab_id+" .language_tabs li:first").addClass("active");
        $("#"+news_tab_id+" .language_tab:first").show();
      });
      $(".language_tabs a").click(function(event) {
        var perant_tab = $(this).closest(".news_tab");
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        perant_tab.find($(".language_tabs li")).removeClass("active");
        this_link.parent().addClass("active");
        perant_tab.find($(".language_tab")).hide();
        perant_tab.find($(clicked_tab)).fadeIn();
        event.preventDefault();
      });
      // end languages tab switcher
      
      //start family tree
      $.each($(".tree li.expandable"), function(){
          var checked_cat = 0;
          var current_list = $(this);
          var current_list_level = current_list.attr("data-level");
          var checkboxes = current_list.find("input[class!='level_1']");
          $.each($(checkboxes), function(){
              if($(this).is(":checked")) checked_cat++;
          });
          if(checked_cat > 0) {
            current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_box").show();
            current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_digits").html(checked_cat);
            if(checked_cat > 1) {
              current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_text").html("подкатегории избрани");
            }
            else {
              current_list.find("a.dropdown_link_"+current_list_level+" .news_cat_count_text").html("подкатегория избрана");
            }
          }
      });
      $('.tree input[type="checkbox"]').on('click', function (e) {
          var state = $(this).is(":checked");
          var category_id = $(this).val();
          var category_name = $(".tree li#"+category_id+" .category_name").html();
          //console.log(state);return;
          if(state) {  
            $("#default_category_id").append("<option value='"+category_id+"'>"+category_name+"</option>");
            $("input.category_name_"+category_id).attr("disabled",false);
          }
          else {
            $('#default_category_id option[value='+category_id+']').remove();
            $("input.category_name_"+category_id).attr("disabled",true);
          }
          e.stopPropagation();
      });
      $('.tree li.expandable .fa, .tree li.expandable .dropdown_link').on('click', function (e) {
          var current_tree_parent = $(this).parent('.expandable');
          var current_tree_id = current_tree_parent.attr('id');
          var child_ul = $(this).parent('.expandable').find(".expandable_ul_"+current_tree_id);
          if(child_ul.is(":visible")) {
            child_ul.hide('fast');
            current_tree_parent.removeClass("active_parent_tree");
            current_tree_parent.find(".fa_"+current_tree_id).removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
          }
          else {
            child_ul.show('fast');
            current_tree_parent.addClass("active_parent_tree");
            current_tree_parent.find(".fa_"+current_tree_id).removeClass("fa-plus-square-o").addClass("fa-minus-square-o");
          }
          e.stopPropagation();
      });
      //end family tree
    });
  </script>
</body>
</html>