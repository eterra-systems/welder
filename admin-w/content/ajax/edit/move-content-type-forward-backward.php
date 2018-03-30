<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['content_type_id'])) {
    $content_type_id =  $_POST['content_type_id'];
  }
  if(isset($_POST['content_type_sort_order'])) {
    $content_type_sort_order =  $_POST['content_type_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($content_type_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_content_type_sort_order = $content_type_sort_order-1;
      $query_update_content_type_1 = "UPDATE `contents_types` SET `content_type_sort_order`='$content_type_sort_order' WHERE `content_type_sort_order` = '$previous_content_type_sort_order'";
      $all_queries .= "\n".$query_update_content_type_1;
        //echo $query_update_content_type_1;
      $result_update_content_type_1 = mysqli_query($db_link, $query_update_content_type_1);
      if(!$result_update_content_type_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_content_type_2 = "UPDATE `contents_types` SET `content_type_sort_order`='$previous_content_type_sort_order' WHERE `content_type_id` = '$content_type_id'";
      $all_queries .= "\n".$query_update_content_type_2;
        //echo $query_update_content_type_2;
      $result_update_content_type_2 = mysqli_query($db_link, $query_update_content_type_2);
      if(!$result_update_content_type_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_content_type_sort_order = $content_type_sort_order+1;
      $query_update_content_type_1 = "UPDATE `contents_types` SET `content_type_sort_order`='$content_type_sort_order' WHERE `content_type_sort_order` = '$next_content_type_sort_order'";
      $all_queries .= "\n".$query_update_content_type_1;
        //echo $query_update_content_type_1;
      $result_update_content_type_1 = mysqli_query($db_link, $query_update_content_type_1);
      if(!$result_update_content_type_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_content_type_2 = "UPDATE `contents_types` SET `content_type_sort_order`='$next_content_type_sort_order' WHERE `content_type_id` = '$content_type_id'";
      $all_queries .= "\n".$query_update_content_type_2;
        //echo $query_update_content_type_2;
      $result_update_content_type_2 = mysqli_query($db_link, $query_update_content_type_2);
      if(!$result_update_content_type_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_content_types();

  }