<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['user_type_id'])) {
    $user_type_id = $_POST['user_type_id'];
  }
  
  if(!empty($user_type_id) && ($user_type_id != 1)) {
    
    $query_users = "SELECT `user_id` FROM `users` WHERE `user_type_id` = '$user_type_id' LIMIT 1";
    $all_queries .= $query_users."\n<br>";
    //echo $query;exit;
    $result_users = mysqli_query($db_link, $query_users);
    if(mysqli_num_rows($result_users) > 0) {

      echo "Има потребители от тази потребителска група. Моля изтрийте първо тях и след това групата.";
      exit;
    }
    
    $query = "DELETE FROM `users_types` WHERE `user_type_id` = '$user_type_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    
    $query = "DELETE FROM `users_types_descriptions` WHERE `user_type_id` = '$user_type_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    
    $query_users_types_rights = "SELECT `users_types_rights_id` FROM `users_types_rights` WHERE `user_type_id` = '$user_type_id' LIMIT 1";
    $all_queries .= $query_users_types_rights."\n<br>";
    //echo $query;exit;
    $result_users_types_rights = mysqli_query($db_link, $query_users_types_rights);
    if(mysqli_num_rows($result_users_types_rights) > 0) {

      $query_users_types_rights = "DELETE FROM `users_types_rights` WHERE `user_type_id` = '$user_type_id'";
      $all_queries .= "$query<br>\n";
      $result_users_types_rights = mysqli_query($db_link, $query_users_types_rights);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
  }