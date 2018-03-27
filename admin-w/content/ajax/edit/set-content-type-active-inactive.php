<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['content_type_id'])) {
    $content_type_id =  $_POST['content_type_id'];
  }
  if(isset($_POST['set_content_type'])) {
    $set_content_type =  $_POST['set_content_type'];
  }
  
  if(!empty($content_type_id)) {
 
    $query_update_content_type = "UPDATE `contents_types` SET  `content_type_is_active`='$set_content_type' WHERE `content_type_id` = '$content_type_id'";
 
    //echo $query_update_content_type;
    $result_update_content_type = mysqli_query($db_link, $query_update_content_type);
    if(!$result_update_content_type) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

  }