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

  $content_name = stripslashes($page_array['content_name']);
  $current_content_menu_text = stripslashes($page_array['content_menu_text']);
  $content_hierarchy_ids = $page_array['content_hierarchy_ids'];
  $content_pretty_url = $page_array['content_pretty_url'];
  $content_meta_title = stripslashes($page_array['content_meta_title']);
  $content_meta_keywords = stripslashes($page_array['content_meta_keywords']);
  $content_meta_description = stripslashes($page_array['content_meta_description']);

      
  print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css = "news-categories");
?>
  <a name="news_anchor" id="news_anchor"></a>
  <!-- WRAPPER-->
  
  <div id="page-header">
    <div class="content-wrapper clearfix">
      <h1><?=$news_cat_name;?></h1>
      <p>
        <a href="/<?=$home_page_url;?>"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right" aria-hidden="true"></i>
        <a href="/<?=$current_lang;?>/<?=$content_pretty_url;?>"><?=$current_content_menu_text;?></a> <i class="fa fa-angle-right" aria-hidden="true"></i>
        <?=$news_cat_name;?>
      </p>	
    </div>
  </div>
  
  <div class="content-wrapper clearfix">
    <main class="main-content">
      <?php 
        list_news($offset = false,$news_count = false, $news_category_id); 
      ?>
    </main>
 
    <aside class="sidebar-content">
      <?php print_html_news_sidebar($print_latest_news = true);?>
    </aside>
  </div>
<?php
  print_html_footer();  
?>