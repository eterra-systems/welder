<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once '../../config.php';
  include_once 'include-functions.php';
  
  start_page_build_time_measure();
  
  mysqli_select_db($db_link, 'website_basic');
  mysqli_set_charset($db_link, 'utf8');

  $sql = 'SHOW TABLE STATUS FROM `website_basic`;';
  $result = mysqli_query($db_link, $sql);

  $rows = array();
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }

//  delete tables
//  foreach ($rows as $row) {
//    $table_name = mysqli_real_escape_string($db_link, $row['Name']);
//    if($table_name == "africa" || $table_name == "first_winner" || $table_name == "gift_card" || $table_name == "igrata_africa" 
//      || $table_name == "igrata_afrika" || $table_name == "mail_list" || $table_name == "mail_list_2" 
//      || $table_name == "mail_test" || $table_name == "rezervi" || $table_name == "rezervi_g_nagrada" || $table_name == "valentine" || $table_name == "valentinka" || 
//      $table_name == "voucher_1" || $table_name == "voucher_2" || $table_name == "voucher_3" || $table_name == "voucher_4") {
//      $sql = 'DROP TABLE IF EXISTS `' . $table_name . '`;';
//      mysqli_query($db_link, $sql);
//      echo "$table_name<br>";
//    }
//  }
  
  // truncate tables
//  foreach ($rows as $row) {
//    $table_name = mysqli_real_escape_string($db_link, $row['Name']);
//    if($table_name != "contents" && $table_name != "contents_types" && $table_name != "countries" && $table_name != "currencies" && $table_name != "customers_groups"  
//      && $table_name != "customers_groups_translations" && $table_name != "languages" && $table_name != "length_class" && $table_name != "length_class_description"  
//      && $table_name != "menus" && $table_name != "menus_translations" && $table_name != "sites" && $table_name != "social_networks" && $table_name != "stock_status"  
//      && $table_name != "users" && $table_name != "users_rights" && $table_name != "users_types" && $table_name != "users_types_descriptions" && $table_name != "users_types_rights" 
//      && $table_name != "weight_class" && $table_name != "weight_class_description") {
//      $sql = 'TRUNCATE TABLE `' . $table_name . '`;';
//      mysqli_query($db_link, $sql);
//      //echo "$table_name<br>";
//    }
//  }
  
  echo "done";
  close_page_build_time_measure($print_time = true);
?>