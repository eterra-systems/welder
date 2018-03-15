<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['contact_id'])) {
    $contact_id =  $_POST['contact_id'];
  }
  if(isset($_POST['contact_sort_order'])) {
    $contact_sort_order =  $_POST['contact_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($contact_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_contact_sort_order = $contact_sort_order-1;
      $query_update_contact_1 = "UPDATE `contacts` SET `contact_sort_order`='$contact_sort_order' WHERE `contact_sort_order` = '$previous_contact_sort_order' ";
      $all_queries .= "\n".$query_update_contact_1;
        //echo $query_update_contact_1;
      $result_update_contact_1 = mysqli_query($db_link, $query_update_contact_1);
      if(!$result_update_contact_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_contact_2 = "UPDATE `contacts` SET `contact_sort_order`='$previous_contact_sort_order' WHERE `contact_id` = '$contact_id'";
      $all_queries .= "\n".$query_update_contact_2;
        //echo $query_update_contact_2;
      $result_update_contact_2 = mysqli_query($db_link, $query_update_contact_2);
      if(!$result_update_contact_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_contact_sort_order = $contact_sort_order+1;
      $query_update_contact_1 = "UPDATE `contacts` SET `contact_sort_order`='$contact_sort_order'  WHERE `contact_sort_order` = '$next_contact_sort_order' ";
      $all_queries .= "\n".$query_update_contact_1;
        //echo $query_update_contact_1;
      $result_update_contact_1 = mysqli_query($db_link, $query_update_contact_1);
      if(!$result_update_contact_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_contact_2 = "UPDATE `contacts` SET `contact_sort_order`='$next_contact_sort_order' WHERE `contact_id` = '$contact_id'";
      $all_queries .= "\n".$query_update_contact_2;
        //echo $query_update_contact_2;
      $result_update_contact_2 = mysqli_query($db_link, $query_update_contact_2);
      if(!$result_update_contact_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");

    list_contacts();

  }
?>