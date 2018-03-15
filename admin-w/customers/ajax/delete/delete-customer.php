<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];
  }
  
  if(!empty($customer_id)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $query = "DELETE FROM `customers` WHERE `customer_id` = '$customer_id'";
    //echo $query;exit;
    $all_queries .= "\n".$query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - `menus` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_select_customers_addresses = "SELECT `customer_address_id` FROM `customers_addresses` WHERE `customer_id` = '$customer_id'";
    $all_queries .= $query_select_customers_addresses."\n<br>";
    //echo $query;exit;
    $result_select_customers_addresses = mysqli_query($db_link, $query_select_customers_addresses);
    if(mysqli_num_rows($result_select_customers_addresses) > 0) {

      $query = "DELETE FROM `customers_addresses` WHERE `customer_id` = '$customer_id'";
      //echo $query;exit;
      $all_queries .= "\n".$query;
      $result = mysqli_query($db_link, $query);
      if(!$result) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_delete']." - `users_rights` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    $query_select_customers_logs = "SELECT `customer_log_id` FROM `customers_logs` WHERE `customer_id` = '$customer_id'";
    $all_queries .= $query_select_customers_logs."\n<br>";
    //echo $query;exit;
    $result_select_customers_logs = mysqli_query($db_link, $query_select_customers_logs);
    if(mysqli_num_rows($result_select_customers_logs) > 0) {

      $query = "DELETE FROM `customers_logs` WHERE `customer_id` = '$customer_id'";
      //echo $query;exit;
      $all_queries .= "\n".$query;
      $result = mysqli_query($db_link, $query);
      if(!$result) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_delete']." - `users_rights` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
  
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
  }
  
  DB_CloseI($db_link);