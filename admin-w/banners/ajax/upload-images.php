<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;

  $banner_id = $_POST['banner_id'];
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    
    $max_image_size = 4; //MB
    $upload_path = SITEFOLDERSL."/images/banners/";
    $full_upload_path = $_SERVER['DOCUMENT_ROOT'].$upload_path;
    $image_params = validate_upload_image($input_name = "file", $full_upload_path, $max_image_size);
    if(!empty($image_params['error'])) {
      foreach($image_params['error'] as $error) {
        echo "<div class='error'>$error</div>";
      }
      exit;
    }
    else {
      $image_tmp_name = $image_params['image_tmp_name'];
      $image_name = $image_params['image_name'];
      $image_exstension = $image_params['image_exstension'];
      $image_name_full = $image_params['image_name_full'];
    }

    //first delete old image
    $query_banner_image = "SELECT `banner_image` FROM `banners` WHERE `banner_id` = '$banner_id'";
    //echo $query_banner_image;exit;
    $result_banner_image = mysqli_query($db_link, $query_banner_image);
    if(!$result_banner_image) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_banner_image) > 0) {

      $banner_image_row = mysqli_fetch_assoc($result_banner_image);

      $banner_image = $banner_details['banner_image'];
      $banner_image_exstension = pathinfo($banner_image, PATHINFO_EXTENSION);
      $banner_image_name = str_replace(".$banner_image_exstension", "", $banner_image);
      $banner_image_site = $banner_image_name."_site.".$banner_image_exstension;
      $banner_image_admin_thumb = $banner_image_name."_admin_thumb.".$banner_image_exstension;
      $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/banners/";

      $file = $upload_path.$banner_image;

      if(file_exists($file)) {
        unlink($file);
      }
      if(file_exists($banner_image_site)) {
        unlink($banner_image_site);
      }
      if(file_exists($banner_image_admin_thumb)) {
        unlink($banner_image_admin_thumb);
      }
    }
    
    $query_update_product = "UPDATE `banners` SET `banner_image` = '$image_name_full' WHERE `banner_id` = '$banner_id'";
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages['sql_error_update']." - 1 `banners` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($image_tmp_name)) {
      move_uploaded_file($image_tmp_name, $full_upload_path.$image_name_full);
    }
    else {
      echo $languages['image_uploading_error'];
      exit;
    }
    
    $file = $full_upload_path.$image_name_full;

    list($width,$height) = getimagesize($file);

    $image = new SimpleImage();
    $image->load($file);

    $image_site_name = $image_name."_site.".$image_exstension;
    $image_site = $full_upload_path.$image_site_name;

    $image_admin_thumb_name = $image_name."_admin_thumb.".$image_exstension;
    $image_admin_thumb = $full_upload_path.$image_admin_thumb_name;

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