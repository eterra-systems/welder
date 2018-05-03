<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  //  echo "<pre>";print_r($_SERVER);EXIT;

  if(isset($_GET['aid'])) {
    $current_ad_id = mysqli_real_escape_string($db_link,intval($_GET['aid'])); // current selected product
  }
  
  $query_ad = "SELECT `cca`.*,`customers`.`customer_image`,`customers_company`.`company_name`,`countries`.`country_name`,
                      `sites`.`site_name` as `bg_site_name`
                 FROM `customers_company_ads` as `cca`
           INNER JOIN `customers` USING(`customer_id`)
           INNER JOIN `customers_company` USING(`customer_id`)
            LEFT JOIN `countries` ON `countries`.`country_id` = `cca`.`country_id`
            LEFT JOIN `sites` ON `sites`.`site_id` = `cca`.`site_id`
                WHERE `cca`.`ad_id` = '$current_ad_id'";
  //echo $query_ad;exit;
  $result_ad = mysqli_query($db_link, $query_ad);
  if (!$result_ad) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_ad)) {

    $ad_row = mysqli_fetch_assoc($result_ad);

    $ad_title = $ad_row['ad_title'];
    $site_name_not_bg = $site_name;
    $ad_summary = $ad_row['ad_summary'];
    $ad_description = $ad_row['ad_description'];
    $ad_start_date = $ad_row['ad_start_date'];
    $customer_id = $ad_row['customer_id'];
    $company_name = stripslashes($ad_row['company_name']);
    $customer_image = $ad_row['customer_image'];
    $ad_site_name = $ad_row['site_name'];
    $bg_site_name = $ad_row['bg_site_name'];
    $country_name = $ad_row['country_name'];
    $site_name = ($country_name == "България") ? $bg_site_name : $ad_site_name;
    $company_logo = (empty($customer_image)) ? SITEFOLDERSL."/images/no-profile-man-medium.jpg" : 
                                                SITEFOLDERSL.DIRECTORY_SEPARATOR."company/profile-images/$customer_id/$customer_image";
    $ad_publish_date = date("d.m.Y", strtotime($ad_row['ad_publish_date']));
  }
  else {
    print_error_page();
  }
  //echo"<pre>";print_r($ad_row);

  //encrease ad_views by one
  $query_update_ad = "UPDATE `customers_company_ads` SET `ad_views` = `ad_views`+1 WHERE `ad_id` = '$current_ad_id'";
  $result_update_ad = mysqli_query($db_link, $query_update_ad);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages['sql_error_update'] . " - " . mysqli_error($db_link);
  }
  
  $body_css = 'ad_details';
  print_html_header($ad_title, $meta_description = $ad_summary, $meta_keywords = "", $additional_css_javascript = false, $body_css);
  //echo "<pre>";print_r($_SERVER);
  //print_array_for_debug($_SESSION);
?>
<!-- Load Facebook SDK for JavaScript -->
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1226495314044586',
      xfbml      : true,
      version    : 'v2.5'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

  <!-- start breadcrumb -->
  <div class="breadcrumb-wrapper">

    <div class="container">

      <ol class="breadcrumb-list booking-step">
        <li><a href="/" title="<?= $languages['title_goto_homepage']; ?>"><?= $languages['menu_home']; ?></a></li>
        <li><a href="#">Всички обяви</a></li>
        <li><span><?=$ad_title?></span></li>;
      </ol>

    </div>

  </div>

  <div class="section sm">
    <div class="container">
      <div class="row">

        <div class="col-md-10 col-md-offset-1">
          <div class="job-detail-wrapper">
            
            <div class="job-detail-header text-center">

              <h2 class="heading mb-15"><?=$ad_title?></h2>

              <div class="meta-div clearfix mb-25">
                <span>at <a href="#">Expedia</a> as </span>
                <span class="job-label label label-success">Freelance</span>
              </div>

              <ul class="meta-list clearfix">
                <li>
                  <h4 class="heading"><?=$languages['text_location'];?>:</h4>
                  <?=$site_name;?>, <?=$country_name;?>
                </li>
                <li>
                  <h4 class="heading">Rate/Salary:</h4>
                  Negotiable
                </li>
                <li>
                  <h4 class="heading">Experience</h4>
                  Expert
                </li>
                <li>
                  <h4 class="heading"><?=$languages['text_published'];?>: </h4>
                  <?=$ad_publish_date;?>
                </li>
              </ul>

            </div>
            
            <div class="job-detail-company-overview clearfix">

              <h3><?=$company_name;?></h3>
              <div class="image">
                <img src="<?=$company_logo;?>" alt="image" />
              </div>

              <p><span class="font600">Expedia</span> is repulsive questions contented him few extensive supported. Of remarkably thoroughly he appearance in. Supposing tolerably applauded or of be. Suffering unfeeling so objection agreeable allowance me of. Ask within entire season sex common far who family. As be valley warmth assure on. Park girl they rich hour new well way you. Face ye be me been room we sons fond. Justice joy manners boy met resolve produce. Bed head loud next plan rent had easy add him... <a href="#"> read more about this company <i class="fa fa-long-arrow-right"></i></a></p>

            </div>

            <div class="job-detail-content mt-30 clearfix">

              <h3><?=$languages['header_description'];?></h3>

              <div><?=$ad_description;?></div>
            </div>
            
            <div class="apply-job-wrapper">

              <button class="btn btn-primary btn-hidden btn-lg collapsed" data-toggle="collapse" data-target="#apply-job-toggle"><?=$languages['btn_apply_for_job'];?></button>
                    
            </div>
            
          </div>
        </div>
        
      </div>
    </div>
  </div>
<?php
  print_html_footer();
?>