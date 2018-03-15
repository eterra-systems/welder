<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  //echo"<pre>";print_r($_POST);echo"</pre>";exit;
  
  check_for_csrf();
    
  if(isset($_SESSION['admin']['user_id']) && !empty($_SESSION['admin']['user_id'])) {
    
    $user_id = $_SESSION['admin']['user_id'];
    if(isset($_POST['content_id'])) {
      $current_content_id = $_POST['content_id'];
    }

    $content_is_section_header = (isset($_POST['content_is_section_header'])) ? 1 : 0;
    $content_show_in_menu = (isset($_POST['content_show_in_menu'])) ? 1 : 0;
    $content_show_in_footer = (isset($_POST['content_show_in_footer'])) ? 1 : 0;
    $content_is_active = (isset($_POST['content_is_active'])) ? 1 : 0;
//    $content_attribute_1 = (isset($_POST['content_attribute_1'])) ? 1 : 0;
    $content_target = prepare_for_null_row($_POST['content_target']);

    $query_update_content = "UPDATE `contents` SET  `content_is_section_header` = '$content_is_section_header',
                                                    `content_show_in_menu`='$content_show_in_menu',
                                                    `content_show_in_footer`='$content_show_in_footer',
                                                    `content_is_active`='$content_is_active',
                                                    `content_target`=$content_target,
                                                    `content_last_modified_by`='$user_id',
                                                    `content_modified_date`=NOW() 
                                              WHERE `content_id` = '$current_content_id'";
    //echo "$query_update_content<br>";exit;
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages['sql_error_update']." - 1 UPDATE `contents` ".mysqli_error($db_link);
      exit;
    }
   
  }
?>