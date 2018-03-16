<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  if(isset($current_page_pretty_url) && $current_page_pretty_url == "logout") {
    unset($_SESSION);
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
  }
   
  $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_is_default_frontend`,`contents_descriptions`.`content_pretty_url` 
                             FROM `languages` 
                       INNER JOIN `contents_descriptions` ON (`contents_descriptions`.`language_id` = `languages`.`language_id`)
                       INNER JOIN `contents` ON (`contents`.`content_id` = `contents_descriptions`.content_id)
                            WHERE `language_code` = '$current_lang' AND `contents`.`content_is_home_page` = '1'";
  //echo $query_content;exit;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if(!$result_current_params) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $language_is_default_frontend = $row_current_params['language_is_default_frontend'];
    $home_page_url = ($language_is_default_frontend == 1) ? "/" : "/$current_lang/".$row_current_params['content_pretty_url'];
  }
  else {
    $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_code`,`languages`.`language_is_default_frontend`,
                                    `contents_descriptions`.`content_pretty_url` 
                               FROM `languages` 
                         INNER JOIN `contents_descriptions` ON (`contents_descriptions`.`language_id` = `languages`.`language_id`)
                         INNER JOIN `contents` ON (`contents`.`content_id` = `contents_descriptions`.content_id)
                              WHERE `language_is_default_frontend` = '1'";
    //echo $query_content_hierarchy_ids;exit;
    $result_current_params = mysqli_query($db_link, $query_current_params);
    if(!$result_current_params) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_current_params) > 0) {
      $row_current_params = mysqli_fetch_assoc($result_current_params);
      $current_language_id = $row_current_params['language_id'];
      $current_lang = $row_current_params['language_code'];
      $language_is_default_frontend = $row_current_params['language_is_default_frontend'];
      $home_page_url = ($language_is_default_frontend == 1) ? "/" : "/$current_lang/".$row_current_params['content_pretty_url'];
    }
  }
  
  $content_meta_title = "";
  $content_meta_description = "";
  $content_meta_keywords = "";
  $content_hierarchy_ids = 0;
  
  

  if(isset($current_page_pretty_url) && $current_page_pretty_url == "registration") {
    
   //echo $current_page_pretty_url;
      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, "<script src='https://www.google.com/recaptcha/api.js'></script>\n", $body_css = "registration");
?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$languages['header_registration'];?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_registration']) ?></p>	
      </div>
    </div>

    <div class="clearfix">
      <main class="main-content main-content-full">
        <?php
          if(isset($page_params)) {
            include_once "$page_params".DIRECTORY_SEPARATOR."$current_page_pretty_url.php";
          }
          else {
            
            $customers_groups = get_customers_groups();
?>
          <div class="row">
<?php 
            foreach($customers_groups as $customers_group) {
              
              $customer_group_name = $customers_group['customer_group_name'];
              $customer_group_code = $customers_group['customer_group_code'];
?>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <div class="option_box inner-box">
                <h3><?= $customer_group_name; ?></h3>
                <p>.........</p>
                <a href="/<?= $current_lang; ?>/registration/<?= $customer_group_code; ?>" class="button2" rel="nofollow"><?= $languages['login_sign_up']; ?></a>
              </div>
            </div>
<?php
            }
?>
          </div>
<?php
          }
?>
      </main>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && ($current_page_pretty_url == "login")) {
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, "<script src='https://www.google.com/recaptcha/api.js'></script>", $body_css = "login");
?>
    <div id="page-header">
      <div class="clearfix">
        <h1 itemprop="name"><?=$languages['header_login'];?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_login']) ?></p>
      </div>
    </div>
    
    <!-- BEGIN .-->
    <div class="clearfix">
      <main class="main-content main-content-full">
        <?php include_once 'login.php'; ?>
      </main>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "search") {
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css = "search");
?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$languages['header_search'];?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_search']) ?></p>	
      </div>
    </div>
    <!-- BEGIN .-->
    <div class="clearfix">
      <main class="main-content main-content-full">
        <?php include_once 'search.php'; ?>
      </main>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "confirm-account") {
    //print_array_for_debug($page_path_array);
    $customer_id = intval($page_params[0]);
    
    if(!empty($customer_id)) {

      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords,$additional_css_javascript = false, $body_css = "confirm-account");
?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$languages['header_registration_confirm_account'];?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_registration_confirm_account']) ?></p>	
      </div>
    </div>
    <!-- BEGIN .-->
    <div class="clearfix">
      <main class="main-content main-content-full">
        <?php include_once 'confirm-account.php'; ?>
      </main>
    </div>
<?php
    }
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "forgotten-password") {
    
    $current_page_text = "header_".str_replace("-", "_", $current_page_pretty_url);
    $content_name = $languages[$current_page_text];
    print_html_header($content_meta_title = $content_name, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css = "my-account");
?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$content_name;?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $content_name) ?></p>	
      </div>
    </div>
    <div class="clearfix">
      <div class="password">
        <main class="main-content main-content-full">
          <?php
            include_once "$current_page_pretty_url.php"; 
          ?>
        </main>
      </div>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && isset($_SESSION['customer_group_code']) && $current_page_pretty_url == $_SESSION['customer_group_code']) {
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css = "my-account");
    $current_page_text = "header_".str_replace("-", "_", $page_params);
    $content_name = $languages[$current_page_text];
