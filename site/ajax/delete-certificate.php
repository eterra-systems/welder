<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  
  $certificate_id = 0;
  if(isset($_POST['certificate_id'])) {
    $certificate_id =  $_POST['certificate_id'];
  }
  if(isset($_POST['certificate_name'])) {
    $certificate_name =  $_POST['certificate_name'];
  }
  
  if(!empty($certificate_id)) {

    $customer_id = $_SESSION['customer_id'];
    $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/certificates/$customer_id/";
    $certificate = "$upload_path$certificate_name";

    if(file_exists($certificate)) {
      unlink($certificate);
    }
    
    $query = "DELETE FROM `customers_welder_certificates` WHERE `certificate_id` = '$certificate_id'";
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_delete']." - ".mysqli_error($db_link);
      exit;
    }
  } 
?>