<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  $_SESSION['admin_dir_name'] = "admin-w";
  $GLOBALS['is_mobile'] = false;
  $useragent=$_SERVER['HTTP_USER_AGENT'];
  if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
    $GLOBALS['is_mobile'] = true;
  }
  
  include_once 'site/config.php';
  include_once 'site/functions/include-functions.php';
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //echo $_GET['page'];
    
    /**
     * the page uri has to be /current_lang/page_pretty_url/page_pretty_url/page_pretty_url
     * so we gonna separate it and take the first parameter wich is the language
     * and the last wich is the current page pretty url
     */
    $page_path_string = mysqli_real_escape_string($db_link,strip_tags($_GET['page']));
    $page_path_array = explode("/", $page_path_string);
    //print_array_for_debug($page_path_array);
    $current_lang = array_shift($page_path_array);
    if(strlen($current_lang) != 2) $current_lang = get_default_lang_code();
    $current_page_pretty_url = array_shift($page_path_array);
    if(count($page_path_array) > 0) {
      $page_params = array_shift($page_path_array);
    }
    
    $query_where_page = "`contents_descriptions`.`content_pretty_url` = '$current_page_pretty_url'";
  }
  else {
    
    /*
     * in case the $_GET['page'] variable is empty that means this is the index page
     * so we gonna get the default language and the default page(home page)
     */
    
    $current_lang = get_default_lang_code();
    $query_where_page = "`contents`.`content_is_home_page` = '1'";
  }
  
  $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_is_default_frontend`,`contents_descriptions`.`content_pretty_url` 
                             FROM `languages` 
                       INNER JOIN `contents_descriptions` ON (`contents_descriptions`.`language_id` = `languages`.`language_id`)
                       INNER JOIN `contents` ON (`contents`.`content_id` = `contents_descriptions`.content_id)
                            WHERE `language_code` = '$current_lang' AND `contents`.`content_is_home_page` = '1'";
  //echo $query_content;exit;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if(!$result_current_params) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $language_is_default_frontend = $row_current_params['language_is_default_frontend'];
    $home_page_url = ($language_is_default_frontend == 1) ? "/" : "/$current_lang/".$row_current_params['content_pretty_url'];
    
    mysqli_free_result($result_current_params);
  }
  
  include_once "site/languages/languages_$current_lang.php";

  if(isset($_GET['cid']) && is_int(intval($_GET['cid']))) {
    //category_id
    
    $current_category_pretty_url = mysqli_real_escape_string($db_link,array_pop($page_path_array));
    
    require_once 'site/categories.php';
  }
  elseif(isset($_GET['ncid'])) {
    //news_category_id
    
    require_once 'site/news-by-category.php';
  }
  elseif(isset($_GET['nid']) && is_int(intval($_GET['nid']))) {
    //news_id
    
    require_once 'site/news-details.php';
  }
  else {
    require_once 'site/index.php';
  }