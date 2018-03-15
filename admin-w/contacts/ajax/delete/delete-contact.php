<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['contact_id'])) {
    $contact_id = $_POST['contact_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  $all_queries = "";
  
  $query = "DELETE FROM `contacts` WHERE `contact_id` = '$contact_id'";
  $all_queries .= "<br>".$query."\n";
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  $query = "DELETE FROM `contacts_descriptions` WHERE `contact_id` = '$contact_id'";
  $all_queries .= "<br>".$query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
        
  $query_contact_phones = "SELECT `contact_phone_id` FROM `contacts_phones` WHERE `contact_id` = '$contact_id' LIMIT 1";
  $all_queries .= $query_contact_phones."<br>";
  $result_contact_phones = mysqli_query($db_link, $query_contact_phones);
  if(!$result_contact_phones) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_contact_phones) > 0) {

    $query = "DELETE FROM `contacts_phones` WHERE `contact_id` = '$contact_id'";
    $all_queries .= "<br>".$query."\n";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    mysqli_free_result($result_contact_phones);
  }
    
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  
  mysqli_query($db_link,"COMMIT");
