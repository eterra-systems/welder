<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  //print_r($_POST);EXIT;
  if(isset($_POST['image_id'])) {
    $news_image_id = $_POST['image_id'];
  }
  if(isset($_POST['image'])) {
    $news_image = $_POST['image'];
  }
  if(isset($_POST['type'])) {
    $type = $_POST['type'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `news_galleries` WHERE `news_gallery_id` = '$news_image_id'";
  $all_queries = $query."\n";;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  if($type == 1) {
    // default picture
//    $query = "UPDATE `products` SET `news_image` = NULL WHERE `news_id` = '$news_id'";
//    $all_queries .= $query;
//    $result = mysqli_query($db_link, $query);
//    if(mysqli_affected_rows($db_link) <= 0) {
//      echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
//      mysqli_query($db_link,"ROLLBACK");
//      exit;
//    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
  
  $file = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/news/large/$news_image";
  
  if(file_exists($file)) unlink($file);

  $image_thumb = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/news/thumbs/$news_image";

  if(file_exists($image_thumb)) unlink($image_thumb);
?>