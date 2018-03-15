<?php

  include_once '../../config.php';
  include_once 'include-functions.php';
  
  mysqli_query($db_link,"BEGIN");
  
  $all_queries = "";

//  $query_delivery = "SELECT `country_id`, `weight_from`, `weight_to`, `days_from`, `days_till`, `price` FROM `delivery_abroad_prices` 
//                    ORDER BY `delivery_abroad_prices`.`delivery_id` ASC  LIMIT 7";
//  $result_delivery = mysqli_query($db_link, $query_delivery);
//  if(!$result_delivery) echo mysqli_error($db_link);
//  if(mysqli_num_rows($result_delivery) > 0) {
//    $key = 1;
//    while($menus_row = mysqli_fetch_assoc($result_delivery)) {
//      $country_id = 203;
//      $weight_from = $menus_row['weight_from'];
//      $weight_to = $menus_row['weight_to'];
//      $days_from = 5;
//      $days_till = 7;
//      switch ($key) {
//        case 1:
//          $price = 48;
//          break;
//        case 2:
//          $price = 60;
//          break;
//        case 3:
//          $price = 72;
//          break;
//        case 4:
//          $price = 96;
//          break;
//        case 5:
//          $price = 96;
//          break;
//        case 6:
//          $price = 138;
//          break;
//        case 7:
//          $price = 180;
//          break;
//        default:
//        break;
//      }
//      
////      $price = $menus_row['price'];
//
//      $query_insert_delivery = "INSERT INTO `delivery_abroad_prices`(`delivery_id`, 
//                                                                    `country_id`,  
//                                                                    `weight_from`, 
//                                                                    `weight_to`, 
//                                                                    `days_from`, 
//                                                                    `days_till`, 
//                                                                    `price`) 
//                                                            VALUES (NULL,
//                                                                    '$country_id',
//                                                                    '$weight_from',
//                                                                    '$weight_to',
//                                                                    '$days_from',
//                                                                    '$days_till',
//                                                                    '$price')";
//      $all_queries .= "<br>".$query_insert_delivery;
//      $result_insert_delivery = mysqli_query($db_link, $query_insert_delivery);
//      if(mysqli_affected_rows($db_link) <= 0) {
//        echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
//        mysqli_query($db_link,"ROLLBACK");
//        exit;
//      }
//      $key++;
//    }
//  }
// echo $all_queries;mysqli_rollback($db_link);exit;
     
//  echo "done";
//  mysqli_commit($db_link);
// 
//  $user_id = $_SESSION['admin']['user_id'];

  $query_categories = "SELECT `categories`.* FROM `categories`";
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_categories) > 0) {
    while($category_row = mysqli_fetch_assoc($result_categories)) {
      
      $category_id = $category_row['category_id'];
      $category_parent_id = $category_row['category_parent_id'];
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_hierarchy_ids = $category_row['category_hierarchy_ids'];
      $category_sort_order = $category_row['category_sort_order'];
      $category_has_children = $category_row['category_has_children'];
      
      $query_insert_ctc = "INSERT INTO `category_to_category`(`category_id`, 
                                                              `category_parent_id`, 
                                                              `category_hierarchy_level`, 
                                                              `category_hierarchy_ids`, 
                                                              `category_sort_order`, 
                                                              `category_has_children`)
                                                        VALUES ('$category_id',
                                                                '$category_parent_id',
                                                                '$category_hierarchy_level',
                                                                '$category_hierarchy_ids',
                                                                '$category_sort_order',
                                                                '$category_has_children')";
      $all_queries .= "<br>".$query_insert_ctc;
      $result_insert_ctc = mysqli_query($db_link, $query_insert_ctc);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $ctcs['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
  }
  //echo $all_queries;mysqli_rollback($db_link);exit;
  
//  $query_menus = "SELECT `menu_id`,`menu_hierarchy_level` FROM `menus`";
//  $result_menus = mysqli_query($db_link, $query_menus);
//  if(!$result_menus) echo mysqli_error($db_link);
//  if(mysqli_num_rows($result_menus) > 0) {
//    while($menus_row = mysqli_fetch_assoc($result_menus)) {
//      $menu_id = $menus_row['menu_id'];
//      $menu_hierarchy_level = $menus_row['menu_hierarchy_level']+1;
//      
//      $query_update_menu = "UPDATE `menus` SET `menu_hierarchy_level`='$menu_hierarchy_level' WHERE `menu_id` = '$menu_id'";
//      $all_queries .= "$query_update_menu <br>";
//      $result_update_menu = mysqli_query($db_link, $query_update_menu);
//      if(!$result_update_menu) {
//        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
//      }
//    }
//  }
  //echo $all_queries;mysqli_rollback($db_link);exit;
     
  echo "done";
  mysqli_commit($db_link);
  
?>
