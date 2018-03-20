<?php

  $current_date = strtotime(date("Y-m-d"));
  $valid_date_for_confirmation = date("Y-m-d", strtotime('-2 days', $current_date));
  
  $query_activate_user = "UPDATE `customers` SET `customer_is_active`='1' WHERE `customer_id` = '$customer_id' AND `customer_registration_date` >= '$valid_date_for_confirmation'";
  //echo $query_activate_user;
  $result_activate_user = mysqli_query($db_link, $query_activate_user);
  if(!$result_activate_user) {
    echo mysqli_error($db_link);
  }
?>
  <div class="inside_container">

    <h2 class="alert alert-success"><?=$languages['header_registration_confirmed_successfully'];?></h2>
    <p>&nbsp;</p>
    <a href="/<?= $current_lang; ?>/login" class="btn btn-primary"><?=$languages['login_sign_in'];?></a>
  </div>
