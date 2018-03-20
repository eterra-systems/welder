<?php 
function print_html_header($meta_title, $meta_description, $meta_keywords, $additional_css_javascript = false, $body_css = false) {

  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $canonical_link;
  global $home_page_url;
  global $current_page_path_string;
  global $fb_image;
  global $fb_width;
  global $fb_height;
  global $current_content_id;

  $logo_path = SITEFOLDERSL."/images/logo.png";
  @list($logo_width,$logo_height) = getimagesize($_SERVER['DOCUMENT_ROOT'].$logo_path);
  $meta_title = (!empty($meta_title)) ? $meta_title : $languages['default_meta_title'];
  $meta_description = (!empty($meta_description)) ? $meta_description : $languages['default_meta_description'];
  $meta_keywords = (!empty($meta_keywords)) ? $meta_keywords : $languages['default_meta_keywords'];
  if(!isset($fb_image)) {
    $fb_image = $logo_path;
    @list($fb_width,$fb_height) = getimagesize($_SERVER['DOCUMENT_ROOT'].$fb_image);
  }
  
  //unset($_COOKIE['cookie_policy']);
  //setcookie('cookie_policy', null, -1);
  //echo "<pre>";print_r($_COOKIE);echo "</pre>";
  //unset($_SESSION);
  //session_destroy();

  if(!$body_css) $body_css = "home";
 ?>
<!DOCTYPE html>
<html dir="ltr" lang="<?=$current_lang;?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <!-- Title Of Site -->
    <title><?=strip_tags($meta_title);?></title>
    <meta name="description" content="<?=strip_tags($meta_description);?>">
    <meta name="keywords" content="<?=strip_tags($meta_keywords);?>" >
    <meta name="author" content="Eterrasystems Ltd.">
    
    <!-- Fav and Touch Icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=SITEFOLDERSL;?>/images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=SITEFOLDERSL;?>/images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=SITEFOLDERSL;?>/images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?=SITEFOLDERSL;?>/images/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="<?=SITEFOLDERSL;?>/images/ico/favicon.png">
    
    <meta name="robots" content="index, follow">
    <meta property="og:site_name" content="<?=$languages['e_shop_cms'];?>">
    <meta property="og:locale" content="bg_BG">
    <meta property="fb:app_id" content="124430207960952">
    <meta property="og:url" content="<?=urldecode(PROTOCOL.DOMAIN.$_SERVER['REQUEST_URI']);?>" >
    <meta property="og:type" content="article" >
    <meta property="og:title" content="<?=strip_tags($meta_title);?>" >
    <meta property="og:description" content="<?=strip_tags($meta_description);?>" >
    <meta property="og:image" content="<?=PROTOCOL.DOMAIN.$fb_image;?>" >
    <meta property="og:image:width" content="<?=$fb_width;?>" >
    <meta property="og:image:height" content="<?=$fb_height;?>" >
<?php if(isset($canonical_link) && !empty($canonical_link)) { echo "$canonical_link \n"; } ?>
    
    <!-- CSS Plugins -->
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/bootstrap/css/bootstrap.min.css" media="screen">	
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/css/animate.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/css/component.css">

    <!-- CSS Font Icons -->
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/linearicons/style.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/ionicons/css/ionicons.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/pe-icon-7-stroke/css/pe-icon-7-stroke.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/rivolicons/style.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/flaticon-line-icon-set/flaticon-line-icon-set.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/flaticon-streamline-outline/flaticon-streamline-outline.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/flaticon-thick-icons/flaticon-thick.css">
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/icons/flaticon-ventures/flaticon-ventures.css">
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui-1.12.1/jquery-ui.min.css">

    <!-- CSS Custom -->
    <link rel="stylesheet" type="text/css" href="<?=SITEFOLDERSL;?>/css/style.css">
    
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery-migrate-1.2.1.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
      var sitefolder = '<?=SITEFOLDER;?>';
    </script>
    <?= $additional_css_javascript; ?>
  </head>

  <body id="<?= $body_css; ?>" class="<?= $body_css; ?>">
<?php
  if(!isset($_COOKIE['cookie_policy'])) {
?>
    <div id="cookies_policy">
      <a href="javascript:;" onclick="ConfirmCookiesPolicy()" class="pull-right btn"><?=$languages['btn_accept_cookie_policy'];?></a>
      <p class="no_margin"><?=$languages['text_cookie_policy'];?>
        <a href="/<?=$current_lang;?>/<?php if($current_lang == "bg") echo "политика-за-бисквитките";else echo "cookie-policy"; ?>" target="_blank">
          <?=$languages['link_cookie_policy'];?>
        </a>
      </p>
    </div>
<?php } ?>

    <div id="modal_window_backgr"></div>
    <div id="modal_window"></div>
    <div id="ajax_loader_backgr"></div>
    <div id="ajax_loader">
      <div class="sk-cube-grid">
        <div class="sk-cube sk-cube1"></div>
        <div class="sk-cube sk-cube2"></div>
        <div class="sk-cube sk-cube3"></div>
        <div class="sk-cube sk-cube4"></div>
        <div class="sk-cube sk-cube5"></div>
        <div class="sk-cube sk-cube6"></div>
        <div class="sk-cube sk-cube7"></div>
        <div class="sk-cube sk-cube8"></div>
        <div class="sk-cube sk-cube9"></div>
      </div>
    </div>
    <input type="hidden" class="language_id" value="<?=$current_language_id;?>" >
    <input type="hidden" class="current_lang" value="<?=$current_lang;?>" >
    <input type="hidden" class="base_url" value="<?=PROTOCOL.DOMAIN;?>/" >
<?php
    if(!isset($_SESSION['contact_address'])) {
      $default_contact_array = get_default_contact();
      //print_r($default_contact_array);
      @$_SESSION['contact_city'] = $default_contact_array['contact_city'];
      @$_SESSION['contact_postcode'] = $default_contact_array['contact_postcode'];
      @$_SESSION['contact_address'] = $default_contact_array['contact_address'];
      @$_SESSION['contact_email'] = $default_contact_array['contact_email'];
      @$_SESSION['contact_home_phone'] = $default_contact_array['contact_home_phone'];
      @$_SESSION['contact_mobile_phones'] = $default_contact_array['contact_mobile_phones'];
    }
?>
    <div class="container-wrapper">

      <header id="header">

        <nav class="navbar navbar-default navbar-fixed-top navbar-sticky-function">

          <div class="container">

            <div class="logo-wrapper">
              <div class="logo">
                <a href="<?=$home_page_url;?>" title="<?=$languages['company_logo_text'];?>">
                  <img src="<?=SITEFOLDERSL;?>/images/logo.png" width="156" height="43" alt="<?=$languages['company_logo_text'];?>" />
                </a>
              </div>
            </div>

            <div id="navbar" class="navbar-nav-wrapper navbar-arrow">

              <ul class="nav navbar-nav" id="responsive-menu">
                <?php print_header_menu($category_parent_id = 0, $content_hierarchy_level_start = 0, $number_of_hierarchy_levels = 3); ?>
              </ul>

            </div><!--/.nav-collapse -->
            
            <div class="nav-mini-wrapper">
              <ul class="nav-mini sign-in">
              <?php if (isset($_SESSION['customer_id'])) { ?>
                <li>
                  <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-data" rel="nofollow"><?= $languages['customer_profile']; ?></a>
                </li>
              <?php } else { ?>
                <li><a href="/<?= $current_lang; ?>/login" rel="nofollow"><?= $languages['login_sign_in']; ?></a></li>
                <li><a href="/<?= $current_lang; ?>/registration" rel="nofollow"><?= $languages['login_sign_up']; ?></a></li>
              <?php } ?>
              </ul>
            </div>

          </div>

          <div id="slicknav-mobile"></div>

        </nav>
        <!-- end Navbar (Header) -->
        
        <input type="hidden" name="current_page_path_string" id="current_page_path_string" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="current_lang" id="current_lang" value="<?= $current_lang; ?>">
      </header>
    <div class="main-wrapper">
<?php
//    echo"<pre>";print_r($_SESSION);echo"</pre>";
}

