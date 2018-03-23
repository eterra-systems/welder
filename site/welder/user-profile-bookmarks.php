<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_FILES);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  $query_bookmarks = "SELECT `customers_welder_bookmarks`.* FROM `customers_welder_bookmarks` WHERE `customer_id` = '$customer_id'";
  //echo $query_bookmarks;
  $result_bookmarks = mysqli_query($db_link, $query_bookmarks);
  if(!$result_bookmarks) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_bookmarks) > 0) {
    while($bookmark_row = mysqli_fetch_assoc($result_bookmarks)) {
      $bookmarks[] = $bookmark_row;
    }
  }
?>
  <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
  <div class="row">
      
<?php
    if(!isset($bookmarks)) {
?>
    <p class="alert alert-info">Все още нямате записани обяви</p>
    <div id="current_bookmarks" class="row">
      
    </div>
<?php
    }
    else {
?>
    <h3 class="title-style2"><?=$languages['header_user_bookmarks'];?></h3>
    <div id="current_bookmarks" class="row">
<?php
      //print_array_for_debug($bookmarks);
      
      foreach($bookmarks as $bookmark) {
        
        $bookmark_name = $bookmark['bookmark_name'];
        $bookmark_exstension = $bookmark['bookmark_exstension'];

?>
        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
          
        </div>
<?php
      }
?>
    </div>
<?php
    }
?>
    <p class="clearfix">&nbsp;</p>
  </div>
  <div class="clearfix">&nbsp;</div>