<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  include_once "../languages/languages_$current_lang.php";
  
  $customer_id = 0;
  if(isset($_POST['customer_id'])) {
    $customer_id =  $_POST['customer_id'];
  }
  if(isset($_POST['customer_email'])) {
    $customer_email =  $_POST['customer_email'];
  }
  
  if(!empty($customer_email)) {
    if(!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
     echo "<div class='alert alert-danger'>".$languages['error_email_is_not_valid']."</div>";
     exit;
    }
    else {
      if(!empty($customer_id)) $query = "SELECT `customer_id` FROM `customers` WHERE `customer_id` <> '$customer_id' AND `customer_email` = '$customer_email'";
      else $query = "SELECT `customer_id` FROM `customers` WHERE `customer_email` = '$customer_email'";
      //echo $query;
      $result = mysqli_query($db_link, $query);
      if(!$result) echo mysqli_error($db_link);
      if(mysqli_num_rows($result) > 0) {
        echo "<div class='alert alert-danger'>".$languages['error_create_customer_email_taken']."</div>";
      }
      else {
        //echo "<p class='green'>ok</p>";
        //echo $laguages[$default_lang]['create_customer_email_taken'];
      }
    } 
  } 
?>