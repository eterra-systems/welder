<?php
  $back_link = "news.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
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
  
  $query_news_details = "SELECT `news`.`news_category_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                                `news`.`news_is_active`,`news`.`news_image`,`news`.`news_created_date`,`news`.`news_modified_date`,`news`.`news_extra`,
                                CONCAT(`users`.`user_firstname`,' ',`users`.`user_lastname`) as `user_fullname`
                           FROM `news` 
                     INNER JOIN `users` ON `users`.`user_id` = `news`.`news_author_id`
                          WHERE `news`.`news_id` = '$current_news_id'";
  //echo $query_news_details;exit;
  $result_news_details = mysqli_query($db_link, $query_news_details);
  if(!$result_news_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_details) > 0) {
    $news_details = mysqli_fetch_assoc($result_news_details);

    $default_category_id = $news_details['news_category_id'];
    $news_post_date = $news_details['news_post_date'];
    $news_post_date_month = date("m", strtotime($news_post_date));
    $news_post_date_day = date("d", strtotime($news_post_date));
    $news_post_date_year = date("Y", strtotime($news_post_date));
    $news_post_date_hour = date("H", strtotime($news_post_date));
    $news_post_date_minute = date("i", strtotime($news_post_date));
    $news_post_date_second = date("s", strtotime($news_post_date));
    $news_start_time = $news_details['news_start_time'];
    if(!is_null($news_start_time)) {
      $news_expiry_start_date_month = date("m", strtotime($news_start_time));
      $news_expiry_start_date_day = date("d", strtotime($news_start_time));
      $news_expiry_start_date_year = date("Y", strtotime($news_start_time));
      $news_expiry_start_date_hour = date("H", strtotime($news_start_time));
      $news_expiry_start_date_minute = date("i", strtotime($news_start_time));
      $news_expiry_start_date_second = date("s", strtotime($news_start_time));
    }
    $news_end_time = $news_details['news_end_time'];
    if(!is_null($news_start_time)) {
      $news_expiry_end_date_month = date("m", strtotime($news_end_time));
      $news_expiry_end_date_day = date("d", strtotime($news_end_time));
      $news_expiry_end_date_year = date("Y", strtotime($news_end_time));
      $news_expiry_end_date_hour = date("H", strtotime($news_end_time));
      $news_expiry_end_date_minute = date("i", strtotime($news_end_time));
      $news_expiry_end_date_second = date("s", strtotime($news_end_time)); 
    } 
    $use_expiry_info = (is_null($news_start_time)) ? 0 : 1;
    $news_is_active = $news_details['news_is_active'];
    $news_images_folder = SITEFOLDERSL."/images/news/";
    if(!empty($news_details['news_image'])) {
      $news_image = $news_details['news_image'];
      $news_image_exploded = explode(".", $news_image);
      $news_image_name = $news_image_exploded[0];
      $news_image_exstension = $news_image_exploded[1];
      $news_image_thumb = $news_images_folder.$news_image_name."_thumb.".$news_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image);
      @$thumb_image_dimensions = $thumb_image_params[3];
    }
    else {
      $news_image_thumb = SITEFOLDERSL."/images/no_image_172x120.jpg";
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
    $news_created_date = $news_details['news_created_date'];
    $news_modified_date = $news_details['news_modified_date'];
    $news_extra = $news_details['news_extra'];
  }
  
  $page_title = $languages['page_title_add_new_news'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
  
  $category_ids = array();
  $category_ids_list = "";
  $query_categories = "SELECT `news_to_news_category`.`news_category_id`,`news_cat_desc`.`news_cat_name`
                         FROM `news_to_news_category` 
                   INNER JOIN `news_cat_desc` USING(`news_category_id`)
                        WHERE `news_to_news_category`.`news_id` = '$current_news_id' AND`news_cat_desc`.`language_id` = '$current_language_id'";
  //echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $categories_count = mysqli_num_rows($result_categories);
  if($categories_count > 0) {
   
    while($row_categories = mysqli_fetch_assoc($result_categories)) {
      //echo"<pre>";print_r($row_categories);
      $category_id = $row_categories['news_category_id'];
      
      $news_categories[] = $row_categories;
      $category_ids[] = $category_id;
      $category_ids_list .= "$category_id,";
      
    }
  }
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_news_categories'];?>"><?=$languages['header_news_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_add_new_news'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_add_new_news'];?></h1>
      
      <ul class="news_tabs tabs">
        <li><a href="#news_main_tab" ajax-fn="EditNewsMainTab" class="main_tab"><?=$languages['header_main_tab'];?></a></li>
        <li><a href="#news_categories_tab" ajax-fn="EditNewsCategoriesTab"><?=$languages['header_categories_tab'];?></a></li>
        <li><a href="#news_meta_information_tab" ajax-fn="EditNewsMetaTab"><?=$languages['header_meta_information_tab'];?></a></li>
        <li><a href="#news_gallery_tab" ajax-fn="EditNewsGalleryTab" class="images"><?=$languages['header_gallery_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>
      
    <div id="edit_news" class="input_form">
      <div>
        <a href="javascript:;" class="button red float_right delete_news_link" data-id="<?=$current_news_id;?>">
          <i class="fa fa-trash-o" aria-hidden="true"></i> <?=$languages['btn_delete'];?>
        </a>
        <a href="javascript:;" onClick="EditNewsMainTab('#news_main_tab')" class="save_news_tab button green">
          <i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save_tab'];?>
        </a>
        <a href="<?=$back_link;?>" class="button blue">
          <i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?>
        </a>
        <input type="hidden" name="news_id" id="news_id" value="<?=$current_news_id;?>" >
        <input type="hidden" name="language_id" id="language_id" value="<?=$current_language_id;?>" />
        <input type="hidden" name="request_uri" id="request_uri" value="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" />
        <input type="hidden" name="text_yes" id="text_yes" value="<?=$languages['yes'];?>" />
        <input type="hidden" name="text_no" id="text_no" value="<?=$languages['no'];?>" />
        <input type="hidden" name="text_btn_delete" id="text_btn_delete" value="<?=$languages['btn_delete'];?>" />
        <input type="hidden" name="text_drag_and_drop_upload" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" />
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_tab_success" value="<?=$languages['ajaxmessage_update_tab_success'];?>" >
      </div>
      <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
        <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_news_warning']?></p>
      </div>
      <script>
      $(function() {
        $("#modal_confirm").dialog({
          resizable: false,
          width: 400,
          height: 200,
          autoOpen: false,
          modal: true,
          draggable: false,
          closeOnEscape: true,
          dialogClass: "modal_confirm",
          buttons: {
            "<?=$languages['btn_delete'];?>": function() {
              DeleteNews('details');
            },
            "<?=$languages['btn_cancel'];?>": function() {
              $(".delete_news_link").removeClass("active");
              $(this).dialog("close");
            }
          }
        });
        $(".delete_news_link").click(function() {
          $(".delete_news_link").removeClass("active");
          $(this).addClass("active");
          $("#modal_confirm").dialog("open");
        });
      });
      </script>
      <p class="clearfix">&nbsp;</p>

      <p><i class="info"><?=$languages['text_required_fields'];?></i></p>

      <div id="news_main_tab" class="news_tab tab row">
        <form method="post" name="edin_news_main_tab" id="edin_news_main_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">

        <div class="ajax_result"></div>
        <div>
          <label for="news_is_active" class="title"><?=$languages['header_news_status'];?><span class="red">*</span></label>
          <select name="news_is_active" id="news_is_active" style="width: auto;">
            <option value="0"<?php if($news_is_active == 0) echo ' selected="selected"';?>><?=$languages['header_news_status_draft'];?></option>
            <option value="1"<?php if($news_is_active == 1) echo ' selected="selected"';?>><?=$languages['header_news_status_published'];?></option>
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
          <input type="text" name="news_extra" id="news_extra" value="<?php if(isset($news_extra)) echo $news_extra;?>">
        </div>
        <p class="clearfix">&nbsp;</p>

        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
?>
            <li>
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
            $no_record = "";

            if(!isset($_POST['edit_news'])) {
              $query_news_desc = "SELECT `news_title`, `news_summary`, `news_text` 
                                    FROM `news_descriptions` 
                                   WHERE `news_id` = '$current_news_id' AND `language_id` = '$language_id'";
              $result_news_desc = mysqli_query($db_link, $query_news_desc);
              if(!$result_news_desc) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_news_desc) > 0) {
                $news_desc = mysqli_fetch_assoc($result_news_desc);

                $news_titles_array[$language_id] = stripslashes($news_desc['news_title']);
                $news_summaries_array[$language_id] = stripcslashes($news_desc['news_summary']);
                $news_texts_array[$language_id] = stripslashes($news_desc['news_text']);
              }
              else {
                $no_record = '<input type="hidden" name="no_record['.$language_id.']" value="1" >';
              }
            }

            $news_details_link = "";

            if(isset($news_titles_array[$language_id])) {
              $news_title_url = str_replace(" ", "-", mb_convert_case($news_titles_array[$language_id], MB_CASE_LOWER, "UTF-8"));
              $news_details_link = "/$current_lang/$news_title_url?nid=$current_news_id";
            }
