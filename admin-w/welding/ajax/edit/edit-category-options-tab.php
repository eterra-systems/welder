<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_array_for_debug($_POST);exit;
  
  if(isset($_POST['category_hierarchy_ids'])) {
    $category_hierarchy_ids =  $_POST['category_hierarchy_ids'];
  }
  $category_show_in_menu = 0;
  $category_is_active = 0;
    if(isset($_POST['category_show_in_menu'])) $category_show_in_menu = 1;
    if(isset($_POST['category_is_active'])) $category_is_active = 1;
  $category_attribute_1 = prepare_for_null_row($_POST['category_attribute_1']);
  $category_attribute_2 = prepare_for_null_row($_POST['category_attribute_2']);

  $user_id = $_SESSION['admin']['user_id'];
  
  $query_update_category = "UPDATE `category_to_category` SET `category_show_in_menu`='$category_show_in_menu',`category_is_active`='$category_is_active'
                             WHERE `category_hierarchy_ids` = '$category_hierarchy_ids'";
  //echo $query_update_category;exit;
  $result_update_category = mysqli_query($db_link, $query_update_category);
  if(!$result_update_category) {
    echo $languages['sql_error_update']." - 1 update `category_to_category` ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }