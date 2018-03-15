<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['contact_id'])) {
    $contact_id =  $_POST['contact_id'];
  }
  
  if(!empty($contact_id)) {
 
    mysqli_query($db_link,"BEGIN");
    $all_queries = "";
    
    $query_update_contact = "UPDATE `contacts` SET `contact_is_default`='0' WHERE `contact_is_default`='1'";
    $all_queries .= $query_update_contact;
    $result_update_contact = mysqli_query($db_link, $query_update_contact);
    if(!$result_update_contact) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_update_contact = "UPDATE `contacts` SET `contact_is_default`='1' WHERE `contact_id` = '$contact_id'";
    $all_queries .= $query_update_contact;
    $result_update_contact = mysqli_query($db_link, $query_update_contact);
    if(!$result_update_contact) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
    
    list_contacts();

  }