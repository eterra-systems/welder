<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['customer_group_id'])) {
    $customer_group_id = $_POST['customer_group_id'];
  }
  
  if(!empty($customer_group_id)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
//    $query = "SELECT `customer_group_id` FROM `menus` WHERE `menu_parent_id` = '$customer_group_id'";
//    //echo $query;exit;
//    $all_queries .= "\n".$query;
//    $result = mysqli_query($db_link, $query);
//    if(!$result) echo mysqli_error($db_link);
//    if(mysqli_num_rows($result) > 0) {
//      echo "This menu has children. Please delete the children first!";
//      mysqli_query($db_link,"ROLLBACK");
//      exit;
//    }
    
    $query = "DELETE FROM `customers_groups` WHERE `customer_group_id` = '$customer_group_id'";
    //echo $query;exit;
    $all_queries .= "\n".$query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - `menus` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_selectcg_translations = "SELECT `customer_group_id` FROM `customers_groups_languages` WHERE `customer_group_id` = '$customer_group_id'";
    $all_queries .= $query_selectcg_translations."\n<br>";
    //echo $query;exit;
    $result_selectcg_translations = mysqli_query($db_link, $query_selectcg_translations);
    if(mysqli_num_rows($result_selectcg_translations) > 0) {

      $query = "DELETE FROM `customers_groups_languages` WHERE `customer_group_id` = '$customer_group_id'";
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