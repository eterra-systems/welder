<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  //echo "<pre>";print_r($_POST);exit;

  $user_type_id = $_POST['user_type_id'];
  $menu_rights = $_POST['menu_rights'];
  
  if(!empty($menu_rights)) {
    
    $all_queries = "";
    
    mysqli_query($db_link,"BEGIN");
    
    foreach($menu_rights as $menu_id => $user_rights) {
      //echo"<pre>";print_r($user_rights);

      $users_rights_access = 0;
        if(isset($user_rights['access'])) $users_rights_access = 1;
      $users_rights_add = 0;
        if(isset($user_rights['add'])) $users_rights_add = 1;
      $users_rights_edit = 0;
        if(isset($user_rights['edit'])) $users_rights_edit = 1;
      $users_rights_delete = 0;
        if(isset($user_rights['delete'])) $users_rights_delete = 1;

      $query_update = "UPDATE `users_types_rights` 
                      SET `users_rights_access`='$users_rights_access',
                          `users_rights_add`='$users_rights_add',
                          `users_rights_edit`='$users_rights_edit',
                          `users_rights_delete`='$users_rights_delete' 
                      WHERE `user_type_id` = '$user_type_id' AND `menu_id` = '$menu_id'";
      $all_queries .= "<br>\n".$query_update;
      $result_update = mysqli_query($db_link, $query_update);
      if(!$result_update) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
  }
        