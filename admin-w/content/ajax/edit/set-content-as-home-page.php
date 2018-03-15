<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['content_id'])) {
    $content_id =  $_POST['content_id'];
  }
  
  if(!empty($content_id)) {
 
    mysqli_query($db_link,"BEGIN");
    
    $all_queries= "";
    
    $query_home_page = "SELECT `content_id` FROM `contents` WHERE `content_is_home_page`='1'";
    $all_queries .= $query_home_page."<br>";
    $result_home_page = mysqli_query($db_link, $query_home_page);
    if(!$result_home_page) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_home_page) > 0) {

      $query = "UPDATE `contents` SET `content_is_home_page` = '0' WHERE `content_is_home_page`='1'";
      $all_queries .= $query."<br>";
      $result = mysqli_query($db_link, $query);
      if(!$result) {
        echo $languages['sql_error_update']." - 1 `contents`".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      mysqli_free_result($result_home_page);
    }
    
    $query_update_content = "UPDATE `contents` SET `content_is_home_page`='1' WHERE `content_id` = '$content_id'";
    $all_queries .= $query_update_content;
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages['sql_error_update']." - 2 `contents`".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
    
    list_contents($parent_id = 0, $path_number = 0);

  }
?>