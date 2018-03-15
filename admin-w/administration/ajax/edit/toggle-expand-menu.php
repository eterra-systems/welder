<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['menu_id'])) {
    $menu_id =  $_POST['menu_id'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  //echo "<pre>";print_r($_SERVER);EXIT;
  if(!empty($menu_id) && !empty($action)) {
    
    $menu_is_collapsed = ($action == "expand") ? 0 : 1; // else $action == collapse
    
    if($menu_id == "all") {
      $query_update_menu = "UPDATE `menus` SET  `menu_is_collapsed`='$menu_is_collapsed' WHERE `menu_has_children` = '1'"; 
    }
    else {
      $query_update_menu = "UPDATE `menus` SET  `menu_is_collapsed`='$menu_is_collapsed' WHERE `menu_id` = '$menu_id'";
    }
    //echo $query_update_menu;
    $result_update_menu = mysqli_query($db_link, $query_update_menu);
    if(!$result_update_menu) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

    list_menus($parent_id = 0, $path_number = 0);

  }
?>