?>
          <div id="<?=$language_code;?>" class="language_tab tab" data-id="<?=$language_id;?>">
            <?=$no_record;?>
            <a href="<?=$news_details_link;?>" target="_blank" style="position: relative;top: 8px;">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/view.gif" class="systemicon" alt="<?=$languages['alt_view'];?>" title="<?=$languages['title_view'];?>" width="16" height="16" />
            </a>
            <div class="clearfix">&nbsp;</div>
            <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
              <label for="news_title" class="title"><?=$languages['header_news_title'];?><span class="red">*</span></label>
              <?php
                if(isset($news_errors['news_title'][$language_id])) {
                  echo "<div class='error'>".$news_errors['news_title'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="news_title[<?=$language_id;?>]" class="news_title" value="<?php if(isset($news_titles_array[$language_id])) echo $news_titles_array[$language_id];?>" />
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="news_summary" class="title"><?=$languages['header_news_summary'];?><span class="red">*</span></label>
              <textarea name="news_summary[<?=$language_id;?>]" ><?php if(isset($news_summaries_array[$language_id])) echo $news_summaries_array[$language_id];?></textarea>
            </div>
            <div class="clearfix">
              <p>&nbsp;</p>
            </div>

            <div>
              <label for="news_text" class="title"><?=$languages['header_news_text'];?><span class="red">*</span></label>
              <?php
                if(isset($news_errors['news_text'][$language_id])) {
                  echo "<div class='error'>".$news_errors['news_text'][$language_id]."</div>";
                }
              ?>
              <textarea name="news_text[<?=$language_id;?>]" ><?php if(isset($news_texts_array[$language_id])) echo $news_texts_array[$language_id];?></textarea>
            </div>
            <div class="clearfix"></div>
          </div>
<?php
          }
        }
