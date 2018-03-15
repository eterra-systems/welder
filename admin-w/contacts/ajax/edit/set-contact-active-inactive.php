<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['contact_id'])) {
    $contact_id =  $_POST['contact_id'];
  }
  if(isset($_POST['set_contact'])) {
    $set_contact =  $_POST['set_contact'];
  }
  
  if(!empty($contact_id)) {
 
    $query_update_contact = "UPDATE `contacts` SET `contact_is_active`='$set_contact' WHERE `contact_id` = '$contact_id'";
 
    //echo $query_update_contact;
    $result_update_contact = mysqli_query($db_link, $query_update_contact);
    if(!$result_update_contact) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

  }