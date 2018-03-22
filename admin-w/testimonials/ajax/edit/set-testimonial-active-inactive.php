<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['testimonial_id'])) {
    $testimonial_id =  $_POST['testimonial_id'];
  }
  if(isset($_POST['set_testimonial'])) {
    $set_testimonial =  $_POST['set_testimonial'];
  }
  
  if(!empty($testimonial_id)) {
 
    $query_update_testimonial = "UPDATE `testimonials` SET  `testimonial_is_active`='$set_testimonial' WHERE `testimonial_id` = '$testimonial_id'";
 
    //echo $query_update_testimonial;
    $result_update_testimonial = mysqli_query($db_link, $query_update_testimonial);
    if(!$result_update_testimonial) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>