<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
  }
  if(isset($_POST['parent_id'])) {
    $category_parent_id = $_POST['parent_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `categories` WHERE `category_id` = '$category_id'";
  $all_queries = $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `categories_descriptions` WHERE `category_id` = '$category_id'";
  $all_queries .= $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `category_to_category` WHERE `category_id` = '$category_id'";
  $all_queries .= $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  //if $category_parent_id != 0 we need to check if the old parent has any children left
  //if not setting it's `category_has_children` parameter to 0
  if($category_parent_id != 0) {
    $query_categories_siblings = "SELECT `category_id` FROM `categories` WHERE `category_parent_id` = '$category_parent_id' LIMIT 1";
    $all_queries .= $query_categories_siblings."<br>";
    $result_categories_siblings = mysqli_query($db_link, $query_categories_siblings);
    if(!$result_categories_siblings) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_categories_siblings) <= 0) {

      $query_update_parent = "UPDATE `categories` SET `category_has_children` = '0' WHERE `category_id` = '$category_parent_id'";
      $all_queries .= $query_update_parent."<br>";
      $result_update_parent = mysqli_query($db_link, $query_update_parent);
      if(!$result_update_parent) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      mysqli_free_result($result_categories_siblings);
    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");

  list_categories($parent_id = 0,$category_root_id = 0,$path_number = 0);
?>
