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
    
    $additional_css_javascript = "<script src='https://www.google.com/recaptcha/api.js'></script>\n";
    $body_css = "not-transparent-header registration";
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript, $body_css);
?>
    <div class="breadcrumb-wrapper">
      <div class="container">
        <ol class="breadcrumb-list">
          <?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_registration']) ?>
        </ol>
      </div>
    </div>

    <div class="section">
      <div class="container">
        
        <div class="row">
          <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

            <div class="section-title bb">

              <h2><?=$languages['header_registration'];?></h2>

            </div>

          </div>
        </div>

        <div class="row mb-30">
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
              $customer_group_text = (empty($customers_group['customer_group_text'])) ? "..." : $customers_group['customer_group_text'];
?>
              <div class="col-sm-4 mb-30">

                <div class="featured-icon-png">

                  <div class="image hidden">
                    <img src="images/colored-line-icons/06.png" alt="Images" />
                  </div>

                  <h5><?= $customer_group_name; ?></h5>
                  <p><?= $customer_group_text; ?></p>

                  <a href="/<?= $current_lang; ?>/registration/<?= $customer_group_code; ?>" class="btn btn-primary" rel="nofollow"><?= $languages['login_sign_up']; ?></a>

                </div>

              </div>
<?php
            }
?>
          </div>
<?php
          }
