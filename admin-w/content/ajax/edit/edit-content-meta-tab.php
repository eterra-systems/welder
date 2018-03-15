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
    
    mysqli_query($db_link,"BEGIN");
    $all_queries = "";
  
    foreach($_POST['has_record_for_language'] as $language_id => $has_record_for_language) {
      
      $content_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_title'][$language_id]));
      $content_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_keywords'][$language_id]));
      $content_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_description'][$language_id]));
      
      if($has_record_for_language == 1) {
        
        $query_update_descriptions = "UPDATE `contents_descriptions` SET `content_meta_title`=$content_meta_title,
                                                                          `content_meta_keywords`=$content_meta_keywords,
                                                                          `content_meta_description`=$content_meta_description
                                                                    WHERE `content_id` = '$current_content_id' AND `language_id` = '$language_id'";
        $all_queries .= $query_update_descriptions."<br>";
        //echo "$query_update_descriptions<br>";exit;
        $result_update_descriptions = mysqli_query($db_link, $query_update_descriptions);
        if(!$result_update_descriptions) {
          echo $languages['sql_error_update']." - update `contents_descriptions` ".mysqli_error($db_link);
          exit;
        }
        
      }
      else {
        echo "<div class='alert alert-danger'>Няма запис за някой от езиците! Моля първо попълнете \"Основни параметри\" и опреснете страницата като натиснете F5.</div>";
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
    }

  }
?>