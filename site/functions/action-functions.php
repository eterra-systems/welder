<?php

function is_active_page($page) {
  
  if(strstr($_GET['page'], $page)) {
    return true;
  }
  else {
    return false;
  }
}
  
function prepare_for_null_row($value) {

  if (empty($value) || is_null($value))
      $value = "NULL";
  else
      $value = "'$value'";

  return $value;
}

function print_array_for_debug($array_for_debug) {

  echo "<pre>";print_r($array_for_debug);echo "</pre>";
}

function is_file_valid_format($file_exstension) {
  
  $valid_formats = array("jpg", "jpeg", "png", "gif", "pdf");
  if(!in_array($file_exstension, $valid_formats)) {
    return false;
  }
  return true;
}

function multiexplode($delimiters,$string) {
  
  $ready = str_replace($delimiters, $delimiters[0], $string);
  $launch = explode($delimiters[0], $ready);
  return  $launch;
    
}
       
function strstr_array($haystack, $needle, $offset=0) {
  if(!is_array($needle)) $needle = array($needle);
  foreach($needle as $query) {
    if(strpos($haystack, $query, $offset) !== false) return true; // stop on first true result
  }
  return false;
}

function generate_bcrypt_salt() {
  
    $rand_string = "";
    $charecters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./";
    for ($i = 0; $i < 22; $i++) {
        $randInt = mt_rand(0, 63);
        $rand_char = $charecters[$randInt];
        $rand_string .= $rand_char;
    }
    return $rand_string;
}

function generate_captcha() {
  
  global $db_link;

  unset($_SESSION['captcha123']);
  $_SESSION['captcha123'] = array();
  $rnd = rand(1,99);
  $query = "SELECT * FROM `captchas` LIMIT $rnd,1";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result)>0){

    $captcha = mysqli_fetch_assoc($result);
    $_SESSION['captcha123']['img'] = $captcha['captcha_image'];
    $_SESSION['captcha123']['code'] = $captcha['captcha_number'];

  }
}

function generate_strong_password($length = 8, $available_sets = 'luds') {
  $sets = array();
  if(strpos($available_sets, 'l') !== false)
          $sets[] = 'abcdefghjkmnpqrstuvwxyz';
  if(strpos($available_sets, 'u') !== false)
          $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
  if(strpos($available_sets, 'd') !== false)
          $sets[] = '23456789';
  if(strpos($available_sets, 's') !== false)
          $sets[] = '!@#$%&*?';

  $all = '';
  $password = '';
  foreach($sets as $set)
  {
          $password .= $set[array_rand(str_split($set))];
          $all .= $set;
  }

  $all = str_split($all);
  for($i = 0; $i < $length - count($sets); $i++)
          $password .= $all[array_rand($all)];

  $password = str_shuffle($password);

  return $password;
}

function check_if_users_passwords_match($user_password,$confirm_user_password) {
  global $languages;
  global $current_lang;
  if($user_password === $confirm_user_password) {
    return "";
  }
  else {
    return $languages['error_customer_passwords_mismatch'];
  }
}

function check_if_user_email_is_valid($customer_email) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  
  if(!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    return false;
  }
  else {
    if(!empty($customer_id)) $query = "SELECT `customer_id` FROM `customers` WHERE `customer_id` <> '$customer_id' AND `customer_email` = '$customer_email'";
    else $query = "SELECT `customer_id` FROM `customers` WHERE `customer_email` = '$customer_email'";
    //echo $query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      return false;
    }
    return true;
  }
}

// Function to get the client ip address
function get_client_ip_env() {
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP')) {
      $ipaddress = getenv('HTTP_CLIENT_IP');
  }
  else if(getenv('HTTP_X_FORWARDED_FOR')) {
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  }
  else if(getenv('HTTP_X_FORWARDED')) {
      $ipaddress = getenv('HTTP_X_FORWARDED');
  }
  else if(getenv('HTTP_FORWARDED_FOR')) {
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
  }
  else if(getenv('HTTP_FORWARDED')) {
      $ipaddress = getenv('HTTP_FORWARDED');
  }
  else if(getenv('REMOTE_ADDR')) {
      $ipaddress = getenv('REMOTE_ADDR');
  }
  else $ipaddress = 'Unknown';

  return $ipaddress;
}

