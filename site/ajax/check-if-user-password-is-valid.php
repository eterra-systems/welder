<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  include_once "../languages/languages_$current_lang.php";
  
  if(isset($_POST['customer_password'])) {
    $customer_password =  $_POST['customer_password'];
  }
  
  if(!empty($customer_password)) {
    $uppercase = preg_match('@[A-Z]@', $customer_password);
    $lowercase = preg_match('@[a-z]@', $customer_password);
    $number    = preg_match('@[0-9]@', $customer_password);

    if(!$uppercase || !$lowercase || !$number || strlen($customer_password) < 8) {
      // tell the user something went wrong
      echo '<div class="alert alert-danger">'.$languages['error_registration_password_is_not_valid'].'</div>';
    }
  } 
?>