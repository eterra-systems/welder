<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['currency_id'])) {
    $currency_id =  $_POST['currency_id'];
  }
  if(isset($_POST['set_currency'])) {
    $set_currency =  $_POST['set_currency'];
  }
  
  if(!empty($currency_id)) {
 
    $query_update_currency = "UPDATE `currencies` SET  `currency_is_active`='$set_currency',`currency_date_modified` = NOW() WHERE `currency_id` = '$currency_id'";
 
    //echo $query_update_currency;
    $result_update_currency = mysqli_query($db_link, $query_update_currency);
    if(!$result_update_currency) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>