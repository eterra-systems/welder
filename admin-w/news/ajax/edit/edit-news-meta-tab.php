<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //echo "<pre>";print_r($_POST);EXIT;
  
  if(isset($_POST['news_id'])) {
    $current_news_id =  $_POST['news_id'];
  }
  if(isset($_POST['has_record_for_language'])) {
    $has_record_for_language =  $_POST['has_record_for_language'];
  }
  if(isset($_POST['news_meta_titles'])) {
    $news_meta_titles =  $_POST['news_meta_titles'];
  }
  if(isset($_POST['news_meta_keywords_array'])) {
    $news_meta_keywords_array =  $_POST['news_meta_keywords_array'];
  }
  if(isset($_POST['news_meta_descriptions'])) {
    $news_meta_descriptions =  $_POST['news_meta_descriptions'];
  }
  if(isset($_POST['news_pretty_urls'])) {
    $news_pretty_urls =  $_POST['news_pretty_urls'];
  }
  if(isset($_POST['current_news_pretty_urls'])) {
    $current_news_pretty_urls =  $_POST['current_news_pretty_urls'];
  }
    
  mysqli_query($db_link,"BEGIN");
  $all_queries = "";

  foreach($news_meta_titles as $language_id => $news_meta_title) {

    $news_pretty_url = $news_pretty_urls[$language_id];
    $current_news_pretty_url = $current_news_pretty_urls[$language_id];
    $news_pretty_url = str_replace(array('\\',':',"'",'?','!','"','.',',','(',')','%',' - ',' ','--'), array('-','-','','','','','','-','-','-','-','-','-','-'), mb_convert_case($news_pretty_urls[$language_id], MB_CASE_LOWER, "UTF-8"));
    $news_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $news_meta_title));
    $news_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $news_meta_descriptions[$language_id]));
    $news_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $news_meta_keywords_array[$language_id]));

    if($has_record_for_language[$language_id] == 1) {

      $query_update_news_description = "UPDATE `news_descriptions` SET ";
      if($current_news_pretty_url != $news_pretty_url) {
                                        $query_update_news_description .= "`news_pretty_url` = '$news_pretty_url',";
      }
                                        $query_update_news_description .= "`news_meta_title` = $news_meta_title,
                                                                            `news_meta_description` = $news_meta_description, 
                                                                            `news_meta_keywords` = $news_meta_keywords
                                    WHERE `news_id` = '$current_news_id' AND `language_id` = '$language_id'";
      //echo $query_update_news_description;
      $all_queries .= "<br>".$query_update_news_description;
      $result_update_news_description = mysqli_query($db_link, $query_update_news_description);
      if(!$result_update_news_description) {
        echo $languages['sql_error_update']." - 1 news_descriptions ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

    } //if(mysqli_num_rows($result_check_for_record) > 0)
    else {

      $news_title_db = "";
      $news_summary_db = "NULL";
      $news_text_db = "";
      
      $query_insert_news_descriptions = "INSERT INTO `news_descriptions`(`news_id`, 
                                                                          `language_id`, 
                                                                          `news_title`, 
                                                                          `news_summary`, 
                                                                          `news_text`,
                                                                          `news_pretty_url`,
                                                                          `news_meta_title`,
                                                                          `news_meta_description`,
                                                                          `news_meta_keywords`)
                                                                  VALUES ('$news_id',
                                                                          '$language_id',
                                                                          '$news_title_db',
                                                                          $news_summary_db,
                                                                          '$news_text_db',
                                                                          '$news_pretty_url',
                                                                          $news_meta_title,
                                                                          $news_meta_description,
                                                                          $news_meta_keywords)";
      //echo $query_insert_news_description;
      $all_queries .= "<br>".$query_insert_news_description;
      $result_insert_news_description = mysqli_query($db_link, $query_insert_news_description);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 2 news_descriptions ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }

  }

  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

  mysqli_query($db_link,"COMMIT");