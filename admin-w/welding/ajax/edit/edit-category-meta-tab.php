<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //echo "<pre>";print_r($_POST);EXIT;
  
  if(isset($_POST['category_id'])) {
    $current_category_id =  $_POST['category_id'];
  }
  if(isset($_POST['has_record_for_language'])) {
    $has_record_for_language =  $_POST['has_record_for_language'];
  }
  if(isset($_POST['cd_meta_titles'])) {
    $cd_meta_titles =  $_POST['cd_meta_titles'];
  }
  if(isset($_POST['cd_meta_keywords_array'])) {
    $cd_meta_keywords_array =  $_POST['cd_meta_keywords_array'];
  }
  if(isset($_POST['cd_meta_descriptions'])) {
    $cd_meta_descriptions =  $_POST['cd_meta_descriptions'];
  }
  if(isset($_POST['cd_pretty_urls'])) {
    $cd_pretty_urls =  $_POST['cd_pretty_urls'];
  }
  if(isset($_POST['current_cd_pretty_urls'])) {
    $current_cd_pretty_urls =  $_POST['current_cd_pretty_urls'];
  }
    
  mysqli_query($db_link,"BEGIN");
  $all_queries = "";

  foreach($cd_meta_titles as $language_id => $cd_meta_title) {

    $cd_pretty_url = $cd_pretty_urls[$language_id];
    $current_cd_pretty_url = $current_cd_pretty_urls[$language_id];

    $cd_pretty_url = mysqli_real_escape_string($db_link, $cd_pretty_urls[$language_id]);
    $cd_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_meta_title));
    $cd_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_meta_descriptions[$language_id]));
    $cd_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_meta_keywords_array[$language_id]));

    if($has_record_for_language[$language_id] == 1) {

      $query_update_cd_description = "UPDATE `categories_descriptions` SET ";
      if($current_cd_pretty_url != $cd_pretty_url) {
                                        $query_update_cd_description .= "`cd_pretty_url` = '$cd_pretty_url',";
      }
                                        $query_update_cd_description .= "`cd_meta_title` = $cd_meta_title,
                                                                        `cd_meta_description` = $cd_meta_description, 
                                                                        `cd_meta_keywords` = $cd_meta_keywords
                                    WHERE `category_id` = '$current_category_id' AND `language_id` = '$language_id'";
      //echo $query_update_cd_description;
      $all_queries .= "<br>".$query_update_cd_description;
      $result_update_cd_description = mysqli_query($db_link, $query_update_cd_description);
      if(!$result_update_cd_description) {
        echo $languages['sql_error_update']." - 1 category_descriptions ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

    } //if(mysqli_num_rows($result_check_for_record) > 0)
    else {

      $cd_name = "";
      $cd_page_title = "";
      $cd_description = "NULL";
      
      $query_insert_cd_description = "INSERT INTO `categories_descriptions`(`category_id`, 
                                                                        `language_id`, 
                                                                        `cd_name`, 
                                                                        `cd_page_title`, 
                                                                        `cd_pretty_url`, 
                                                                        `cd_description`, 
                                                                        `cd_meta_title`,  
                                                                        `cd_meta_description`,  
                                                                        `cd_meta_keywords`) 
                                                                VALUES ('$current_category_id',
                                                                        '$language_id',
                                                                        '$cd_name',
                                                                        '$cd_page_title',
                                                                        '$cd_pretty_url',
                                                                        $cd_description,
                                                                        $cd_meta_title,
                                                                        $cd_meta_description,
                                                                        $cd_meta_keywords)";
      //echo $query_insert_cd_description;
      $all_queries .= "<br>".$query_insert_cd_description;
      $result_insert_cd_description = mysqli_query($db_link, $query_insert_cd_description);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 2 category_descriptions ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }

  }

  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

  mysqli_query($db_link,"COMMIT");