<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $page_title = $languages['page_title_contents'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_contents'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_contents'];?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="content-add-new.php" title="<?=$languages['title_add_new_content'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_content'];?>" />
          <?=$languages['link_add_new_content'];?>
        </a>
        <a class="pageoptions edit_link" href="javascript:;" onclick="ToggleExpandContent('all','expand')" title="<?=$languages['title_expand_all_sections'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/expandall.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_expand_all_sections'];?>" />
          <?=$languages['menu_expand_all_sections'];?>
        </a>
        <a class="pageoptions edit_link" href="javascript:;" onclick="ToggleExpandContent('all','collapse')" title="<?=$languages['title_collapse_all_sections'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/contractall.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_collapse_all_sections'];?>" />
          <?=$languages['menu_collapse_all_sections'];?>
        </a>
        <a class="pageoptions edit_link" href="content-reorder.php" <?=$_SESSION['edit_link_fn'];?> title="<?=$languages['title_reorder_pages'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/reorder.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_reorder_pages'];?>" />
          <?=$languages['menu_reorder_pages'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&nbsp;</th>
            <th width="5%" class="text_left">&num;</th>
            <th width="25%" class="text_left"><?=$languages['header_content_menu_name'];?></th>
            <th width="15%" class="text_left"><?=$languages['header_content_alias'];?></th>
            <th width="10%"><?=$languages['header_content_type'];?></th>
            <th width="10%"><?=$languages['header_content_is_active'];?></th>
            <th width="10%"><?=$languages['header_content_is_home_page'];?></th>
            <th width="7%"><?=$languages['header_reorder'];?></th>
            <th width="12%"><?=$languages['header_actions'];?></th>
            <th width="4%" title="<?=$languages['title_toggle_checkbox_all'];?>">
              <input id="selectall" type="checkbox" onclick="SelectAllCheckboxes(this,'multicontent')" />
            </th>
          </tr>
        </thead>
      </table>

      <div id="contents_list" class="list_container">
<?php
        list_contents($parent_id = 0, $path_number = 0);
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
