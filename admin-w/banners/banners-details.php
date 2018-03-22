<?php
    
  $back_link = "banners.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  $query_banner_details = "SELECT `banner_id`,`banner_image`,`banner_is_active`,`banner_sort_order` FROM `banners` WHERE `banner_id` = '$current_banner_id'";
  //echo $query_banner_details;exit;
  $result_banner_details = mysqli_query($db_link, $query_banner_details);
  if(!$result_banner_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_banner_details) > 0) {
    $banner_details = mysqli_fetch_assoc($result_banner_details);

    $banner_id = $banner_details['banner_id'];
    $banner_image = $banner_details['banner_image'];
    $banner_image_exstension = pathinfo($banner_image, PATHINFO_EXTENSION);
    $banner_image_name = str_replace(".$banner_image_exstension", "", $banner_image);
    $banner_image_thumb = SITEFOLDERSL."/images/banners/".$banner_image_name."_admin_thumb.".$banner_image_exstension;
    @$image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$banner_image_thumb);
    $image_dimensions = $image_params[3];
    $banner_is_active = $banner_details['banner_is_active'];
  }
  
  if(isset($_POST['edit_banner'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['banner_link'] as $language_id => $banner_link) {
      if(empty($banner_link)) $banner_errors['banner_link'][$language_id] = $languages['required_field_error'];
      
      $banner_names_array[$language_id] = $_POST['banner_name'][$language_id];
      $banner_links_array[$language_id] = $_POST['banner_link'][$language_id];
      $banner_has_record_in_db[$language_id] = $_POST['banner_has_record_in_db'][$language_id];
    }
    
    $banner_is_active = 0;
    if(isset($_POST['banner_is_active'])) $banner_is_active = 1;
    
    if(!isset($banner_errors)) {
      //if there are no form errors we can insert the information
      
      $query_update_banner = "UPDATE `banners` SET `banner_is_active`='$banner_is_active' WHERE `banner_id` = '$current_banner_id'";
      $all_queries .= "<br>".$query_update_banner;
      $result_update_banner = mysqli_query($db_link, $query_update_banner);
      if(!$result_update_banner) {
        echo $languages['sql_error_update']." - 1 `banners`".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($banner_links_array as $language_id => $banner_link) {
        
        $banner_name_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $banner_names_array[$language_id]));
        $banner_link_db = mysqli_real_escape_string($db_link, $banner_link);
      
        if($banner_has_record_in_db[$language_id] == 0) {
          $query_insert_banner_desc = "INSERT INTO `banners_links`(`banner_id`,`language_id`,`banner_name`,`banner_link`) 
                                                          VALUES ('$banner_id','$language_id',$banner_name_db,'$banner_link_db')";
          $all_queries .= "<br>".$query_insert_banner_desc;
          $result_insert_banner_desc = mysqli_query($db_link, $query_insert_banner_desc);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - 2 INSERT `banners_links` ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_update_banner_links = "UPDATE `banners_links` SET `banner_name`=$banner_name_db,`banner_link`='$banner_link_db' 
                                        WHERE `banner_id` = '$current_banner_id' AND `language_id` = '$language_id'";
          $all_queries .= "<br>".$query_update_banner_links;
          $result_update_banner_links = mysqli_query($db_link, $query_update_banner_links);
          if(!$result_update_banner_links) {
            echo $languages['sql_error_update']." - 2 UPDATE `banners_links`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
          
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
      
    } //if(!isset($banner_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages['banner_details_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>"><?=$languages['header_banners'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_banner_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_banner_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <p>
          <button type="submit" name="edit_banner" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>
        
        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
            $class_error = (isset($banner_errors['banner_link'][$language_id])) ? ' class="red"' : "";
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
          
          if(!isset($_POST['edit_banner'])) {
            $banner_has_record_in_db[$language_id] = 0;
            
            $query_banner_link = "SELECT `banner_name`,`banner_link` FROM `banners_links` WHERE `banner_id` = '$current_banner_id' AND `language_id` = '$language_id'";
            $result_banner_link = mysqli_query($db_link, $query_banner_link);
            if(!$result_banner_link) { echo mysqli_error($db_link); }
            if(mysqli_num_rows($result_banner_link) > 0) {
              $banner_link = mysqli_fetch_assoc($result_banner_link);

              $banner_names_array[$language_id] = $banner_link['banner_name'];
              $banner_links_array[$language_id] = $banner_link['banner_link'];
              
              $banner_has_record_in_db[$language_id] = 1;
            }
          }
?>
        <div id="<?=$language_code;?>" class="language_tab tab row">
          <input type="hidden" name="banner_has_record_in_db[<?=$language_id;?>]" value="<?=$banner_has_record_in_db[$language_id];?>" >
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="banner_name" class="title"><?=$languages['header_header'];?></label>
            <?php
              if(isset($banner_errors['banner_name'][$language_id])) {
                echo "<div class='error'>".$banner_errors['banner_name'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="banner_name[<?=$language_id;?>]" class="banner_name" value="<?php if(isset($banner_names_array[$language_id])) echo $banner_names_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>
          
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="banner_link" class="title"><?=$languages['header_link'];?></label>
            <?php
              if(isset($banner_errors['banner_link'][$language_id])) {
                echo "<div class='error'>".$banner_errors['banner_link'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="banner_link[<?=$language_id;?>]" class="banner_link" value="<?php if(isset($banner_links_array[$language_id])) echo $banner_links_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>
        </div>
<?php
    }
  }
?>
        <div>
          <label for="banner_is_active" class="title"><?=$languages['header_status'];?></label>
          <?php
            if(isset($banner_is_active)) {
              if($banner_is_active == 0) echo '<input type="checkbox" name="banner_is_active" id="banner_is_active" />';
              else echo '<input type="checkbox" name="banner_is_active" id="banner_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="banner_is_active" id="banner_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"><p>&nbsp;</p></div>

        <h4><?=$languages['header_current_image'];?></h4>
        <p></p>
        <p><i class="info"><?=$languages['info_image']." ".$languages['btn_save'];?></i></p>
          
        <div id="dropzone" style="padding-bottom: 396px;">
          <div id="current_image">
            <img src="<?=$banner_image_thumb;?>" <?=$image_dimensions;?>>
          </div>
          <p>&nbsp;</p>
          <h4><?=$languages['header_change_image'];?> (196x92px)</h4>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="edit_banner" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" >
        
      </form>
      
      <form action="ajax/upload-images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_banner_success" value="<?=$languages['ajaxmessage_update_banner_success'];?>" >
        <input type="hidden" name="banner_id" class="banner_id" value="<?=$current_banner_id;?>" >
        <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" >
      </form>
      
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      Dropzone.options.filedrop = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              GetBannerImage('<?=$current_banner_id;?>');
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

      // language tab switcher
      $(".language_tabs li").removeClass("active");
      $(".language_tab").hide();
      $(".language_tabs li:first").addClass("active");
      $(".language_tab:first").show();
      $(".language_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".language_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".language_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end language tab switcher
    });
  </script>
</body>
</html>