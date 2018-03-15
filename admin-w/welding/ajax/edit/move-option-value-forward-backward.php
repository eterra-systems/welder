<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['option_id'])) {
    $current_option_id =  $_POST['option_id'];
  }
  if(isset($_POST['option_value_id'])) {
    $option_value_id =  $_POST['option_value_id'];
  }
  if(isset($_POST['ov_sort_order'])) {
    $ov_sort_order =  $_POST['ov_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($option_value_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_ov_sort_order = $ov_sort_order-1;
      $query_update_attribute_1 = "UPDATE `option_value` 
                                      SET `ov_sort_order`='$ov_sort_order' 
                                    WHERE `option_id` = '$current_option_id' AND `ov_sort_order` = '$previous_ov_sort_order' ";
      $all_queries .= "\n".$query_update_attribute_1;
        //echo $query_update_attribute_1;
      $result_update_attribute_1 = mysqli_query($db_link, $query_update_attribute_1);
      if(!$result_update_attribute_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_attribute_2 = "UPDATE `option_value` SET `ov_sort_order`='$previous_ov_sort_order' WHERE `option_value_id` = '$option_value_id'";
      $all_queries .= "\n".$query_update_attribute_2;
        //echo $query_update_attribute_2;
      $result_update_attribute_2 = mysqli_query($db_link, $query_update_attribute_2);
      if(!$result_update_attribute_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_ov_sort_order = $ov_sort_order+1;
      $query_update_attribute_1 = "UPDATE `option_value` 
                                      SET `ov_sort_order`='$ov_sort_order'  
                                    WHERE `option_id` = '$current_option_id' AND `ov_sort_order` = '$next_ov_sort_order' ";
      $all_queries .= "\n".$query_update_attribute_1;
        //echo $query_update_attribute_1;
      $result_update_attribute_1 = mysqli_query($db_link, $query_update_attribute_1);
      if(!$result_update_attribute_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_attribute_2 = "UPDATE `option_value` SET `ov_sort_order`='$next_ov_sort_order' WHERE `option_value_id` = '$option_value_id'";
      $all_queries .= "\n".$query_update_attribute_2;
        //echo $query_update_attribute_2;
      $result_update_attribute_2 = mysqli_query($db_link, $query_update_attribute_2);
      if(!$result_update_attribute_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");

    $languages_array = get_languages();
  
    list_products_options_values();
  }
?>