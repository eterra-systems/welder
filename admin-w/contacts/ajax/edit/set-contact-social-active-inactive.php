<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['contact_social_id'])) {
    $contact_social_id =  $_POST['contact_social_id'];
  }
  if(isset($_POST['set_contact_social'])) {
    $set_contact_social =  $_POST['set_contact_social'];
  }
  
  if(!empty($contact_social_id)) {
 
    $query_update_contact_social = "UPDATE `contacts_socials` SET `contact_social_is_active`='$set_contact_social' WHERE `contact_social_id` = '$contact_social_id'";
 
    //echo $query_update_contact_social;
    $result_update_contact_social = mysqli_query($db_link, $query_update_contact_social);
    if(!$result_update_contact_social) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>