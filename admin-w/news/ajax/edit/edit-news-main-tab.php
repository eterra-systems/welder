<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  //check_ajax_request();
  
  //echo "<pre>";print_r($_POST);
  
  mysqli_query($db_link,"BEGIN");

  $all_queries = "";

  $current_news_id = $_POST['news_id'];
  
  foreach($_POST['news_title'] as $language_id => $news_title) {
    if($language_id != 0) {
      if(empty($news_title)) $news_errors['news_title'][$language_id] = $languages['required_field_error'];
      if(empty($_POST['news_summary'][$language_id])) $news_errors['news_summary'][$language_id] = $languages['required_field_error'];
      if(empty($_POST['news_text'][$language_id])) $news_errors['news_text'][$language_id] = $languages['required_field_error'];

      $news_titles_array[$language_id] = $_POST['news_title'][$language_id];
      $news_summaries_array[$language_id] = $_POST['news_summary'][$language_id];
      $news_texts_array[$language_id] = $_POST['news_text'][$language_id];
    }
  }
  
  $news_is_active = $_POST['news_is_active'];
  $news_post_date_year = $_POST['news_post_date_year'];
  $news_post_date_month = $_POST['news_post_date_month'];
  $news_post_date_day = $_POST['news_post_date_day'];
  $news_post_date_hour = $_POST['news_post_date_hour'];
  $news_post_date_minute = $_POST['news_post_date_minute'];
  $news_post_date_second = $_POST['news_post_date_second'];
  $news_post_date = "$news_post_date_year-$news_post_date_month-$news_post_date_day $news_post_date_hour:$news_post_date_minute:$news_post_date_second";
  $use_expiry_info = 0;
  if(isset($_POST['use_expiry_info'])) $use_expiry_info = 1;
  if($use_expiry_info == 1) {
    $news_expiry_start_date_year = $_POST['news_expiry_start_date_year'];
    $news_expiry_start_date_month = $_POST['news_expiry_start_date_month'];
    $news_expiry_start_date_day = $_POST['news_expiry_start_date_day'];
    $news_expiry_start_date_hour = $_POST['news_expiry_start_date_hour'];
    $news_expiry_start_date_minute = $_POST['news_expiry_start_date_minute'];
    $news_expiry_start_date_second = $_POST['news_expiry_start_date_second'];
    $news_expiry_end_date_year = $_POST['news_expiry_end_date_year'];
    $news_expiry_end_date_month = $_POST['news_expiry_end_date_month'];
    $news_expiry_end_date_day = $_POST['news_expiry_end_date_day'];
    $news_expiry_end_date_hour = $_POST['news_expiry_end_date_hour'];
    $news_expiry_end_date_minute = $_POST['news_expiry_end_date_minute'];
    $news_expiry_end_date_second = $_POST['news_expiry_end_date_second'];
    $news_start_time = "'$news_expiry_start_date_year-$news_expiry_start_date_month-$news_expiry_start_date_day $news_expiry_start_date_hour:$news_expiry_start_date_minute:$news_expiry_start_date_second'";
    $news_end_time = "'$news_expiry_end_date_year-$news_expiry_end_date_month-$news_expiry_end_date_day $news_expiry_end_date_hour:$news_expiry_end_date_minute:$news_expiry_end_date_second'";
  }
  else {
    $news_expiry_start_date_month = false;
    $news_expiry_start_date_day = false;
    $news_expiry_start_date_year = false;
    $news_expiry_start_date_hour = false;
    $news_expiry_start_date_minute = false;
    $news_expiry_start_date_second = false;
    $news_expiry_end_date_month = false;
    $news_expiry_end_date_day = false;
    $news_expiry_end_date_year = false;
    $news_expiry_end_date_hour = false;
    $news_expiry_end_date_minute = false;
    $news_expiry_end_date_second = false;
    $news_start_time = "NULL";
    $news_end_time = "NULL";
  }

  $news_extra = $_POST['news_extra'];

  if(!isset($news_errors)) {
    //if there are no form errors we can insert the information

    $news_extra = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['news_extra']));

    $query_update_news = "UPDATE `news` SET `news_post_date`='$news_post_date',
                                            `news_start_time`=$news_start_time, 
                                            `news_end_time`=$news_end_time, 
                                            `news_is_active`='$news_is_active', 
                                            `news_modified_date`=NOW(), 
                                            `news_extra`=$news_extra
                                      WHERE `news_id` = '$current_news_id'";
    $all_queries .= "<br>\n".$query_update_news;
    $result_update_news = mysqli_query($db_link, $query_update_news);
    if(!$result_update_news) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    foreach($news_titles_array as $language_id => $news_title) {

      if($language_id != 0) {
        $news_title_db = mysqli_real_escape_string($db_link, $news_title);
        $news_summary_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $news_summaries_array[$language_id]));
        $news_text_db = mysqli_real_escape_string($db_link, $news_texts_array[$language_id]);

        if(isset($_POST['no_record'][$language_id])) {
          
          $news_pretty_url = "";
          $news_meta_title = "NULL";
          $news_meta_description = "NULL";
          $news_meta_keywords = "NULL";
      
          $query_insert_news_descriptions = "INSERT INTO `news_descriptions`(`news_id`, 
                                                                            `language_id`, 
                                                                            `news_title`, 
                                                                            `news_summary`, 
                                                                            `news_text`,
                                                                            `news_pretty_url`,
                                                                            `news_meta_title`,
                                                                            `news_meta_description`,
                                                                            `news_meta_keywords`)
                                                                    VALUES ('$current_news_id',
                                                                            '$language_id',
                                                                            '$news_title_db',
                                                                            $news_summary_db,
                                                                            '$news_text_db',
                                                                            '$news_pretty_url',
                                                                            $news_meta_title,
                                                                            $news_meta_description,
                                                                            $news_meta_keywords)";
          $all_queries .= "<br>\n".$query_insert_news_descriptions;
          $result_insert_news_descriptions = mysqli_query($db_link, $query_insert_news_descriptions);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - INSERT INTO `news_descriptions`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_update_news_desc = "UPDATE `news_descriptions` SET `news_title`='$news_title_db',
                                                                    `news_summary`=$news_summary_db,
                                                                    `news_text`='$news_text_db' 
                                                              WHERE `news_id` = '$current_news_id' AND `language_id` = '$language_id'";
          $all_queries .= "<br>\n".$query_update_news_desc;
          $result_update_news_desc = mysqli_query($db_link, $query_update_news_desc);
          if(!$result_update_news_desc) {
            echo $languages['sql_error_update']." - UPDATE `news_descriptions`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");

  }
  else {
    
  }
  