?>
          <p class="clearfix">&nbsp;</p>
          
          <h4><?=$languages['header_current_image'];?></h4>
          <p></p>
          <p><i class="info"><?=$languages['info_slider_image']." ".$languages['btn_save'];?></i></p>

          <div id="dropzone_current_image" style="padding-bottom: 396px;">
            <div id="current_image">
              <img src="<?=$news_image_thumb;?>" <?=$thumb_image_dimensions;?>>
            </div>
            <p>&nbsp;</p>
            <h4><?=$languages['header_change_image'];?> (840x420px)</h4>
          </div>
          <div class="clearfix"></div>
        </form>
      </div>

      <!--news_categories_tab-->
      <div id="news_categories_tab" class="news_tab tab">
        <form method="post" name="edit_news_categories_tab" id="edit_news_categories_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
          
        <div class="ajax_result"></div>
        <div>
          <label for="news_categories" class="title"><?=$languages['header_news_categories'];?><span class="red">*</span></label>
          <div class="tree">
            <ul>
              <?php list_news_categories_with_checkboxes($cat_parent_id = 0, $category_ids); ?>
            </ul>
          </div>
          <input type="hidden" name="old_categories_list" id="old_categories_list" value="<?=$category_ids_list;?>" />
          <input type="hidden" name="categories_list" id="categories_list" value="<?=$category_ids_list;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="news_categories" class="title"><?=$languages['header_default_category'];?></label>
          <select name="new_default_category_id" id="new_default_category_id" style="width: auto;">
<?php
          foreach($news_categories as $category_row) {

            $category_id = $category_row['news_category_id'];
            $news_cat_name = $category_row['news_cat_name'];
            $selected = ($default_category_id == $category_id) ? 'selected="selected"' : "";
?>
            <option value="<?=$category_id;?>" <?=$selected;?>><?=$news_cat_name;?></option>
<?php
          }
