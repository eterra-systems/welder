<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","8192000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = "";

  $slider_id = $_POST['slider_id'];

  //print_r($_FILES);exit;
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    $extension_array = explode("/", $_FILES['file']['type']);
    $extension = mb_convert_case($extension_array[1], MB_CASE_LOWER, "UTF-8");
    if(!in_array($extension, $valid_formats)) {
      echo $languages['image_extension_error']."$extension<br>";
      exit;
    }

    if((isset($_FILES['file'])) && ($_FILES['file']['size'] < MAX_FILE_SIZE) && ($_FILES['file']['error'] == 0)) {
      // no error
      $slider_image_tmp_name  = $_FILES['file']['tmp_name'];
      $slider_image_name = $_FILES['file']['name'];
      $slider_image_name_exploded = explode(".", $slider_image_name);
      $image_name = $slider_image_name_exploded[0];
      $image_exstension = mb_convert_case($slider_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $slider_image_name = "$image_name.$image_exstension";
      //echo $upload_path;
    }
    elseif((isset($_FILES['file'])) && ($_FILES['file']['size'] > MAX_FILE_SIZE) || ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2)) {
      echo $languages['image_size_error']."8MB<br>";
        exit;
    }
    else {
      if($_FILES['file']['error'] != 4) { // error 4 means no file was uploaded
        echo $languages['image_uploading_error']."<br>";
        exit;
      }
    }
  
    $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/sliders/";
    if(!is_dir($upload_path)) {
      mkdir($upload_path, 0777);
      chmod($upload_path, 0777);
    }
    
    $query_slider_details = "SELECT `slider_image`
                              FROM `sliders`
                              WHERE `slider_id` = '$slider_id'";
    //echo $query_slider_details;exit;
    $result_slider_details = mysqli_query($db_link, $query_slider_details);
    if(!$result_slider_details) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_slider_details) > 0) {
      $slider_details = mysqli_fetch_assoc($result_slider_details);

      $slider_image = $slider_details['slider_image'];
      if(!is_null($slider_image)) {
        $slider_image_exploded = explode(".", $slider_image);
        $current_slider_image_name = $slider_image_exploded[0];
        $current_slider_image_exstension = $slider_image_exploded[1];

        $file = $upload_path."$current_slider_image_name.$current_slider_image_exstension";

        if(file_exists($file)) unlink($file);

        $image_admin_thumb_name = $current_slider_image_name."_admin_thumb.".$current_slider_image_exstension;
        $image_admin_thumb = "$upload_path$image_admin_thumb_name";

        if(file_exists($image_admin_thumb)) unlink($image_admin_thumb);

        $image_site_name = $current_slider_image_name."_site.".$current_slider_image_exstension;
        $image_site = "$upload_path$image_site_name";

        if(file_exists($image_site)) unlink($image_site);
      }
    }

    $query_update_product = "UPDATE `sliders` SET `slider_image` = '$slider_image_name' WHERE `slider_id` = '$slider_id'";
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages['sql_error_update']." - 1 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($slider_image_tmp_name)) {
      move_uploaded_file($slider_image_tmp_name, $upload_path.$slider_image_name);
    }
    else {
      echo $languages['image_uploading_error'];
      exit;
    }
    
    $file = $upload_path.$slider_image_name;
    
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
    
    @$image_admin_params = getimagesize($image_admin_thumb);
    $image_admin_dimensions = $image_admin_params[3];
?>
  <img src="<?=SITEFOLDERSL;?>/images/sliders/<?=$image_admin_thumb_name;?>" <?=$image_admin_dimensions;?>>
<?php
  }