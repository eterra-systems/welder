<?php

function include_file($file_name) {
  
  if (!defined($file_name)) {
    require($file_name);
    define($file_name, 1);
  }
}

function is_active_page($active_page) {
  
  if(strstr($_SERVER['PHP_SELF'], $active_page)) {
    return true;
  }
  else {
    return false;
  }
}
   
function multiexplode($delimiters,$string) {
  
  $ready = str_replace($delimiters, $delimiters[0], $string);
  $launch = explode($delimiters[0], $ready);
  return  $launch;
    
}
        
function is_only_numbers($value) {

  if(preg_match("/[0-9]/", $value)) { 
      return true;
   }  
  else {
    return false;
  }
  
}

function prepare_for_null_row($value) {

  $return_val = (empty($value) || is_null($value)) ? "NULL" : "'$value'";

  return $return_val;
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

function start_page_build_time_measure() {
  
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $starttime = $mtime;
}

function close_page_build_time_measure($print_time = false) {
  
  global $starttime;
  
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $endtime = $mtime; 
  $totaltime = ($endtime - $starttime);
  if($print_time) echo "<br><p>This page was created in ".$totaltime." seconds</p>";
}

function readableColour($bckgr_color){
  $hex = substr($bckgr_color, 1, 6);
  
  $r = hexdec(substr($hex,0,2));
  $g = hexdec(substr($hex,2,2));
  $b = hexdec(substr($hex,4,2));

  $contrast = sqrt(
      $r * $r * .241 +
      $g * $g * .691 +
      $b * $b * .068
  );

  if($contrast >= 140) {
    return '#000000';
  }
  elseif($contrast >= 60 && $contrast < 140) {
    return '#EEEEEE';
  }
  else{
    return '#FFFFFF';
  }
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

function print_array_for_debug($array_for_debug) {

  echo "<pre>";print_r($array_for_debug);echo "</pre>";
}

function get_lаst_inserted_id($table_name, $key_name) {
  
  global $db_link;
  
  $query_lаst_inserted_id = "SELECT `$key_name` FROM `$table_name` ORDER BY `$key_name` DESC LIMIT 1";
  //echo $query_lаst_inserted_id;
  $result_lаst_inserted_id = mysqli_query($db_link, $query_lаst_inserted_id);
  if(!$result_lаst_inserted_id) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_lаst_inserted_id) > 0) {
    $row_lаst_inserted_id = mysqli_fetch_assoc($result_lаst_inserted_id);
    $lаst_inserted_id = $row_lаst_inserted_id[$key_name];
    
    mysqli_free_result($result_lаst_inserted_id);
  }
  
  return $lаst_inserted_id;
}

function validate_upload_image($input_name, $upload_path, $max_image_size) {
  
  global $languages;
  
  $max_image_size_bytes = 1048576*$max_image_size;
  $valid_formats = array("jpg", "jpeg", "png", "gif", "svg");
  $image_name = "";
  $error = array();
  $extension = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
  $image_exstension = mb_convert_case($extension, MB_CASE_LOWER, "UTF-8");
  if(!in_array($image_exstension, $valid_formats)) {
    $error['extension'] = $languages['image_extension_error']."$image_exstension<br>";
  }
  //echo"<pre>";print_r($image_exstension_array);echo $image_exstension;exit;
  if(($_FILES[$input_name]['size'] < $max_image_size_bytes) && ($_FILES[$input_name]['error'] == 0)) {
    // no error
    $origin_image_tmp_name  = $_FILES[$input_name]['tmp_name'];
    $origin_image_name = $_FILES[$input_name]['name'];
    $image_name = str_replace(".$extension", "", $origin_image_name);
    $image_name_fixed = "$image_name.$image_exstension";
  }
  elseif(($_FILES[$input_name]['size'] > $max_image_size_bytes) || ($_FILES[$input_name]['error'] == 1 || $_FILES[$input_name]['error'] == 2)) {
    $error['size'] = $languages['image_size_error'].$max_image_size."MB<br>";
  }
  else {
    if($_FILES[$input_name]['error'] != 4) { // error 4 means no file was uploaded
      $error['upload'] = $languages['image_uploading_error']."<br>";
    }
  }
  
  if(!is_dir($upload_path)) {
    mkdir($upload_path, 0777);
    chmod($upload_path, 0777);
  }
 
  $image_params['error'] = $error;
  if(empty($error)) {
    $image_params['image_tmp_name'] = $origin_image_tmp_name;
    $image_params['image_name'] = $image_name;
    $image_params['image_exstension'] = $image_exstension;
    $image_params['image_name_full'] = $image_name_fixed;
  }
  
  return $image_params;
}

