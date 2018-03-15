<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages['company_name']." администрация";
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
<!--main-->
  <main>
    <div class="inside_container">
      
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_menu'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="administration-menu-add-new.php">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_language'];?>" title="<?=$languages['title_add_new_language'];?>" />
          <?=$languages['link_add_new_menu'];?>
        </a>
        <a class="pageoptions edit_link" href="javascript:;" onclick="ToggleExpandMenu('all','expand')" title="<?=$languages['title_expand_all_sections'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/expandall.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_expand_all_sections'];?>" />
          <?=$languages['menu_expand_all_sections'];?>
        </a>
        <a class="pageoptions edit_link" href="javascript:;" onclick="ToggleExpandMenu('all','collapse')" title="<?=$languages['title_collapse_all_sections'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/contractall.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_collapse_all_sections'];?>" />
          <?=$languages['menu_collapse_all_sections'];?>
        </a>
        <a class="pageoptions edit_link" href="administration-menu-reorder.php" title="<?=$languages['title_reorder_menus'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/reorder.gif" class="systemicon" width="16" height="16" alt="" />
          <?=$languages['menu_reorder_pages'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&nbsp;</th>
            <th width="5%" class="text_left">&num;</th>
            <th width="60%" class="text_left"><?=$languages['header_menu_name'];?></th>
            <th width="5%"><?=$languages['header_menu_is_active'];?></th>
            <th width="14%"><?=$languages['header_reorder'];?></th>
            <th width="14%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="menus_list" class="list_container">
<?php
        list_menus($parent_id = 0, $path_number = 0);
?>
      </div>
      
    </div>
  </main>
<!--main-->
<?php 
    print_html_admin_footer();
?>
</body>
</html>