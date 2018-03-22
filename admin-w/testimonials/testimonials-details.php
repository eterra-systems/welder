<?php
    
  $back_link = "testimonials.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  $query_testimonial_details = "SELECT `testimonial_id`,`testimonial_image`,`testimonial_is_active`,`testimonial_sort_order`
                                  FROM `testimonials`
                                 WHERE `testimonial_id` = '$current_testimonial_id'";
  //echo $query_testimonial_details;exit;
  $result_testimonial_details = mysqli_query($db_link, $query_testimonial_details);
  if(!$result_testimonial_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_testimonial_details) > 0) {
    $testimonial_details = mysqli_fetch_assoc($result_testimonial_details);

    $testimonial_id = $testimonial_details['testimonial_id'];
    $testimonial_image = $testimonial_details['testimonial_image'];
    if(!empty($testimonial_image)) {
      $testimonial_image_thumb = SITEFOLDERSL."/images/testimonials/$testimonial_image";
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$testimonial_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
    else {
      $testimonial_image_thumb = SITEFOLDERSL."/images/no_image_172x120.jpg";
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$testimonial_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
  }
  
  if(isset($_POST['edit_testimonial'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['testimonial_author'] as $language_id => $testimonial_author) {
      if(empty($testimonial_author)) $testimonial_errors['testimonial_author'][$language_id] = $languages['required_field_error'];
      if(empty($_POST['testimonial_text'][$language_id])) $testimonial_errors['testimonial_text'][$language_id] = $languages['required_field_error'];
      
      $testimonial_authors_array[$language_id] = $_POST['testimonial_author'][$language_id];
      $testimonial_author_websites_array[$language_id] = $_POST['testimonial_author_website'][$language_id];
      $testimonial_texts_array[$language_id] = $_POST['testimonial_text'][$language_id];
      $testimonial_has_record_in_db[$language_id] = $_POST['testimonial_has_record_in_db'][$language_id];
    }
    
    $testimonial_is_active = 0;
    if(isset($_POST['testimonial_is_active'])) $testimonial_is_active = 1;
    
    if(!isset($testimonial_errors)) {
      //if there are no form errors we can insert the information
      
      $query_update_testimonial = "UPDATE `testimonials` SET `testimonial_is_active`='$testimonial_is_active' WHERE `testimonial_id` = '$current_testimonial_id'";
      $all_queries .= "<br>".$query_update_testimonial;
      $result_update_testimonial = mysqli_query($db_link, $query_update_testimonial);
      if(!$result_update_testimonial) {
        echo $languages['sql_error_update']." - 1 `testimonials`".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($testimonial_authors_array as $language_id => $testimonial_author) {
        
        $testimonial_has_record = $testimonial_has_record_in_db[$language_id];
        $testimonial_author_db = mysqli_real_escape_string($db_link, $testimonial_author);
        $testimonial_author_website_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $testimonial_author_websites_array[$language_id]));
        $testimonial_text_db = mysqli_real_escape_string($db_link, $testimonial_texts_array[$language_id]);
      
        if($testimonial_has_record == 1) {
          $query_update_testimonial_desc = "UPDATE `testimonials_descriptions` SET `testimonial_author`='$testimonial_author_db',
                                                                                    `testimonial_author_website`=$testimonial_author_website_db,
                                                                                    `testimonial_text`='$testimonial_text_db'
                                                                        WHERE `testimonial_id` = '$current_testimonial_id' AND `language_id` = '$language_id'";
          $all_queries .= "<br>".$query_update_testimonial_desc;
          $result_update_testimonial_desc = mysqli_query($db_link, $query_update_testimonial_desc);
          if(!$result_update_testimonial_desc) {
            echo $languages['sql_error_update']." - 2 UPDATE `testimonials_descriptions`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_insert_testimonial_desc = "INSERT INTO `testimonials_descriptions`(`testimonial_id`,
                                                                                    `language_id`,
                                                                                    `testimonial_author`,
                                                                                    `testimonial_author_website`,
                                                                                    `testimonial_text`) 
                                                                          VALUES ('$current_testimonial_id',
                                                                                  '$language_id',
                                                                                  '$testimonial_author_db',
                                                                                  $testimonial_author_website_db,
                                                                                  '$testimonial_text_db')";
          $all_queries .= "<br>".$query_insert_testimonial_desc;
          $result_insert_testimonial_desc = mysqli_query($db_link, $query_insert_testimonial_desc);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - 2 INSERT `testimonials_descriptions`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
      
    } //if(!isset($testimonial_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages['testimonial_details_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>"><?=$languages['header_testimonials'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_testimonial_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_testimonial_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <p>
          <button type="submit" name="edit_testimonial" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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
            $class_error = (isset($testimonial_errors['testimonial_author'][$language_id]) || isset($testimonial_errors['testimonial_text'][$language_id])) ? ' class="red"' : "";
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
          
          if(!isset($_POST['edit_testimonial'])) {
            $testimonial_has_record_in_db[$language_id] = 0;
            
            $query_testimonial_desc = "SELECT `testimonial_author`,`testimonial_author_website`,`testimonial_text`
                                         FROM `testimonials_descriptions` 
                                        WHERE `testimonial_id` = '$current_testimonial_id' AND `language_id` = '$language_id'";
            //echo "$query_testimonial_desc<br>";
            $result_testimonial_desc = mysqli_query($db_link, $query_testimonial_desc);
            if(!$result_testimonial_desc) { echo mysqli_error($db_link); }
            if(mysqli_num_rows($result_testimonial_desc) > 0) {
              $testimonial_desc = mysqli_fetch_assoc($result_testimonial_desc);

              $testimonial_authors_array[$language_id] = $testimonial_desc['testimonial_author'];
              $testimonial_author_websites_array[$language_id] = $testimonial_desc['testimonial_author_website'];
              $testimonial_texts_array[$language_id] = $testimonial_desc['testimonial_text'];
              $testimonial_has_record_in_db[$language_id] = 1;
            }
          } 
?>
        <div id="<?=$language_code;?>" class="language_tab tab row">
          <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <label for="testimonial_author" class="title"><?=$languages['header_author'];?><span class="red">*</span></label>
            <?php
              if(isset($testimonial_errors['testimonial_author'][$language_id])) {
                echo "<div class='error'>".$testimonial_errors['testimonial_author'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="testimonial_author[<?=$language_id;?>]" class="testimonial_author" value='<?php if(isset($testimonial_authors_array[$language_id])) { echo $testimonial_authors_array[$language_id]; }?>' />
          </div>
          <div class="clearfix"></div>
          
          <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <label for="testimonial_author_website" class="title"><?=$languages['header_website'];?></label>
            <?php
              if(isset($testimonial_errors['testimonial_author_website'][$language_id])) {
                echo "<div class='error'>".$testimonial_errors['testimonial_author_website'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="testimonial_author_website[<?=$language_id;?>]" class="testimonial_author_website" value="<?php if(isset($testimonial_author_websites_array[$language_id])) { echo $testimonial_author_websites_array[$language_id]; }?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="testimonial_text" class="title"><?=$languages['header_text'];?><span class="red">*</span></label>
            <?php
              if(isset($testimonial_errors['testimonial_text'][$language_id])) {
                echo "<div class='error'>".$testimonial_errors['testimonial_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="testimonial_text[<?=$language_id;?>]" id="ckeditor_testimonial_text_<?=$language_code;?>" class="default_text"><?php if(isset($testimonial_texts_array[$language_id])) echo $testimonial_texts_array[$language_id];?></textarea>
          </div>
          <input type="hidden" name="testimonial_has_record_in_db[<?=$language_id;?>]" value="<?=$testimonial_has_record_in_db[$language_id];?>" >
          <div class="clearfix"></div>
        </div>
<?php
        }
      }
?>
        <div>
          <label for="testimonial_is_active" class="title"><?=$languages['header_status'];?></label>
          <?php
            if(isset($testimonial_is_active)) {
              if($testimonial_is_active == 0) echo '<input type="checkbox" name="testimonial_is_active" id="testimonial_is_active" />';
              else echo '<input type="checkbox" name="testimonial_is_active" id="testimonial_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="testimonial_is_active" id="testimonial_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"><p>&nbsp;</p></div>
        
        <h2><?=$languages['header_current_image'];?> (80x80px)</h2>
        <p></p>
        <p><i class="info"><?=$languages['info_image']." ".$languages['btn_save'];?></i></p>
          
        <div id="dropzone" style="padding-bottom: 410px;">
          <div id="current_image">
            <img src="<?=$testimonial_image_thumb;?>" <?=$thumb_image_dimensions;?>>
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages['header_change_image'];?></h2>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="edit_testimonial" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      
      <form action="ajax/upload-images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_product_tab_success" value="<?=$languages['ajaxmessage_update_product_tab_success'];?>" >
        <input type="hidden" name="testimonial_id" id="testimonial_id" value="<?=$current_testimonial_id;?>" >
        <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" >
      </form>
      <div class="clearfix"></div>
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
            this.removeFile(file);
          });
          this.on("success", function(file, responseImage) {
            if(responseImage == "" || responseImage == " ") {
              
            }
            else {
              $("#current_image").html(responseImage);
            }
          });
        }
      };
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {
              
          $language_code = $row_languages['language_code'];
?>
          CKEDITOR.replace('ckeditor_testimonial_text_<?=$language_code;?>');
<?php
        }
      }
?>
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