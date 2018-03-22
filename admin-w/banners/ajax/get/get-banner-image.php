<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['banner_id'])) {
    $banner_id =  $_POST['banner_id'];
  }
  
  $query_banner_details = "SELECT `banner_image` FROM `banners` WHERE `banner_id` = '$banner_id'";
  //echo $query_banner_details;exit;
  $result_banner_details = mysqli_query($db_link, $query_banner_details);
  if(!$result_banner_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_banner_details) > 0) {
    $banner_details = mysqli_fetch_assoc($result_banner_details);

    $banner_image = $banner_details['banner_image'];
    $banner_image_thumb = SITEFOLDERSL."/images/banners/".$banner_image;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$banner_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$banner_image_thumb;?>" <?=$thumb_image_dimensions;?>>