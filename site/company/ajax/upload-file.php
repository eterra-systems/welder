<?php

  $current_lang =  $_POST['current_lang'];
  $customer_id = $_POST['customer_id'];

  include_once '../../config.php';
  include_once '../../functions/include-functions.php';
  include_once "../../languages/languages_$current_lang.php";
  
  check_ajax_request();
  
  //1048576 bytes = 1MB
  $max_file_size = 8; //8MB
  $max_file_size_bytes = 1048576*$max_file_size;
  $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/welder/certificates/$customer_id/";
  $display_path = SITEFOLDERSL."/welder/certificates/$customer_id/";
  if(!is_dir($upload_path)) {
    mkdir($upload_path, 0777);
    chmod($upload_path, 0777);
  }
  
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

  //print_r($_FILES);exit;
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    $customer_certificate_tmp_name  = $_FILES['file']['tmp_name'];
    $customer_certificate_name = $_FILES['file']['name'];
    $extension = pathinfo($customer_certificate_name, PATHINFO_EXTENSION);
    $file_exstension = mb_convert_case($extension, MB_CASE_LOWER, "UTF-8");
    $file_name = str_replace(".$extension", "", $customer_certificate_name);
    $customer_certificate_name = "$file_name.$file_exstension";
    if(!is_file_valid_format($file_exstension)) {
      echo $languages['error_file_extension'].$file_exstension;
      exit;
    }

    if((isset($_FILES['file'])) && ($_FILES['file']['size'] < $max_file_size_bytes) && ($_FILES['file']['error'] == 0)) {
      // no error
    }
    elseif((isset($_FILES['file'])) && ($_FILES['file']['size'] > $max_file_size_bytes) || ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2)) {
      echo $languages['error_file_size'].$max_file_size."MB<br>";
      exit;
    }
    else {
      if($_FILES['file']['error'] != 4) { // error 4 means no file was uploaded
        echo $languages['error_file_uploading'];
        exit;
      }
    }

    $q_insert_ctc = "INSERT INTO `customers_welder_certificates`(`certificate_id`, `customer_id`, `certificate_name`, `certificate_exstension`) 
                                                                 VALUES (NULL,'$customer_id','$customer_certificate_name','$file_exstension')";
    //echo $q_insert_ctc;
    $result_insert_ctc = mysqli_query($db_link, $q_insert_ctc);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_insert']." - 1 `customers_to_certificates` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($customer_certificate_tmp_name)) {
      move_uploaded_file($customer_certificate_tmp_name, $upload_path.$customer_certificate_name);
    }
    else {
      echo $languages['error_file_uploading']." - 2 file $customer_certificate_name ($customer_certificate_tmp_name) - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
?>
  <div class="certificate col-lg-3 col-md-4 col-sm-12 col-xs-12">
    <img src="<?="$display_path$customer_certificate_name";?>" width="auto" height="200" alt="<?=$customer_certificate_name;?>">
  </div>
<?php
    
  }