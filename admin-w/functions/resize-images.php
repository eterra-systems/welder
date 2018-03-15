<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once 'config.php';
  include_once 'admin-534/functions/include-functions.php';
  
  $query_product_image = "SELECT `product_image_id`,`pi_name` FROM `products_images` WHERE `product_image_id` = 890";
  $result_product_image = mysqli_query($db_link, $query_product_image);
  if(!$result_product_image) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_product_image) > 0) {
    while($product_image_row = mysqli_fetch_assoc($result_product_image)) {
      $product_image_id = $product_image_row['product_image_id'];
      $product_image_name = $product_image_row['pi_name'];
      
      $product_image_name_exploded = explode(".", $product_image_name);
      $image_name = $product_image_name_exploded[0];
      $image_exstension = mb_convert_case($product_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/frontstore/images/products/";

      $file = $upload_path.$product_image_name;
      
      echo "$product_image_id - $file<br>";
      
      list($width,$height) = getimagesize($file);
    
      $image = new SimpleImage(); 
      $image->load($file);

      $image_cat_thumb_name = $image_name."_cat_thumb.".$image_exstension;
      $image_cat_thumb = $upload_path.$image_cat_thumb_name;
      unlink($image_cat_thumb);

      $image_gal_zoom_name = $image_name."_gal_zoom.".$image_exstension;
      $image_gal_zoom = $upload_path.$image_gal_zoom_name;
      unlink($image_gal_zoom);

      $image_gal_thumb_name = $image_name."_gal_thumb.".$image_exstension;
      $image_gal_thumb = $upload_path.$image_gal_thumb_name;
      unlink($image_gal_thumb);
    
      $image_thickbox_default_name = $image_name."_thickbox_default.".$image_exstension;
      $image_thickbox_default = $upload_path.$image_thickbox_default_name;

      $image_home_default_name = $image_name."_home_default.".$image_exstension;
      $image_home_default = $upload_path.$image_home_default_name;

      $image_large_default_name = $image_name."_large_default.".$image_exstension;
      $image_large_default = $upload_path.$image_large_default_name;

      $image_small_default_name = $image_name."_small_default.".$image_exstension;
      $image_small_default = $upload_path.$image_small_default_name;

      $image_cart_default_name = $image_name."_cart_default.".$image_exstension;
      $image_cart_default = $upload_path.$image_cart_default_name;

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

          $image->save($file,$image_type);
        }

        $image->resizeToWidth(800);

        $image->save($image_thickbox_default,$image_type);

        $image->resizeToWidth(458);

        $image->save($image_large_default,$image_type);

        $image->resizeToWidth(250);

        $image->save($image_home_default,$image_type);

        $image->resizeToWidth(80);

        $image->save($image_small_default,$image_type);

        $image->resizeToWidth(80);

        $image->save($image_cart_default,$image_type);

      }
      else {
        if($height > 1280) {
          $image->resizeToHeight(1280);

          $image->save($file,$image_type);
        }

        $image->resizeToHeight(800);

        $image->save($image_thickbox_default,$image_type);

        $image->resizeToHeight(458);

        $image->save($image_large_default,$image_type);

        $image->resizeToHeight(250);

        $image->save($image_home_default,$image_type);

        $image->resizeToHeight(85);

        $image->save($image_small_default,$image_type);

        $image->resizeToHeight(80);

        $image->save($image_cart_default,$image_type);
      }
      
    }
  }
  
  echo "done";
  mysqli_commit($db_link);
