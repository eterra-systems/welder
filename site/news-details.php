<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['nid'])) {
    $current_news_id = mysqli_real_escape_string($db_link,intval($_GET['nid']));
  }

  $query_news = "SELECT `news`.`news_id`,`news`.`news_category_id`,`news`.`news_post_date`,`news`.`news_image`,`news`.`news_views`,`news_descriptions`.`news_title`,
                        `news_descriptions`.`news_summary`,`news_descriptions`.`news_text`,`news_descriptions`.`news_meta_title`,`news_descriptions`.`news_meta_description`,
                        `news_descriptions`.`news_meta_keywords` 
                   FROM `news` 
             INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                  WHERE `news`.`news_id` = '$current_news_id' AND `news_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_news;exit;
  $result_news = mysqli_query($db_link, $query_news);
  if (!$result_news) echo mysqli_error($db_link);
  if (mysqli_num_rows($result_news) > 0) {
    $news_row = mysqli_fetch_assoc($result_news);

    $news_id = $news_row['news_id'];
    $news_category_id = $news_row['news_category_id'];
    $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
    $news_post_date_month_text = "text_date_month_" . date("m", strtotime($news_row['news_post_date']));
    $news_post_date_month = $languages[$news_post_date_month_text];
    $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
    $news_images_folder = SITEFOLDERSL."/images/news/";
    $news_image = $news_images_folder . $news_row['news_image'];
    @$news_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'] . $news_image);
    $news_image_dimensions = @$news_image_params[3];
    $news_image_splitted = explode(".", $news_row['news_image']);
    $news_image_name = $news_image_splitted[0];
    $news_image_ext = $news_image_splitted[1];
    $news_image_thumb = $news_image;
    @$news_image_thumb_params = getimagesize($_SERVER['DOCUMENT_ROOT'] . $news_image_thumb);
    $news_image_thumb_dimensions = @$news_image_thumb_params[3];
    $fb_image = $news_images_folder . $news_image_name . "_thumb." . $news_image_ext;
    $news_title = $news_row['news_title'];
    $news_summary = stripslashes($news_row['news_summary']);
    $news_text = stripslashes($news_row['news_text']);
    $news_meta_title = (!empty($news_row['news_meta_title']) || !is_null($news_row['news_meta_title'])) ? stripslashes($news_row['news_meta_title']) : $news_title;
    $news_meta_description = (!empty($news_row['news_meta_description']) || !is_null($news_row['news_meta_description'])) ? stripslashes($news_row['news_meta_description']) : $news_summary;
    $news_meta_keywords = stripslashes($news_row['news_meta_keywords']);
    $news_views = $news_row['news_views'];
  }
  else {
    print_error_page();
  }

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

  $page_array = get_page_by_type("news");
  $current_content_menu_text = stripslashes($page_array['content_menu_text']);
  $content_pretty_url = $page_array['content_pretty_url'];
  $content_meta_title = stripslashes($page_array['content_meta_title']);
  $content_meta_keywords = stripslashes($page_array['content_meta_keywords']);
  $content_meta_description = stripslashes($page_array['content_meta_description']);

  $query_update_news = "UPDATE `news` SET `news_views`=`news_views`+1 WHERE `news_id` = '$current_news_id'";
  $result_update_news = mysqli_query($db_link, $query_update_news);
  
  $body_css = "not-transparent-header news-details";
  
  print_html_header($news_meta_title, $news_meta_description, $news_meta_keywords, $additional_css_javascript = false, $body_css);
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
    
  <div class="breadcrumb-wrapper">
    <div class="container">
      <ol class="breadcrumb-list">
        <li><a href="<?=$home_page_url;?>" title="<?= $languages['title_goto_homepage']; ?>"><?= $languages['menu_home']; ?></a></li>
        <li><a href="/<?=$current_lang;?>/<?=$content_pretty_url;?>"><?=$current_content_menu_text;?></a></li>
        <li><a href="/<?=$current_lang;?>/<?=$content_pretty_url;?>?ncid=<?=$news_category_id;?>"><?=$news_cat_name;?></a></li>
        <li><span><?=$news_title;?></span></li>
      </ol>
    </div>
  </div>

  <div class="section sm">
    <div class="container">
      <div class="row">

        <div class="col-sm-8 col-md-9">
          <div class="blog-wrapper">
            <div class="blog-item blog-single">

              <div class="blog-media">
                <img src="<?=$news_image;?>" <?=$news_image_dimensions;?> alt="<?=$news_title;?>" class="blog-image">
              </div>

              <div class="blog-content">
                <h3><?=$news_title;?></h3>
                <ul class="blog-meta clearfix">
                  <li><?=$news_post_date_day;?> <?=$news_post_date_month;?> <?=$news_post_date_year;?></li>
                  <li><i class="fa fa-eye" aria-hidden="true"></i> <?=$news_views;?></li>
                </ul>
                <div class="blog-entry">
                  <?=$news_text;?>
                </div>
              </div>

              <div class="news_back_link">
                <a href="/<?=$current_lang;?>/<?=$content_pretty_url;?>?ncid=<?=$news_category_id;?>" class="btn btn-primary">
                  <i class="fa fa-angle-double-left" aria-hidden="true"></i> <?=$languages['btn_back_to_all_news'];?>
                </a>
              </div>

            </div>
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
  print_html_footer();
?>