function print_header_menu($content_parent_id, $content_hierarchy_level_start , $number_of_hierarchy_levels) {
    
  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from site/index.php or site/categories.php

  //echo $content_hierarchy_ids;
  if(strstr($content_hierarchy_ids, ".")) {
    $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  } else {
    $content_hierarchy_ids_array[0] = $content_hierarchy_ids;
  }
    
  $content_hierarchy_level_in_query = "";

  if($content_hierarchy_level_start == 0) {
    if($number_of_hierarchy_levels != 0) $content_hierarchy_level_in_query = " AND `contents`.`content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `contents`.`content_id`,`contents`.`content_type_id`,`contents`.`content_has_children`,`contents`.`content_hierarchy_level`,
                           `contents`.`content_menu_order`,`contents`.`content_target`,`contents_descriptions`.`content_menu_text`,
                           `contents_descriptions`.`content_pretty_url`,`contents_descriptions`.`content_text` 
                      FROM `contents`
                INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                     WHERE `contents`.`content_parent_id` = '$content_parent_id' $content_hierarchy_level_in_query
                       AND `contents`.`content_is_active` = '1' AND `contents`.`content_show_in_menu` = '1' 
                       AND `contents_descriptions`.`content_desc_is_active` = '1' AND `contents_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `content_menu_order` ASC";
  //echo "$query_content<br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_pretty_url = $content_row['content_pretty_url'];
          break;
        case 2:
          $content_pretty_url = $content_row['content_pretty_url'];
          break;
        case 4:
          $content_pretty_url = $content_text;
          break;
        default: $content_pretty_url = $content_row['content_pretty_url'];
          break;
      }
      switch($content_hierarchy_level) {
        case 1:
          $a_class = "main-menu";
          break;
        case 2:
          $a_class = "link-page";
          break;
        case 3:
          $a_class = "link-page";
          break;
        default: $a_class = "link-page";
          break;
      }
      $class_active = "";
      if(in_array($content_id,$content_hierarchy_ids_array)) $class_active = ' current-menu-item';
      

      $content_has_active_children = check_if_content_has_active_children($content_id);
      $content_is_last_child = check_if_this_is_content_last_child($content_parent_id,$content_menu_order);
      $url = "/$current_lang/$content_pretty_url";

      if($content_has_children == 1 && $content_hierarchy_level < $number_of_hierarchy_levels && $content_has_active_children) {
        $dropdown_arrow = ($content_hierarchy_level == 1) ? '<b class="caret"></b>' : "";
?>
      <li class="menu-item-has-children current_page_item<?=$class_active;?>">
        <a href="<?=$url;?>" class="<?=$a_class;?>">
          <?="$content_menu_text";?>
        </a>
        <?php //if($is_mobile) echo $down_arrow; ?>
        <ul class="level-<?=$content_hierarchy_level;?>">
<?php
        print_header_menu($content_id, $content_hierarchy_level_start = 0, $number_of_hierarchy_levels);
      }
      else {
?>
      <li class="<?=$class_active;?>">
        <a href="<?=$url;?>" class="<?="$a_class$class_active";?>" <?=$content_target;?>><?="$content_menu_text";?></a>
      </li>
<?php
      }

      if($content_hierarchy_level > 1 && $content_is_last_child) {
?>
        </ul>
      </li>
<?php
      }
    }
    mysqli_free_result($result_content);
  }
}

function print_html_company_profile_menu() {
    global $languages;
    global $current_lang;
?>
  <li<?php if (is_active_page("user-profile-data")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-data" rel="nofollow">
      <i class="fa fa-user" aria-hidden="true"></i>
      <span><?= $languages['header_user_data']; ?></span>
    </a>
  </li>
  <li<?php if (is_active_page("user-profile-addresses") || is_active_page("user-profile-address-add") || is_active_page("user-profile-address-edit")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-addresses" rel="nofollow">
      <i class="fa fa-truck" aria-hidden="true"></i>
      <span><?= $languages['header_delivery_addresses']; ?></span>
    </a>
  </li>
<?php
}

function print_html_welder_profile_menu() {
    global $languages;
    global $current_lang;
?>
  <li<?php if (is_active_page("user-profile-dashboard")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-dashboard" rel="nofollow">
      <i class="fa fa-tachometer" aria-hidden="true"></i>
      <span><?= $languages['header_user_dashboard']; ?></span>
    </a>
  </li>
  <li<?php if (is_active_page("user-profile-data")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-data" rel="nofollow">
      <i class="fa fa-user" aria-hidden="true"></i>
      <span><?= $languages['header_user_data']; ?></span>
    </a>
  </li>
  <li<?php if (is_active_page("user-profile-password")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-password" rel="nofollow">
      <i class="fa fa-key" aria-hidden="true"></i>
      <span><?= $languages['header_customer_new_password']; ?></span>
    </a>
  </li>
  <li<?php if (is_active_page("user-profile-skills")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-skills" rel="nofollow">
      <i class="fa fa-wrench" aria-hidden="true"></i>
      <span><?= $languages['header_skills']; ?></span>
    </a>
  </li>
  <li<?php if (is_active_page("user-profile-certificates")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-certificates" rel="nofollow">
      <i class="fa fa-newspaper-o" aria-hidden="true"></i>
      <span><?= $languages['header_certificates']; ?></span>
    </a>
  </li>
  <li<?php if (is_active_page("user-profile-bookmarks")) echo ' class="active"'; ?>>
    <a href="/<?= $current_lang; ?>/<?=$_SESSION['customer_group_code'];?>/user-profile-bookmarks" rel="nofollow">
      <i class="fa fa-bookmark"></i>
      <span><?= $languages['header_saved_jobs']; ?></span>
    </a>
  </li>
  <li>
    <a href="/<?= $current_lang; ?>/logout" rel="nofollow">
      <i class="fa fa-sign-out"></i>
      <?= $languages['logout']; ?>
    </a>
  </li>
<?php
}

function print_index_sliders($count) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $content_hierarchy_ids; //coming from site/index.php or site/categories.php

  $limit = ($count == 0) ? "" : "LIMIT $count";

  $query_sliders = "SELECT `sliders`.`slider_id`,`sliders`.`slider_image`,`sliders`.`slider_sort_order`,
                           `sliders_descriptions`.`slider_header`,`sliders_descriptions`.`slider_text` ,`sliders_descriptions`.`slider_link`
                      FROM `sliders`
                INNER JOIN `sliders_descriptions` ON `sliders_descriptions`.`slider_id` = `sliders`.`slider_id`
                     WHERE `sliders`.`slider_is_active` = '1' AND `sliders_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `slider_sort_order` ASC $limit";
  //echo $query_sliders;exit;
  $result_sliders = mysqli_query($db_link, $query_sliders);
  if(!$result_sliders) echo mysqli_error($db_link);
  $sliders_count = mysqli_num_rows($result_sliders);
  if($sliders_count > 0) {
?>
    <!-- BEGIN .slider-wrapper -->
    <div class="slider-wrapper">

      <div class="slider-navigation">
        <a class="arrow-left" href="javascript:;"></a> 
        <a class="arrow-right" href="javascript:;"></a>
      </div>

      <div class="swiper-container">
        <div class="swiper-wrapper">
<?php
      while($slider_row = mysqli_fetch_assoc($result_sliders)) {

        $slider_id = $slider_row['slider_id'];
        $slider_header = $slider_row['slider_header'];
        $slider_text = $slider_row['slider_text'];
        $slider_link = (empty($slider_row['slider_link']) || is_null($slider_row['slider_link'])) ? "javascript:;" : $slider_row['slider_link'];
        $slider_image_orig = $slider_row['slider_image'];
        $slider_image_exploded = explode(".", $slider_image_orig);
        $slider_image_name = $slider_image_exploded[0];
        @$slider_image_exstension = $slider_image_exploded[1];
        $slider_image_site = $slider_image_name."_site.".$slider_image_exstension;
        $slider_image = SITEFOLDERSL."/images/sliders/$slider_image_site";
        @$slider_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_image);
        $slider_image_dimensions = $slider_image_params[3];
?>
        <div class="swiper-slide" style="background: url('<?=$slider_image;?>');" >
          <div class="slider-caption-wrapper">
<?php
        if((!empty($slider_header) && !is_null($slider_header)) || (!empty($slider_text) && !is_null($slider_text))) {
?>
            <div class="slider-caption">
              <p class="colour-caption1 large-caption"><?=$slider_header?></p>
              <div class="clearboth"></div>
              <p class="colour-caption2 large-caption"><?=$slider_text?></p>
              <a href="<?=$slider_link;?>" class="button"><?=$languages['btn_read_more'];?></a>
            </div>
<?php
        }
?>
          </div>
        </div>
<?php
      } //while($slider_row)
      mysqli_free_result($result_sliders);
?>
        </div>
      </div>

      <div class="pagination"></div>

    </div><!-- END .slider-wrapper -->
<?php
  } //if(mysqli_num_rows($result_sliders) > 0)
}

