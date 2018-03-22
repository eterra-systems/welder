<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['banner_id'])) {
    $banner_id =  $_POST['banner_id'];
  }
  if(isset($_POST['set_banner'])) {
    $set_banner =  $_POST['set_banner'];
  }
  
  if(!empty($banner_id)) {
 
    $query_update_banner = "UPDATE `banners` SET  `banner_is_active`='$set_banner' WHERE `banner_id` = '$banner_id'";
    //echo $query_update_banner;
    $result_update_banner = mysqli_query($db_link, $query_update_banner);
    if(!$result_update_banner) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>