function rrmdir($dir) { 
  if (is_dir($dir)) { 
    $objects = scandir($dir); 
    foreach ($objects as $object) { 
      if ($object != "." && $object != "..") { 
        if (is_dir($dir."/".$object))
          rrmdir($dir."/".$object);
        else
          unlink($dir."/".$object); 
      } 
    }
    rmdir($dir); 
  } 
}

function get_combinations($arrays) {
  $result = array(array());
  foreach ($arrays as $property => $property_values) {
    $tmp = array();
    foreach ($result as $result_item) {
      foreach ($property_values as $property_key => $property_value) {
        $tmp[] = $result_item + array($property_key => $property_value);
      }
    }
    $result = $tmp;
  }
  return $result;
}
  
function get_admin_user_rights($menu_url) {
  
  global $db_link;
  
  $user_id = $_SESSION['admin']['user_id'];
  $user_rights = array();

  $user_rights['users_rights_add'] = 0;
  $user_rights['users_rights_edit'] = 0;
  $user_rights['users_rights_delete'] = 0;
  
  $query_user_rights = "SELECT `users_rights`.*
                          FROM `menus`
                    INNER JOIN `users_rights` ON `users_rights`.`menu_id` = `menus`.`menu_id`
                         WHERE `menus`.`menu_url` = '$menu_url' AND `users_rights`.`user_id` = '$user_id'";
  //if($user_id == 1) echo $query_user_rights."<br>";
  $result_user_rights = mysqli_query($db_link, $query_user_rights);
  if (!$result_user_rights) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_user_rights) > 0) {
    $user_rights = mysqli_fetch_assoc($result_user_rights);
    
    mysqli_free_result($result_user_rights);
  }
  
  return $user_rights;
}

function get_contents_hierarchy_ids($content_id) {
  
  global $db_link;
  
  $content_hierarchy_ids = "";
  $query_content_hierarchy_ids = "SELECT `content_hierarchy_ids` FROM `contents` WHERE `content_id` = '$content_id'";
  //echo $query_content_hierarchy_ids;
  $result_content_hierarchy_ids = mysqli_query($db_link, $query_content_hierarchy_ids);
  if(!$result_content_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_hierarchy_ids) > 0) {
    $row_content_hierarchy_ids = mysqli_fetch_assoc($result_content_hierarchy_ids);
    $content_hierarchy_ids = $row_content_hierarchy_ids['content_hierarchy_ids'];
    mysqli_free_result($result_content_hierarchy_ids);
  }
  return $content_hierarchy_ids;
}

function check_if_content_pretty_url_is_unique($content_pretty_url,$content_id = NULL) {
  
  global $db_link;
  
  $query_pretty_url = "SELECT `content_id` FROM `contents_descriptions` WHERE `content_pretty_url` = '$content_pretty_url' AND `content_id` <> '$content_id'";
  //echo $query_pretty_url;
  $result_pretty_url = mysqli_query($db_link, $query_pretty_url);
  if(!$result_pretty_url) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pretty_url) > 0) {
    mysqli_free_result($result_pretty_url);
    return false;
  }

  return true;
}

function check_if_content_has_children($content_id) {
  
  global $db_link;
  
  $query_content_has_children = "SELECT `contents`.`content_has_children` FROM `contents` WHERE `contents`.`content_id` = '$content_id'";
  //echo $query_content_has_children;
  $result_content_has_children = mysqli_query($db_link, $query_content_has_children);
  if(!$result_content_has_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_has_children) > 0) {
    $row_content_has_children = mysqli_fetch_assoc($result_content_has_children);
    $content_has_children = $row_content_has_children['content_has_children'];
    mysqli_free_result($result_content_has_children);
  }
  if($content_has_children == 1) return true;
  else return false;
}