function list_categories_with_checkboxes($category_parent_id,$category_root_id,$category_ids_tree) {

  global $db_link;
  global $current_language_id;
  global $current_lang;

  $and_root_id = ($category_root_id == 0) ? "" : " AND `ctc`.`category_root_id` = '$category_root_id'";
  $query_categories = "SELECT `categories`.`category_id`,`ctc`.`category_root_id`,`ctc`.`category_hierarchy_level`,
                              `ctc`.`category_hierarchy_ids`,`ctc`.`category_sort_order`,`ctc`.`category_has_children`,`cd`.`cd_name`
                         FROM `categories`
                   INNER JOIN `category_to_category` as `ctc` USING(`category_id`)
                   INNER JOIN `categories_descriptions` as `cd` USING(`category_id`)
                        WHERE `ctc`.`category_parent_id` = '$category_parent_id' $and_root_id
                          AND `cd`.`language_id` = '$current_language_id'
                     ORDER BY `ctc`.`category_sort_order` ASC";
  //echo $query_categories;exit;
  $result_categories = mysqli_query($db_link, $query_categories);
  if (!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if ($category_count > 0) {

    while ($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id'];
      $category_root_id = $category_row['category_root_id'];
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_hierarchy_ids = $category_row['category_hierarchy_ids'];
      $category_id_tree = str_replace(".", "", $category_hierarchy_ids);
      $category_sort_order = $category_row['category_sort_order'];
      $category_has_children = $category_row['category_has_children'];
      $cd_name = $category_row['cd_name'];

      $class_li = "";
      $class_label = "";
      $label_title = "";
      if($category_hierarchy_level == 1) {
        $class_label = 'class="btn btn-default"';
        $label_title = ' title="Кликнете ако искате да изберете цялото дърво надолу"';
        $label_title = "";
      }
      if($category_has_children == 1) {
        $class_li = "expandable";
      }
      $category_is_last_child = false;
      if($category_hierarchy_level > 1) { $category_is_last_child = check_if_this_is_category_last_child($category_root_id, $category_parent_id, $category_sort_order); }
      if(is_array($category_ids_tree)) {
        if(in_array($category_id_tree, $category_ids_tree)) {
          $checkbox_checked = "checked='checked'";
        }
        else {
          $checkbox_checked = "";
        }
      }
      else {
        if($category_ids_tree == $category_id_tree) {
          $checkbox_checked = "checked='checked'";
        }
        else {
          $checkbox_checked = "";
        }
      }

      if ($category_has_children == 1) {
?>
      <li id="<?=$category_id_tree;?>" data-level="<?= $category_hierarchy_level; ?>" class="level_<?= "$category_hierarchy_level $class_li"; ?> col-lg-8 col-md-8 col-sm-12 col-xs-12">
        <label for="<?=$category_id_tree;?>" class="btn btn-default" <?=$label_title?> data-root="<?=$category_root_id;?>">
          <?php if($category_hierarchy_level != 1 && false) { ?>
          <input type="checkbox" value="<?=$category_id_tree;?>" id="<?=$category_id_tree;?>" class="categories_<?=$category_root_id;?>" name="categories[]" <?=$checkbox_checked;?> />
          <input type="hidden" value="<?=$category_id;?>" name="category_ids[<?=$category_id_tree;?>]" />
          <input type="hidden" value="<?=$category_hierarchy_ids;?>" name="category_hierarchy_ids[<?=$category_id_tree;?>]" />
          <?php } ?>
          <?=$cd_name;?>
          <i class="fa fa-lg fa-angle-down" aria-hidden="true"></i>
          <i class="fa fa-lg fa-angle-up" aria-hidden="true"></i>
        </label>
        <ul class="expandable_ul expandable_ul_<?=$category_id_tree;?> ul_level_<?= $category_hierarchy_level; ?>">
<?php
          list_categories_with_checkboxes($category_id,$category_root_id,$category_ids_tree);
      } else {
?>
      <li class="level_<?= "$category_hierarchy_level $class_li"; ?>">
        <input type="checkbox" value="<?=$category_id_tree;?>" id="<?=$category_id_tree;?>" class="categories_<?=$category_root_id;?>" name="categories[]" <?=$checkbox_checked;?> />
        <label for="<?=$category_id_tree;?>">
          <input type="hidden" value="<?=$category_id;?>" name="category_ids[<?=$category_id_tree;?>]" />
          <input type="hidden" value="<?=$category_hierarchy_ids;?>" name="category_hierarchy_ids[<?=$category_id_tree;?>]" />
          <?=$cd_name;?>
        </label>
      </li>
<?php
      }
      if ($category_hierarchy_level > 1 && $category_is_last_child) {
?>
        </ul>
      </li>
<?php
      }
    } //while ($category_row = mysqli_fetch_assoc($result_categories))
    
    mysqli_free_result($result_categories);
  }
}

function list_contacts() {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  
  $query_contacts = "SELECT `contacts`.`contact_id`,`contacts`.`contact_is_active`,`contacts`.`contact_sort_order`,`contacts`.`contact_is_default`,
                            `contacts_descriptions`.`contact_city`,`contacts_descriptions`.`contact_postcode`,`contacts_descriptions`.`contact_address`,
                            `contacts_descriptions`.`contact_info`
                       FROM `contacts`
                 INNER JOIN `contacts_descriptions` ON `contacts_descriptions`.`contact_id` = `contacts`.`contact_id`
                      WHERE `contacts_descriptions`.`language_id` = '$current_language_id'
                   ORDER BY `contact_sort_order` ASC";
  //echo $query_contacts;exit;
  $result_contacts = mysqli_query($db_link, $query_contacts);
  if(!$result_contacts) echo mysqli_error($db_link);
  $contacts_count = mysqli_num_rows($result_contacts);
  if($contacts_count > 0) {
    
    while($contact_row = mysqli_fetch_assoc($result_contacts)) {
      
      $contact_id = $contact_row['contact_id'];
      $contact_city = $contact_row['contact_city'];
      $contact_address = stripslashes($contact_row['contact_address']);
      $contact_postcode = $contact_row['contact_postcode'];
      $contact_info = stripslashes($contact_row['contact_info']);
      $contact_address .= (!empty($contact_info)) ? " ($contact_info)" : "";
      $contact_address .= ", $contact_postcode $contact_city";
      $contact_is_active = $contact_row['contact_is_active'];
      $contact_is_default = $contact_row['contact_is_default'];
      $contact_sort_order = $contact_row['contact_sort_order'];
?>
      <div class="col-md-12">
        <!--<div id="map-canvas-sofia" class="map-canvas"></div>-->
        <div class="kf_with_us_addres">
          <i class="fa fa-map-marker" aria-hidden="true"></i>
          <h2><?=$languages['header_address'];?></h2>
          <div class="kf_with_us_p">
            <p><span><?=$contact_city;?></span> 
              <?=$contact_address;?>
            </p>
          </div>
        </div>
      </div>
<?php
    }
    mysqli_free_result($result_contacts);
  }
}

function list_contacts_socials() {
  
  global $db_link;
  
  $query_contact_socials = "SELECT `contacts_socials`.`contact_social_address`,`contacts_socials`.`contact_social_icon`,`social_networks`.`social_network_icon`
                              FROM `contacts_socials`
                        INNER JOIN `social_networks` ON `social_networks`.`social_network_id` = `contacts_socials`.`social_network_id`
                             WHERE `contacts_socials`.`contact_social_is_active` = '1'
                          ORDER BY `contacts_socials`.`contact_social_sort_order` ASC";
  //echo $query_contact_socials;exit;
  $result_contact_socials = mysqli_query($db_link, $query_contact_socials);
  if(!$result_contact_socials) echo mysqli_error($db_link);
  $contact_socials_count = mysqli_num_rows($result_contact_socials);
  if($contact_socials_count > 0) {
    while($contact_social_row = mysqli_fetch_assoc($result_contact_socials)) {
      
      $contact_social_address = $contact_social_row['contact_social_address'];
      $contact_social_icon = $contact_social_row['contact_social_icon'];
      $social_network_icon = $contact_social_row['social_network_icon'];
?>
      <li><a href="<?=$contact_social_address;?>" target="_blank"><?=$social_network_icon;?></a></li>
<?php
    }
    mysqli_free_result($result_contact_socials);
  }
}

function print_content_breadcrumbs($content_hierarchy_ids, $current_content_name) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $home_page_url;

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  //print_array_for_debug($content_hierarchy_ids_array);
  $ids_count = count($content_hierarchy_ids_array);

  if ($ids_count > 2) {
?>
    <li><a href="<?=$home_page_url;?>" title="<?= $languages['title_goto_homepage']; ?>"><?= $languages['menu_home']; ?></a></li>
<?php
    foreach ($content_hierarchy_ids_array as $key => $content_id) {

      if ($key != 0 && $key != ($ids_count - 1)) {
        $query_content = "SELECT `content_type_id`,`content_menu_text`,`content_pretty_url`
                            FROM `contents`
                           WHERE `content_id` = '$content_id' AND `content_show_in_menu` = '1'";
        //echo "<br>$query_content<br>";
        $result_content = mysqli_query($db_link, $query_content);
        if (!$result_content)
          echo mysqli_error($db_link);
        if (mysqli_num_rows($result_content) > 0) {
          $content_row = mysqli_fetch_assoc($result_content);

          $content_type_id = $content_row['content_type_id'];
          $content_menu_text = stripslashes($content_row['content_menu_text']);

          switch ($content_type_id) {
            case 1:
              $content_pretty_url = $content_row['content_pretty_url'];
              break;
            case 2:
              $content_pretty_url = "javascript:;";
              break;
            case 4:
              $content_pretty_url = $content_text;
              break;
            default: $content_pretty_url = $content_row['content_pretty_url'];
              break;
          }
          if ($category_is_section_header == 0) { 
?>
          <li><a href="<?= "/$current_lang/$content_pretty_url" ?>" title="<?= $content_menu_text; ?>" ><?= $content_menu_text; ?></a></li>
<?php
          } else {
?>
          <li><span><?= $content_menu_text; ?></span></li>
<?php
          }
        }
      } //if($key != 0 || $key != $ids_count-1)
    } //foreach($content_hierarchy_ids_array
?>
    <li><span><?= $current_content_name; ?></span></li>
<?php
  } else {
?>
    <li><a href="<?=$home_page_url;?>" title="<?= $languages['title_goto_homepage']; ?>"><?= $languages['menu_home']; ?></a></li>
    <li><span><?= $current_content_name; ?></span></li>
<?php
  }
}

