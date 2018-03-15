<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['category_hierarchy_ids'])) {
    $category_hierarchy_ids =  $_POST['category_hierarchy_ids'];
  }
  if(isset($_POST['set_category'])) {
    $set_category =  $_POST['set_category'];
  }
  
  if(!empty($category_hierarchy_ids)) {
 
    $query_update_category = "UPDATE `category_to_category` SET  `category_is_active`='$set_category' WHERE `category_hierarchy_ids` = '$category_hierarchy_ids'";
 
    //echo $query_update_category;
    $result_update_category = mysqli_query($db_link, $query_update_category);
    if(!$result_update_category) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>