<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['testimonial_id'])) {
    $testimonial_id = $_POST['testimonial_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $all_queries = "";
  
  $query_testimonial_image = "SELECT `testimonial_image` FROM `testimonials` WHERE `testimonial_id` = '$testimonial_id' LIMIT 1";
  //echo $query_testimonial_image;exit;
  $result_testimonial_image = mysqli_query($db_link, $query_testimonial_image);
  if(!$result_testimonial_image) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_testimonial_image) > 0) {
    $testimonial_image_row = mysqli_fetch_assoc($result_testimonial_image);

    $testimonial_image = $testimonial_image_row['testimonial_image'];
    
    if(!is_null($testimonial_image)) {
      $testimonial_image_exploded = explode(".", $testimonial_image);
      $current_testimonial_image_name = $testimonial_image_exploded[0];
      $current_testimonial_image_exstension = $testimonial_image_exploded[1];
      $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/testimonials/";

      $file = $upload_path."$current_testimonial_image_name.$current_testimonial_image_exstension";

      if(file_exists($file)) unlink($file);

      $image_site_name = $current_testimonial_image_name."_site.".$current_testimonial_image_exstension;
      $image_site = "$upload_path$image_site_name";

      if(file_exists($image_site))  unlink($image_site);
    }
  }
  
  $query = "DELETE FROM `testimonials` WHERE `testimonial_id` = '$testimonial_id'";
  $all_queries .= "<br>".$query."\n";
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  $query = "DELETE FROM `testimonials_descriptions` WHERE `testimonial_id` = '$testimonial_id'";
  $all_queries .= "<br>".$query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
        
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
  
  list_testimonials();
?>
