<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
//  echo"<pre>";print_r($_POST);exit;
  check_for_csrf();

  if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  }
  if (isset($_POST['user_username'])) {
    $user_username = $_POST['user_username'];
  }
  if (isset($_POST['user_password'])) {
    $user_password = $_POST['user_password'];
  }
  $user_is_active = 0;
    if(isset($_POST['user_is_active'])) $user_is_active = 1;
  $user_is_ip_in_use = 0;
    if(isset($_POST['user_is_ip_in_use'])) $user_is_ip_in_use = 1;

  if (isset($_POST['menu_rights'])) {
    $menu_rights = $_POST['menu_rights'];
  }
  //echo"<pre>";print_r($menu_rights);exit;

  if(!empty($user_id) && !empty($user_username)) {

    $query = "SELECT `user_id` FROM `users` WHERE `user_username` = '$user_username' AND `user_id` <> '$user_id'";
    //echo $query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      echo "This username is already taken. Please choose another one";
      exit;
    }

    mysqli_query($db_link, "START TRANSACTION");

    $user_username = mysqli_real_escape_string($db_link,$user_username);

    //update user name, password or  status
    $query_update_user = "UPDATE `users` SET `user_username` = '$user_username', ";
    //if password is filled
    if(!empty($user_password)) {
        $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
        $bcrypt_password = crypt($user_password , $bcrypt_salt);
        $query_update_user .= "`user_salted_password` = '" . $bcrypt_password . "', ";
    }
    $query_update_user .= "`user_is_active` = '$user_is_active', `user_is_ip_in_use` = '$user_is_ip_in_use' WHERE `user_id` = '$user_id'";
    $all_queries = "<br>\n".$query_update_user;
    //echo $query_update_user;exit;
    $result_update_user = mysqli_query($db_link, $query_update_user);
    if(!$result_update_user) echo "User data not changed!".  mysqli_error($db_link);

    if(!empty($menu_rights)) {
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

        $query_update = "UPDATE `users_rights` 
                            SET `users_rights_access`='$users_rights_access',
                                `users_rights_add`='$users_rights_add',
                                `users_rights_edit`='$users_rights_edit',
                                `users_rights_delete`='$users_rights_delete' 
                          WHERE `user_id` = '$user_id' AND `menu_id` = '$menu_id'";
        $all_queries .= "<br>\n".$query_update;
        $result_update = mysqli_query($db_link, $query_update);
        if(!$result_update) {
          echo $languages['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

      }
    }

    //echo $all_queries;mysqli_query($db_link, "ROLLBACK");exit;

    mysqli_query($db_link, "COMMIT");
  }// if( !empty($user_id) && !empty($user_username) )
?>
