<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['banner_id'])) {
    $banner_id = $_POST['banner_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $all_queries = "";
  
  //delete images
  $query_banner_details = "SELECT `banner_image` FROM `banners` WHERE `banner_id` = '$banner_id' LIMIT 1";
  $all_queries .= $query_banner_details."\n";
  //echo $query_banner_details;exit;
  $result_banner_details = mysqli_query($db_link, $query_banner_details);
  if(!$result_banner_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_banner_details) > 0) {
    
    $banner_details = mysqli_fetch_assoc($result_banner_details);
      
    $banner_image = $banner_details['banner_image'];
    if(!empty($partner_image)) {
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
  }
  
  $query = "DELETE FROM `banners` WHERE `banner_id` = '$banner_id'";
  $all_queries .= $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `banners_links` WHERE `banner_id` = '$banner_id'";
  $all_queries .= $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
