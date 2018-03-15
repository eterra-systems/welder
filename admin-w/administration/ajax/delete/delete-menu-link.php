<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['menu_id'])) {
    $menu_id = $_POST['menu_id'];
  }
  
  if(!empty($menu_id)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $query = "SELECT `menu_id` FROM `menus` WHERE `menu_parent_id` = '$menu_id'";
    //echo $query;exit;
    $all_queries .= "\n".$query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      echo "This menu has children. Please delete the children first!";
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query = "DELETE FROM `menus` WHERE `menu_id` = '$menu_id'";
    //echo $query;exit;
    $all_queries .= "\n".$query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." 1 - `menus` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query = "DELETE FROM `menus_translations` WHERE `menu_id` = '$menu_id'";
    //echo $query;exit;
    $all_queries .= "\n".$query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." 2 - `menus_translations` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_select_users_rights = "SELECT `users_rights_id` FROM `users_rights` WHERE `menu_id` = '$menu_id'";
    $all_queries .= $query_select_users_rights."\n<br>";
    //echo $query;exit;
    $result_select_users_rights = mysqli_query($db_link, $query_select_users_rights);
    if(mysqli_num_rows($result_select_users_rights) > 0) {

      $query = "DELETE FROM `users_rights` WHERE `menu_id` = '$menu_id'";
      //echo $query;exit;
      $all_queries .= "\n".$query;
      $result = mysqli_query($db_link, $query);
      if(!$result) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_delete']." 3 - `users_rights` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    $query_select_users_rights = "SELECT `users_rights_id` FROM `users_types_rights` WHERE `menu_id` = '$menu_id'";
    $all_queries .= $query_select_users_rights."\n<br>";
    //echo $query;exit;
    $result_select_users_rights = mysqli_query($db_link, $query_select_users_rights);
    if(mysqli_num_rows($result_select_users_rights) > 0) {

      $query = "DELETE FROM `users_types_rights` WHERE `menu_id` = '$menu_id'";
      //echo $query;exit;
      $all_queries .= "\n".$query;
      $result = mysqli_query($db_link, $query);
      if(!$result) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_delete']." 4 - `users_types_rights` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
//    echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
  }