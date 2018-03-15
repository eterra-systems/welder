<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);exit;
  
  if(isset($_POST['news_id'])) {
    $news_id = $_POST['news_id'];
  }
  if(isset($_POST['page'])) {
    $page = $_POST['page'];
  }
  
  mysqli_query($db_link,"BEGIN");
  $all_queries = "";
  
  $query_news_image = "SELECT `news_image` FROM `news` WHERE `news_id` = '$news_id' LIMIT 1";
  //echo $query_news_image;exit;
  $result_news_image = mysqli_query($db_link, $query_news_image);
  if(!$result_news_image) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_image) > 0) {
    $news_image = mysqli_fetch_assoc($result_news_image);

    $news_image = $news_image['news_image'];
    
    if(!is_null($news_image) && !empty($news_image)) {
      $news_image_exploded = explode(".", $news_image);
      $current_news_image_name = $news_image_exploded[0];
      $current_news_image_exstension = $news_image_exploded[1];
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/news/";

      $file = $upload_path."$current_news_image_name.$current_news_image_exstension";

      if(file_exists($file)) unlink($file);

      $image_admin_thumb_name = $current_news_image_name."_thumb.".$current_news_image_exstension;
      $image_admin_thumb = "$upload_path$image_admin_thumb_name";

      if(file_exists($image_admin_thumb)) unlink($image_admin_thumb);
    }
  }
    
  $query = "DELETE FROM `news` WHERE `news_id` = '$news_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query_news_desc = "SELECT `news_id` FROM `news_descriptions` WHERE `news_id` = '$news_id' LIMIT 1";
  //echo $query_news_desc;exit;
  $result_news_desc = mysqli_query($db_link, $query_news_desc);
  if(!$result_news_desc) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_desc) > 0) {
    
    $query = "DELETE FROM `news_descriptions` WHERE `news_id` = '$news_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
  $query_news_images = "SELECT `name` FROM `news_gallery` WHERE `news_id` = '$news_id' LIMIT 1";
  //echo $query_news_images;exit;
  $result_news_images = mysqli_query($db_link, $query_news_images);
  if(!$result_news_images) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_images) > 0) {
    while($news_images = mysqli_fetch_assoc($result_news_images)) {
      
      $news_image = $news_images['name'];

      if(!is_null($news_image) && !empty($news_image)) {
        
        $file = $_SERVER['DOCUMENT_ROOT']."/site/images/news/large/$news_image";

        if(file_exists($file)) unlink($file);

        $image_thumb = $_SERVER['DOCUMENT_ROOT']."/site/images/news/thumbs/$news_image";

        if(file_exists($image_thumb)) unlink($image_thumb);
      }
    }
    
    $query = "DELETE FROM `news_gallery` WHERE `news_id` = '$news_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
  
  if($page == "news_list") {
    $filters_array = "";
    list_news($filters_array);
  }
?>