?>
        </div>
      </div>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && ($current_page_pretty_url == "login")) {
    
    $additional_css_javascript = "<script src='https://www.google.com/recaptcha/api.js'></script>\n";
    $body_css = "not-transparent-header login";
    
    $sitekey = "6Le3IEYUAAAAAPssLvABf4DmEfxX5RLwb04bIRHw";
    $secretkey = "6Le3IEYUAAAAAE47DgddVeqGkccpLkbh-7fnFYs9";
    
    if(isset($_SERVER['HTTP_REFERER'])) {
      if(!isset($_POST['login'])) {
        if(strstr($_SERVER['HTTP_REFERER'], "confirm-account")) $_SESSION['redirect_link'] = "/index.php";
        else $_SESSION['redirect_link'] = $_SERVER['HTTP_REFERER'];
      }
    }
    else {
      $_SESSION['redirect_link'] = $_SERVER['PHP_SELF'];
    }
    
    if(isset($_POST['login'])) {

      $recaptcha_response = false;
      if(isset($_POST['g-recaptcha-response'])) {
        $g_recaptcha_response = $_POST['g-recaptcha-response'];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array('secret' => "$secretkey", 'response' => $g_recaptcha_response);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context));

        $recaptcha_response = $result->success;
        if(!$recaptcha_response) {
          $errors['recaptcha_response_field'] = $languages['error_create_customer_recaptcha'];  
        }
      }

      if($recaptcha_response) {

        $customer_password = $_POST['customer_password'];
        $customer_email = $_POST['customer_email'];
        $user_is_active = true;

        $query_user_is_active = "SELECT `customer_id` 
                                   FROM `customers` 
                                  WHERE `customer_email` = '$customer_email' AND `customers`.`customer_is_active` = '0'";
        //echo $query_user_is_active."<br>";
        $result_user = mysqli_query($db_link,$query_user_is_active);
        if(!$result_user) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_user) > 0) {
          $user_is_active = false;
        }
        mysqli_free_result($result_user);

        if($user_is_active) {
          $query_user = "SELECT `customers`.`customer_id`,`customers`.`customer_salted_password`,`customers`.`customer_image`,`customers_groups`.`customer_group_code`
                           FROM `customers` 
                     INNER JOIN `customers_groups` USING(`customer_group_id`)
                          WHERE `customer_email` = '$customer_email' AND `customers`.`customer_is_active` = '1' AND `customers`.`customer_is_blocked` = '0'";
          //echo $query_user."<br>";
          $result_user = mysqli_query($db_link,$query_user);
          if (!$result_user) echo mysqli_error($db_link);
          if(mysqli_num_rows($result_user) > 0) {
            $customer = mysqli_fetch_assoc($result_user);

            $customer_id = $customer['customer_id'];
            $customer_group_code = $customer['customer_group_code'];
            $password_hash = $customer['customer_salted_password'];
            $customer_image = $customer['customer_image'];
            $customer_ip = get_client_ip_env();

            if(password_verify($customer_password, $password_hash)) {
              // password is correct

              if(isset($_POST['remember_me'])) {
                setcookie("login", $customer_id, time()+60*60*24*30, "/", DOMAIN, $secure = false, $httponly = true);
                //echo "remember_me on ".DOMAIN;exit;
              }

              $customer_group_table = "`customers_$customer_group_code`";
              $query_customer_group = "SELECT $customer_group_table.* FROM $customer_group_table WHERE `customer_id` = '$customer_id'";
              //echo $query_customer_group;
              $result_customer_group = mysqli_query($db_link,$query_customer_group);
              if (!$result_customer_group) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_customer_group) > 0) {

                $customer_group = mysqli_fetch_assoc($result_customer_group);
                $customer_firstname = $customer_group['first_name'];
                $customer_lastname = $customer_group['last_name'];

              }

              //make record for table users_log
              $query = "INSERT INTO `customers_logs`(`customer_log_id`, 
                                                    `customer_id`,
                                                    `customer_ip`, 
                                                    `customer_log_date`)
                                            VALUES (NULL,
                                                    '$customer_id',
                                                    '$customer_ip',
                                                    NOW())";
              $result = mysqli_query($db_link, $query);
              if (!$result) echo mysqli_error($db_link);

              $_SESSION['customer_id'] = $customer_id;
              $_SESSION['customer_group_code'] = $customer_group_code;
              $_SESSION['customer_name'] = "$customer_firstname $customer_lastname";
              $_SESSION['customer_image'] = $customer_image;
              $_SESSION[$customer_group_code] = $customer_group;
              $redirect_link = $_SESSION['redirect_link'];
              unset($_SESSION['redirect_link']);
              ?>
                <script>window.location.href="<?=$redirect_link;?>"</script>
              <?php
            }
            else {
              $_SESSION['error_login']['text'] = $languages['error_login'];
            }

            mysqli_free_result($result_user);
          } // if(mysqli_num_rows($result_user) > 0)
          else {
            $_SESSION['error_login']['text'] = $languages['error_login'];
          }
        } //if($user_is_active)
        else {
          $_SESSION['error_login']['text'] = $languages['error_profile_not_active'];
        }
      } //if($recaptcha_response) 
    }
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript, $body_css);
?>
    <div class="breadcrumb-wrapper">
      <div class="container">
        <ol class="breadcrumb-list">
          <?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_registered_users']) ?>
        </ol>
      </div>
    </div>

    <div class="login-container-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <div class="row">
              <div class="col-sm-6 col-sm-offset-3">
                <div class="login-box-wrapper">
                  <?php include_once "$current_page_pretty_url.php"; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "confirm-account") {
    //print_array_for_debug($page_path_array);
    $customer_id = intval($page_params[0]);
    
    if(!empty($customer_id)) {

      $body_css = "not-transparent-header confirm-account";
      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords,$additional_css_javascript = false, $body_css);
?>
    <div class="breadcrumb-wrapper">
      <div class="container">
        <ol class="breadcrumb-list">
          <?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_registration_confirm_account']) ?>
        </ol>
      </div>
    </div>

    <div class="section">
      <div class="container">
        <?php include_once "$current_page_pretty_url.php"; ?>
      </div>
    </div>
<?php
    }
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "forgotten-password") {
    
    $current_page_text = "header_".str_replace("-", "_", $current_page_pretty_url);
    $content_menu_text = $languages[$current_page_text];
    $body_css = "not-transparent-header my-account";
    print_html_header($content_meta_title = $content_menu_text, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css);
?>
    <div class="breadcrumb-wrapper">
      <div class="container">
        <ol class="breadcrumb-list">
          <?php print_content_breadcrumbs($content_hierarchy_ids, $content_menu_text) ?>
        </ol>
      </div>
    </div>
    
    <div class="login-container-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-md-10 col-md-offset-1">
            <div class="row">
              <div class="col-sm-6 col-sm-offset-3">
                <div class="login-box-wrapper">
                  <?php include_once "$current_page_pretty_url.php"; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "search") {
    
    $body_css = "not-transparent-header search";
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css);
?>
    <div class="breadcrumb-wrapper">
      <div class="container">
        <ol class="breadcrumb-list">
          <?php print_content_breadcrumbs($content_hierarchy_ids, $languages['header_search']) ?>
        </ol>
      </div>
    </div>

    <div class="section">
      <div class="container">
        <?php include_once 'search.php'; ?>
      </div>
    </div>
