<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "banners.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_POST['add_banner'])) {
    
    //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['banner_link'] as $language_id => $banner_link) {
      if(empty($banner_link)) $banner_errors['banner_link'][$language_id] = $languages['required_field_error'];
      
      $banner_names_array[$language_id] = $_POST['banner_name'][$language_id];
      $banner_links_array[$language_id] = $_POST['banner_link'][$language_id];
    }
    
    $banner_is_active = (isset($_POST['banner_is_active'])) ? 1 : 0;
    
    $input_name = "banner_image";
    $max_image_size = 2; // MB
    $banner_image_set = false;
    if(isset($_FILES[$input_name]) && ($_FILES[$input_name]['error'] != 4)) {
      $banner_image_set = true;
      $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/banners/";
      $image_params = validate_upload_image($input_name, $upload_path, $max_image_size);
      //echo "<pre>";print_r($image_params);exit;
      if(!empty($image_params['error'])) {
        $banner_errors[$input_name] = $image_params['error']; // array that may contain extension, size, upload
      }
      else {
        $image_tmp_name = $image_params['image_tmp_name'];
        $image_name = $image_params['image_name'];
        $image_exstension = $image_params['image_exstension'];
        $image_name_full = $image_params['image_name_full'];
      }
    }
    else {
      $banner_errors['error']['empty'] = $languages['image_must_be_uploded_error']."<br>";
    }
    
    if(!isset($banner_errors)) {
      //if there are no form errors we can insert the information
      
      $banner_sort_order = get_banner_last_order_value()+1;
      
      $query_insert_banner = "INSERT INTO `banners`(`banner_id`, `banner_image`, `banner_is_active`, `banner_sort_order`) 
                                            VALUES (NULL,'$image_name_full','$banner_is_active','$banner_sort_order')";
      $all_queries .= "<br>".$query_insert_banner;
      $result_insert_banner = mysqli_query($db_link, $query_insert_banner);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - banners ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $banner_id = mysqli_insert_id($db_link);
      
      foreach($banner_links_array as $language_id => $banner_link) {
        
        $banner_name_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $banner_names_array[$language_id]));
        $banner_link_db = mysqli_real_escape_string($db_link, $banner_link);
      
        $query_insert_banner_desc = "INSERT INTO `banners_links`(`banner_id`,`language_id`,`banner_name`,`banner_link`) 
                                                        VALUES ('$banner_id','$language_id',$banner_name_db,'$banner_link_db')";
        $all_queries .= "<br>".$query_insert_banner_desc;
        $result_insert_banner_desc = mysqli_query($db_link, $query_insert_banner_desc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - banners_descriptions ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    
      if($banner_image_set) {
        
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
          $image->resizeToWidth(300);

          $image->save($image_site,$image_type);

          $image->resizeToWidth(200);

          $image->save($image_admin_thumb,$image_type);

        }
        else {
          $image->resizeToHeight(120);

          $image->save($image_site,$image_type);

          $image->resizeToHeight(120);

          $image->save($image_admin_thumb,$image_type);
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
        <?=$languages['header_banner_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_banner_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        <p>
          <button type="submit" name="add_banner" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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
?>
        <div id="<?=$language_code;?>" class="language_tab tab row">
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
          
        <div>
          <label for="banner_image" class="title"><?=$languages['header_image'];?><span class="red">*</span> (196x92px)</label>
          <?php
            if(!empty($banner_errors['banner_image'])) {
              foreach($banner_errors['banner_image'] as $error) {
                echo "<div class='error'>$error</div>";
              }
            }
          ?>
          <p><input type="file" name="banner_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="add_banner" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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