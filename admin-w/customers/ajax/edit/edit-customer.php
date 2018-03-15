<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];
  }
  if(isset($_POST['customer_group_id'])) {
    $customer_group_id = $_POST['customer_group_id'];
  }
  if(isset($_POST['customer_is_in_mailist'])) {
    $customer_is_in_mailist = $_POST['customer_is_in_mailist'];
  }
  if(isset($_POST['customer_is_blocked'])) {
    $customer_is_blocked = $_POST['customer_is_blocked'];
  }
  if(isset($_POST['customer_is_active'])) {
    $customer_is_active = $_POST['customer_is_active'];
  }
  
  if(!empty($customer_id) && !empty($customer_group_id)) {
    
    $query = "UPDATE `customers` 
              SET `customer_group_id`='$customer_group_id',`customer_is_in_mailist`='$customer_is_in_mailist',
                  `customer_is_blocked`='$customer_is_blocked',`customer_is_active`='$customer_is_active'
              WHERE `customer_id` = '$customer_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
?>