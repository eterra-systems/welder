<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

define("DOMAIN", "zavarka.bg");
define("PROTOCOL", "http://");
define("SITEFOLDER", "site");
define("SITEFOLDERSL", "/site");
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") define("PROTOCOL", "https://");

//setlocale(LC_ALL, 'bg_BG.UTF-8');
date_default_timezone_set('Europe/Sofia');

//start session
if(!strpos($_SERVER['PHP_SELF'], "ajax") || strlen(session_id()) < 1) {
    session_start();
}

if(empty($_SESSION['admin']['user_id']) || !isset($_SESSION['admin']['user_id'])) {
    //header('Location: /_admin');
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    // last request was more than 1 hour ago
    unset($_SESSION['admin']);
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

function DB_OpenI() {
  
  $db_name = "eterrasy_weler";
  $db_user = "eterrasy_weler";
  $db_password = "mZm8dTMvqLJu(!n8";

  $mysqli = new mysqli("localhost", $db_user, $db_password, $db_name);

  /* check connection */
  if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
  }

  /* change character set to utf8 */
  if (!$mysqli->set_charset("utf8")) {
      printf("Error loading character set utf8: %s\n", $mysqli->error);
  } else {
      //printf("Current character set: %s\n", $mysqli->character_set_name());
  }

  return $mysqli;
}

function DB_CloseI($db_link) {
  mysqli_close($db_link);
}

function check_if_user_is_logged() {
  
  if(!defined('DOMAIN')) exit('<h1>No sufficient rights!</h1>');
  
  if(isset($_SESSION['admin']['user_id']) && !empty($_SESSION['admin']['user_id'])) {
    // it's ok
  }
  else {
    // this seems to be an outside atack
    exit('<h1>No sufficient rights!</h1>');
  }
  
}

function check_ajax_request() {
  
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
    // this is an ajax request
  }
  else {
    exit ("<h1>No sufficient rights!</h1>");
  }
}

function check_for_csrf() {
  
  check_if_user_is_logged();
  check_ajax_request();
  
}

function check_for_csrf_in_reports() {
  
  check_if_user_is_logged();
  
}
  
if(!isset($_SESSION['admin_dir_name'])) {
  $url_exploded = explode("/", substr($_SERVER['PHP_SELF'], 1));
  $_SESSION['admin_dir_name'] = array_shift($url_exploded);
}
  
if(isset($_GET['logout']) && $_GET['logout'] == "yes") {
  unset($_SESSION['admin']['user_id']);
  unset($_SESSION['admin']['user_type_id']);
  unset($_SESSION['admin']['user_username']);
  unset($_SESSION['admin']['user_fullname']);
  //session_destroy();
  header("Location:/".$_SESSION['admin_dir_name']);
}
else {
  $db_link = DB_OpenI();

  $query_language = "SELECT `language_id`,`language_code` FROM `languages` WHERE `language_is_default_backend` = '1'";
  //echo $query_content;exit;
  $result_language = mysqli_query($db_link, $query_language);
  if(!$result_language) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language) > 0) {
    $row_language = mysqli_fetch_assoc($result_language);
    $current_language_id = $row_language['language_id'];
    $current_lang = $row_language['language_code'];
  }
  
  if(isset($_COOKIE['admin_lang'])) {
    $current_lang = $_COOKIE['admin_lang'];
  }
  if(!isset($current_lang)) {
    $current_lang = "bg";
    $current_language_id = 1;
  }
}

$GLOBALS['is_mobile'] = false;
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
  $GLOBALS['is_mobile'] = true;
}

require_once("languages/languages_$current_lang.php");
?>