?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$content_name;?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $content_name) ?></p>	
      </div>
    </div>
    <div class="clearfix">
      <div class="row">
<?php 
  if(user_is_loged()) {
    $customer_fullname = $_SESSION['customer_name']; 
?>
        <aside class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
          <section class="widget clearfix">
            <h4 class="title-style3"><?=$customer_fullname;?> <span class="title-block"></span></h4>
            <ul class="user_menu unstyled clearfix">
              <?php 
                $function_name = "print_html_".$_SESSION['customer_group_code']."_profile_menu";
                echo $function_name(); 
              ?>
            </ul>
          </section>
        </aside>
<?php
  } 
?>
        <main class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
          <?php 
            if(!user_is_loged()) {
              echo "<h1>".$languages['error_secured']."</h1>";
            }
            else {
              include_once "$current_page_pretty_url/$page_params.php"; 
            }
          ?>
        </main>
      </div>
    </div>
<?php
  }
  else {
    
    $content_is_home_page = 0;
    $content_name = "";
    $content_text = "";
    $content_type = "";

    /*
     * get the current page by the it's pretty url
     */
    $query_content = "SELECT `contents`.`content_id`,`contents`.`content_hierarchy_ids`,`contents`.`content_is_home_page`,
                             `contents_descriptions`.`content_name`,`contents_descriptions`.`content_menu_text`,`contents_descriptions`.`content_meta_title`,
                             `contents_descriptions`.`content_meta_keywords`,`contents_descriptions`.`content_meta_description`,
                             `contents_descriptions`.`content_summary`,`contents_descriptions`.`content_text`,`contents_types`.`content_type`
                        FROM `contents`
                  INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                  INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                       WHERE $query_where_page AND `contents_descriptions`.`language_id` = '$current_language_id'";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $current_content_id = $content_array['content_id'];
      $content_type = $content_array['content_type'];
      $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
      $content_is_home_page = $content_array['content_is_home_page'];
      $content_name = stripslashes($content_array['content_name']);
      $content_menu_text = stripslashes($content_array['content_menu_text']);
      $content_meta_title = stripslashes($content_array['content_meta_title']);
      $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
      $content_meta_description = stripslashes($content_array['content_meta_description']);
      $content_summary = stripslashes($content_array['content_summary']);
      $content_text = stripslashes($content_array['content_text']);
    }
    else {
      /*
       * if there is no result from the get page query we gonna get the error page
       */
      
      $content_type_id = 3; //error page set by default if no other page is found

      $query_content = "SELECT `contents`.`content_id`,`contents`.`content_hierarchy_ids`,`contents_descriptions`.`content_name`,`contents_types`.`content_type`,
                               `contents_descriptions`.`content_menu_text`,`contents_descriptions`.`content_meta_title`,`contents_descriptions`.`content_meta_keywords`,
                               `contents_descriptions`.`content_meta_description`,`contents_descriptions`.`content_summary`,`contents_descriptions`.`content_text`
                          FROM `contents`
                    INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                    INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                         WHERE `contents`.`content_type_id` = '$content_type_id' AND `contents_descriptions`.`language_id` = '$current_language_id'";
      //echo $query_content."<br>";
      $result_content = mysqli_query($db_link, $query_content);
      if(!$result_content) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_content) > 0) {
        $content_array = mysqli_fetch_assoc($result_content);
        $current_content_id = $content_array['content_id'];
        $content_type = $content_array['content_type'];
        $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
        $content_name = stripslashes($content_array['content_name']);
        $content_menu_text = stripslashes($content_array['content_menu_text']);
        $content_meta_title = (empty($content_array['content_meta_title'])) ? $content_name : stripslashes($content_array['content_meta_title']);
        $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
        $content_meta_description = stripslashes($content_array['content_meta_description']);
        $content_summary = stripslashes($content_array['content_summary']);
        $content_text = stripslashes($content_array['content_text']);
      }
    }

    if($content_is_home_page == 1) {
      
      $canonical_link = "<link rel=\"canonical\" href=\"".PROTOCOL.DOMAIN.urldecode($home_page_url)."\" />";
      
      print_html_header($content_meta_title,$content_meta_description,$content_meta_keywords,$additional_css_javascript = false);
       
      $query_content = "SELECT `contents`.`content_id`,`contents_descriptions`.`content_name`,`contents_descriptions`.`content_pretty_url`
                          FROM `contents`
                    INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                    INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                         WHERE `contents_types`.`content_type` = 'returns_policy' AND `contents_descriptions`.`language_id` = '$current_language_id'";
      //echo $query_content."<br><br>";
      $result_content = mysqli_query($db_link, $query_content);
      if(!$result_content) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_content) > 0) {
        $content_array = mysqli_fetch_assoc($result_content);
        $returns_policy_name = $content_array['content_name'];
        $returns_policy_url = $content_array['content_pretty_url'];
      }
