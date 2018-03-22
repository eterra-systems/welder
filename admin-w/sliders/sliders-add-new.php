<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "sliders.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_POST['add_slider'])) {
    
    //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['slider_header'] as $language_id => $slider_header) {
//      if(empty($slider_header)) $slider_errors['slider_header'][$language_id] = $languages['required_field_error'];
//      if(empty($_POST['slider_text'][$language_id])) $slider_errors['slider_text'][$language_id] = $languages['required_field_error'];
      
      $slider_headers_array[$language_id] = $_POST['slider_header'][$language_id];
      $slider_texts_array[$language_id] = $_POST['slider_text'][$language_id];
      $slider_links_array[$language_id] = $_POST['slider_link'][$language_id];
    }
    
    $slider_is_active = 0;
    if(isset($_POST['slider_is_active'])) $slider_is_active = 1;
    
    $input_name = "slider_image";
    $max_image_size = 4; //MB
    $slider_image_set = false;
    if(isset($_FILES[$input_name]) && ($_FILES[$input_name]['error'] != 4)) {
      $slider_image_set = true;
      $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/sliders/";
      $image_params = validate_upload_image($input_name, $upload_path, $max_image_size);
      //echo "<pre>";print_r($image_params);exit;
      if(!empty($image_params['error'])) {
        $slider_errors[$input_name] = $image_params['error']; // array that may contain extension, size, upload
      }
      else {
        $image_tmp_name = $image_params['image_tmp_name'];
        $image_name = $image_params['image_name'];
        $image_exstension = $image_params['image_exstension'];
        $image_name_full = $image_params['image_name_full'];
      }
    }
        
    if(!isset($slider_errors)) {
      //if there are no form errors we can insert the information
      
      $slider_sort_order = get_slider_last_order_value()+1;
      
      $query_insert_slider = "INSERT INTO `sliders`(`slider_id`,`slider_image`,`slider_is_active`,`slider_sort_order`) 
                                            VALUES (NULL,'$image_name_full','$slider_is_active','$slider_sort_order')";
      //echo $query_insert_slider;
      $all_queries .= "<br>".$query_insert_slider;
      $result_insert_slider = mysqli_query($db_link, $query_insert_slider);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $slider_id = mysqli_insert_id($db_link);
      
      foreach($slider_headers_array as $language_id => $slider_header) {
        
        $slider_header_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_header));
        $slider_text_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_texts_array[$language_id]));
        $slider_link_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $slider_links_array[$language_id]));
      
        $query_insert_slider_desc = "INSERT INTO `sliders_descriptions`(`slider_id`, `language_id`, `slider_header`, `slider_text`, `slider_link`) 
                                                                VALUES ('$slider_id','$language_id',$slider_header_db,$slider_text_db,$slider_link_db)";
        $all_queries .= "<br>".$query_insert_slider_desc;
        $result_insert_slider_desc = mysqli_query($db_link, $query_insert_slider_desc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      if($slider_image_set) {

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

        $image_admin_thumb_name = $image_name."_admin_thumb.".$image_exstension;
        $image_admin_thumb = $upload_path.$image_admin_thumb_name;

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
          
          if($width > 1920) {
            $image->resizeToWidth(1920);
          }
          $image->save($image_site,$image_type);

          $image->resizeToWidth(480);

          $image->save($image_admin_thumb,$image_type);

        }
        else {

          if($height > 600) {
            $image->resizeToHeight(600);
          }
          $image->save($image_site,$image_type);

          $image->resizeToHeight(200);

          $image->save($image_admin_thumb,$image_type);
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
      
    } //if(!isset($slider_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages['slider_details_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>"><?=$languages['header_sliders'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_slider_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_slider_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        <p class="float_right">
          <button type="submit" name="add_slider" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>
        
        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

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
?>
        <div id="<?=$language_code;?>" class="language_tab tab row">
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="slider_header" class="title"><?=$languages['header_header'];?></label>
            <?php
              if(isset($slider_errors['slider_header'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_header'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_header[<?=$language_id;?>]" class="slider_header" value="<?php if(isset($slider_headers_array[$language_id])) echo $slider_headers_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>
          
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="slider_link" class="title"><?=$languages['header_slider_link'];?></label>
            <?php
              if(isset($slider_errors['slider_link'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_link'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_link[<?=$language_id;?>]" class="slider_link" value="<?php if(isset($slider_links_array[$language_id])) echo $slider_links_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="slider_text" class="title"><?=$languages['header_slider_text'];?></label>
            <?php
              if(isset($slider_errors['slider_text'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="slider_text[<?=$language_id;?>]" id="ckeditor_slider_text_<?=$language_code;?>" class="default_text"><?php if(isset($slider_texts_array[$language_id])) echo $slider_texts_array[$language_id];?></textarea>
          </div>
          <div class="clearfix"></div>
        </div>
<?php
    }
  }
?>
        <div>
          <label for="slider_is_active" class="title"><?=$languages['header_status'];?></label>
          <?php
            if(isset($slider_is_active)) {
              if($slider_is_active == 0) echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" />';
              else echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"><p>&nbsp;</p></div>
          
        <div>
          <label for="slider_image" class="title"><?=$languages['header_add_image'];?> (1920x600px)</label>
          <?php
            if(isset($slider_errors['slider_image'])) {
              foreach($slider_errors['slider_image'] as $error) {
                echo "<div class='error'>$error</div>";
              }
            }
          ?>
          <p><input type="file" name="slider_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="add_slider" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {
              
          $language_code = $row_languages['language_code'];
?>
          CKEDITOR.replace('ckeditor_slider_text_<?=$language_code;?>');
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