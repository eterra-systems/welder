<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
 
  if(isset($_POST['customer_group_id'])) {
    $customer_group_id = $_POST['customer_group_id'];
  }
  if(isset($_POST['language_id'])) {
    $language_id = $_POST['language_id'];
  }
  if(isset($_POST['customer_group_translation_text'])) {
    $customer_group_translation_text = $_POST['customer_group_translation_text'];
  }
  
  if(!empty($customer_group_id) && !empty($language_id) && !empty($customer_group_translation_text)) {
    $customer_group_translation_text = mysqli_real_escape_string($db_link,$customer_group_translation_text);
    $query = "INSERT INTO `customers_groups_translations`(`customer_group_id`, `language_id`, `customer_group_translation_text`) 
                                                  VALUES ('$customer_group_id','$language_id','$customer_group_translation_text')";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
  
?>
