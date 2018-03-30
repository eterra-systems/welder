<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "testimonials.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_POST['add_testimonial'])) {
    
    //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['testimonial_author'] as $language_id => $testimonial_author) {
      if(empty($testimonial_author)) $testimonial_errors['testimonial_author'][$language_id] = $languages['required_field_error'];
      
      $testimonial_authors_array[$language_id] = $_POST['testimonial_author'][$language_id];
      $testimonial_author_websites_array[$language_id] = $_POST['testimonial_author_website'][$language_id];
      $testimonial_texts_array[$language_id] = $_POST['testimonial_text'][$language_id];
    }
    
    $testimonial_is_active = 0;
    if(isset($_POST['testimonial_is_active'])) $testimonial_is_active = 1;
    
    $input_name = "testimonial_image";
    $max_image_size = 4; //MB
    $testimonial_image_set = false;
    if(isset($_FILES[$input_name]) && ($_FILES[$input_name]['error'] != 4)) {
      $testimonial_image_set = true;
      $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/testimonials/";
      $image_params = validate_upload_image($input_name, $upload_path, $max_image_size);
      //echo "<pre>";print_r($image_params);exit;
      if(!empty($image_params['error'])) {
        $testimonial_errors[$input_name] = $image_params['error']; // array that may contain extension, size, upload
      }
      else {
        $image_tmp_name = $image_params['image_tmp_name'];
        $image_name = $image_params['image_name'];
        $image_exstension = $image_params['image_exstension'];
        $image_name_full = $image_params['image_name_full'];
      }
    }
        
    if(!isset($testimonial_errors)) {
      //if there are no form errors we can insert the information
      
      $testimonial_sort_order = get_last_sort_order("testimonials","testimonial_sort_order")+1;
      $testimonial_image_name_db = ($testimonial_image_set) ? "'$image_name_full'" : "NULL";
      
      $query_insert_testimonial = "INSERT INTO `testimonials`(`testimonial_id`, `testimonial_image`, `testimonial_is_active`, `testimonial_sort_order`) 
                                                      VALUES (NULL,$testimonial_image_name_db,'$testimonial_is_active','$testimonial_sort_order')";
      //echo $query_insert_testimonial;
      $all_queries .= "<br>".$query_insert_testimonial;
      $result_insert_testimonial = mysqli_query($db_link, $query_insert_testimonial);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $testimonial_id = mysqli_insert_id($db_link);
      
      foreach($testimonial_authors_array as $language_id => $testimonial_author) {
        
        $testimonial_author_db = mysqli_real_escape_string($db_link, $testimonial_author);
        $testimonial_author_website_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $testimonial_author_websites_array[$language_id]));
        $testimonial_text_db = mysqli_real_escape_string($db_link, $testimonial_texts_array[$language_id]);
        
        $query_insert_testimonial_desc = "INSERT INTO `testimonials_descriptions`(`testimonial_id`,
                                                                                  `language_id`,
                                                                                  `testimonial_author`,
                                                                                  `testimonial_author_website`,
                                                                                  `testimonial_text`) 
                                                                          VALUES ('$testimonial_id',
                                                                                  '$language_id',
                                                                                  '$testimonial_author_db',
                                                                                  $testimonial_author_website_db,
                                                                                  '$testimonial_text_db')";
        $all_queries .= "<br>".$query_insert_testimonial_desc;
        $result_insert_testimonial_desc = mysqli_query($db_link, $query_insert_testimonial_desc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      if($testimonial_image_set) {

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

        $image_site_name = $image_name."_site.".$image_exstension;
        $image_site = $upload_path.$image_site_name;

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
          if($width > 170) {
            $image->resizeToWidth(170);
          }
          $image->save($image_site,$image_type);
        }
        else {
          if($height > 204) {
            $image->resizeToHeight(204);
          }
          $image->save($image_site,$image_type);
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
      
    } //if(!isset($testimonial_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages['testimonial_add_new_title'];
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
        <?=$languages['header_testimonial_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_testimonial_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        <p class="float_right">
          <button type="submit" name="add_testimonial" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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
?>
        <div id="<?=$language_code;?>" class="language_tab tab row">
          <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
            <label for="testimonial_author" class="title"><?=$languages['header_author'];?><span class="red">*</span></label>
            <?php
              if(isset($testimonial_errors['testimonial_author'][$language_id])) {
                echo "<div class='error'>".$testimonial_errors['testimonial_author'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="testimonial_author[<?=$language_id;?>]" class="testimonial_author" value="<?php if(isset($testimonial_authors_array[$language_id])) echo $testimonial_authors_array[$language_id];?>" />
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
          
        <div>
          <label for="testimonial_image" class="title"><?=$languages['header_add_image'];?> (80x80px)</label>
          <?php
            if(isset($product_errors['testimonial_image'])) {
              echo "<div class='error'>".$product_errors['testimonial_image']."</div>";
            }
          ?>
          <p><input type="file" name="testimonial_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="add_testimonial" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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
  
?>
  <script type="text/javascript" src="/modules/ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
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