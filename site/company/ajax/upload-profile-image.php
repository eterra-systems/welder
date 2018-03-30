<?php

  $current_lang =  $_POST['current_lang'];

  include_once '../../config.php';
  include_once '../../functions/include-functions.php';
  include_once "../../languages/languages_$current_lang.php";
  
  $customer_id = $_SESSION['customer_id'];
  
  //print_r($_FILES);exit;
  
  check_ajax_request();
  
  //1048576 bytes = 1MB
  $max_file_size = 1; //8MB
  $max_file_size_bytes = 1048576*$max_file_size;
  $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/profile-images/$customer_id/";
  $display_path = SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/profile-images/$customer_id/";
  if(!is_dir($upload_path)) {
    mkdir($upload_path, 0777);
    chmod($upload_path, 0777);
  }
  
  delete_all_from_directory($upload_path);
  
//  $query_content_details = "SELECT `content_image`
//                            FROM `contents`
//                            WHERE `content_id` = '$content_id'";
//  //echo $query_content_details;exit;
//  $result_content_details = mysqli_query($db_link, $query_content_details);
//  if(!$result_content_details) echo mysqli_error($db_link);
//  if(mysqli_num_rows($result_content_details) > 0) {
//    $content_details = mysqli_fetch_assoc($result_content_details);
//
//    $content_image = $content_details['content_image'];
//    
//    if(!is_null($content_image)) {
//      $content_image_exploded = explode(".", $content_image);
//      $current_content_image_name = $content_image_exploded[0];
//      $current_content_image_exstension = $content_image_exploded[1];
//
//      $file = $upload_path."$current_content_image_name.$current_content_image_exstension";
//
//      if(file_exists($file)) unlink($file);
//
//      $image_thumb_name = $current_content_image_name."_thumb.".$current_content_image_exstension;
//      $image_thumb = "$upload_path$image_thumb_name";
//
//      if(file_exists($image_thumb)) unlink($image_thumb);
//    }
//  }
  
  if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] != 4) {
    $customer_image_tmp_name  = $_FILES['profile_image']['tmp_name'];
    $customer_image_name = $_FILES['profile_image']['name'];
    $extension = pathinfo($customer_image_name, PATHINFO_EXTENSION);
    $image_exstension = mb_convert_case($extension, MB_CASE_LOWER, "UTF-8");
    $file_name = str_replace(".$extension", "", $customer_image_name);
    $customer_image_name = "$file_name.$image_exstension";
    if(!is_file_valid_format($image_exstension)) {
      echo $languages['error_file_extension'].$image_exstension;
      exit;
    }

    if((isset($_FILES['profile_image'])) && ($_FILES['profile_image']['size'] < $max_file_size_bytes) && ($_FILES['profile_image']['error'] == 0)) {
      // no error
    }
    elseif((isset($_FILES['profile_image'])) && ($_FILES['profile_image']['size'] > $max_file_size_bytes) || ($_FILES['profile_image']['error'] == 1 || $_FILES['profile_image']['error'] == 2)) {
      echo $languages['error_file_size'].$max_file_size."MB<br>";
      exit;
    }
    else {
      if($_FILES['profile_image']['error'] != 4) { // error 4 means no file was uploaded
        echo $languages['error_file_uploading'];
        exit;
      }
    }

    $query_update_user = "UPDATE `customers` SET `customer_image`='$customer_image_name' WHERE `customer_id` = '$customer_id'";
    //echo $query_update_user;exit;
    $result_update_user = mysqli_query($db_link, $query_update_user);
    if(!$result_update_user) {
      echo $languages['sql_error_update']." - 2 ".mysqli_error($db_link);
    }
      
    if(is_uploaded_file($customer_image_tmp_name)) {
      move_uploaded_file($customer_image_tmp_name, $upload_path.$customer_image_name);
    }
    else {
      echo $languages['error_file_uploading']." - 2 file $customer_image_name ($customer_image_tmp_name) - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $file = $upload_path.$customer_image_name;

    list($width,$height) = getimagesize($file);

    $image = new SimpleImage();
    $image->load($file);

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
      
      if($width > 150) {
        $image->resizeToWidth(150);
        $image->save($file,$image_type);
      }

    }
    else {
      if($height > 150) {
        $image->resizeToHeight(150);
        $image->save($file,$image_type);
      }
    }
    
    $_SESSION['customer_image'] = $customer_image_name;
    $display_image['image'] = $display_path.$customer_image_name;
    echo json_encode($display_image);
  }