<?php
  }
  elseif(isset($current_page_pretty_url) && isset($_SESSION['customer_group_code']) && $current_page_pretty_url == $_SESSION['customer_group_code']) {
    
    setcookie("login", $_SESSION['customer_id'], time()+60*60*24*30, "/", DOMAIN, $secure = false, $httponly = true);
    
    $body_css = "not-transparent-header my-account";
    $additional_css_javascript = '<script type="text/javascript">
                                    var text_upload_files = "'.$languages['text_upload_files'].'"
                                    var text_clear_files = "'.$languages['text_clear_files'].'"
                                    var text_upload_photo = "'.$languages['text_upload_photo'].'"
                                    var btn_upload = "'.$languages['btn_upload'].'"
                                    var btn_remove = "'.$languages['btn_remove'].'"
                                    var btn_browse = "'.$languages['btn_browse'].'"
                                    var btn_cancel = "'.$languages['btn_cancel'].'"
                                  </script>
                                  <script type="text/javascript" src="'.SITEFOLDERSL.'/js/fileinput.min.js"></script>
                                  <script type="text/javascript" src="'.SITEFOLDERSL.'/js/customs-fileinput.js"></script>';
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript, $body_css);
    
    $current_page_text = "header_".str_replace("-", "_", $page_params);
    $content_menu_text = $languages[$current_page_text];
?>
    <div class="breadcrumb-wrapper">
      <div class="container">
        <ol class="breadcrumb-list">
          <?php print_content_breadcrumbs($content_hierarchy_ids, $content_menu_text) ?>
        </ol>
      </div>
    </div>

    <div class="admin-container-wrapper">
      <div class="container">
        <div class="GridLex-gap-15-wrappper">
          <div class="GridLex-grid-noGutter-equalHeight">
<?php 
//print_array_for_debug($_SESSION);
  if(user_is_loged()) {

    $function_name = "print_html_".$_SESSION['customer_group_code']."_profile_menu";
    echo $function_name(); 

  }
