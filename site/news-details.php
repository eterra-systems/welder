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
    $news_images_folder = "/frontstore/images/news/";
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

  $content_type_id = 7; // news
  $query_content = "SELECT `contents`.`content_hierarchy_ids`,`contents_descriptions`.`content_name`,`contents_descriptions`.`content_menu_text`,
                           `contents_descriptions`.`content_hierarchy_path`
                      FROM `contents`
                INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                     WHERE `content_type_id` = '$content_type_id' AND `contents_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_name = stripslashes($content_array['content_name']);
    $content_menu_text = stripslashes($content_array['content_menu_text']);
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_hierarchy_path = $content_array['content_hierarchy_path'];
  }

  $query_update_news = "UPDATE `news` SET `news_views`=`news_views`+1 WHERE `news_id` = '$current_news_id'";
  $result_update_news = mysqli_query($db_link, $query_update_news);
  if (!$result_update_news) {
    echo $languages['sql_error_update'] . " - " . mysqli_error($db_link);
    mysqli_query($db_link, "ROLLBACK");
    exit;
  }
  
  print_html_header($news_meta_title, $news_meta_description, $news_meta_keywords);
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
    
  <div id="page-header">
    <div class="content-wrapper clearfix">
      <h1><?=$news_title;?></h1>
      <p>
        <a href="<?=$home_page_url;?>"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right" aria-hidden="true"></i>
        <a href="/<?=$current_lang;?>/<?=$content_hierarchy_path;?>"><?=$content_menu_text;?></a> <i class="fa fa-angle-right" aria-hidden="true"></i>
        <a href="/<?=$current_lang;?>/<?=$content_hierarchy_path;?>?ncid=<?=$news_category_id;?>"><?=$news_cat_name;?></a> <i class="fa fa-angle-right" aria-hidden="true"></i>
        <?=$news_title;?>
      </p>
    </div>
  </div>

  <div class="content-wrapper clearfix">
    <main class="main-content">

      <article class="blog-post-wrapper">
        
        <div id="news_image"><img src="<?=$news_image;?>" <?=$news_image_dimensions;?> class="blog-image"></div>
        
        <h3 class="blog-title"><?=$news_title;?></h3>
        
        <div class="blog-meta">
          <?=$news_post_date_day;?> <?=$news_post_date_month;?> <?=$news_post_date_year;?> / <i class="fa fa-eye"></i><?=$news_views;?>
        </div>
        
        <div class="news_text clearfix">
          <?=$news_text;?>
        </div>
        
        <div class="news_back_link">
          <a href="/<?=$current_lang;?>/<?=$content_hierarchy_path;?>?ncid=<?=$news_category_id;?>" class="btn btn-primary">
            <i class="fa fa-angle-double-left" aria-hidden="true"></i> <?=$languages['btn_back_to_all_news'];?>
          </a>
        </div>
        
      </article>

    </main>
 
    <aside class="sidebar-content">
      <?php print_html_news_sidebar($print_latest_news = true);?>
    </aside>
  </div>
<?php
  print_html_footer();
?>