function update_contents_children_hierarchy_ids_and_level($content_parent_id, $content_parent_hierarchy_ids_list, $content_parent_hierarchy_level) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  
  $query_select_children_id = "SELECT `content_id` FROM `contents` WHERE `content_parent_id` = '$content_parent_id'";
  $result_select_children_id = mysqli_query($db_link, $query_select_children_id);
  if(!$result_select_children_id) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_select_children_id) > 0) {
    $content_hierarchy_level = $content_parent_hierarchy_level+1;
    while($row_children_ids = mysqli_fetch_assoc($result_select_children_id)) {
      $content_id = $row_children_ids['content_id'];
      $content_hierarchy_ids_list = "$content_parent_hierarchy_ids_list.$content_id";
      
      $query_update_content = "UPDATE `contents` SET `content_hierarchy_ids`='$content_hierarchy_ids_list',
                                                     `content_hierarchy_level`='$content_hierarchy_level'
                                               WHERE `content_id` = '$content_id'";
      //echo $query_update_content."<br>";
      $result_update_content = mysqli_query($db_link, $query_update_content);
      if(!$result_update_content) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
       
      $content_has_children = check_if_content_has_children($content_id); // this function returns true or false
      if($content_has_children) {
        // if true we need to update the children's `content_hierarchy_ids` and `content_hierarchy_level`
        update_contents_children_hierarchy_ids_and_level($content_id, $content_hierarchy_ids_list, $content_hierarchy_level);
      }
    }
  }
}

function get_content_lаst_child_order_value($content_id) {
  
  global $db_link;
  
  $query_content_menu_order = "SELECT `content_menu_order` FROM `contents` WHERE `content_parent_id` = '$content_id'
                             ORDER BY `content_menu_order` DESC LIMIT 1";
  //echo $query_content_menu_order;
  $result_content_menu_order = mysqli_query($db_link, $query_content_menu_order);
  if(!$result_content_menu_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_menu_order) > 0) {
    $row_content_menu_order = mysqli_fetch_assoc($result_content_menu_order);
    $content_menu_order = $row_content_menu_order['content_menu_order'];
    mysqli_free_result($result_content_menu_order);
  }
  else {
    $content_menu_order = 0;
  }
  
  return $content_menu_order;
}

function check_if_menu_has_active_children($menu_id,$user_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `menu_id` FROM `menus` WHERE `menu_parent_id` = '$menu_id' AND `menu_is_active` = '1'";
  //if($menu_id == 20) echo $query_active_children."<br>";
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {
    while($menu_id_row = mysqli_fetch_assoc($result_active_children)) {
      $menu_id = $menu_id_row['menu_id'];

      $query_menus = "SELECT `users_rights_id` FROM `users_rights` WHERE `menu_id` = '$menu_id' AND `user_id` = '$user_id'";
      //if($menu_id == 22) echo $query_menus."<br>";
      $result_menus = mysqli_query($db_link, $query_menus);
      if(!$result_menus) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_menus) > 0) {

        return true;
      }
      mysqli_free_result($result_menus);
    }
    mysqli_free_result($result_active_children);
  }
  return false;
}

function check_if_this_is_menu_last_child($menu_parent_id,$menu_sort_order,$user_id) {
  
  global $db_link;
 
  $query_last_child = "SELECT `menus`.`menu_id` 
                         FROM `menus` 
                   INNER JOIN `users_rights` ON `users_rights`.`menu_id` = `menus`.`menu_id`
                        WHERE `menus`.`menu_parent_id` = '$menu_parent_id' AND `users_rights`.`user_id` = '$user_id' AND `menus`.`menu_sort_order` > '$menu_sort_order'
                          AND `menus`.`menu_is_active` = '1'";
  //echo "$query_last_child<br>";
  $result_last_child = mysqli_query($db_link, $query_last_child);
  if(!$result_last_child) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_last_child) > 0) {
    mysqli_free_result($result_last_child);
    
    return false;
  }
  else return true;
}

