<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  //echo "<pre>";print_r($_POST);exit;
  
  define ("MAX_FILE_SIZE","4096000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/category-thumbs/";
  if(!is_dir($upload_path)) {
    mkdir($upload_path, 0777);
    chmod($upload_path, 0777);
  }

  $category_id = $_POST['category_id'];
  $category_image = $_POST['category_image'];
  
  if(!empty($category_image)) {
    $category_image_exploded = explode(".", $category_image);
    $current_category_image_name = $category_image_exploded[0];
    $current_category_image_exstension = $category_image_exploded[1];

    $file = $upload_path."$current_category_image_name.$current_category_image_exstension";

    if(file_exists($file)) unlink($file);

    $image_admin_thumb_name = $current_category_image_name."_cat_thumb.".$current_category_image_exstension;
    $image_admin_thumb = "$upload_path$image_admin_thumb_name";

    if(file_exists($image_admin_thumb)) unlink($image_admin_thumb);
  }
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    $extension_array = explode("/", $_FILES['file']['type']);
    $extension = mb_convert_case($extension_array[1], MB_CASE_LOWER, "UTF-8");
    if(!in_array($extension, $valid_formats)) {
      $category_errors['file'] = "Не е позлволено качването на снимка с разширение $extension<br>";
    }

    if((isset($_FILES['file'])) && ($_FILES['file']['size'] < MAX_FILE_SIZE) && ($_FILES['file']['error'] == 0)) {
      // no error
      $category_image_tmp_name  = $_FILES['file']['tmp_name'];
      $category_image_name = $_FILES['file']['name'];
      $category_image_name_exploded = explode(".", $category_image_name);
      $image_name = $category_image_name_exploded[0];
      $image_exstension = mb_convert_case($category_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $category_image_name = "$image_name.$image_exstension";
      //echo $upload_path;
    }
    elseif((isset($_FILES['file'])) && ($_FILES['file']['size'] > MAX_FILE_SIZE) || ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2)) {
      $category_errors['file'] = "You have exceeded the size limit! Please choose a default image smaller then 4MB<br>";
    }
    else {
      if($_FILES['file']['error'] != 4) { // error 4 means no file was uploaded
        $category_errors['file'] = "An error occured while uploading the file<br>";
      }
    }
    
    if(isset($category_errors['file'])) {
      echo $category_errors['file'];
      exit;
    }

    $query_update_category = "UPDATE `categories` SET `category_image` = '$category_image_name' WHERE `category_id` = '$category_id'";
    //echo $query_update_category;exit;
    $result_update_category = mysqli_query($db_link, $query_update_category);
    if(!$result_update_category) {
      echo $languages['sql_error_update']." - 1 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($category_image_tmp_name)) {
      move_uploaded_file($category_image_tmp_name, $upload_path.$category_image_name);

      $file = $upload_path.$category_image_name;

      $image = new SimpleImage(); 
      $image->load($file);

      $image_cat_thumb_name = $image_name."_cat_thumb.".$image_exstension;
      $image_cat_thumb = $upload_path.$image_cat_thumb_name;

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
      $image->resizeToWidth(150);

      $image->save($image_cat_thumb,$image_type);
    }
    else {
      echo $languages['image_uploading_error'];
      exit;
    }
    
    @$image_params = getimagesize($image_cat_thumb_name);
    $image_dimensions = $image_params[3];
?>
<img src="<?=SITEFOLDERSL;?>/images/category-thumbs/<?=$image_cat_thumb_name;?>" <?=$image_dimensions;?>>
<?php
    
  }