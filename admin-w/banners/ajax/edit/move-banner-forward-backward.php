<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['banner_id'])) {
    $banner_id =  $_POST['banner_id'];
  }
  if(isset($_POST['banner_sort_order'])) {
    $banner_sort_order =  $_POST['banner_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($banner_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_banner_sort_order = $banner_sort_order-1;
      $query_update_banner_1 = "UPDATE `banners` SET `banner_sort_order`='$banner_sort_order' WHERE `banner_sort_order` = '$previous_banner_sort_order'";
      $all_queries .= "\n".$query_update_banner_1;
        //echo $query_update_banner_1;
      $result_update_banner_1 = mysqli_query($db_link, $query_update_banner_1);
      if(!$result_update_banner_1) {
        echo $banners['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_banner_2 = "UPDATE `banners` SET `banner_sort_order`='$previous_banner_sort_order' WHERE `banner_id` = '$banner_id'";
      $all_queries .= "\n".$query_update_banner_2;
        //echo $query_update_banner_2;
      $result_update_banner_2 = mysqli_query($db_link, $query_update_banner_2);
      if(!$result_update_banner_2) {
        echo $banners['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_banner_sort_order = $banner_sort_order+1;
      $query_update_banner_1 = "UPDATE `banners` SET `banner_sort_order`='$banner_sort_order' WHERE `banner_sort_order` = '$next_banner_sort_order'";
      $all_queries .= "\n".$query_update_banner_1;
        //echo $query_update_banner_1;
      $result_update_banner_1 = mysqli_query($db_link, $query_update_banner_1);
      if(!$result_update_banner_1) {
        echo $banners['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_banner_2 = "UPDATE `banners` SET `banner_sort_order`='$next_banner_sort_order' WHERE `banner_id` = '$banner_id'";
      $all_queries .= "\n".$query_update_banner_2;
        //echo $query_update_banner_2;
      $result_update_banner_2 = mysqli_query($db_link, $query_update_banner_2);
      if(!$result_update_banner_2) {
        echo $banners['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_banners();

  }
?>
