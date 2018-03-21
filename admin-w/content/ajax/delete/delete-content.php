<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['content_id'])) {
    $content_id = $_POST['content_id'];
  }
  if(isset($_POST['content_parent_id'])) {
    $content_parent_id = $_POST['content_parent_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query_content_details = "SELECT `content_image` FROM `contents` WHERE `content_id` = '$content_id' LIMIT 1";
  //echo $query_content_details;exit;
  $result_content_details = mysqli_query($db_link, $query_content_details);
  if(!$result_content_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_details) > 0) {
    $content_details = mysqli_fetch_assoc($result_content_details);

    $content_image = $content_details['content_image'];
    if(!empty($content_image) || !is_null($content_image)) {
      $content_image_exploded = explode(".", $content_image);
      $current_content_image_name = $content_image_exploded[0];
      $current_content_image_exstension = $content_image_exploded[1];
      $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/contents/";

      $file = $upload_path."$current_content_image_name.$current_content_image_exstension";

      unlink($file);

      $image_thumb_name = $current_content_image_name."_thumb.".$current_content_image_exstension;
      $image_thumb = "$upload_path$image_thumb_name";

      unlink($image_thumb);
    }
  }
  
  $query = "DELETE FROM `contents` WHERE `content_id` = '$content_id'";
  $all_queries = $query;
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  $query = "DELETE FROM `contents_descriptions` WHERE `content_id` = '$content_id'";
  $all_queries = $query;
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  //if $content_parent_id != 0 we need to check if the old parent has any children left
  //if not setting it's `content_has_children` parameter to 0
  if($content_parent_id != 0) {
    $query_contents_siblings = "SELECT `content_id` FROM `contents` WHERE `content_parent_id` = '$content_parent_id' LIMIT 1";
    $all_queries .= $query_contents_siblings."<br>";
    $result_contents_siblings = mysqli_query($db_link, $query_contents_siblings);
    if(!$result_contents_siblings) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_contents_siblings) <= 0) {

      $query_update_parent = "UPDATE `contents` SET `content_has_children` = '0' WHERE `content_id` = '$content_parent_id'";
      $all_queries .= $query_update_parent."<br>";
      $result_update_parent = mysqli_query($db_link, $query_update_parent);
      if(!$result_update_parent) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      mysqli_free_result($result_contents_siblings);
    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");

  list_contents($parent_id = 0, $path_number = 0);
?>
