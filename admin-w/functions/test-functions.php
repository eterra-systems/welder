<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once '../../config.php';
  include_once 'include-functions.php';
  
  start_page_build_time_measure();
  
//  $db_name = "larose";
//  $table_name = "products";
//  
//  $column_names_array = get_column_names($db_name,$table_name);
//  echo "<pre>";print_r($column_names_array);

//  mysqli_query($db_link,"BEGIN");
//  
//  $all_queries = "";
  
  $user_type_id = 1;
  $user_username = "larose";
  $user_password = 'b7!:S{fr="kqxe(T';
  $bcrypt_password = "";
  $user_firstname = "larose";
  $user_lastname = "larose";
  $user_address = "NULL";
  $user_phone = "NULL";
  $user_email = "sales@larose.com";
  $user_info = "NULL";
  $user_address = "NULL";
  $user_is_ip_in_use= 0;
  $user_is_active= 1;
  if (!empty($user_password)) {
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($user_password , $bcrypt_salt);
  }
  else {
    if ($create_user_account == 1) {
      $user_password = generateRandomString(8);
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($user_password , $bcrypt_salt);
    }
  }
  
  $query_update_user = "UPDATE `users` SET `user_salted_password` = '$bcrypt_password' WHERE `user_id` = '1'";
  //echo $query_update_user;exit;
  $result_insert_user = mysqli_query($db_link, $query_update_user);
  if(!$result_insert_user) {
    mysqli_query($db_link, "ROLLBACK");
    exit;
  }
  exit;
  
  mysqli_select_db($db_link, 'rentam');
  mysqli_set_charset($db_link, 'utf8');

  $sql = 'SHOW TABLE STATUS FROM `rentam`;';
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
//    if($table_name != "contents_types" && $table_name != "contents" && $table_name != "customers_groups" && $table_name != "customers_groups_translations" 
//      && $table_name != "currencies" && $table_name != "length_class" && $table_name != "length_class_description" && $table_name != "countries" && $table_name != "menus" 
//      && $table_name != "menus_translations" && $table_name != "languages" && $table_name != "sites" && $table_name != "stock_status" && $table_name != "users" 
//      && $table_name != "users_rights" && $table_name != "users_types" && $table_name != "users_types_rights" && $table_name != "weight_class" 
//      && $table_name != "weight_class_description") {
//      $sql = 'TRUNCATE TABLE `' . $table_name . '`;';
//      mysqli_query($db_link, $sql);
//      //echo "$table_name<br>";
//    }
//  }
  
  close_page_build_time_measure($print_time = true);
?>