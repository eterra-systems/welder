<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  //echo"<pre>";print_r($_POST);echo"</pre>";exit;
  
  check_for_csrf();
  
  if(isset($_POST['content_id'])) {
    $current_content_id = $_POST['content_id'];
  }
  
  $content_parent_id = 0;
  $change_content_type = false;
  
  if(isset($_POST['content_type_id'])) {
    $change_content_type_id = $_POST['change_content_type_id'];
    $cct_id = $_POST['content_type_id']; //current_content_type_id
    $change_content_type = ($change_content_type_id == $cct_id) ? false : true;
  }
  
  mysqli_query($db_link,"BEGIN");
    
  $all_queries = "";
  $content_errors = array();
  $user_id = $_SESSION['admin']['user_id'];

  foreach($_POST['content_name'] as $language_id => $content_name) {
    if(empty($content_name)) $content_errors['content_name'][$language_id] = $languages['required_field_error'];
    if(empty($_POST['content_menu_text'][$language_id])) $content_errors['content_menu_text'][$language_id] = $languages['required_field_error'];
  }
  /*
  *  $_POST['content_parent_id_level'] has three parameters - content_id, content_hierarchy_level
  */
  $content_parent_id_level = explode("|", $_POST['content_parent_id_level']);
  $content_parent_id = $content_parent_id_level[0];
  $content_hierarchy_level = $content_parent_id_level[1]+1;
  $current_content_parent_id = $_POST['current_content_parent_id'];
  $current_content_hierarchy_level = $_POST['current_content_hierarchy_level'];
  $current_content_menu_order = $_POST['current_content_menu_order'];

  if(empty($content_errors)) {
    //if there are no form errors we can insert the information

    /*
     * we have to check if the content has new parent
     * i.e. $current_content_parent_id(from hidden input) is not equal to $content_parent_id(from select parent option)
     * if the parent is changed, not counting the case when setting the content from not having a parent to having one
     * wich means $current_content_parent_id == 0 and $content_parent_id != 0
     * in case the user has choosen new parent for the content
     * we need to update the new parent's column `content_has_children` to 1, wich means it has children
     * we need to check if the old parent has any children left, and if not - setting it's `content_has_children` parameter to 0
     * we also need to update the content's `content_hierarchy_ids` and `content_sort_order` columns
    */

    $content_hierarchy_ids_list = "";
    if($current_content_parent_id != $content_parent_id) {

      if($content_parent_id == 0) {
        $content_hierarchy_ids_list = $current_content_id;
      }
      else {

        $query_update_parent = "UPDATE `contents` SET `content_has_children` = '1' WHERE `content_id` = '$content_parent_id'";
        $all_queries .= $query_update_parent."<br>";
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages['sql_error_update']." - 1 `contents` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $content_hierarchy_ids = get_contents_hierarchy_ids($content_parent_id);
        $content_hierarchy_ids_list .= "$content_hierarchy_ids.$current_content_id";
      }
      $content_menu_order = get_content_lаst_child_order_value($content_parent_id)+1;
    }

    $query_update_content = "UPDATE `contents` SET  `content_type_id`='$cct_id',";
    if($current_content_parent_id != $content_parent_id) {
                          $query_update_content .= "`content_parent_id`='$content_parent_id',
                                                    `content_hierarchy_ids`='$content_hierarchy_ids_list',
                                                    `content_hierarchy_level`='$content_hierarchy_level',
                                                    `content_menu_order`='$content_menu_order',";
    }
                          $query_update_content .= "`content_last_modified_by`='$user_id',
                                                    `content_modified_date`=NOW() 
                                              WHERE `content_id` = '$current_content_id'";
    $all_queries .= $query_update_content."<br>";
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages['sql_error_update']." - 2 `contents` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    /*
     * we have to check if the old parent has any children left
     * if not setting it's `content_has_children` parameter to 0
     */

    if($current_content_parent_id != 0 && $current_content_parent_id != $content_parent_id) {
      $query_contents_siblings = "SELECT `content_id` FROM `contents` WHERE `content_parent_id` = '$current_content_parent_id'";
      $all_queries .= $query_contents_siblings."<br>";
      $result_contents_siblings = mysqli_query($db_link, $query_contents_siblings);
      if(!$result_contents_siblings) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_contents_siblings) <= 0) {

        $query_update_parent = "UPDATE `contents` SET `content_has_children` = '0' WHERE `content_id` = '$current_content_parent_id'";
        $all_queries .= $query_update_parent."<br>";
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages['sql_error_update']." - 3 `contents` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        mysqli_free_result($result_contents_siblings);
      }
    }
      
    /*
     * if the content has new parent we have to reorder the content's old siblings, if any at all,
     * that was with higher `content_menu_order` value and move them with one forward
     * we need to check also if it has children and if so update it's children's ids and levels params
     */
      
    if($current_content_parent_id != $content_parent_id) {
      $query_contents_for_reorder = "SELECT `content_id` FROM `contents` 
                                      WHERE `content_parent_id` = '$current_content_parent_id' AND `content_hierarchy_level` = '$current_content_hierarchy_level' 
                                        AND `content_menu_order` > '$current_content_menu_order'";
      $all_queries .= $query_contents_for_reorder."<br>";
      $result_contents_for_reorder = mysqli_query($db_link, $query_contents_for_reorder);
      if(!$result_contents_for_reorder) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_contents_for_reorder) > 0) {
        while($row_contents_for_reorder = mysqli_fetch_assoc($result_contents_for_reorder)) {
          $row_content_id = $row_contents_for_reorder['content_id'];

          $query_update_content = "UPDATE `contents` SET  `content_menu_order`= `content_menu_order` - 1 WHERE `content_id` = '$row_content_id'";
          $all_queries .= $query_update_content."<br>";
          $result_update_content = mysqli_query($db_link, $query_update_content);
          if(!$result_update_content) {
            echo $languages['sql_error_update']." - 4 `contents` ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        mysqli_free_result($result_contents_for_reorder);
      }
      
      $content_has_children = check_if_content_has_children($current_content_id); // this function returns true or false
      if($content_has_children) {
        update_contents_children_hierarchy_ids_and_level($current_content_id, $content_hierarchy_ids_list, $content_hierarchy_level);
      }
      
    }
    
    foreach($_POST['content_name'] as $language_id => $content_name) {
        
      $has_record_for_language = $_POST['has_record_for_language'][$language_id];
      $content_name = mysqli_real_escape_string($db_link, $content_name);
      $content_menu_text = mysqli_real_escape_string($db_link, $_POST['content_menu_text'][$language_id]);
      if($cct_id == 4) {
        //redirect url
        $content_summary = "NULL";
        $content_text = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_redirect_url'][$language_id]));
      }
      else {
        $content_summary = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_summary'][$language_id]));
        $content_text = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_text'][$language_id]));
      }
      $current_content_pretty_url = $_POST['current_content_pretty_url'][$language_id];
      $content_pretty_url = $_POST['content_pretty_url'][$language_id];
      $content_desc_is_active = (isset($_POST['content_desc_is_active'][$language_id])) ? 1 : 0;
      
      if($has_record_for_language == 1) {

        $query_update_content = "UPDATE `contents_descriptions` SET `content_name`='$content_name',
                                                                    `content_menu_text`='$content_menu_text',
                                                                    `content_summary`=$content_summary,
                                                                    `content_text`=$content_text,
                                                                    `content_pretty_url`='$content_pretty_url',
                                                                    `content_desc_is_active`='$content_desc_is_active'
                                                  WHERE `content_id` = '$current_content_id' AND `language_id` = '$language_id'";
        $all_queries .= $query_update_content."<br>";
        $result_update_content = mysqli_query($db_link, $query_update_content);
        if(!$result_update_content) {
          echo $languages['sql_error_update']." - 5 `contents_descriptions` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      else {
        
        $content_meta_title = "NULL";
        $content_meta_keywords = "NULL";
        $content_meta_description = "NULL";
        
        if(empty($content_pretty_url)) {
          $content_pretty_url = str_replace(array('\\',"'",'?','!','"','.',',','(',')','%',' - ',' '), array('-','','','','','','-','-','-','-','-','-'), mb_convert_case($_POST['content_name'][$language_id], MB_CASE_LOWER, "UTF-8"));
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_pretty_url = $content_pretty_url."-1";
            $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
            if(!$is_pretty_url_unique) {
              $content_errors['content_pretty_url'] = $languages['content_pretty_url_error'];
            }
          }
        }
        else {
          $content_pretty_url = str_replace(array('\\',"'",'?','!','"','.',',','(',')','%',' - ',' '), array('-','','','','','','-','-','-','-','-','-'), mb_convert_case($content_pretty_url, MB_CASE_LOWER, "UTF-8"));
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_pretty_url = $content_pretty_url."-1";
            $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
            if(!$is_pretty_url_unique) {
              $content_errors['content_pretty_url'] = $languages['content_pretty_url_error'];
              $content_pretty_url = $content_pretty_url."-1";
            }
          }
        }
      
        $query_insert_descriptions = "INSERT INTO `contents_descriptions`(`content_id`, 
                                                                          `language_id`, 
                                                                          `content_name`, 
                                                                          `content_menu_text`, 
                                                                          `content_meta_title`, 
                                                                          `content_meta_keywords`, 
                                                                          `content_meta_description`, 
                                                                          `content_summary`, 
                                                                          `content_text`, 
                                                                          `content_pretty_url`, 
                                                                          `content_desc_is_active`) 
                                                                  VALUES ('$current_content_id',
                                                                          '$language_id',
                                                                          '$content_name',
                                                                          '$content_menu_text',
                                                                          $content_meta_title,
                                                                          $content_meta_keywords,
                                                                          $content_meta_description,
                                                                          $content_summary,
                                                                          $content_text,
                                                                          '$content_pretty_url',
                                                                          '$content_desc_is_active')";
        //echo $query_insert_descriptions;
        $all_queries .= "<br>".$query_insert_descriptions;
        $result_insert_descriptions = mysqli_query($db_link, $query_insert_descriptions);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 3 `contents_descriptions` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
    } //foreach($_POST['content_name'] as $language_id => $content_name)

    /*
     * we must be sure the user is logged before we commit the quieries
     */
    if($user_id == 0) {
      echo $languages['sql_error_update'];
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
  }//if(empty($content_errors))
  else {
    echo "<div class='alert alert-danger'>";
    foreach($content_errors as $error) {
      if(is_array($error)) {
        foreach($error as $error_lang) {
          echo "<p>$error_lang</p>";
        }
      }
      else echo "<p>$error</p>";
    }
    echo "</div>";
  }
?>