?>
        <!-- start hero-header -->
        <div class="hero" style="background-image:url('<?=SITEFOLDERSL;?>/images/hero-header/01.jpg');">
          <div class="container">

            <h1>your future starts here now</h1>
            <p>Finding your next job or career more 1000+ availabilities</p>

            <div class="main-search-form-wrapper">

              <form>

                <div class="form-holder">
                  <div class="row gap-0">

                    <div class="col-xss-6 col-xs-6 col-sm-6">
                      <input class="form-control" placeholder="Looking for job" />
                    </div>

                    <div class="col-xss-6 col-xs-6 col-sm-6">
                      <input class="form-control" placeholder="Place to work" />
                    </div>

                  </div>

                </div>

                <div class="btn-holder">
                  <button class="btn"><i class="ion-android-search"></i></button>
                </div>

              </form>

            </div>


          </div>

        </div>
        <!-- end hero-header -->
<?php
    }
    else {
      if($content_type == "error_page") {
        // error page
        
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false,$body_css = "error");
?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$content_name;?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $content_name) ?></p>	
      </div>
    </div>
    <div class="clearfix">
      <main class="main-content main-content-full">
        <?=$content_text;?>
      </main>
    </div>
<?php
      }
      elseif($content_type == "news") {
        
        $news_category_id = 1; // all news
        $body_css = "news-categories";

        $query_news_category = "SELECT `news_categories`.`news_category_id`,`news_cat_desc`.`news_cat_name`
                                  FROM `news_categories` 
                            INNER JOIN `news_cat_desc` ON `news_cat_desc`.`news_category_id` = `news_categories`.`news_category_id`
                                 WHERE `news_categories`.`news_category_id` = '$news_category_id' AND `news_cat_desc`.`language_id` = '$current_language_id'";
        //echo $query_news_category;exit;
        $result_news_category = mysqli_query($db_link, $query_news_category);
        if(!$result_news_category) echo mysqli_error($db_link);
        $news_count = mysqli_num_rows($result_news_category);
        if(mysqli_num_rows($result_news_category) > 0) {

          $news_cat_name_row = mysqli_fetch_array($result_news_category);
          $news_cat_name = $news_cat_name_row['news_cat_name'];
        }
      
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false,$body_css = "news");

?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$news_cat_name;?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $news_cat_name) ?></p>	
      </div>
    </div>
    <div class="clearfix">
      <main class="main-content">
        <?php 
          list_news($offset = false,$news_count = false, $news_category_id); 
        ?>
      </main>
      <!--Aside bar Wrap Start-->
      <aside class="sidebar-content">
          <?php print_html_news_sidebar($print_latest_news = true);?>
      </aside>
    </div>
<?php
      }
      elseif($content_type == "contacts") {

      $body_css = "contacts";
      $sitekey = "6Le3IEYUAAAAAPssLvABf4DmEfxX5RLwb04bIRHw";

      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false,$body_css);
      //echo "<pre>";print_r($_SERVER);echo "</pre>";
?>
      <div id="page-header">
        <div class="clearfix">
          <h1><?=$content_name;?></h1>
          <p><?php print_content_breadcrumbs($content_hierarchy_ids, $content_name) ?></p>	
        </div>
      </div>
      
      <div class="clearfix">
<?php
        include_once 'contacts-form.php';
?>
      </div>
<?php
      }
      else {
        
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false,$body_css = "pages");
?>
    <div id="page-header">
      <div class="clearfix">
        <h1><?=$content_name;?></h1>
        <p><?php print_content_breadcrumbs($content_hierarchy_ids, $content_name) ?></p>	
      </div>
    </div>
    <div class="clearfix">
      <main class="main-content">
        <p><?=$content_text?></p>
      </main>
    </div>
<?php
      }
    } // else of if($content_is_home_page == 1)
  }
 
  print_html_footer();