// Function to get the client ip address
function get_client_ip_server() {
  $ipaddress = '';
  if ($_SERVER['HTTP_CLIENT_IP']) {
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  }
  else if($_SERVER['HTTP_X_FORWARDED_FOR']) {
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else if($_SERVER['HTTP_X_FORWARDED']) {
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  }
  else if($_SERVER['HTTP_FORWARDED_FOR']) {
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  }
  else if($_SERVER['HTTP_FORWARDED']) {
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
  }
  else if($_SERVER['REMOTE_ADDR']) {
      $ipaddress = $_SERVER['REMOTE_ADDR'];
  }
  else $ipaddress = 'Unknown';

  return $ipaddress;
}

function return_color($bckgr_color){

  $hex = substr($bckgr_color, 1, 6);
  //echo "$hex<br>";

  //break up the color in its RGB components
  $r = hexdec(substr($hex,0,2));
  $g = hexdec(substr($hex,2,2));
  $b = hexdec(substr($hex,4,2));

  //do simple weighted avarage
  //
  //(This might be overly simplistic as different colors are perceived
  // differently. That is a green of 128 might be brighter than a red of 128.
  // But as long as it's just about picking a white or black text color...)
  if($r + $g + $b >= 350){
    return '#000000';
  }
  elseif(($r + $g +$b > 250) && $r + $g +$b < 350){
    return '#EEEEEE';
  }
  else{
    return '#FFFFFF';
  }
  
  $c_r = hexdec(substr($hex, 0, 2));
  $c_g = hexdec(substr($hex, 2, 2));
  $c_b = hexdec(substr($hex, 4, 2));

  $brightness = (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
  if($brightness > 130) {
    return '#000000';
  }
  else{
    return '#FFFFFF';
  }
}

function check_if_content_has_active_children($content_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `content_id` FROM `contents` WHERE `content_parent_id` = '$content_id' AND `content_show_in_menu` = '1' AND `content_is_active` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return true;
  }
  else return false;
}

function check_if_this_is_content_last_child($content_parent_id,$content_menu_order) {
  
  global $db_link;
  
  $query_active_children = "SELECT `content_id` FROM `contents` 
                             WHERE `content_parent_id` = '$content_parent_id' AND `content_menu_order` > '$content_menu_order'
                               AND `content_show_in_menu` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return false;
  }
  else return true;
}

function check_if_this_is_category_last_child($category_root_id, $category_parent_id,$category_sort_order) {
  
  global $db_link;
  
  $query_last_child = "SELECT `category_id` FROM `category_to_category` 
                        WHERE `category_root_id` = '$category_root_id' AND `category_parent_id` = '$category_parent_id' AND `category_sort_order` > '$category_sort_order' 
                        LIMIT 1";
  //if($category_parent_id == 72) echo "$query_last_child<br>";
  $result_last_child = mysqli_query($db_link, $query_last_child);
  if(!$result_last_child) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_last_child) > 0) {
    
    return false;
  }
  return true;
}

function check_if_news_cat_has_active_children($news_category_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `news_category_id` FROM `news_categories` WHERE `news_cat_parent_id` = '$news_category_id' AND `content_show_in_menu` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return true;
  }
  else return false;
}

function check_if_this_is_news_cat_last_child($news_category_id,$news_cat_sort_order) {
  
  global $db_link;
  
  $query_active_children = "SELECT `news_category_id` FROM `news_categories` 
                            WHERE `news_cat_parent_id` = '$news_category_id' AND `news_cat_sort_order` > '$news_cat_sort_order'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return false;
  }
  else return true;
}

function get_lаst_auto_increment_id($table_name) {
  
  global $db_link;
  
  $query_lаst_auto_increment_id = "SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '$table_name'";
  //echo $query_lаst_auto_increment_id;
  $result_lаst_auto_increment_id = mysqli_query($db_link, $query_lаst_auto_increment_id);
  if(!$result_lаst_auto_increment_id) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_lаst_auto_increment_id) > 0) {
    while($row_lаst_auto_increment_id = mysqli_fetch_assoc($result_lаst_auto_increment_id)) {
      $lаst_auto_increment_ids[] = $row_lаst_auto_increment_id['auto_increment'];
    }
    if(isset($lаst_auto_increment_ids[1])) {
      $lаst_auto_increment_id = $lаst_auto_increment_ids[1];
    }
    else {
      $lаst_auto_increment_id = $lаst_auto_increment_ids[0];
    }
    
    mysqli_free_result($result_lаst_auto_increment_id);
  }
  
  return $lаst_auto_increment_id;
}