function check_if_this_is_news_category_last_child($news_cat_parent_id,$news_cat_sort_order) {
  
  global $db_link;
  
  $query_last_child = "SELECT `news_category_id` FROM `news_categories` 
                        WHERE `news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_sort_order` > '$news_cat_sort_order'
                        LIMIT 1";
  //if($category_parent_id == 72) echo "$query_last_child<br>";
  $result_last_child = mysqli_query($db_link, $query_last_child);
  if(!$result_last_child) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_last_child) > 0) {
    mysqli_free_result($result_last_child);

    return false;
  }
  else return true;
}

function get_news_highest_order_value_for_category($category_id) {
  
  global $db_link;
  
  $query_highest_news_sort_order = "SELECT MAX(`news_sort_order`) as `nso` FROM `news_to_news_category` WHERE `news_category_id` = '$category_id'";
  //echo $query_highest_news_sort_order;
  $result_highest_news_sort_order = mysqli_query($db_link, $query_highest_news_sort_order);
  if(!$result_highest_news_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_highest_news_sort_order) > 0) {
    $row_news_sort_order = mysqli_fetch_assoc($result_highest_news_sort_order);
    $news_sort_order = $row_news_sort_order['nso'];
    mysqli_free_result($result_highest_news_sort_order);
  }
  else {
    $news_sort_order = 0;
  }
  
  return $news_sort_order;
}

function get_news_category_hierarchy_ids($news_category_id) {
  
  global $db_link;
  
  $query_news_cat_hierarchy_ids = "SELECT `news_cat_hierarchy_ids` FROM `news_categories` WHERE `news_category_id` = '$news_category_id'";
  //echo $query_news_cat_hierarchy_ids."<br>";
  $result_news_cat_hierarchy_ids = mysqli_query($db_link, $query_news_cat_hierarchy_ids);
  if(!$result_news_cat_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_cat_hierarchy_ids) > 0) {
    $row_news_cat_hierarchy_ids = mysqli_fetch_assoc($result_news_cat_hierarchy_ids);
    $news_cat_hierarchy_ids = $row_news_cat_hierarchy_ids['news_cat_hierarchy_ids'];
    mysqli_free_result($result_news_cat_hierarchy_ids);
  }
  return $news_cat_hierarchy_ids;
}

function get_lаst_news_category_order_value($news_cat_parent_id) {
  
  global $db_link;
  
  $query_news_category_order = "SELECT `news_cat_sort_order` FROM `news_categories` WHERE `news_cat_parent_id` = '$news_cat_parent_id' ORDER BY `news_cat_sort_order` DESC LIMIT 1";
  //echo $query_news_category_order;
  $result_news_category_order = mysqli_query($db_link, $query_news_category_order);
  if(!$result_news_category_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_category_order) > 0) {
    $row_news_category_order = mysqli_fetch_assoc($result_news_category_order);
    $news_category_order = $row_news_category_order['news_cat_sort_order'];
    mysqli_free_result($result_news_category_order);
  }
  else {
    $news_category_order = 0;
  }
  
  return $news_category_order;
}

function get_news_last_image_order_value($news_id) {
  
  global $db_link;
  
  $query_image_sort_order = "SELECT `ng_sort_order` FROM `news_galleries` WHERE `news_id` = '$news_id' ORDER BY `ng_sort_order` DESC LIMIT 1";
  //echo $query_image_sort_order;exit;
  $result_image_sort_order = mysqli_query($db_link, $query_image_sort_order);
  if(!$result_image_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_image_sort_order) > 0) {
    $row_image_sort_order = mysqli_fetch_assoc($result_image_sort_order);
    $image_sort_order = $row_image_sort_order['ng_sort_order'];
    mysqli_free_result($result_image_sort_order);
  }
  else {
    $image_sort_order = 0;
  }
  
  return $image_sort_order;
}

