<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['menu_id'])) {
    $menu_id =  $_POST['menu_id'];
  }
  if(isset($_POST['menu_parent_id'])) {
    $menu_parent_id =  $_POST['menu_parent_id'];
  }
  if(isset($_POST['menu_sort_order'])) {
    $menu_sort_order =  $_POST['menu_sort_order'];
  }
  if(isset($_POST['menu_hierarchy_level'])) {
    $menu_hierarchy_level =  $_POST['menu_hierarchy_level'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($menu_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_menu_sort_order = $menu_sort_order-1;
      $query_update_menu_1 = "UPDATE `menus` SET `menu_sort_order`='$menu_sort_order' 
                                WHERE `menu_parent_id` = '$menu_parent_id' AND `menu_sort_order` = '$previous_menu_sort_order' 
                                  AND `menu_hierarchy_level` = '$menu_hierarchy_level'";
      $all_queries .= "\n".$query_update_menu_1;
        //echo $query_update_menu_1;
      $result_update_menu_1 = mysqli_query($db_link, $query_update_menu_1);
      if(!$result_update_menu_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_menu_2 = "UPDATE `menus` SET `menu_sort_order`='$previous_menu_sort_order' WHERE `menu_id` = '$menu_id'";
      $all_queries .= "\n".$query_update_menu_2;
        //echo $query_update_menu_2;
      $result_update_menu_2 = mysqli_query($db_link, $query_update_menu_2);
      if(!$result_update_menu_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_menu_sort_order = $menu_sort_order+1;
      $query_update_menu_1 = "UPDATE `menus` SET `menu_sort_order`='$menu_sort_order' 
                              WHERE `menu_parent_id` = '$menu_parent_id' AND `menu_sort_order` = '$next_menu_sort_order' 
                                AND `menu_hierarchy_level` = '$menu_hierarchy_level'";
      $all_queries .= "\n".$query_update_menu_1;
        //echo $query_update_menu_1;
      $result_update_menu_1 = mysqli_query($db_link, $query_update_menu_1);
      if(!$result_update_menu_1) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_menu_2 = "UPDATE `menus` SET `menu_sort_order`='$next_menu_sort_order' WHERE `menu_id` = '$menu_id'";
      $all_queries .= "\n".$query_update_menu_2;
        //echo $query_update_menu_2;
      $result_update_menu_2 = mysqli_query($db_link, $query_update_menu_2);
      if(!$result_update_menu_2) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");

    list_menus($parent_id = 0, $path_number = 0);

  }