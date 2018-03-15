<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","4096000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/contents/";
  if(!is_dir($upload_path)) {
    mkdir($upload_path, 0777);
    chmod($upload_path, 0777);
  }

  $content_id = $_POST['content_id'];
  
  $query_content_details = "SELECT `content_image`
                            FROM `contents`
                            WHERE `content_id` = '$content_id'";
  //echo $query_content_details;exit;
  $result_content_details = mysqli_query($db_link, $query_content_details);
  if(!$result_content_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_details) > 0) {
    $content_details = mysqli_fetch_assoc($result_content_details);

    $content_image = $content_details['content_image'];
    
    if(!is_null($content_image)) {
      $content_image_exploded = explode(".", $content_image);
      $current_content_image_name = $content_image_exploded[0];
      $current_content_image_exstension = $content_image_exploded[1];

      $file = $upload_path."$current_content_image_name.$current_content_image_exstension";

      if(file_exists($file)) unlink($file);

      $image_thumb_name = $current_content_image_name."_thumb.".$current_content_image_exstension;
      $image_thumb = "$upload_path$image_thumb_name";

      if(file_exists($image_thumb)) unlink($image_thumb);
    }
      
  }

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
      $content_image_tmp_name  = $_FILES['file']['tmp_name'];
      $content_image_name = $_FILES['file']['name'];
      $content_image_name_exploded = explode(".", $content_image_name);
      $image_name = $content_image_name_exploded[0];
      $image_exstension = mb_convert_case($content_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $content_image_name = "$image_name.$image_exstension";
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

    $query_update = "UPDATE `contents` SET `content_image` = '$content_image_name' WHERE `content_id` = '$content_id'";
    $result_update = mysqli_query($db_link, $query_update);
    if(!$result_update) {
      echo $languages['sql_error_update']." - 1 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($content_image_tmp_name)) {
      move_uploaded_file($content_image_tmp_name, $upload_path.$content_image_name);
    }
    else {
      echo $languages['image_uploading_error'];
      exit;
    }
    
    $file = $upload_path.$content_image_name;
    
    list($width,$height) = getimagesize($file);
    
    $image = new SimpleImage();
    $image->load($file);
    
    $image_thumb_name = $image_name."_thumb.".$image_exstension;
    $image_thumb = $upload_path.$image_thumb_name;
      
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
      $image->resizeToWidth(340);

      $image->save($image_thumb,$image_type);

    }
    else {
      $image->resizeToHeight(211);

      $image->save($image_thumb,$image_type);
    }
    
    @$image_thumb_params = getimagesize($image_thumb);
    $image_thumb_dimensions = $image_thumb_params[3];
?>
<img src="/site/images/contents/<?=$image_thumb_name;?>" <?=$image_thumb_dimensions;?>>
<?php
    
  }