?>
          </select>
          <input type="hidden" name="old_default_category_id" id="old_default_category_id" value="<?=$default_category_id;?>" />
        </div>
        <div class="clearfix">&nbsp;</div>

        </form>
      </div>
      <!--news_categories_tab-->

      <!--news_meta_information_tab-->
      <div id="news_meta_information_tab" class="news_tab tab row">
        <form method="post" name="edit_news_meta_information_tab" id="edit_news_meta_information_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">

        <div class="ajax_result"></div>
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

          $language_record = 0;
          $query_news_descriptions = "SELECT `news_pretty_url`,`news_meta_title`,`news_meta_description`,`news_meta_keywords`
                                        FROM `news_descriptions`
                                       WHERE `news_id` = '$current_news_id' AND `language_id` = '$language_id'";
          //echo $query_news_descriptions;
          $result_news_descriptions = mysqli_query($db_link, $query_news_descriptions);
          if(!$result_news_descriptions) echo mysqli_error($db_link);
          if(mysqli_num_rows($result_news_descriptions) > 0) {
            $news_descriptions_array = mysqli_fetch_assoc($result_news_descriptions);
            //echo"<pre>";print_r($news_array);
            $news_pretty_urls_array[$language_id] = $news_descriptions_array['news_pretty_url'];
            $current_news_pretty_urls[$language_id] = $news_descriptions_array['news_pretty_url'];
            $news_meta_titles_array[$language_id] = $news_descriptions_array['news_meta_title'];
            $news_meta_descriptions_array[$language_id] = $news_descriptions_array['news_meta_description'];
            $news_meta_keywords_array[$language_id] = $news_descriptions_array['news_meta_keywords'];
            $language_record = 1;
          }
          else {
            $news_pretty_urls_array[$language_id] = "";
            $current_news_pretty_urls[$language_id] = "";
            $news_meta_titles_array[$language_id] = "";
            $news_meta_descriptions_array[$language_id] = "";
            $news_meta_keywords_array[$language_id] = "";
          }
?>
        <div class="language_tab tab tab_<?=$language_id;?>" data-id="<?=$language_id;?>">
          <input type="hidden" name="has_record_for_language[<?=$language_id;?>]" value="<?=$language_record;?>" />

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="news_meta_title" class="title"><?=$languages['header_meta_title'];?></label>
            <input type="text" name="news_meta_titles[<?=$language_id;?>]" class="news_meta_titles" onkeyup="CountCharacters(this,'55')" value="<?php if(isset($news_meta_titles_array[$language_id])) echo $news_meta_titles_array[$language_id];?>" />
            <span class="info"><b class="info_b"></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages['news_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_meta_title'];?></i>
          </div>
          <div class="clearfix"></div>

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="news_meta_keywords" class="title"><?=$languages['header_meta_keywords'];?></label>
            <input type="text" name="news_meta_keywords_array[<?=$language_id;?>]" class="news_meta_keywords" value="<?php if(isset($news_meta_keywords_array[$language_id])) echo $news_meta_keywords_array[$language_id];?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_meta_keywords'];?></i>
          </div>
          <div class="clearfix"></div>

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="news_meta_description" class="title"><?=$languages['header_meta_description'];?></label>
            <textarea name="news_meta_descriptions[<?=$language_id;?>]" class="news_meta_descriptions" onkeyup="CountCharacters(this,'200')"/><?php if(isset($news_meta_descriptions_array[$language_id])) echo $news_meta_descriptions_array[$language_id];?></textarea>
            <span class="info"><b class="info_b"></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages['news_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages['info_meta_description'];?></i>
          </div>
          <div class="clearfix"></div>

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label for="news_pretty_url" class="title"><?=$languages['header_pretty_url'];?></label>
            <?php
              if(isset($news_errors['news_pretty_url'][$language_id])) {
                echo "<div class='error'>".$news_errors['news_pretty_url'][$language_id]."</div>";
              }
              if(isset($news_errors['news_pretty_url_is_not_unique'][$language_id])) {
                echo "<div class='error'>".$news_errors['news_pretty_url_is_not_unique'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="news_pretty_urls[<?=$language_id;?>]" id="news_pretty_urls_<?=$language_id;?>" value="<?php if(isset($news_pretty_urls_array[$language_id])) echo $news_pretty_urls_array[$language_id];?>" />
            <input type="hidden" name="current_news_pretty_urls[<?=$language_id;?>]" value="<?php if(isset($current_news_pretty_urls[$language_id])) echo $current_news_pretty_urls[$language_id];?>" />
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
        </form>
      </div>
      <!--news_meta_information_tab-->
        
      <!--news_gallery_tab-->
      <div id="news_gallery_tab" class="news_tab tab">
<?php
        $news_gallery_images_array = get_news_images($current_news_id);
        $news_images_folder = SITEFOLDERSL."/images/news/thumbs/";
?>
        <h4><?=$languages['header_news_images'];?></h4>
        <p><i><?=$languages['info_image_default'];?></i></p>
        <ul id="sortable">
<?php
        if(isset($news_gallery_images_array)) {

          foreach($news_gallery_images_array as $news_gallery_image_row) {
            //echo"<pre>";print_r($news_gallery_image_row);
            $news_gallery_image_id = $news_gallery_image_row['news_gallery_id'];
            $news_gallery_image = $news_images_folder.$news_gallery_image_row['ng_name'];
            @$news_gallery_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_gallery_image);
            $news_gallery_image_dimensions = $news_gallery_image_params[3];
?>
            <li id="gallery_image_<?=$news_gallery_image_id?>" data-id="<?=$news_gallery_image_id?>" class="ui-state-default">
              <input type="button" class="delete_img" data-id="<?=$news_gallery_image_id?>" data-image="<?=$news_gallery_image?>" data-type="2" value="<?=$languages['btn_delete'];?>">
              <a class="move_img"><?=$languages['btn_move'];?></a>
              <div class="clearfix"></div>
              <img src="<?=$news_gallery_image?>" <?=$news_gallery_image_dimensions;?> class="dbx-handle" />
            </li>
<?php
          }
        }
?>
        </ul>
        <div class="clearfix"></div>
        <p>&nbsp;</p>

        <h4><?=$languages['header_add_images'];?></h4>

      </div>
        
      <div>
        <a href="javascript:;" onClick="EditNewsMainTab('#news_main_tab')" class="save_news_tab button green">
          <i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save_tab'];?>
        </a>
        <a href="/<?=$_SESSION['admin_dir_name'];?>/news/news.php?news_id=<?=$current_news_id;?>" class="button blue">
          <i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?>
        </a>
      </div>
      <div class="clearfix">&nbsp;</div>
    </div>
      
      <form action="ajax/upload_default_image.php" id="dropzoneDefaultImg" class="dropzone" style="display: block;">
        <input type="hidden" name="news_id" value="<?=$current_news_id;?>" >
      </form>

      <form action="ajax/upload_gallery_images.php" id="dropzoneGallery" class="dropzone" style="display: none;">
        <input type="hidden" name="news_id" value="<?=$current_news_id;?>" >
      </form>
      <div class="clearfix">&nbsp;</div>
    </div>
  </main>
<!--navigation-->

  <!--modal_confirm_delete_img-->
  <div style="display:none;" id="modal_confirm_delete_img" class="clearfix" title="<?=$languages['text_are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;">Сигурни ли сте, че искате да изтриете тази снимка?</p>
  </div>
  
<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="/modules/ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      
      Dropzone.options.dropzoneDefaultImg = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              GetNewsDefaultImage(<?=$current_news_id;?>);
            }
            this.removeFile(file);
          });
          this.on("success", function(file, responseText) {
            if(responseText == "" || responseText == " ") {
              
            }
            else {
              alert(responseText);
              this.removeFile(file);
            }
          });
        }
      };
      
      Dropzone.options.dropzoneGallery = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              GetNewsGalleryImages(<?=$current_news_id;?>);
            }
            this.removeFile(file);
          });
          this.on("success", function(file, responseText) {
            if(responseText == "" || responseText == " ") {
              
            }
            else {
              alert(responseText);
              this.removeFile(file);
            }
          });
        }
      };
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {

          $language_id = $row_languages['language_id'];
?>
          CKEDITOR.replace('news_summary[<?=$language_id;?>]');
          CKEDITOR.replace('news_text[<?=$language_id;?>]');
<?php
        }
      }
