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

  $content_type_id = 7; // news
  $query_content = "SELECT `contents`.`content_hierarchy_ids`,`contents_descriptions`.`content_hierarchy_path`,`contents_descriptions`.`content_name`,
                           `contents_descriptions`.`content_menu_text`,`contents_descriptions`.`content_meta_title`,
                           `contents_descriptions`.`content_meta_keywords`,`contents_descriptions`.`content_meta_description`
                      FROM `contents`
                INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                     WHERE `content_type_id` = '$content_type_id' AND `contents_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_name = stripslashes($content_array['content_name']);
    $current_content_menu_text = stripslashes($content_array['content_menu_text']);
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_hierarchy_path = $content_array['content_hierarchy_path'];
    $content_meta_title = stripslashes($content_array['content_meta_title']);
    $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
    $content_meta_description = stripslashes($content_array['content_meta_description']);
  }
      
  print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_css_javascript = false, $body_css = "news-categories");
?>
  <a name="news_anchor" id="news_anchor"></a>
  <!-- WRAPPER-->
  
  <div id="page-header">
    <div class="content-wrapper clearfix">
      <h1><?=$news_cat_name;?></h1>
      <p>
        <a href="/<?=$home_page_url;?>"><i class="fa fa-home"></i></a> <i class="fa fa-angle-right" aria-hidden="true"></i>
        <a href="/<?=$current_lang;?>/<?=$content_hierarchy_path;?>"><?=$current_content_menu_text;?></a> <i class="fa fa-angle-right" aria-hidden="true"></i>
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