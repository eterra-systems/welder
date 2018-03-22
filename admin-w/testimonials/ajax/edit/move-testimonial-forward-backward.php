<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['testimonial_id'])) {
    $testimonial_id =  $_POST['testimonial_id'];
  }
  if(isset($_POST['testimonial_sort_order'])) {
    $testimonial_sort_order =  $_POST['testimonial_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($testimonial_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_testimonial_sort_order = $testimonial_sort_order-1;
      $query_update_testimonial_1 = "UPDATE `testimonials` SET `testimonial_sort_order`='$testimonial_sort_order' WHERE `testimonial_sort_order` = '$previous_testimonial_sort_order'";
      $all_queries .= "\n".$query_update_testimonial_1;
        //echo $query_update_testimonial_1;
      $result_update_testimonial_1 = mysqli_query($db_link, $query_update_testimonial_1);
      if(!$result_update_testimonial_1) {
        echo $testimonials['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_testimonial_2 = "UPDATE `testimonials` SET `testimonial_sort_order`='$previous_testimonial_sort_order' WHERE `testimonial_id` = '$testimonial_id'";
      $all_queries .= "\n".$query_update_testimonial_2;
        //echo $query_update_testimonial_2;
      $result_update_testimonial_2 = mysqli_query($db_link, $query_update_testimonial_2);
      if(!$result_update_testimonial_2) {
        echo $testimonials['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_testimonial_sort_order = $testimonial_sort_order+1;
      $query_update_testimonial_1 = "UPDATE `testimonials` SET `testimonial_sort_order`='$testimonial_sort_order' WHERE `testimonial_sort_order` = '$next_testimonial_sort_order'";
      $all_queries .= "\n".$query_update_testimonial_1;
        //echo $query_update_testimonial_1;
      $result_update_testimonial_1 = mysqli_query($db_link, $query_update_testimonial_1);
      if(!$result_update_testimonial_1) {
        echo $testimonials['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_testimonial_2 = "UPDATE `testimonials` SET `testimonial_sort_order`='$next_testimonial_sort_order' WHERE `testimonial_id` = '$testimonial_id'";
      $all_queries .= "\n".$query_update_testimonial_2;
        //echo $query_update_testimonial_2;
      $result_update_testimonial_2 = mysqli_query($db_link, $query_update_testimonial_2);
      if(!$result_update_testimonial_2) {
        echo $testimonials['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_testimonials();

  }
?>
