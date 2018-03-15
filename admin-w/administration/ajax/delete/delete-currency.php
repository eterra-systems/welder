<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['currency_id'])) {
    $currency_id = $_POST['currency_id'];
  }
  
  if(!empty($currency_id)) {
    
    $query = "DELETE FROM `currencies` WHERE `currency_id` = '$currency_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    }
  }
?>