function print_header_language_menu() {

  global $db_link;
  global $current_language_id;
  global $current_page_pretty_url;
  global $current_content_id;
  
  $and = (isset($current_content_id) && !empty($current_content_id)) ? "AND `contents_descriptions`.`content_id` = '$current_content_id'" : "AND `contents`.`content_is_home_page` = '1'";
  $query_languages = "SELECT `languages`.`language_id`,`languages`.`language_code`,`languages`.`language_name`,`languages`.`language_is_default_frontend`
                        FROM `languages`
                       WHERE `languages`.`language_is_active` = '1'
                    ORDER BY `languages`.`language_menu_order` ASC";
  //echo "$query_languages<br>";
  $result_languages = mysqli_query($db_link, $query_languages);
  if(!$result_languages) echo mysqli_error($db_link);
  $language_count = mysqli_num_rows($result_languages);
  if($language_count > 1) {
    while($language_row = mysqli_fetch_assoc($result_languages)) {

      $language_id = $language_row['language_id'];
      $language_code = $language_row['language_code'];
      $language_is_default_frontend = $language_row['language_is_default_frontend'];
      $language_name = stripslashes($language_row['language_name']);
      $class_active = ($language_id == $current_language_id) ? " class='active'" : "";
      
      if(isset($current_content_id) && !empty($current_content_id)) {
        $query_url = "SELECT `contents_descriptions`.`content_pretty_url` 
                        FROM `contents_descriptions` 
                  INNER JOIN `contents` USING(`content_id`)
                       WHERE `contents_descriptions`.`language_id` = '$language_id' $and";
        //echo $query_url;
        $result_url = mysqli_query($db_link, $query_url);
        if(!$result_url) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_url) > 0) {
          $url_row = mysqli_fetch_assoc($result_url);
          $href = "/$language_code/".$url_row['content_pretty_url'];
        }
        mysqli_free_result($result_url);
      }
      else {
        if(isset($_GET['cid'])) {
          $current_category_id = $_GET['cid']; // current selected category_id

          $query_categories = "SELECT `categories`.`category_parent_id`,`categories`.`category_is_section_header`,`categories_descriptions`.`cd_pretty_url`
                                 FROM `categories`
                           INNER JOIN `categories_descriptions` USING(`category_id`)
                                WHERE `categories`.`category_id` = '$current_category_id' AND `categories_descriptions`.`language_id` = '$language_id'";
          //echo $query_categories;
          $result_categories = mysqli_query($db_link, $query_categories);
          if (!$result_categories) echo mysqli_error($db_link);
          $category_count = mysqli_num_rows($result_categories);
          if ($category_count > 0) {

            $category_row = mysqli_fetch_assoc($result_categories);

            $category_is_section_header = $category_row['category_is_section_header'];
            $category_parent_id = $category_row['category_parent_id'];
            $cd_pretty_url = $category_row['cd_pretty_url'];
            if($category_is_section_header == 0) {
              $class_section_header = "";
              $href = "/$language_code/$cd_pretty_url?cid=$current_category_id";
            } else {
              $class_section_header = " section_header";
              $href = "javascript:;";
            }
          }
        }
        else {
          $page_path_string = $_GET['page'];
          $page_path_array = explode("/", $page_path_string);
          array_shift($page_path_array);
          $href = "/$language_code/".implode("/", $page_path_array);
          if(isset($_GET['mid'])) $href .= "?mid=".$_GET['mid'];
        }
          
      }
?>
      <li<?=$class_active;?> data-code="<?=$language_code;?>"> 
        <a href="<?=$href;?>" title="<?=$language_name;?>" onclick="createCookie('lang','<?=$language_code;?>')">
          <span class="lang_flag"><img src="<?=SITEFOLDERSL;?>/images/flags/<?=$language_code;?>.png" alt="<?=$language_code;?>" width="16" height="11" ></span>
          <span class="lang_name hidden-xs"><?=$language_name;?></span>
        </a>
      </li>
<?php
    }
    mysqli_free_result($result_languages);
  }
}

