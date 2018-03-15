<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  //echo "<pre>";print_r($_POST);exit;
  
  define("MAX_FILE_SIZE","8388608");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/news";
  if(!is_dir($upload_path)) {
    mkdir($upload_path, 0777);
    chmod($upload_path, 0777);
  }
  $upload_path_large = "$upload_path/large";
  if(!is_dir($upload_path_large)) {
    mkdir($upload_path_large, 0777);
    chmod($upload_path_large, 0777);
  }
  $upload_path_thumbs = "$upload_path/thumbs";
  if(!is_dir($upload_path_thumbs)) {
    mkdir($upload_path_thumbs, 0777);
    chmod($upload_path_thumbs, 0777);
  }

  $news_id = $_POST['news_id'];
  mysqli_query($db_link,"BEGIN");
  $all_queries= "";
  //print_r($_FILES);exit;
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    $extension_array = explode("/", $_FILES['file']['type']);
    $extension = mb_convert_case($extension_array[1], MB_CASE_LOWER, "UTF-8");
    if(!in_array($extension, $valid_formats)) {
      echo "Не е позлволено качването на снимка с разширение $extension<br>";
      exit;
    }

    if((isset($_FILES['file'])) && ($_FILES['file']['size'] < MAX_FILE_SIZE) && ($_FILES['file']['error'] == 0)) {
      // no error
      $news_image_tmp_name  = $_FILES['file']['tmp_name'];
      $news_image_name = $_FILES['file']['name'];
      $news_image_name_exploded = explode(".", $news_image_name);
      $image_name = $news_image_name_exploded[0];
      $image_exstension = mb_convert_case($news_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $news_image_name = "$image_name.$image_exstension";
      //echo $upload_path;
    }
    elseif((isset($_FILES['file'])) && ($_FILES['file']['size'] > MAX_FILE_SIZE) || ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2)) {
      echo "You have exceeded the size limit! Please choose a default image smaller then 4MB<br>";
        exit;
    }
    else {
      if($_FILES['file']['error'] != 4) { // error 4 means no file was uploaded
        echo "An error occured while uploading the file<br>";
        exit;
      }
    }
    
    $query_select_news_image = "SELECT `news_gallery_id` FROM `news_galleries` WHERE `ng_name` = '$news_image_name'";
    //echo $query;exit;
    $result_select_news_image = mysqli_query($db_link, $query_select_news_image);
    if(mysqli_num_rows($result_select_news_image) > 0) {
      echo $languages['warning_image_is_already_in_database'];
      exit;
    }
    
    $image_sort_order = get_news_last_image_order_value($news_id);
    if($image_sort_order == 0) {
      $image_is_default = 1;
      $image_sort_order = 1;
//      $query_update_news = "UPDATE `news` SET `news_image` = '$news_image_name' WHERE `news_id` = '$news_id'";
//      $result_update_news = mysqli_query($db_link, $query_update_news);
//      if(!$result_update_news) {
//        echo $languages['sql_error_update']." - 1 ".mysqli_error($db_link);
//        mysqli_query($db_link,"ROLLBACK");
//        exit;
//      }
    }
    else {
      $image_is_default = 0;
      $image_sort_order+1;
    }
    
    $query_insert_news_image = "INSERT INTO `news_galleries`(`news_gallery_id`, 
                                                            `news_id`, 
                                                            `ng_name`, 
                                                            `ng_is_default`, 
                                                            `ng_sort_order`)
                                                    VALUES (NULL,
                                                            '$news_id',
                                                            '$news_image_name',
                                                            '$image_is_default',
                                                            '$image_sort_order')";
    //$all_queries .= "<br>".$query_insert_news_image;
    $result_insert_news_image = mysqli_query($db_link, $query_insert_news_image);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($news_image_tmp_name)) {
      move_uploaded_file($news_image_tmp_name, $upload_path_large.$news_image_name);
    }
    else {
      echo $languages['image_uploading_error'];
      exit;
    }
    
    $file = $upload_path_large.$news_image_name;
    
    list($width,$height) = getimagesize($file);
    
    $image = new SimpleImage();
    $image->load($file);
    
    $image_thumb = "$upload_path_thumbs$news_image_name";
      
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
      if($width > 1280) {
        $image->resizeToWidth(1280);
      }

      $image->save($file,$image_type);
      
      $image->resizeToWidth(228);

      $image->save($image_thumb,$image_type);
      
    }
    else {
      if($height > 800) {
        $image->resizeToHeight(800);
      }

      $image->save($file,$image_type);
      
      $image->resizeToHeight(142);

      $image->save($image_thumb,$image_type);
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");

  }