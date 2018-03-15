<?php
  include_once '../../../../config.php';
  include_once "../../../../languages/languages_$current_lang.php";
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  }
  
  if(!empty($user_id)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $query = "DELETE FROM `users` WHERE `user_id` = '$user_id'";
    $all_queries .= "$query<br>\n";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_users_rights = "SELECT `users_rights_id` FROM `users_rights` WHERE `user_id` = '$user_id' LIMIT 1";
    $all_queries .= $query_users_rights."\n<br>";
    //echo $query;exit;
    $result_users_rights = mysqli_query($db_link, $query_users_rights);
    if(mysqli_num_rows($result_users_rights) > 0) {

      $query_users_rights = "DELETE FROM `users_rights` WHERE `user_id` = '$user_id'";
      $all_queries .= "$query<br>\n";
      $result_users_rights = mysqli_query($db_link, $query_users_rights);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    $query_users_logs = "SELECT `user_log_id` FROM `users_logs` WHERE `user_id` = '$user_id'";
    $all_queries .= $query_users_logs."\n<br>";
    //echo $query;exit;
    $result_users_logs = mysqli_query($db_link, $query_users_logs);
    if(mysqli_num_rows($result_users_logs) > 0) {

      $query_delete_users_logs = "DELETE FROM `users_logs` WHERE `user_id` = '$user_id'";
      $all_queries .= $query_delete_users_logs."\n<br>";
      //echo $query;exit;
      $result_delete_users_logs = mysqli_query($db_link, $query_delete_users_logs);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
  }