function print_error_page() {
  
  global $db_link;
  global $current_lang;
  global $current_language_id;
  
  $content_type_id = 3;

  //error page
  $query_content = "SELECT `contents_descriptions`.`content_pretty_url`
                      FROM `contents`
                INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                     WHERE `content_type_id` = '$content_type_id' 
                       AND `contents_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_pretty_url = $content_array['content_pretty_url'];

    header("Location: /$current_lang/$content_pretty_url");
  }
  
}

function list_news($offset = false,$news_count = false, $news_category_id = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;

  $and_news_category = "";
  if($news_category_id) {
    $and_news_category = ($news_category_id == 1) ? "" : "AND `news`.`news_category_id` = '$news_category_id'";
  }
  $page_offset = 6;
  $offset = ($offset) ? $offset : 0;
  
  if(!$news_count) {
    $query_news = "SELECT `news`.`news_id`  FROM `news` WHERE `news`.`news_is_active` = '1' $and_news_category";
    //echo $query_news."<br>";
    $result_news = mysqli_query($db_link, $query_news);
    if(!$result_news) echo mysqli_error($db_link);
    $news_count = mysqli_num_rows($result_news);
    mysqli_free_result($result_news);
  }
  
  $current_datetime = date("Y-m-d H:i:s");
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,`news`.`news_image`,
                        `news`.`news_views`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary`,`news_descriptions`.`news_pretty_url` 
                   FROM `news` 
             INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                  WHERE `news`.`news_is_active` = '1' AND `news_descriptions`.`language_id` = '$current_language_id' $and_news_category
                    AND ((`news`.`news_start_time` IS NULL AND `news`.`news_end_time` IS NULL)
                     OR (`news`.`news_start_time` IS NOT NULL AND `news`.`news_end_time` IS NOT NULL
                    AND `news`.`news_start_time` <= '$current_datetime' AND `news`.`news_end_time` >= '$current_datetime'))
               ORDER BY `news`.`news_created_date` DESC
                  LIMIT $offset,$page_offset";
  //echo $query_news;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {
    
    // if the results are more then $page_offset
    // making a pagination, finding how many pages will be needed
    $current_page = ($offset/$page_offset)+1;

    if($news_count > $page_offset) {
      $page_count = ceil($news_count/$page_offset);
    }
    
    $block = 1;
    
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_pretty_url = $news_row['news_pretty_url'];
      //$news_summary = truncate($news_row['news_summary']);
      $news_summary = stripslashes($news_row['news_summary']);
      $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
      $news_post_date_month_text = "text_date_month_full_".date("m", strtotime($news_row['news_post_date']));
      $news_post_date_month = $languages[$news_post_date_month_text];
      $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
      $news_start_time = $news_row['news_start_time'];
      $news_end_time = $news_row['news_end_time'];
      $news_images_folder = SITEFOLDERSL."/images/news/";
      $news_image = $news_images_folder.$news_row['news_image'];
      $news_image_exploded = explode(".", $news_image);
      $current_news_image_name = $news_image_exploded[0];
      $current_news_image_exstension = $news_image_exploded[1];
      $image_thumb_name = $current_news_image_name."_thumb.".$current_news_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$image_thumb_name);
      $thumb_image_dimensions = $thumb_image_params[3];
      $news_views = $news_row['news_views'];
      $news_details_link = "/$current_lang/$news_pretty_url?nid=$news_id";
      
?>
      <article class="blog-post-wrapper">

        <a href="<?=$news_details_link;?>">
          <img src="<?=$image_thumb_name;?>" alt="<?=$news_title;?>" <?=$thumb_image_dimensions;?> class="blog-image" />
        </a>

        <h3 class="blog-title">
          <a href="<?=$news_details_link;?>"><?=$news_title;?></a>
        </h3>

        <div class="blog-meta">
          <?=$news_post_date_day;?> <?=$news_post_date_month;?> <?=$news_post_date_year;?> / <i class="fa fa-eye" aria-hidden="true"></i><?=$news_views;?>
        </div>

        <p><?=$news_summary;?></p>
        <p><a href="<?=$news_details_link;?>" class="more-link"><?=$languages['btn_read_more'];?></a></p>
        
      </article>