function get_news_images($news_id) {
  
  global $db_link;
  
  $news_names_array = array();
  
  $query_news_name = "SELECT `news_gallery_id`, `ng_name` FROM `news_galleries` WHERE `news_id` = '$news_id' ORDER BY `ng_sort_order` ASC";
  //echo $query_news_name;
  $result_news_name = mysqli_query($db_link, $query_news_name);
  if(!$result_news_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_name) > 0) {
    while($news_names_row = mysqli_fetch_assoc($result_news_name)) {
      $news_names_array[] = $news_names_row;
    }
    mysqli_free_result($result_news_name);
  }
  
  return $news_names_array;
}

function get_languages() {
  
  global $db_link;
  
  $languages_array = array();
  $query_languages = "SELECT `language_id`,`language_code`,`language_menu_name`,`language_name` FROM `languages` WHERE `language_is_active` = '1' ORDER BY `language_menu_order` ASC";
  $result_languages = mysqli_query($db_link, $query_languages);
  if (!$result_languages) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_languages) > 0) {
    while($row_languages = mysqli_fetch_assoc($result_languages)) {
      $language_id = $row_languages['language_id'];
      $languages_array[$language_id] = $row_languages; 
    }
    mysqli_free_result($result_languages);
  }
  
  return $languages_array;
}

function get_lаst_language_menu_order_value() {
  
  global $db_link;
  
  $query_language_menu_order = "SELECT `language_menu_order` FROM `languages` ORDER BY `language_menu_order` DESC LIMIT 1";
  //echo $query_language_menu_order;
  $result_language_menu_order = mysqli_query($db_link, $query_language_menu_order);
  if(!$result_language_menu_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language_menu_order) > 0) {
    $row_language_menu_order = mysqli_fetch_assoc($result_language_menu_order);
    $language_menu_order = $row_language_menu_order['language_menu_order'];
    mysqli_free_result($result_language_menu_order);
  }
  else {
    $language_menu_order = 0;
  }
  
  return $language_menu_order;
}

function check_if_cd_pretty_url_is_unique($cd_pretty_url,$category_id = NULL) {
  
  global $db_link;
  
  $query_pretty_url = "SELECT `category_id` FROM `categories_descriptions` WHERE `cd_pretty_url` = '$cd_pretty_url' AND `category_id` <> '$category_id'";
  //echo $query_pretty_url;
  $result_pretty_url = mysqli_query($db_link, $query_pretty_url);
  if(!$result_pretty_url) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pretty_url) > 0) {
    mysqli_free_result($result_pretty_url);
    return false;
  }
  else {
    return true;
  }
}

function get_categories_hierarchy_ids($category_id) {
  
  global $db_link;
  
  $query_category_hierarchy_ids = "SELECT `category_hierarchy_ids` FROM `category_to_category` WHERE `category_id` = '$category_id'";
  //echo $query_category_hierarchy_ids."<br>";
  $result_category_hierarchy_ids = mysqli_query($db_link, $query_category_hierarchy_ids);
  if(!$result_category_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_hierarchy_ids) > 0) {
    $row_category_hierarchy_ids = mysqli_fetch_assoc($result_category_hierarchy_ids);
    $category_hierarchy_ids = $row_category_hierarchy_ids['category_hierarchy_ids'];
    mysqli_free_result($result_category_hierarchy_ids);
  }
  return $category_hierarchy_ids;
}

function get_category_sort_order_value($category_root_id,$category_parent_id) {
  
  global $db_link;
  
  $category_sort_order = 0;
  $query_category_sort_order = "SELECT MAX(`category_sort_order`) as `cso` FROM `category_to_category` WHERE `category_root_id` = '$category_root_id' AND `category_parent_id` = '$category_parent_id'";
  //echo $query_category_sort_order."<br>";
  $result_category_sort_order = mysqli_query($db_link, $query_category_sort_order);
  if(!$result_category_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_sort_order) > 0) {
    $row_category_sort_order = mysqli_fetch_assoc($result_category_sort_order);
    $category_sort_order = $row_category_sort_order['cso'];
    mysqli_free_result($result_category_sort_order);
  }
  
  return $category_sort_order+1;
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

