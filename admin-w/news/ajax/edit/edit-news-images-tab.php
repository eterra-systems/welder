<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  
  if(isset($_POST['news_id'])) {
    $news_id =  $_POST['news_id'];
  }
  if(isset($_POST['news_image_ids'])) {
    $news_image_ids =  $_POST['news_image_ids'];
  }
  
  if(!empty($_POST) && is_array($news_image_ids)) {
    
    mysqli_query($db_link,"BEGIN");
    $all_queries = "";
      
    foreach($news_image_ids as $key => $news_image_id) {
      
      $is_default = ($key == 0) ? 1 : 0; // first image is default
      $sort_order = $key+1;
      
      $query_update_news_image = "UPDATE `news_gallery` SET `is_default`='$is_default',`sort_order`='$sort_order' WHERE `id` = '$news_image_id'";
      $all_queries .= "<br>\n".$query_update_news_image;
      $result_update_news_image = mysqli_query($db_link, $query_update_news_image);
      if(!$result_update_news_image) {
        echo $languages['sql_error_update']." - 2 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  }
  