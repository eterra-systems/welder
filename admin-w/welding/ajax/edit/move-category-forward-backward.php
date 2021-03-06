<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['category_id'])) {
    $category_id =  $_POST['category_id'];
  }
  if(isset($_POST['category_parent_id'])) {
    $category_parent_id =  $_POST['category_parent_id'];
  }
  if(isset($_POST['category_root_id'])) {
    $category_root_id =  $_POST['category_root_id'];
  }
  if(isset($_POST['category_sort_order'])) {
    $category_sort_order =  $_POST['category_sort_order'];
  }
  if(isset($_POST['category_hierarchy_level'])) {
    $category_hierarchy_level =  $_POST['category_hierarchy_level'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($category_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $and_root = ($category_parent_id == 0) ? "" : "AND `category_root_id` = '$category_root_id'";
    
    if($action == "forward") {
      $previous_category_sort_order = $category_sort_order-1;
  
      $query_update_category_1 = "UPDATE `category_to_category` SET `category_sort_order`='$category_sort_order' 
                                  WHERE `category_parent_id` = '$category_parent_id' $and_root
                                    AND `category_sort_order` = '$previous_category_sort_order' AND `category_hierarchy_level` = '$category_hierarchy_level'";
      $all_queries .= "\n<br>".$query_update_category_1;
        //echo $query_update_category_1;
      $result_update_category_1 = mysqli_query($db_link, $query_update_category_1);
      if(!$result_update_category_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_category_2 = "UPDATE `category_to_category` SET `category_sort_order`='$previous_category_sort_order' 
                                   WHERE `category_id` = '$category_id' AND `category_parent_id` = '$category_parent_id' $and_root";
      $all_queries .= "\n<br>".$query_update_category_2;
        //echo $query_update_category_2;
      $result_update_category_2 = mysqli_query($db_link, $query_update_category_2);
      if(!$result_update_category_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_category_sort_order = $category_sort_order+1;
      
      $query_update_category_1 = "UPDATE `category_to_category` SET `category_sort_order`='$category_sort_order' 
                                   WHERE `category_parent_id` = '$category_parent_id' $and_root
                                     AND `category_sort_order` = '$next_category_sort_order' AND `category_hierarchy_level` = '$category_hierarchy_level'";
      $all_queries .= "\n<br>".$query_update_category_1;
        //echo $query_update_category_1;
      $result_update_category_1 = mysqli_query($db_link, $query_update_category_1);
      if(!$result_update_category_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_category_2 = "UPDATE `category_to_category` SET `category_sort_order`='$next_category_sort_order' 
                                   WHERE `category_id` = '$category_id' AND `category_parent_id` = '$category_parent_id' $and_root";
      $all_queries .= "\n<br>".$query_update_category_2;
        //echo $query_update_category_2;
      $result_update_category_2 = mysqli_query($db_link, $query_update_category_2);
      if(!$result_update_category_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");

    list_categories($parent_id = 0,$category_root_id = 0,$path_number = 0);

  }
?>