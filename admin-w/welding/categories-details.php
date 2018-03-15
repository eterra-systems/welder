<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "categories.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  $languages_count = count($languages_array);
  
  if(isset($_GET['category_hierarchy_ids'])) {
    $category_hierarchy_ids = $_GET['category_hierarchy_ids'];
  }
  if(isset($_GET['category_id'])) {
    $current_category_id = $_GET['category_id'];
  }
  else {
    exit("Error, missing category_id");
  }

  $query_category = "SELECT `categories`.*,`ctc`.`category_is_active`,`ctc`.`category_show_in_menu`,CONCAT(`users`.`user_firstname`, ' ', `users`.`user_lastname`) as userfullname
                       FROM `categories`
                 INNER JOIN `category_to_category` as `ctc` ON (`ctc`.`category_id` = `categories`.`category_id` AND `ctc`.`category_hierarchy_ids` = '$category_hierarchy_ids')
                  LEFT JOIN `users` ON `users`.`user_id` = `categories`.`category_modified_by`
                      WHERE `categories`.`category_id` = '$current_category_id'";
  //echo $query_category;
  $result_category = mysqli_query($db_link, $query_category);
  if(!$result_category) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category) > 0) {
    $category_array = mysqli_fetch_assoc($result_category);
    //echo"<pre>";print_r($category_array);
    $default_category_id = $category_array['category_parent_id'];
    $category_show_in_menu = $category_array['category_show_in_menu'];
    $category_image = $category_array['category_image'];
    if(!empty($category_image) || !is_null($category_image)) {
      $category_image_exploded = explode(".", $category_image);
      $category_image_name = $category_image_exploded[0];
      $category_image_exstension = $category_image_exploded[1];
      $category_image_thumb = SITEFOLDERSL."/images/category-thumbs/".$category_image_name."_cat_thumb.".$category_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$category_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
    else {
      $category_image_thumb = SITEFOLDERSL."/images/no_image_172x120.jpg";
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$category_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
    $category_is_active = $category_array['category_is_active'];
    $category_attribute_1 = $category_array['category_attribute_1'];
    $category_attribute_2 = $category_array['category_attribute_2'];
    $category_last_modified_by = $category_array['userfullname']; // user_id
    $category_modified_date = $category_array['category_date_modified'];
  }
  
  $page_title = $languages['page_title_edit_category'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
  
  $category_ids = array();
  $category_ids_list = "";
  $query_categories = "SELECT `ctc`.`category_parent_id`,`ctc`.`category_root_id`,`ctc`.`category_hierarchy_ids`,`cd`.`cd_name`
                         FROM `category_to_category` as `ctc`
                   INNER JOIN `categories_descriptions` as `cd` ON `ctc`.`category_parent_id` = `cd`.`category_id`
                        WHERE `ctc`.`category_id` = '$current_category_id' AND`cd`.`language_id` = '$current_language_id'";
  //echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $categories_count = mysqli_num_rows($result_categories);
  if($categories_count > 0) {
   
    $count = strlen($current_category_id);
    
    while($row_categories = mysqli_fetch_assoc($result_categories)) {
      
      $category_hierarchy_ids = str_replace(".", "", $row_categories['category_hierarchy_ids']);
      $category_id = substr_replace($category_hierarchy_ids, "", -$count);
      $row_categories['category_parent_id'] = $category_id;
 
      $category_categories[] = $row_categories;
      $category_ids[] = $category_id;
      $category_ids_list .= "$category_id,";
      
    }
  }
  //print_array_for_debug($category_categories);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_categories'];?>"><?=$languages['header_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_category_edit'];?>
      </div>
      
      <h1 id="pagetitle"><?=$languages['header_category_edit'];?></h1>
      
      <ul class="category_tabs tabs">
        <li><a href="#category_main_tab" ajax-fn="EditCategoryMainTab"><?=$languages['header_main_tab'];?></a></li>
        <li><a href="#category_categories_tab" ajax-fn="EditCategoryCategoriesTab"><?=$languages['header_categories_tab'];?></a></li>
        <li><a href="#category_options_tab" class="images" ajax-fn="EditCategoryOptionsTab"><?=$languages['header_options_tab'];?></a></li>
        <li><a href="#category_meta_information_tab" ajax-fn="EditCategoryMetaTab"><?=$languages['header_meta_information_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>
      
      <div id="edit_category" class="input_form">
        <div>
          <a href="javascript:;" class="button red float_right delete_category_link" data-id="<?=$current_category_id;?>">
            <i class="icon icon_delete_sign"></i><?=$languages['btn_delete'];?>
          </a>
          <a href="javascript:;" onClick="EditCategoryMainTab('#category_main_tab')" class="save_category_tab button green">
            <i class="icon icon_save_sign"></i><?=$languages['btn_save_tab'];?>
          </a>
          <a href="<?=$back_link;?>" class="button blue">
            <i class="icon icon_cancel_sign"></i><?=$languages['btn_cancel'];?>
          </a>
          <input type="hidden" name="language_id" id="language_id" value="<?=$current_language_id;?>" />
          <input type="hidden" name="languages_count" id="languages_count" value="<?=$languages_count;?>" />
          <input type="hidden" name="category_hierarchy_ids" id="category_hierarchy_ids" value="<?=$category_hierarchy_ids;?>" />
          <input type="hidden" name="category_id" id="category_id" value="<?=$current_category_id;?>" />
          <input type="hidden" name="request_uri" id="request_uri" value="<?=$_SERVER['REQUEST_URI'];?>" />
          <input type="hidden" id="text_yes" value="<?=$languages['yes'];?>" />
          <input type="hidden" id="text_no" value="<?=$languages['no'];?>" />
          <input type="hidden" id="text_btn_delete" value="<?=$languages['btn_delete'];?>" />
          <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_tab_success" value="<?=$languages['ajaxmessage_update_tab_success'];?>" >
          <input type="hidden" name="text_drag_and_drop_upload" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" >
        </div>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>
        
        <!--category_main_tab-->
        <div id="category_main_tab" class="category_tab tab row">
          <form method="post" name="edit_category_main_tab" id="edit_category_main_tab" action="<?=$_SERVER['REQUEST_URI'];?>">

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
              
              $query_category_descriptions = "SELECT `cd_name`,`cd_page_title`,`cd_description`,`cd_is_active`
                                                FROM `categories_descriptions`
                                               WHERE `category_id` = '$current_category_id' AND `language_id` = '$language_id'";
              //echo $query_category_descriptions;
              $result_category_descriptions = mysqli_query($db_link, $query_category_descriptions);
              if(!$result_category_descriptions) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_category_descriptions) > 0) {
                $category_descriptions_array = mysqli_fetch_assoc($result_category_descriptions);
                //echo"<pre>";print_r($category_array);
                $cd_names_array[$language_id] = $category_descriptions_array['cd_name'];
                $cd_page_titles_array[$language_id] = $category_descriptions_array['cd_page_title'];
                $cd_descriptions_array[$language_id] = $category_descriptions_array['cd_description'];
                $cd_is_actives_array[$language_id] = $category_descriptions_array['cd_is_active'];
                $language_record = 1;
              }
?>
          <div class="language_tab tab tab_<?=$language_id;?>" data-id="<?=$language_id;?>">
            <input type="hidden" name="has_record_for_language[<?=$language_id;?>]" value="<?=$language_record;?>" />
            
            <div>
              <label for="cd_is_active" class="title"><?=$languages['header_category_is_active'];?></label>
              <?php
                if(isset($cd_is_actives_array[$language_id]) && $cd_is_actives_array[$language_id] == 0) {
                  echo '<input type="checkbox" name="cd_is_active['.$language_id.']" class="cd_is_active" />';
                }
                else {
                  echo '<input type="checkbox" name="cd_is_active['.$language_id.']" class="cd_is_active" checked="checked" />';
                }
              ?>
            </div>
            <div class="clearfix"></div>
            
            <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
              <label for="category_name" class="title"><?=$languages['header_category_name'];?><span class="red">*</span></label>
              <?php
                if(isset($category_errors['cd_name'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="cd_names[<?=$language_id;?>]" class="cd_names" value="<?php if(isset($cd_names_array[$language_id])) echo $cd_names_array[$language_id];?>" />
            </div>
            <div class="clearfix"></div>
            
            <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
              <label for="cd_page_title" class="title"><?=$languages['header_category_page_title'];?><span class="red">*</span></label>
              <?php
                if(isset($category_errors['cd_page_title'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_page_title'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="cd_page_titles[<?=$language_id;?>]" class="cd_page_titles" value="<?php if(isset($cd_page_titles_array[$language_id])) echo $cd_page_titles_array[$language_id];?>" />
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="category_description" class="title"><?=$languages['header_category_description'];?></label>
              <?php
                if(isset($category_errors['category_meta_keywords'])) {
                  echo "<div class='error'>".$category_errors['category_meta_keywords']."</div>";
                }
              ?>
              <textarea name="cd_descriptions[<?=$language_id;?>]" class="cd_descriptions[<?=$language_id;?>]" class="default_text"><?php if(isset($cd_descriptions_array[$language_id])) echo $cd_descriptions_array[$language_id];?></textarea>
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
        <!--category_main_tab-->

      <!--category_categories_tab-->
      <div id="category_categories_tab" class="category_tab tab">
        <form method="post" name="edit_category_categories_tab" id="edit_category_categories_tab" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
          
        <div class="ajax_result"></div>
        <div>
          <label for="category_categories" class="title"><?=$languages['header_categories'];?><span class="red">*</span></label>
          <input type="checkbox" name="select_all" class="select_all"> Избери всички
          <div class="tree">
            <ul>
              <?php list_categories_with_checkboxes($category_parent_id = 0,$category_root_id = 0, $category_ids); ?>
            </ul>
          </div>
          <input type="hidden" name="old_categories_list" id="old_categories_list" value="<?=$category_ids_list;?>" />
          <input type="hidden" name="categories_list" id="categories_list" value="<?=$category_ids_list;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="category_categories" class="title"><?=$languages['header_default_category'];?></label>
          <select name="new_default_category_id" id="new_default_category_id" style="width: auto;">
<?php
          foreach($category_categories as $category_row) {

            $category_parent_id = $category_row['category_parent_id'];
            $category_cat_name = $category_row['cd_name'];
            $selected = ($default_category_id == $category_parent_id) ? 'selected="selected"' : "";
?>
            <option value="<?=$category_parent_id;?>" <?=$selected;?>><?=$category_cat_name;?></option>
<?php
          }
?>
          </select>
          <input type="hidden" name="old_default_category_id" id="old_default_category_id" value="<?=$default_category_id;?>" />
        </div>
        <div class="clearfix">&nbsp;</div>

        </form>
      </div>
      <!--category_categories_tab-->
        
        <!--category_options_tab-->
        <div id="category_options_tab" class="category_tab tab row">
          <form method="post" name="edit_category_options_tab" id="edit_category_options_tab" action="<?=$_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data">
          
          <div>
            <label for="category_show_in_menu" class="title"><?=$languages['header_category_show_in_menu'];?></label>
            <?php
              if(isset($category_show_in_menu) && $category_show_in_menu == 0) echo '<input type="checkbox" name="category_show_in_menu" id="category_show_in_menu" />';
              else echo '<input type="checkbox" name="category_show_in_menu" id="category_show_in_menu" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_is_active" class="title"><?=$languages['header_category_is_active'];?></label>
            <?php
              if(isset($category_is_active) && $category_is_active == 0) echo '<input type="checkbox" name="category_is_active" id="category_is_active" />';
              else echo '<input type="checkbox" name="category_is_active" id="category_is_active" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div class="hidden col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <label for="category_attribute_1" class="title"><?=$languages['header_extra_attribute_1'];?></label>
            <input type="text" name="category_attribute_1" id="category_attribute_1" value="<?php if(isset($category_attribute_1)) echo $category_attribute_1;?>" />
          </div>
          <div class="clearfix"></div>
          
          <div class="hidden col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <label for="category_attribute_2" class="title"><?=$languages['header_extra_attribute_2'];?></label>
            <input type="text" name="category_attribute_2" id="category_attribute_2" value="<?php if(isset($category_attribute_2)) echo $category_attribute_2;?>" />
          </div>
          <p class="clearfix">&nbsp;</p>
          
          <h2><?=$languages['header_current_image'];?></h2>
          <p><i><?=$languages['info_image'];?></i></p>
        
          <div id="dropzone" style="padding-bottom: 360px;">
            <div id="current_image">
              <img src="<?=$category_image_thumb?>" <?=$thumb_image_dimensions;?>>
            </div>
            <p>&nbsp;</p>
            <h2><?=$languages['header_change_image'];?></h2>
          </div>
          <div class="clearfix"></div>
          
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        
          </form>
        </div>
        <form action="ajax/upload-category-image.php" id="filedrop" class="dropzone" style="display: none;">
          <input type="hidden" name="category_id" id="category_id" value="<?=$current_category_id;?>" />
          <input type="hidden" name="category_image" id="category_image" value="<?=$category_image;?>" />
        </form>
        <div class="clearfix"></div>
        <!--category_options_tab-->

        <!--category_meta_information_tab-->
        <div id="category_meta_information_tab" class="category_tab tab row">
          <form method="post" name="edit_category_meta_information_tab" id="edit_category_meta_information_tab" action="<?=$_SERVER['REQUEST_URI'];?>">

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
            $query_category_descriptions = "SELECT `cd_pretty_url`,`cd_meta_title`,`cd_meta_description`,`cd_meta_keywords`
                                              FROM `categories_descriptions`
                                             WHERE `category_id` = '$current_category_id' AND `language_id` = '$language_id'";
            //echo $query_category_descriptions;
            $result_category_descriptions = mysqli_query($db_link, $query_category_descriptions);
            if(!$result_category_descriptions) echo mysqli_error($db_link);
            if(mysqli_num_rows($result_category_descriptions) > 0) {
              $category_descriptions_array = mysqli_fetch_assoc($result_category_descriptions);
              //echo"<pre>";print_r($category_array);
              $cd_pretty_urls_array[$language_id] = $category_descriptions_array['cd_pretty_url'];
              $current_cd_pretty_urls[$language_id] = $category_descriptions_array['cd_pretty_url'];
              $cd_meta_titles_array[$language_id] = $category_descriptions_array['cd_meta_title'];
              $cd_meta_descriptions_array[$language_id] = $category_descriptions_array['cd_meta_description'];
              $cd_meta_keywords_array[$language_id] = $category_descriptions_array['cd_meta_keywords'];
              $language_record = 1;
            }
            else {
              $cd_pretty_urls_array[$language_id] = "";
              $current_cd_pretty_urls[$language_id] = "";
              $cd_meta_titles_array[$language_id] = "";
              $cd_meta_descriptions_array[$language_id] = "";
              $cd_meta_keywords_array[$language_id] = "";
            }
?>
          <div class="language_tab tab tab_<?=$language_id;?>" data-id="<?=$language_id;?>">
            <input type="hidden" name="has_record_for_language[<?=$language_id;?>]" value="<?=$language_record;?>" />
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label for="category_meta_title" class="title"><?=$languages['header_category_meta_title'];?></label>
              <input type="text" name="cd_meta_titles[<?=$language_id;?>]" class="cd_meta_titles" onkeyup="CountCharacters(this,'55')" value="<?php if(isset($cd_meta_titles_array[$language_id])) echo $cd_meta_titles_array[$language_id];?>" />
              <span class="info"><b class="info_b"></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages['category_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_category_meta_title'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label for="category_meta_keywords" class="title"><?=$languages['header_category_meta_keywords'];?></label>
              <input type="text" name="cd_meta_keywords_array[<?=$language_id;?>]" class="cd_meta_keywords" value="<?php if(isset($cd_meta_keywords_array[$language_id])) echo $cd_meta_keywords_array[$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_category_meta_keywords'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label for="category_meta_description" class="title"><?=$languages['header_category_meta_description'];?></label>
              <textarea name="cd_meta_descriptions[<?=$language_id;?>]" class="cd_meta_descriptions" onkeyup="CountCharacters(this,'200')"/><?php if(isset($cd_meta_descriptions_array[$language_id])) echo $cd_meta_descriptions_array[$language_id];?></textarea>
              <span class="info"><b class="info_b"></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages['category_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_category_meta_description'];?></i>
            </div>
            <div class="clearfix"></div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <label for="category_pretty_url" class="title"><?=$languages['header_category_pretty_url'];?></label>
              <?php
                if(isset($category_errors['cd_pretty_url'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_pretty_url'][$language_id]."</div>";
                }
                if(isset($category_errors['cd_pretty_url_is_not_unique'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_pretty_url_is_not_unique'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="cd_pretty_urls[<?=$language_id;?>]" id="cd_pretty_urls_<?=$language_id;?>" value="<?php if(isset($cd_pretty_urls_array[$language_id])) echo $cd_pretty_urls_array[$language_id];?>" />
              <input type="hidden" name="current_cd_pretty_urls[<?=$language_id;?>]" value="<?php if(isset($current_cd_pretty_urls[$language_id])) echo $current_cd_pretty_urls[$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages['info_category_pretty_url'];?></i>
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
        <!--category_meta_information_tab-->

        <div>
          <a href="javascript:;" onClick="EditCategoryMainTab('#category_main_tab')" class="save_category_tab button green">
            <i class="icon icon_save_sign"></i><?=$languages['btn_save_tab'];?>
          </a>
          <a href="<?=$back_link;?>" class="button blue">
            <i class="icon icon_cancel_sign"></i><?=$languages['btn_cancel'];?>
          </a>
        </div>
        <div class="clearfix">&nbsp;</div>
        
      </form>
      <div class="clearfix">&nbsp;</div>
    </div>
  </main>
<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="/modules/ckeditor/ckeditor/ckeditor.js"></script>
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
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {

          $language_id = $row_languages['language_id'];
?>
          CKEDITOR.replace('cd_descriptions[<?=$language_id;?>]');
<?php
        }
      }
?>
      // category tab switcher
      $(".category_tabs li").removeClass("active");
      $(".category_tab").hide();
      $(".category_tabs li:first").addClass("active");
      $(".category_tab:first").show();
      $(".category_tabs a").click(function() {
        var this_link = $(this);
        if(this_link.hasClass("images")) {
          $("#filedrop").show();
        }
        else {
          $("#filedrop").hide();
        }
        var clicked_tab = this_link.attr("href");
        var ajax_fn = this_link.attr("ajax-fn");
        $(".save_category_tab").attr("onClick",""+ajax_fn+"('"+clicked_tab+"')");
        $(".category_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".category_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end category tab switcher
      
      //start family tree
      CalculateSelectedSubcategories();
      $('.select_all').on('click', function (e) {
          var state = $(this).is(":checked");
          if(state) $("#new_default_category_id").html("");
          var checkboxes = document.getElementsByClassName("categories");
          for (var i=0; i<checkboxes.length ; i++) {
            if(checkboxes[i].type == "checkbox") {
              var category_id = checkboxes[i].value;
              var category_name = $(".tree li#"+category_id+" .category_name").html();
              if(state) {
                $("#new_default_category_id").append("<option value='"+category_id+"'>"+category_name+"</option>");
                $("input.category_name_"+category_id).attr("disabled",false);
              }
              else {
                $('#new_default_category_id option[value='+category_id+']').remove();
                $("input.category_name_"+category_id).attr("disabled",true);
              }
              checkboxes[i].checked = state;
            }
          }
          CalculateSelectedSubcategories();
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
            //console.log(is_selected);
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
          CalculateSelectedSubcategories();
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
      
      // languages tab switcher
      $.each($(".category_tab"), function(){
        var category_tab_id = $(this).attr("id");
        $("#"+category_tab_id+" .language_tabs li").removeClass("active");
        $("#"+category_tab_id+" .language_tab").hide();
        $("#"+category_tab_id+" .language_tabs li:first").addClass("active");
        $("#"+category_tab_id+" .language_tab:first").show();
      });
      $(".language_tabs a").click(function() {
        var perant_tab = $(this).closest(".category_tab");
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