function get_category_name($category_id,$current_language_id) {
  
  global $db_link;
  
  $cd_name = "";
  $query_category_name = "SELECT `categories_descriptions`.`cd_name` 
                            FROM `categories`
                      INNER JOIN `categories_descriptions` USING(`category_id`)
                           WHERE `categories`.`category_id` = '$category_id' AND `categories_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_category_name;exit;
  $result_category_name = mysqli_query($db_link, $query_category_name);
  if(!$result_category_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_name)) {
    $category_row = mysqli_fetch_assoc($result_category_name);
    $cd_name = $category_row['cd_name'];
  }
  return $cd_name;
}

function get_ov_lаst_child_order_value($option_id) {
  
  global $db_link;
  
  $query_ov_sort_order = "SELECT `ov_sort_order` FROM `option_value` WHERE `option_id` = '$option_id' ORDER BY `ov_sort_order` DESC LIMIT 1";
  //echo $query_ov_sort_order;
  $result_ov_sort_order = mysqli_query($db_link, $query_ov_sort_order);
  if(!$result_ov_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_ov_sort_order) > 0) {
    $row_ov_sort_order = mysqli_fetch_assoc($result_ov_sort_order);
    $ov_sort_order = $row_ov_sort_order['ov_sort_order'];
    mysqli_free_result($result_ov_sort_order);
  }
  else {
    $ov_sort_order = 0;
  }
  
  return $ov_sort_order;
}

function get_option_lаst_child_sort_order() {
  
  global $db_link;
  
  $query_option_sort_order = "SELECT `option_sort_order` FROM `options` ORDER BY `option_sort_order` DESC LIMIT 1";
  //echo $query_option_sort_order;
  $result_option_sort_order = mysqli_query($db_link, $query_option_sort_order);
  if(!$result_option_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_option_sort_order) > 0) {
    $row_option_sort_order = mysqli_fetch_assoc($result_option_sort_order);
    $option_sort_order = $row_option_sort_order['option_sort_order'];
    mysqli_free_result($result_option_sort_order);
  }
  else {
    $option_sort_order = 0;
  }
  
  return $option_sort_order;
}

function resize_crop_image($img_file,$extension) {
  
  $dimensions = @getimagesize($img_file);
  //print_r($dimensions);exit;
  $original_width = $dimensions[0]; //echo $img_file;exit;
  $original_height = $dimensions[1];
  if($original_width > $original_height) {
    $landscape = true;
  }
  if($landscape) {
    $width = 375;
    $height = 250;
  }
  else {
    $width = 169;
    $height = 225;
  }
  $picture_name = 'restaurant_gallery_picture';
  
  $ratio = (($original_width / $original_height) < ($width / $height)) ? $width / $original_width : $height / $original_height;
  $x = max(0, round($original_width / 2 - ($width / 2) / $ratio));
  $y = max(0, round($original_height / 2 - ($height / 2) / $ratio));
  if($extension == 'jpg' || $extension == 'jpeg') {
   $src = imagecreatefromjpeg($img_file);
  }
  elseif($extension == 'gif') {
    $src = imagecreatefromgif($img_file);
  }
  elseif($extension == 'png') {
    $src = imagecreatefrompng($img_file);
  }
  else {
    return $errors['new_picture'] = "Unknown picture format!";
  }
  if($src == false)
  {
     $error = "Unknown problem trying to open uploaded image.";
     return false;
  }
  $resized = imagecreatetruecolor($width, $height);
  $result = imagecopyresampled($resized, $src, 0, 0, $x, $y, $width, $height,
            round($width / $ratio, 0), round($height / $ratio));
  if($result == false)
  {
     $error = "Error trying to resize and crop image.";
     return false;
  }
  else
  {
    if($extension == 'jpg' || $extension == 'jpeg') {
      imagejpeg($resized, $picture_name.'.jpg', 80);
     }
     elseif($extension == 'gif') {
       imagegif($resized, $picture_name.'.gif', 80);
     }
     elseif($extension == 'png') {
       imagepng($resized, $picture_name.'.png', 2);
     }
     else {
       return $errors['new_picture'] = "Unknown picture format!";
     }
    imagedestroy($src);
    imagedestroy($resized);
  }
}

class SimpleImage { 
  var $image; 
  var $image_type;   
  
  function load($filename) {   
    $image_info = getimagesize($filename); 
    $this->image_type = $image_info[2]; 
    if( $this->image_type == IMAGETYPE_JPEG ) {   
      $this->image = imagecreatefromjpeg($filename);
    } elseif($this->image_type == IMAGETYPE_GIF ) {   
      $this->image = imagecreatefromgif($filename); 
    } elseif( $this->image_type == IMAGETYPE_PNG ) {  
      $this->image = imagecreatefrompng($filename); 
    } 
  } 
  
  function save($filename, $image_type=IMAGETYPE_JPEG, $compression=68, $permissions=null) {   
    if( $image_type == IMAGETYPE_JPEG ) { 
      imagejpeg($this->image,$filename,$compression);
    } elseif( $image_type == IMAGETYPE_GIF ) {   
      imagegif($this->image,$filename); 
    } elseif( $image_type == IMAGETYPE_PNG ) {  
      imagepng($this->image,$filename); 
    }
    
    if( $permissions != null) {   
      chmod($filename,$permissions);
    } 
    
  } 
  
  function output($image_type=IMAGETYPE_JPEG) {   
    if( $image_type == IMAGETYPE_JPEG ) { 
      imagejpeg($this->image); 
    } elseif( $image_type == IMAGETYPE_GIF ) {  
      imagegif($this->image); 
    } elseif( $image_type == IMAGETYPE_PNG ) {  
      imagepng($this->image); 
    } 

  } 
  
  function getWidth() {   
    return imagesx($this->image); 
  } 
  
  function getHeight() {   
    return imagesy($this->image); 
  } 
  
  function resizeToHeight($height) {   
    $ratio = $height / $this->getHeight(); 
    $width = $this->getWidth() * $ratio; 
    $this->resize($width,$height); 
  }
  
  function resizeToWidth($width) { 
    $ratio = $width / $this->getWidth(); 
    $height = $this->getheight() * $ratio; 
    $this->resize($width,$height); 
  }
  
  function scale($scale) { 
    $width = $this->getWidth() * $scale/100; 
    $height = $this->getheight() * $scale/100; 
    $this->resize($width,$height); 
  }
  
  function resize($width,$height) { 
    $new_image = imagecreatetruecolor($width, $height); 
    if( $this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG ) {
      $current_transparent = imagecolortransparent($this->image); 
      
      if($current_transparent != -1) {
        $transparent_color = imagecolorsforindex($this->image, $current_transparent); 
        $current_transparent = imagecolorallocate($new_image, $transparent_color['red'],$transparent_color['green'], $transparent_color['blue']); 
        imagefill($new_image, 0, 0, $current_transparent); 
        imagecolortransparent($new_image, $current_transparent); 
      } elseif( $this->image_type == IMAGETYPE_PNG) { 
        imagealphablending($new_image, false); 
        $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127); 
        imagefill($new_image, 0, 0, $color); imagesavealpha($new_image, true);
      } 
    }
    
    imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight()); 
    $this->image = $new_image;
  }
}

function save_image($image_tmp_name,$image_name,$image_ext,$site_thumb_width,$site_thumb_height,$admin_thumb_width,$admin_thumb_height,$upload_path) {
  
  global $db_link;
  global $languages;
  global $current_lang;

  if(is_uploaded_file($image_tmp_name)) {
    move_uploaded_file($image_tmp_name, "$upload_path$image_name.$image_ext");
  }
  else {
    echo $languages['image_uploading_error'];
    exit;
  }

  $file = "$upload_path$image_name.$image_ext";

  list($width,$height) = getimagesize($file);

  $image = new SimpleImage();
  $image->load($file);

  switch($image_ext) {
    case "gif" : $image_type = 1;
      break;
    case "jpg" : $image_type = 2;
      break;
    case "jpeg" : $image_type = 2;
      break;
    case "png" : $image_type = 3;
      break;
  }

  $image_admin_name = $image_name."_admin.".$image_ext;
  $image_admin = $upload_path.$image_admin_name;
  
  $image_site_name = $image_name."_site.".$image_ext;
  $image_site = $upload_path.$image_site_name;
  
  if($width > $height) {
    $image->resizeToWidth($admin_thumb_width);

    $image->save($image_admin,$image_type);
    
    $image->resizeToWidth($site_thumb_width);

    $image->save($image_site,$image_type);

  }
  else {
    $image->resizeToHeight($admin_thumb_height);

    $image->save($image_admin,$image_type);
    
    $image->resizeToHeight($site_thumb_height);

    $image->save($image_site,$image_type);
  }
}

function get_last_sort_order($table_name, $column_name, $parent_column_name = false, $parent_id = false) {
  
  global $db_link;
  
  $where = ($parent_column_name && $parent_id) ? "WHERE `$parent_column_name` = '$parent_id'" : "";
  
  $query_sort_order_order = "SELECT `$column_name` FROM `$table_name` $where ORDER BY `$column_name` DESC LIMIT 1";
  //echo $query_sort_order_order;
  $result_sort_order_order = mysqli_query($db_link, $query_sort_order_order);
  if(!$result_sort_order_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_sort_order_order) > 0) {
    $row_sort_order_order = mysqli_fetch_assoc($result_sort_order_order);
    $sort_order_order = $row_sort_order_order[$column_name];
    mysqli_free_result($result_sort_order_order);
  }
  else {
    $sort_order_order = 0;
  }
  
  return $sort_order_order;
}

function get_column_names($db_name,$table_name) {
  
  global $db_link;
  $column_names_array = array();
  
  $query_column_names = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = '$table_name'";
  //echo $query_column_names;exit;
  $result_column_names = mysqli_query($db_link, $query_column_names);
  if(!$result_column_names) echo mysqli_error($db_link);
  $column_names_count = mysqli_num_rows($result_column_names);
  if($column_names_count > 0) {
    while($column_names_row = mysqli_fetch_assoc($result_column_names)) {
      $column_names_array[] = $column_names_row['COLUMN_NAME'];
    }
    mysqli_free_result($result_column_names);
  }
  
  return $column_names_array;
}

function get_slider_last_order_value() {
  
  global $db_link;
  
  $query_slider_sort_order = "SELECT `slider_sort_order` FROM `sliders` ORDER BY `slider_sort_order` DESC LIMIT 1";
  //echo $query_slider_sort_order;exit;
  $result_slider_sort_order = mysqli_query($db_link, $query_slider_sort_order);
  if(!$result_slider_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_sort_order) > 0) {
    $row_slider_sort_order = mysqli_fetch_assoc($result_slider_sort_order);
    $slider_sort_order = $row_slider_sort_order['slider_sort_order'];
    mysqli_free_result($result_slider_sort_order);
  }
  else {
    $slider_sort_order = 0;
  }
  
  return $slider_sort_order;
}

function get_content_include_blocks() {
  
  global $db_link;
  global $current_language_id;
  
  $menu_array = array();
  $query_menus = "SELECT `menus`.`menu_id`,`menus`.`menu_css_id`,`menus`.`menu_include_block_fn`,`menus_translations`.`menu_translation_text`
                    FROM `menus` 
              INNER JOIN `menus_translations` ON `menus_translations`.`menu_id` = `menus`.`menu_id`
                   WHERE `menus`.`menu_hierarchy_level` = '1' AND `menus`.`menu_use_as_include_block` = '1'
                     AND `menus_translations`.`language_id` = '$current_language_id' 
                ORDER BY `menus`.`menu_sort_order` ASC";
  //if($parent_id == 4) echo $query_menus;
  $result_menus = mysqli_query($db_link,$query_menus);
  if(!$result_menus) echo mysqli_error($db_link);
  $menu_count = mysqli_num_rows($result_menus);
  if($menu_count > 0) {
    while($menu_row = mysqli_fetch_assoc($result_menus)) {
      $menu_array[] = $menu_row;
    }
    mysqli_free_result($result_menus);
  }
  
  return $menu_array;
}
?>