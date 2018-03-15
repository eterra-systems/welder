<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
 
  if(isset($_POST['customer_group_name'])) {
    $customer_group_name = $_POST['customer_group_name'];
  }
  if(isset($_POST['customer_group_sort_order'])) {
    $customer_group_sort_order = $_POST['customer_group_sort_order'];
  }
  
  if(!empty($customer_group_sort_order) && !empty($customer_group_name)) {
    $customer_group_name = mysqli_real_escape_string($db_link,$customer_group_name);
    $query = "INSERT INTO `customers_groups`(`customer_group_id`, `customer_group_name`, `customer_group_sort_order`) 
                                    VALUES (NULL,'$customer_group_name','$customer_group_sort_order')";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
  
?>