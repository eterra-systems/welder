<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf_in_reports();
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?=$languages['e_shop_cms'];?></title>
<style>
body {
  width: 100%;
  padding:0;
  font-family:Garamond, "Times New Roman",Helvetica, sans-serif;	
  font-size:12px;
  line-height: 15px;
  text-align: left;
  color:#333;
  background-color: #fff;
}
h1 {
  margin: 30px 0 8px;
  font-size:20px;
}
h2 {
  margin: 20px 0 8px;
  font-size:16px;
}
h3,li {
  margin: 0 0 4px;
  font-size:15px;
}
p {
  margin:0 0 8px;
}
small {
  font-size:10px;
}
.btn {
  margin-bottom: 1px;
  padding: 5px 10px !important;
  font-weight: bold;
  text-align: center;
  background: #fffbe2; /* Old browsers */
  background: -moz-linear-gradient(top,  #fffbe2 0%, #fffffd 23%, #fff697 85%, #fffef4 100%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fffbe2), color-stop(23%,#fffffd), color-stop(85%,#fff697), color-stop(100%,#fffef4)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* IE10+ */
  background: linear-gradient(to bottom,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fffbe2', endColorstr='#fffef4',GradientType=0 ); /* IE6-9 */
  border: 1px solid #a8a8a7;
  border-radius: 10px;
  -moz-border-radius: 10px;
  -o-border-radius: 10px;
  -webkit-border-radius: 10px;
}
</style>
</head>
<body>
<form>
  <input type="button" class="btn" value="<?=$languages['btn_print'];?>" onClick="this.parentNode.style.display='none';window.print();">
 </form>
<?php
  if(isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];
  }
  
  $query = "SELECT `customers`.`customer_firstname`, `customers`.`customer_lastname` 
            FROM `customers`
            WHERE `customers`.`customer_id` = '$customer_id'";
  $result_customer_log = mysqli_query($db_link,$query);
  if(!$result_customer_log) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer_log) > 0) {
    
    $customer_details = mysqli_fetch_assoc($result_customer_log);
    
    $customer_firstname = $customer_details['customer_firstname'];
    $customer_lastname = $customer_details['customer_lastname'];
//    $basic_customer_ip = $customer_details['customer_ip'];
    
  }
  $query = "SELECT `customers_logs`.* FROM `customers_logs` 
            WHERE `customer_id` = '$customer_id'
            ORDER BY `customer_log_id` DESC
            LIMIT 50";
  //echo $query;exit;
  $result = mysqli_query($db_link,$query);
  if(!$result) echo mysqli_error($db_link);
  else {
    while($row = mysqli_fetch_assoc($result)) {
      $customers_logs[] = $row;
    }

    if(!empty($customers_logs)) {
      
      echo "<h1 style=\"text-align:center;\">$customer_firstname $customer_lastname - ".$languages['header_user_logs']."</h1>";
      echo "<ol>";
      foreach($customers_logs as $customers_log) {

        $customer_log_date = $customers_log['customer_log_date'];
//        $customer_location_city = (empty($customers_log['customer_location_city'])) ? "" : $customers_log['customer_location_city']." / ";
//        $customer_ip = (empty($customers_log['customer_ip'])) ? $basic_customer_ip : $customers_log['customer_ip'];
        
        echo "<li>$customer_log_date</li>";
      }
      echo "</ol>";
      
    }
    else echo "<h2>$customer_firstname $customer_lastname ".$languages['text_no_logs_yet']."</h2>";
    
  }
?>
</body>
</html>