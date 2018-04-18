<?php
  $customer_id = $_SESSION['customer_id'];
  
  $query_ads = "SELECT `cca`.*
                  FROM `customers_company_ads` as `cca`
             LEFT JOIN `sites` USING(`site_id`)
                 WHERE `cca`.`customer_id` = '$customer_id'
              ORDER BY `cca`.`ad_publish_date` DESC";
  //echo $query_ads;exit;
  $result_ads = mysqli_query($db_link, $query_ads);
  if (!$result_ads) echo mysqli_error($db_link);
  $ad_count = mysqli_num_rows($result_ads);
  if($ad_count > 0) {
?>
  <div class="recent-job-wrapper">
<?php
    while($ad_row = mysqli_fetch_assoc($result_ads)) {
      //print_array_for_debug($ad_row);
      $ad_id = $ad_row['ad_id'];
      $ad_title = $ad_row['ad_title'];
      $ad_publish_date = date("d.m.Y", strtotime($ad_row['ad_publish_date']));
      $edit_link = "/$current_lang/".$_SESSION['customer_group_code']."/user-profile-ad-edit";
?>
      <form action="<?=$edit_link;?>" method="post" class="recent-job-item clearfix">
        <div class="GridLex-grid-middle">
          
          <div class="GridLex-col-6_xs-12"><?=$ad_title;?></div>
          <div class="GridLex-col-4_xs-8_xss-12 mt-10-xss"><?=$ad_publish_date;?></div>
          <div class="GridLex-col-2_xs-4_xss-12">
            <button class="btn btn-primary"><?=$languages['btn_edit'];?></button>
          </div>
          <input type="hidden" name="ad_id" value="<?=$ad_id;?>">
          
        </div>
      </form>
<?php
    }
?>
  </div>
  <p class="clearfix">&nbsp;</p>
<?php
    mysqli_free_result($result_ads);
  }
?>