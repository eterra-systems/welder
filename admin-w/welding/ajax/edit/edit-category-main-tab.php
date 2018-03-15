<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
//  echo "<pre>";print_r($_POST);EXIT;
  
  if(isset($_POST['category_id'])) {
    $current_category_id =  $_POST['category_id'];
  }
  if(isset($_POST['has_record_for_language'])) {
    $has_record_for_language =  $_POST['has_record_for_language'];
  }
  if(isset($_POST['cd_names'])) {
    $cd_names =  $_POST['cd_names'];
  }
  if(isset($_POST['cd_page_titles'])) {
    $cd_page_titles =  $_POST['cd_page_titles'];
  }
  if(isset($_POST['cd_descriptions'])) {
    $cd_descriptions =  $_POST['cd_descriptions'];
  }
  
  if(!empty($cd_names)) {
    
    mysqli_query($db_link,"BEGIN");
    $all_queries = "";
    
    foreach($cd_names as $language_id => $cd_name) {

      $cd_name = mysqli_real_escape_string($db_link, $cd_name);
      $cd_page_title = mysqli_real_escape_string($db_link, $cd_page_titles[$language_id]);
      $cd_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_descriptions[$language_id]));
      $cd_is_active = (isset($_POST['cd_is_active'][$language_id])) ? 1 : 0;

      if($has_record_for_language[$language_id] == 1) {

        $query_update_cd_description = "UPDATE `categories_descriptions` SET `cd_name` = '$cd_name',
                                                                             `cd_page_title` = '$cd_page_title',
                                                                             `cd_description` = $cd_description,
                                                                             `cd_is_active` = '$cd_is_active'
                                         WHERE `category_id` = '$current_category_id' AND `language_id` = '$language_id'";
        //echo $query_update_cd_description;
        $all_queries .= "<br>".$query_update_cd_description;
        $result_update_cd_description = mysqli_query($db_link, $query_update_cd_description);
        if(!$result_update_cd_description) {
          echo $languages['sql_error_update']." - 1 update `categories_descriptions` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

      } //if(mysqli_num_rows($result_check_for_record) > 0)
      else {

        $cd_pretty_url = str_replace(array('\\',"'",'?','!','"','.',',','(',')','%',' - ',' '), array('-','','','','','','-','-','-','-','-','-'), mb_convert_case($cd_name, MB_CASE_LOWER, "UTF-8"));
        $is_pretty_url_unique = check_if_cd_pretty_url_is_unique($cd_pretty_url,$current_category_id);
        if(!$is_pretty_url_unique) {
          $cd_pretty_url = $cd_pretty_url."-1";
          $is_pretty_url_unique = check_if_cd_pretty_url_is_unique($cd_pretty_url,$current_category_id);
          if(!$is_pretty_url_unique) {
            $cd_pretty_url = $cd_pretty_url."-1";
          }
        }

        $cd_meta_title = "NULL";
        $cd_meta_keywords = "NULL";
        $cd_meta_description = "NULL";

        $query_insert_cd_description = "INSERT INTO `categories_descriptions`(`category_id`, 
                                                                          `language_id`, 
                                                                          `cd_name`, 
                                                                          `cd_page_title`, 
                                                                          `cd_pretty_url`, 
                                                                          `cd_description`, 
                                                                          `cd_is_active`, 
                                                                          `cd_meta_title`,  
                                                                          `cd_meta_description`,  
                                                                          `cd_meta_keywords`) 
                                                                  VALUES ('$current_category_id',
                                                                          '$language_id',
                                                                          '$cd_name',
                                                                          '$cd_page_title',
                                                                          '$cd_pretty_url',
                                                                          $cd_description,
                                                                          '$cd_is_active',
                                                                          $cd_meta_title,
                                                                          $cd_meta_description,
                                                                          $cd_meta_keywords)";
        //echo $query_insert_cd_description;
        $all_queries .= "<br>".$query_insert_cd_description;
        $result_insert_cd_description = mysqli_query($db_link, $query_insert_cd_description);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 2 insert `categories_descriptions` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  }