<?php
      $block++;
    }
    mysqli_free_result($result_news);
    
    if(isset($page_count)) {
?>
    <div class="page-pagination">
      <ul class="clearfix">
<?php
        $pages = 1;
        $current_offset = $offset;
        $offset = 0;

        if($current_page == 1) {
?>
        <li class="disabled btn_prev_page"><a href="javascript:;" data="">&larr; </a></li>
<?php
        }
        else {
          $prev_offset = $current_offset - $page_offset;
?>
        <li class="btn_prev_page"><a href="#news_anchor" data="<?=$prev_offset;?>">&larr; </a></li>
<?php
        }

        while($pages <= $page_count) {

          if($current_page == $pages) {
?>
        <li id="pag_<?=$pages;?>"><span class="current"><?=$pages;?></span></li>
<?php
          }
          else {
?>
        <li id="pag_<?=$pages;?>"><a href="#news_anchor" class="inactive" data="<?=$offset;?>"><?=$pages;?></a></li>
<?php
          }

          $pages++;
          $offset += $page_offset;
        }
        if($current_page == $page_count) {
?>
        <li class="disabled btn_next_page"><a href="javascript:;" data=""> &rarr;</a></li>
<?php
        }
        else {
          $next_offset = $current_offset + $page_offset;
?>
        <li class="btn_next_page"><a href="#news_anchor" data="<?=$next_offset;?>">&rarr; </a></li>
<?php
        }
?>
      </ul>
      <input type="hidden" class="news_count" value="<?=$news_count;?>" >
    </div>
    <script>
      $(function() {
        $(".pagination a").bind('click', function() {
          var offset = $(this).attr("data");
          LoadPaginationNews(offset);
        });
      });
    </script>
<?php
    }
  }
}

