<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once 'config.php';
  include_once 'functions/include-functions.php';
  
  if(isset($_POST['newsletter_email'])) {
    $newsletter_email =  $_POST['newsletter_email'];
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  
  include_once "languages/languages_$current_lang.php";
  //echo "<pre>";print_r($_POST);echo "</pre>";exit;
  
  mysqli_query($db_link,"BEGIN");

  $campaign_id = 1; //this is hardcoded for now
  
  $query_subscribe = "INSERT INTO `email_campaign_list`(`campaign_list_id`, `campaign_id`, `email`) 
                                                VALUES (NULL,'$campaign_id','$newsletter_email')";
  //echo "$query_subscribe<br>";
  $result_subscribe = mysqli_query($db_link, $query_subscribe);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  echo $languages['text_newsletter_subscription_success'];
  
  mysqli_query($db_link,"COMMIT");
?>
