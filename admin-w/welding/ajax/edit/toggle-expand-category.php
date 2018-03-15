<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['category_hierarchy_ids'])) {
    $category_hierarchy_ids =  $_POST['category_hierarchy_ids'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  //echo "<pre>";print_r($_SERVER);EXIT;
  if(!empty($category_hierarchy_ids) && !empty($action)) {
    
    $category_is_collapsed = ($action == "expand") ? 0 : 1; // else $action == collapse
    
    if($category_hierarchy_ids == "all") {
      $query_update_category = "UPDATE `category_to_category` SET  `category_is_collapsed`='$category_is_collapsed' WHERE `category_has_children` = '1'"; 
    }
    else {
      $query_update_category = "UPDATE `category_to_category` SET  `category_is_collapsed`='$category_is_collapsed' WHERE `category_hierarchy_ids` = '$category_hierarchy_ids'";
    }
    //echo $query_update_category;
    $result_update_category = mysqli_query($db_link, $query_update_category);
    if(!$result_update_category) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

    list_categories($parent_id = 0,$category_root_id = 0,$path_number = 0);

  }
?>