function list_news_in_footer($news_count = false, $news_category_id = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;

  $and_news_category = "";
  if($news_category_id) {
    $and_news_category = ($news_category_id == 1) ? "" : "AND `news`.`news_category_id` = '$news_category_id'";
  }
  
  $limit = ($news_count) ? "LIMIT $news_count" : "";
  
  $current_datetime = date("Y-m-d H:i:s");
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,`news`.`news_image`,
                        `news`.`news_views`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary`,`news_descriptions`.`news_pretty_url` 
                   FROM `news` 
             INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                  WHERE `news`.`news_is_active` = '1' AND `news_descriptions`.`language_id` = '$current_language_id' $and_news_category
                    AND ((`news`.`news_start_time` IS NULL AND `news`.`news_end_time` IS NULL)
                     OR (`news`.`news_start_time` IS NOT NULL AND `news`.`news_end_time` IS NOT NULL
                    AND `news`.`news_start_time` <= '$current_datetime' AND `news`.`news_end_time` >= '$current_datetime'))
               ORDER BY `news`.`news_created_date` DESC
                        $limit";
  //echo $query_news;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {
    
    $block = 1;
    
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_pretty_url = $news_row['news_pretty_url'];
       //$news_summary = truncate($news_row['news_summary']);
      $news_summary = stripslashes($news_row['news_summary']);
      $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
      $news_post_date_month_text = "text_date_month_full_".date("m", strtotime($news_row['news_post_date']));
      $news_post_date_month = $languages[$news_post_date_month_text];
      $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
      $news_start_time = $news_row['news_start_time'];
      $news_end_time = $news_row['news_end_time'];
      $news_images_folder = SITEFOLDERSL."/images/news/";
      $news_image = $news_row['news_image'];
      $news_image_exploded = explode(".", $news_image);
      $current_news_image_name = $news_image_exploded[0];
      $current_news_image_exstension = $news_image_exploded[1];
      $image_thumb_name = $news_images_folder.$current_news_image_name."_sidebar_thumb.".$current_news_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$image_thumb_name);
      $thumb_image_dimensions = $thumb_image_params[3];
      $news_views = $news_row['news_views'];
      $news_details_link = "/$current_lang/$news_pretty_url?nid=$news_id";
      
?>
      <li class="clearfix">
        <div class="lpl-img">
          <a href="<?=$news_details_link;?>">
            <img src="<?=$image_thumb_name;?>" alt="<?=$news_title;?>" <?=$thumb_image_dimensions;?> />
          </a>
        </div>
        <div class="lpl-content">
          <h6>
            <a href="<?=$news_details_link;?>"><?=$news_title;?></a> 
            <span><i class="fa fa-calendar" aria-hidden="true"></i><?=$news_post_date_day;?> <?=$news_post_date_month;?> <?=$news_post_date_year;?></span>
            <span><i class="fa fa-eye" aria-hidden="true"></i><?=$news_views;?></span>
          </h6>
        </div>
      </li>
<?php
      $block++;
    }
    mysqli_free_result($result_news);
  }
}

function list_news_categories($news_cat_parent_id, $news_categories_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;
          
  $limit = ($news_categories_count) ? "LIMIT $news_categories_count" : "";
  
  $current_news_category_id = (isset($_GET['ncid'])) ? $_GET['ncid'] : ((isset($_GET['ncid_d'])) ? $_GET['ncid_d'] : 0);
  
  $query_news_categories = "SELECT `news_categories`.`news_category_id`, `news_categories`.`news_cat_hierarchy_level`, `news_categories`.`news_cat_has_children`,
                                   `news_categories`.`news_cat_sort_order`,`news_cat_desc`.`news_cat_name`,`news_cat_desc`.`news_cat_hierarchy_path`, 
                                   `news_cat_desc`.`news_cat_long_name` 
                              FROM `news_categories` 
                        INNER JOIN `news_cat_desc` ON `news_cat_desc`.`news_category_id` = `news_categories`.`news_category_id`
                             WHERE `news_categories`.`news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_desc`.`language_id` = '$current_language_id'
                          ORDER BY `news_categories`.`news_cat_sort_order` ASC $limit";
  //echo $query_news_categories;exit;
  $result_news_categories = mysqli_query($db_link, $query_news_categories);
  if(!$result_news_categories) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news_categories);
  if($news_count > 0) {
    $key = 0;
    while($news_category_row = mysqli_fetch_assoc($result_news_categories)) {

      $news_category_id = $news_category_row['news_category_id'];
      $news_cat_hierarchy_level = $news_category_row['news_cat_hierarchy_level'];
      $news_cat_has_children = $news_category_row['news_cat_has_children'];
      $news_cat_sort_order = $news_category_row['news_cat_sort_order'];
      $news_cat_name = $news_category_row['news_cat_name'];
      $news_cat_hierarchy_path = $news_category_row['news_cat_hierarchy_path'];
      $news_cat_long_name = $news_category_row['news_cat_long_name'];
      $news_category_link = "/$current_lang/$news_cat_hierarchy_path?ncid=$news_category_id";
      
      if(isset($_GET['ncid']) || isset($_GET['ncid_d'])) {
        $class_active = ($current_news_category_id == $news_category_id) ? "active" : "";
      }
      else {
        $class_active = ($key == 0) ? "active" : "";
      }
      
      $class_has_parent = ($news_cat_parent_id == 0) ? "" : " has_parent";
      //$news_category_has_active_children = check_if_news_category_has_active_children($news_category_id);
      $news_category_is_last_child = check_if_this_is_news_cat_last_child($news_cat_parent_id,$news_cat_sort_order);

      if($news_cat_has_children == 1) {
?>
      <li class="category <?="$class_active$class_has_parent";?> has_children">
        <a href="<?=$news_category_link;?>" class="category-link"><?=$news_cat_name;?></a>
        <ul>
<?php
        list_news_categories($news_category_id);
      }
      else {
?>
      <li class="category <?="$class_active$class_has_parent";?>"><a href="<?=$news_category_link;?>" class="category-link"><?=$news_cat_name;?></a></li>
<?php
      }

      if($news_cat_hierarchy_level > 1 && $news_category_is_last_child) {
?>
        </ul>
      </li>
<?php
      }
      $key++;
    }
    mysqli_free_result($result_news_categories);
  }
?>
    
<?php
}

function list_news_include_block($news_ids) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  
  $current_datetime = date("Y-m-d H:i:s");
    
  $query_news = "SELECT `news`.`news_id`,`news`.`news_category_id`,`news`.`news_post_date`,`news`.`news_image`,`news`.`news_views`,
                        `news_descriptions`.`news_title`,`news_descriptions`.`news_summary`,`news_descriptions`.`news_pretty_url` 
                   FROM `news` 
             INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                  WHERE `news`.`news_id` IN(".implode(",", $news_ids).") AND `news_descriptions`.`language_id` = '$current_language_id' $and_news_category
                    AND ((`news`.`news_start_time` IS NULL AND `news`.`news_end_time` IS NULL)
                     OR (`news`.`news_start_time` IS NOT NULL AND `news`.`news_end_time` IS NOT NULL
                    AND `news`.`news_start_time` <= '$current_datetime' AND `news`.`news_end_time` >= '$current_datetime'))
               ORDER BY `news`.`news_created_date` DESC";
  //echo $query_news;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {

    $news_counter = 1;
    
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_category_id = $news_row['news_category_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_pretty_url = $news_row['news_pretty_url'];
      //$news_summary = truncate($news_row['news_summary']);
      $news_summary = stripslashes($news_row['news_summary']);
      $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
      $news_post_date_month_text = "text_date_month_".date("m", strtotime($news_row['news_post_date']));
      $news_post_date_month = $languages[$news_post_date_month_text];
      $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
      $news_images_folder = SITEFOLDERSL."/images/news/";
      $news_image = $news_row['news_image'];
      $news_image_exploded = explode(".", $news_image);
      $current_news_image_name = $news_image_exploded[0];
      $current_news_image_exstension = $news_image_exploded[1];
      $image_thumb_name = $news_images_folder.$current_news_image_name."_thumb.".$current_news_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$image_thumb_name);
      $thumb_image_dimensions = $thumb_image_params[3];
      $news_views = $news_row['news_views'];
      $news_details_link = "/$current_lang/$news_pretty_url?ncid_d=$news_category_id&nid=$news_id";
?>
      <div class="item">
        <div class="kf_blog_post_wrap">
          <h6>.0<?=$news_counter;?></h6>
          <figure>
            <img src="<?=$image_thumb_name;?>" alt="<?=$news_title;?>" <?=$thumb_image_dimensions;?>>
          </figure>
          <div class="kf_blog_des">
            <h6><a href="<?=$news_details_link;?>"><?=$news_title;?></a></h6>
            <ul class="kf_blog_post_meta">
              <li><i class="fa fa-calendar" aria-hidden="true"></i><a href="javascript:;"><?=$news_post_date_day;?> <?=$news_post_date_month;?> <?=$news_post_date_year;?></a></li>
              <li><i class="fa fa-eye" aria-hidden="true"></i><a href="javascript:;"><?=$news_views;?></a></li>
            </ul>
            <p><?=$news_summary;?></p>
            <a class="kf_link_2" href="<?=$news_details_link;?>"><?= $languages['btn_read_more']; ?></a>
          </div>
        </div>
      </div>
<?php
      $news_counter++;
    } //while($news_row)
    mysqli_free_result($result_news);
  }
}

function list_latest_news_for_category($news_category_id, $news_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page_offset;
  
  $limit = ($news_count) ? "LIMIT $news_count" : "";
  $and_news_category = "";
  if($news_category_id) {
    $and_news_category = ($news_category_id == 1) ? "" : "AND `news`.`news_category_id` = '$news_category_id'";
  }
  
  $current_datetime = date("Y-m-d H:i:s");
    
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,`news`.`news_image`,
                        `news`.`news_views`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary`,`news_descriptions`.`news_pretty_url` 
                   FROM `news` 
             INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                  WHERE `news`.`news_is_active` = '1' AND `news_descriptions`.`language_id` = '$current_language_id' $and_news_category
                    AND ((`news`.`news_start_time` IS NULL AND `news`.`news_end_time` IS NULL)
                     OR (`news`.`news_start_time` IS NOT NULL AND `news`.`news_end_time` IS NOT NULL
                    AND `news`.`news_start_time` <= '$current_datetime' AND `news`.`news_end_time` >= '$current_datetime'))
               ORDER BY `news`.`news_created_date` DESC
                        $limit";
  //echo $query_news;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {
  
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_pretty_url = $news_row['news_pretty_url'];
      //$news_summary = truncate($news_row['news_summary']);
      $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
      $news_post_date_month_text = "text_date_month_".date("m", strtotime($news_row['news_post_date']));
      $news_post_date_month = $languages[$news_post_date_month_text];
      $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
      $news_images_folder = SITEFOLDERSL."/images/news/";
      $news_image = $news_row['news_image'];
      $news_image_exploded = explode(".", $news_image);
      $current_news_image_name = $news_image_exploded[0];
      $current_news_image_exstension = $news_image_exploded[1];
      $image_thumb_name = $news_images_folder.$current_news_image_name."_sidebar_thumb.".$current_news_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$image_thumb_name);
      $thumb_image_dimensions = $thumb_image_params[3];
      $news_views = $news_row['news_views'];
      $news_details_link = "/$current_lang/$news_pretty_url?ncid_d=$news_category_id&nid=$news_id";
?>
      <li class="clearfix">
        <div class="lpl-img">
          <a href="<?=$news_details_link;?>">
            <img src="<?=$image_thumb_name;?>" alt="<?=$news_title;?>" <?=$thumb_image_dimensions;?> />
          </a>
        </div>
        <div class="lpl-content">
          <h6>
            <a href="<?=$news_details_link;?>"><?=$news_title;?></a> 
            <span><i class="fa fa-calendar" aria-hidden="true"></i><?=$news_post_date_day;?> <?=$news_post_date_month;?> <?=$news_post_date_year;?></span>
            <span><i class="fa fa-eye" aria-hidden="true"></i><?=$news_views;?></span>
          </h6>
        </div>
      </li>
<?php
    } //while($news_row)
    mysqli_free_result($result_news);
  }
}

function print_html_news_sidebar($print_latest_news = true) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $current_news_id;
  global $news_category_id;

?>
    <section class="widget clearfix">
      <h4 class="title-style3" style="margin-bottom: 20px !important"><?= $languages['header_news_categories']; ?>
        <span class="title-block"></span>
      </h4>
      <ul class="clearfix" style="margin-bottom: 20px">
        <?php list_news_categories($news_cat_parent_id = 0, $news_categories_count = false) ?>
      </ul>
    </section>