function get_customers_groups() {
  
  global $db_link;
  global $current_language_id;
  
  $query_customers_groups = "SELECT `customers_groups`.`customer_group_id`,`customers_groups`.`customer_group_code`,`cgl`.`customer_group_name`
                               FROM `customers_groups`
                         INNER JOIN `customers_groups_languages` as `cgl` ON `cgl`.`customer_group_id` = `customers_groups`.`customer_group_id`
                              WHERE `cgl`.`language_id` = '$current_language_id'
                           ORDER BY `customers_groups`.`customer_group_sort_order` ASC";
  $result_customers_groups = mysqli_query($db_link,$query_customers_groups);
  if(!$result_customers_groups) echo mysqli_error($db_link);
  $customer_groups_count = mysqli_num_rows($result_customers_groups);
  if(mysqli_num_rows($result_customers_groups) > 0) {
    while($customers_groups_row = mysqli_fetch_assoc($result_customers_groups)) {
      $customers_groups_array[] = $customers_groups_row;
    }
    mysqli_free_result($result_customers_groups);
  }
  
  return $customers_groups_array;
}

function get_page_by_type($page_type) {
  
  global $db_link;
  global $current_language_id;
  
  $content_array = array();
  $query_content = "SELECT `contents`.`content_hierarchy_ids`,`cd`.`content_summary`,`cd`.`content_pretty_url`,`cd`.`content_name`,
                           `cd`.`content_menu_text`,`cd`.`content_meta_title`,
                           `cd`.`content_meta_keywords`,`cd`.`content_meta_description`
                      FROM `contents`
                INNER JOIN `contents_descriptions` as `cd` ON `cd`.`content_id` = `contents`.`content_id`
                INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                     WHERE `contents_types`.`content_type` = '$page_type' AND `cd`.`language_id` = '$current_language_id'";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    
    $content_array = mysqli_fetch_assoc($result_content);
    
    mysqli_free_result($result_content);
  }
  
  return $content_array;
}

function get_gallery_images($gallery_id,$count = false) {
  
  global $db_link;
  global $current_language_id;
  $gi_names_array = array();
  
  $limit = ($count) ? "LIMIT $count" : "";
  $query_gi_name = "SELECT `galleries_images`.`gallery_image_id`,`galleries_images`.`gi_name`,`galleries_images`.`gi_is_album_cover`,`galleries_images`.`gi_is_active`,
                           `galleries_images`.`gi_sort_order`,`gid`.`gallery_image_id`,`gid`.`gallery_image_title`,`gid`.`gallery_image_comment`
                      FROM `galleries_images` 
                INNER JOIN `galleries_images_descriptions` as `gid` USING(`gallery_image_id`)
                     WHERE `galleries_images`.`gallery_id` = '$gallery_id' AND `gid`.`language_id` = '$current_language_id'
                  ORDER BY `galleries_images`.`gi_sort_order` ASC $limit";
  //echo $query_gi_name;
  $result_gi_name = mysqli_query($db_link, $query_gi_name);
  if(!$result_gi_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_gi_name) > 0) {
    while($gi_names_row = mysqli_fetch_assoc($result_gi_name)) {
      $gi_names_array[] = $gi_names_row;
    }
    mysqli_free_result($result_gi_name);
  }
  
  return $gi_names_array;
}

function get_galleries($count) {
  
  global $db_link;
  global $current_language_id;
  
  $galleries_array = array();
  $limit = ($count == 0) ? "" : "LIMIT $count";
  
  $query_galleries = "SELECT `galleries`.`gallery_id`,`galleries_descriptions`.`gallery_name`,`galleries_images`.`gi_name` as `album_cover`
                        FROM `galleries` 
                  INNER JOIN `galleries_descriptions` USING(`gallery_id`)
                  INNER JOIN `galleries_images` USING(`gallery_id`)
                       WHERE `galleries`.`gallery_is_active` = '1' AND `galleries_descriptions`.`language_id` = '$current_language_id'
                         AND `galleries_images`.`gi_is_album_cover` = '1'
                    ORDER BY `galleries`.`gallery_sort_order` ASC $limit";
  //echo $query_galleries;exit;
  $result_galleries = mysqli_query($db_link, $query_galleries);
  if(!$result_galleries) echo mysqli_error($db_link);
  $galleries_count = mysqli_num_rows($result_galleries);
  if($galleries_count > 0) {
    
    while($gallery_row = mysqli_fetch_assoc($result_galleries)) {

      $galleries_array[] = $gallery_row;
    }
    mysqli_free_result($result_galleries);

  }
  
  return $galleries_array;
}

