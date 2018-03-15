<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['news_category_id'])) {
    $current_news_category_id = $_GET['news_category_id'];
    
    include_once 'news-categories-details.php';
  }
  else {
    
    $page_title = $languages['news_categories_title'];
    $page_description = $languages['company_name']." администрация";

    print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_news_categories'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="news-categories-add-new.php" title="<?=$languages['title_add_news_category'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_news'];?>" />
          <?=$languages['link_add_new_category'];?>
        </a>
        <a class="pageoptions edit_link" href="javascript:;" onclick="ToggleExpandNewsCategory('all','expand')" title="<?=$languages['title_expand_all_sections'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/expandall.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_expand_all_sections'];?>" />
          <?=$languages['menu_expand_all_sections'];?>
        </a>
        <a class="pageoptions edit_link" href="javascript:;" onclick="ToggleExpandNewsCategory('all','collapse')" title="<?=$languages['title_collapse_all_sections'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/contractall.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_collapse_all_sections'];?>" />
          <?=$languages['menu_collapse_all_sections'];?>
        </a>
        <a class="pageoptions edit_link" href="news-categories-reorder.php" title="<?=$languages['title_reorder_news_categories'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/reorder.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_reorder_news_categories'];?>" />
          <?=$languages['menu_reorder_news_categories'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&nbsp;</th>
            <th width="5%" class="text_left">&num;</th>
            <th width="63%" class="text_left"><?=$languages['header_news_category_name'];?></th>
            <th width="15%"><?=$languages['header_reorder'];?></th>
            <th width="15%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="news_categories_list" class="list_container">
<?php
        list_news_categories($news_cat_parent_id = 0, $path_number = 0);
?>
      </div>
    </div>
  </main>
<!--contents list-->

<?php
  
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }