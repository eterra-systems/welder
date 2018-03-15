<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['currency_id'])) {
    $currency_id =  $_POST['currency_id'];
  }
  
  if(!empty($currency_id)) {
 
    mysqli_query($db_link,"BEGIN");
    
    $query_update_currency = "UPDATE `currencies` SET `currency_is_default`='0' WHERE `currency_is_default`='1'";
    //echo $query_update_currency;
    $result_update_currency = mysqli_query($db_link, $query_update_currency);
    if(!$result_update_currency) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_update_currency = "UPDATE `currencies` SET `currency_is_default`='1' WHERE `currency_id` = '$currency_id'";
    //echo $query_update_currency;
    $result_update_currency = mysqli_query($db_link, $query_update_currency);
    if(!$result_update_currency) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    mysqli_query($db_link,"COMMIT");
    
    list_currencies();

  }
?>