function get_default_lang_code() {
  
  global $db_link;

  $query_language = "SELECT `language_code` FROM `languages` WHERE `language_is_default_frontend` = '1'";
  $result_language = mysqli_query($db_link, $query_language);
  if(!$result_language) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language) > 0) {
    $language_array = mysqli_fetch_assoc($result_language);
    $current_lang = stripslashes($language_array['language_code']);
    mysqli_free_result($result_language);
  }
  return $current_lang;
}

function get_default_contact() {
  
  global $db_link;
  global $current_language_id;
  
  $contact_array = array();
  $query_contact = "SELECT `contacts`.`contact_id`,`contacts`.`contact_email`,`contact_city`,`contact_postcode`,`contacts_descriptions`.`contact_address` 
                      FROM `contacts` 
                INNER JOIN `contacts_descriptions` ON `contacts_descriptions`.`contact_id` = `contacts`.`contact_id`
                     WHERE `contacts`.`contact_is_default` = '1' AND `contacts_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_contact;
  $result_contact = mysqli_query($db_link, $query_contact);
  if(!$result_contact) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_contact) > 0) {
    
    $contact_array = mysqli_fetch_assoc($result_contact);
    
    $query_phones = "SELECT `contact_phone_id`,`contact_phone`,`contact_phone_is_home` FROM `contacts_phones` 
                      WHERE `contact_id` = '".$contact_array['contact_id']."'";
    $result_phones = mysqli_query($db_link, $query_phones);
    if(!$result_phones) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_phones) > 0) {
      while($contact_phone_row = mysqli_fetch_assoc($result_phones)) {

        if($contact_phone_row['contact_phone_is_home'] == 1) {
          $contact_array['contact_home_phone'] = $contact_phone_row['contact_phone'];
        }
        else {
          $contact_array['contact_mobile_phones'][] = $contact_phone_row['contact_phone'];
        }
      }
      mysqli_free_result($result_phones);
    }

    mysqli_free_result($result_contact);
  }
  
  return $contact_array;
}

function get_contacts() {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  
  $contacts_array = array();
  $query_contacts = "SELECT `contacts`.`contact_id`,`contacts`.`contact_map_lat`,`contacts`.`contact_map_lng`,`contacts`.`contact_email`,`contacts`.`contact_is_default`,
                            `contacts_descriptions`.`contact_city`,`contacts_descriptions`.`contact_postcode`,`contacts_descriptions`.`contact_address`,
                            `contacts_descriptions`.`contact_info`
                       FROM `contacts`
                 INNER JOIN `contacts_descriptions` ON `contacts_descriptions`.`contact_id` = `contacts`.`contact_id`
                      WHERE `contacts`.`contact_is_active` = '1' AND `contacts_descriptions`.`language_id` = '$current_language_id'
                   ORDER BY `contact_sort_order` ASC";
  //echo $query_contacts;exit;
  $result_contacts = mysqli_query($db_link, $query_contacts);
  if(!$result_contacts) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_contacts) > 0) {
    while($contact_row = mysqli_fetch_assoc($result_contacts)) {
      $contacts_array[] = $contact_row;
    }
    mysqli_free_result($result_contacts);
  }
  
  return $contacts_array;
}

function get_random_ids_list($column_name,$table_name,$where_cond,$count) {
  
  global $db_link;
  
  $random_ids_list = "0";
  $query_random_ids = "SELECT `$column_name` FROM `$table_name` $where_cond ORDER BY RAND() LIMIT $count";
  //echo $query_random_ids."<br>";
  $result_random_ids = mysqli_query($db_link, $query_random_ids);
  if(!$result_random_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_random_ids) > 0) {
    $key = 0;
    while($random_ids_row = mysqli_fetch_assoc($result_random_ids)) {

      $random_ids_list .= ($key == 0) ? $random_ids_row[$column_name] : ",".$random_ids_row[$column_name];

      $key++;
    }
  }
  return $random_ids_list;
}

//epay function
function hmac($algo,$data,$passwd){
  /* md5 and sha1 only */
  $algo=strtolower($algo);
  $p=array('md5'=>'H32','sha1'=>'H40');
  if(strlen($passwd)>64) $passwd=pack($p[$algo],$algo($passwd));
  if(strlen($passwd)<64) $passwd=str_pad($passwd,64,chr(0));

  $ipad=substr($passwd,0,64) ^ str_repeat(chr(0x36),64);
  $opad=substr($passwd,0,64) ^ str_repeat(chr(0x5C),64);

  return($algo($opad.pack($p[$algo],$algo($ipad.$data))));
}
?>
