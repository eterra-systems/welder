<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  //print_r($_FILES);exit;

  $testimonial_id = $_POST['testimonial_id'];
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {

    $max_image_size = 4; //MB
    $upload_path = SITEFOLDERSL."/images/testimonials/";
    $full_upload_path = $_SERVER['DOCUMENT_ROOT'].$upload_path;
    $image_params = validate_upload_image($input_name = "file", $full_upload_path, $max_image_size);
    $image_tmp_name = $image_params['image_tmp_name'];
    $image_name = $image_params['image_name'];
    $image_exstension = $image_params['image_exstension'];
    $image_name_full = $image_params['image_name_full'];
    
    $query_testimonial_details = "SELECT `testimonial_image` FROM `testimonials` WHERE `testimonial_id` = '$testimonial_id'";
    //echo $query_testimonial_details;exit;
    $result_testimonial_details = mysqli_query($db_link, $query_testimonial_details);
    if(!$result_testimonial_details) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_testimonial_details) > 0) {
      $testimonial_details = mysqli_fetch_assoc($result_testimonial_details);

      $testimonial_image = $testimonial_details['testimonial_image'];
      if(!is_null($testimonial_image) || !empty($testimonial_image)) {
        $testimonial_image_exploded = explode(".", $testimonial_image);
        $current_testimonial_image_name = $testimonial_image_exploded[0];
        $current_testimonial_image_exstension = $testimonial_image_exploded[1];

        $file = $full_upload_path.$testimonial_image;

        if(file_exists($file)) unlink($file);

        $image_site_name = $current_testimonial_image_name."_site.".$current_testimonial_image_exstension;
        $image_site = "$full_upload_path$image_site_name";

        if(file_exists($image_site))  unlink($image_site);
      }
    }

    $query_update_product = "UPDATE `testimonials` SET `testimonial_image` = '$image_name_full' WHERE `testimonial_id` = '$testimonial_id'";
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages['sql_error_update']." - 1 ".mysqli_error($db_link);
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
    
    @$image_site_params = getimagesize($image_site);
    $image_site_dimensions = $image_site_params[3];
?>
<img src="<?="$upload_path$image_site_name";?>" <?=$image_site_dimensions;?>>
<?php
    
  }