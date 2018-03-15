<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['customer_group_id'])) {
    $customer_group_id = $_POST['customer_group_id'];
  }
  if(isset($_POST['customer_group_name'])) {
    $customer_group_name = mysqli_real_escape_string($db_link, $_POST['customer_group_name']);
  }
  if(isset($_POST['customer_group_sort_order'])) {
    $customer_group_sort_order = $_POST['customer_group_sort_order'];
  }
  
  if(!empty($customer_group_id) && !empty($customer_group_name)) {
    
    $query = "UPDATE `customers_groups` 
              SET `customer_group_name`='$customer_group_name',`customer_group_sort_order`='$customer_group_sort_order' 
              WHERE `customer_group_id` = '$customer_group_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
?>