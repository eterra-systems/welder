<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $page_title = $languages['categories_options_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_categories_options'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="categories-options-add-new.php" title="<?=$languages['title_add_new_categories_options'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_categories_options'];?>" />
          <?=$languages['link_add_new_categories_options'];?>
        </a>
        <a class="pageoptions edit_link" href="categories-options-reorder.php" title="<?=$languages['title_reorder_categories_options'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/reorder.gif" class="systemicon" width="16" height="16" alt="" />
          <?=$languages['menu_reorder_options'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="55%" class="text_left"><?=$languages['header_categories_option_name'];?></th>
            <th width="10%">ID</th>
            <th width="10%"><?=$languages['header_sort_order'];?></th>
            <th width="10%"><?=$languages['header_reorder'];?></th>
            <th width="15%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="categories_options_list" class="list_container">
<?php
        list_categories_options();
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