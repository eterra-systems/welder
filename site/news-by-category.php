<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['ncid'])) {
    $news_category_id = intval(mysqli_real_escape_string($db_link,$_GET['ncid'])); // current selected news_category_id
  }
  if(isset($_GET['offset'])) {
    $offset = intval(mysqli_real_escape_string($db_link,$_GET['offset']));
  }
  else $offset = 0;
  
  //$current_news_category_id
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
  else {
    print_error_page();
  }

  $page_array = get_page_by_type("news");

  $current_content_menu_text = stripslashes($page_array['content_menu_text']);
  $content_pretty_url = $page_array['content_pretty_url'];
  $content_meta_title = stripslashes($page_array['content_meta_title']);
  $content_meta_keywords = stripslashes($page_array['content_meta_keywords']);
  $content_meta_description = stripslashes($page_array['content_meta_description']);

  $body_css = "not-transparent-header news-categories";
  
  print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css);
?>
  <a name="news_anchor" id="news_anchor"></a>
  <!-- WRAPPER-->
  
  <div class="breadcrumb-wrapper">
    <div class="container">
      <ol class="breadcrumb-list">
        <li><a href="<?=$home_page_url;?>" title="<?= $languages['title_goto_homepage']; ?>"><?= $languages['menu_home']; ?></a></li>
        <li><a href="/<?=$current_lang;?>/<?=$content_pretty_url;?>"><?=$current_content_menu_text;?></a></li>
        <li><span><?=$news_cat_name;?></span></li>
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
  print_html_footer();  
?>