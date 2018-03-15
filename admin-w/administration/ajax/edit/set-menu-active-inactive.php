<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['menu_id'])) {
    $menu_id =  $_POST['menu_id'];
  }
  if(isset($_POST['set_menu'])) {
    $set_menu =  $_POST['set_menu'];
  }
  
  if(!empty($menu_id)) {
 
    $query_update_menu = "UPDATE `menus` SET  `menu_is_active`='$set_menu' WHERE `menu_id` = '$menu_id'";
 
    //echo $query_update_menu;
    $result_update_menu = mysqli_query($db_link, $query_update_menu);
    if(!$result_update_menu) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>