?>
      $("#sortable").sortable({
        placeholder: "ui-state-highlight"
      });
      $("#sortable").disableSelection();
      
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
          var categories_ids = $("#categories_list").val();
          //console.log(state);return;
          if(state) {
            categories_ids = $("#old_categories_list").val();
            var is_selected = categories_ids.search(category_id+","); // the method search() returns -1 if no match was found
            console.log(is_selected);
            if(is_selected != '-1') {
              categories_ids = $("#categories_list").val();
              $("#categories_list").val(category_id + "," + categories_ids); 
            }
            $("#new_default_category_id").append("<option value='"+category_id+"'>"+category_name+"</option>");
            $("input.category_name_"+category_id).attr("disabled",false);
          }
          else {
            var new_categories_ids = categories_ids.replace(category_id+",","");
            $("#categories_list").val(new_categories_ids);
            $('#new_default_category_id option[value='+category_id+']').remove();
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
      
      $("#modal_confirm_delete_img").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm_delete_img",
        buttons: {
          "<?=$languages['btn_delete'];?>": function() {
            var image_id = $(".delete_img.active").attr("data-id");
            var image_data = $(".delete_img.active").attr("data-image");
            var image_type = $(".delete_img.active").attr("data-type");
            //alert(image_data);
            DeleteNewsImage(image_id,image_data,image_type);
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(this).dialog("close");
          }
        }
      });
      
      $(".delete_img").click(function() {
        $(".delete_img").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm_delete_img").dialog("open");
      });
    });
  </script>
</body>
</html>