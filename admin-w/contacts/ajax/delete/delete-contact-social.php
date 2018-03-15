<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['contact_social_id'])) {
    $contact_social_id = $_POST['contact_social_id'];
  }
  
  $query = "DELETE FROM `contacts_socials` WHERE `contact_social_id` = '$contact_social_id'";
  $all_queries .= "<br>".$query."\n";
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