<?php
    if($print_latest_news) {
?> 
    <section class="widget clearfix">
      <h4 class="title-style3"><?= $languages['header_latest_news_for_category']; ?>
        <span class="title-block"></span>
      </h4>
      <ul class="latest-posts-list clearfix">
        <?php list_latest_news_for_category($news_category_id, $news_count = 5) ?>
      </ul>
    </section>
<?php
    }
}

function print_footer_menu($content_hierarchy_level_start,$number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from site/index.php or site/categories.php

  //echo $content_hierarchy_ids;
  if(strstr($content_hierarchy_ids, ".")) {
    $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  } else {
    $content_hierarchy_ids_array[0] = $content_hierarchy_ids;
  }

  $content_hierarchy_level_in_query = "";

  if($content_hierarchy_level_start == 1) {
    if($number_of_hierarchy_levels != 1) $content_hierarchy_level_in_query = " AND `contents`.`content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `contents`.`content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `contents`.`content_id`,`contents`.`content_type_id`,`contents`.`content_has_children`,`contents`.`content_hierarchy_level`,
                           `contents`.`content_menu_order`,`contents`.`content_target` ,`contents_descriptions`.`content_text`,
                           `contents_descriptions`.`content_menu_text`,`contents_descriptions`.`content_pretty_url`
                      FROM `contents`
                INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                     WHERE `content_show_in_footer` = '1' AND `content_is_active` = '1' $content_hierarchy_level_in_query
                       AND `contents_descriptions`.`content_desc_is_active` = '1' AND `contents_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `contents`.`content_hierarchy_level` ASC, `contents`.`content_menu_order` ASC";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_pretty_url = $content_row['content_pretty_url'];
          break;
        case 2:
          $content_pretty_url = "javascript:;";
          break;
        case 4:
          $content_pretty_url = $content_text;
          break;
        default: $content_pretty_url = $content_row['content_pretty_url'];
          break;
      }
      $class_active = "";

      if(in_array($content_id,$content_hierarchy_ids_array)) $class_active = ' class="active"';

      echo "<li$class_active><a href='/$current_lang/$content_pretty_url' class='list-footer-link' $content_target>$content_menu_text</a></li>\n";

    }
    mysqli_free_result($result_content);
  }
}

function print_html_footer() {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $current_language_id;
?>
        <!--footer-->
        <footer class="footer-wrapper">

          <div class="main-footer">

            <div class="container">

              <div class="row">

                <div class="col-sm-12 col-md-9">

                  <div class="row">

                    <div class="col-sm-6 col-md-4">

                      <div class="footer-about-us">
                        <h5 class="footer-title"><?=$languages['header_about_us'];?></h5>
                        <p><?=$languages['text_company_footer'];?></p>
                        <a href="#"><?=$languages['btn_read_more'];?></a>
                      </div>

                    </div>

                    <div class="col-sm-6 col-md-5 mt-30-xs">
                      <h5 class="footer-title"><?=$languages['header_menu'];?></h5>
                      <ul class="footer-menu clearfix">
                        <?php print_footer_menu($content_hierarchy_level_start = 1,$number_of_hierarchy_levels = 2); ?>
                      </ul>

                    </div>

                  </div>

                </div>

                <div class="col-sm-12 col-md-3 mt-30-sm">

                  <h5 class="footer-title"><?=$languages['header_newsletter'];?></h5>

                  <p><?=$languages['header_newsletter_signup'];?></p>

                  <div class="footer-newsletter">

                    <form name="newsletterform" method="post" action="<?=SITEFOLDERSL;?>/subscribe.php" class="form-group">
                      <input name="newsletter_email" type="email" required="required" class="form-control" placeholder="<?=$languages['text_enter_your_email'];?>" />
                      <input type="hidden" name="current_lang" value="<?=$current_lang;?>">
                      <button type="submit" class="btn btn-primary"><?=$languages['btn_subscribe'];?></button>
                    </form>

                    <p class="font-italic font13">*** Don't worry, we wont spam you!</p>

                  </div>

                </div>

              </div>

            </div>

          </div>

          <div class="bottom-footer">

            <div class="container">

              <div class="row">

                <div class="col-sm-6 col-md-8">

                  <p class="copy-right">
                    &COPY; <?=$languages['e_shop_cms'];?> <?=date("Y");?>&nbsp; <?=$languages['text_all_rights_reserved'];?>.
                    <?=$languages['text_developed_by'];?> <span><a href="http://www.eterrasystems.com/" target="_blank" class="noprint">Eterrasystems</a></span>
                  </p>

                </div>

                <div class="col-sm-4 col-md-4 hidden">

                  <ul class="bottom-footer-menu">
                    <li><a href="#">Cookies</a></li>
                    <li><a href="#">Policies</a></li>
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">Blogs</a></li>
                  </ul>

                </div>

                <div class="col-sm-6 col-md-4">
                  <ul class="bottom-footer-menu for-social">
                    <?php list_contacts_socials(); ?>
                  </ul>
                </div>

              </div>

            </div>

          </div>

        </footer>
        <!--footer-->

      </div>
      <!-- end Main Wrapper -->

    </div> <!-- / .wrapper -->
    <!-- end Container Wrapper -->

    <!-- start Back To Top -->
    <div id="back-to-top">
      <a href="#"><i class="ion-ios-arrow-up"></i></a>
    </div>
    <!-- end Back To Top -->

    <!-- JS -->
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap-modalmanager.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap-modal.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/smoothscroll.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.waypoints.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/wow.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.slicknav.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.placeholder.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap-tokenfield.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/typeahead.bundle.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap3-wysihtml5.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery-filestyle.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap-select.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/ion.rangeSlider.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/handlebars.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.countimator.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.countimator.wheel.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/slick.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/easy-ticker.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.introLoader.min.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/jquery.responsivegrid.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/customs.js"></script>
    <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/functions.js"></script>
    <script type="text/javascript">
      $(function () {
        $("#page-header").css("background-image","url('<?=SITEFOLDERSL;?>/images/page-header-<?=mt_rand(1, 1);?>.jpg')");
        $("#primary-navigation .menu-item-has-children li.current-menu-item").parents("li").addClass("current-menu-item");

        $(".choose_language").html($("#languages li.active a").html());
        $("#languages li.active").hide();
        $("#choose_language .choose_language").bind('click', function () {
          if ($("#languages").css("display") == "block") {
            $("#languages").slideUp();
            $("#choose_language").removeClass("active");
          }
          else {
            $("#languages").slideDown();
            $("#choose_language").addClass("active");
          }
        });

        $('form[name="newsletter-form"]').submit(function (event) {

          var This = $(this);
          var action = $(This).attr('action');
          var data_value = decodeURI($(This).serialize());

          $.ajax({
            type: "POST",
            url: action,
            data: data_value,
            error: function (xhr, status, error) {
              confirm('The page save failed.');
            },
            success: function (response) {
              $('.newsletter-wrapper').hide();
              $('#ajax_subscribe_msg').html(response);
              $('#ajax_subscribe_msg').slideDown('slow');
              setTimeout(function () { $("#ajax_subscribe_msg").slideUp(); }, 5000);
            }
          });

          event.preventDefault();
        });
      });
    </script>
  </body>
</html>

<?php
}