?>
            <div class="GridLex-col-9_sm-8_xs-12">
              <div class="admin-content-wrapper">
                
                <div class="admin-section-title">

                  <h2><?=$content_menu_text;?></h2>

                </div>
                <?php 
                  if(!user_is_loged()) {
                    echo "<h1>".$languages['error_secured']."</h1>";
                  }
                  else {
                    include_once "$current_page_pretty_url/$page_params.php"; 
                  }
                ?>
              </div>
            </div>
            
          </div>
        </div>
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

      $query_content = "SELECT `contents`.`content_id`,`contents`.`content_hierarchy_ids`,`contents_descriptions`.`content_name`,`contents_types`.`content_type`,
                               `contents_descriptions`.`content_menu_text`,`contents_descriptions`.`content_meta_title`,`contents_descriptions`.`content_meta_keywords`,
                               `contents_descriptions`.`content_meta_description`,`contents_descriptions`.`content_summary`,`contents_descriptions`.`content_text`
                          FROM `contents`
                    INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                    INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                         WHERE `contents_types`.`content_type` = 'error_page' AND `contents_descriptions`.`language_id` = '$current_language_id'";
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
        
        $body_css = "not-transparent-header error";
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false,$body_css);
?>
        <div class="breadcrumb-wrapper">
          <div class="container">
            <ol class="breadcrumb-list">
              <?php print_content_breadcrumbs($content_hierarchy_ids, $content_menu_text) ?>
            </ol>
          </div>
        </div>

        <div class="error-page-wrapper">
          <div class="container">
            <div class="row">

              <div class="col-sm-10 col-md-8 col-sm-offset-1 col-md-offset-2">

                <div class="error-404">404</div>

                <h3><?=$languages['header_page_not_found'];?></h3>

                <p><?=$content_text;?></p>


                <a href="<?=$home_page_url;?>" class="btn btn-primary mt-15"><?= $languages['title_goto_homepage']; ?></a>

              </div>

            </div>
          </div>
        </div>
<?php
      }
      elseif($content_type == "news") {
        
        $news_category_id = 1; // all news
        $body_css = "not-transparent-header news";

        $page_array = get_page_by_type("news");
        $current_content_menu_text = stripslashes($page_array['content_menu_text']);
        $content_meta_title = stripslashes($page_array['content_meta_title']);
        $content_meta_keywords = stripslashes($page_array['content_meta_keywords']);
        $content_meta_description = stripslashes($page_array['content_meta_description']);
      
        $body_css = "not-transparent-header news";
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css);

?>
      <div class="breadcrumb-wrapper">
        <div class="container">
          <ol class="breadcrumb-list">
            <li><a href="<?=$home_page_url;?>" title="<?= $languages['title_goto_homepage']; ?>"><?= $languages['menu_home']; ?></a></li>
            <li><span><?=$current_content_menu_text;?></span></li>
          </ol>
        </div>
      </div>

      <div class="section sm">
        <div class="container">
          <div class="row">

            <div class="col-sm-8 col-md-9">
              <div class="blog-wrapper">
              <?php 
                list_news($offset = false,$news_count = false, $news_category_id); 
              ?>
              </div>
            </div>

            <div class="col-sm-4 col-md-3 mt-50-xs">
              <aside class="sidebar">
                <div class="sidebar-inner no-border for-blog">
                  
                  <?php print_html_news_sidebar($print_latest_news = true);?>
                  
                </div>
              </aside>
            </div>
            
          </div>
        </div>
      </div>
<?php
      }
      elseif($content_type == "contacts") {

      $body_css = "not-transparent-header contacts";

      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false,$body_css);
      //echo "<pre>";print_r($_SERVER);echo "</pre>";
?>
      <div class="breadcrumb-wrapper">
        <div class="container">
          <ol class="breadcrumb-list">
            <?php print_content_breadcrumbs($content_hierarchy_ids, $content_menu_text) ?>
          </ol>
        </div>
      </div>

      <?php include_once 'contacts-form.php'; ?>
<?php
      }
      else {
        
        $body_css = "not-transparent-header pages";
        print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false,$body_css);
?>
        <div class="breadcrumb-wrapper">
          <div class="container">
            <ol class="breadcrumb-list">
              <?php print_content_breadcrumbs($content_hierarchy_ids, $content_menu_text) ?>
            </ol>
          </div>
        </div>

        <div class="section sm pb-20">
          <div class="container">
            
            <div class="row">
              <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                <div class="section-title">

                  <h2><?=$content_name;?></h2>
                  <p><?=$content_summary;?></p>

                </div>
              </div>
            </div>
            
            <?=$content_text?>
          </div>
        </div>
<?php
      }
    } // else of if($content_is_home_page == 1)
  }
 
  print_html_footer();