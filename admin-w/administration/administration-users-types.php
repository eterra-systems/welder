<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages['text_users_types'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['text_users_types'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="/<?=$_SESSION['admin_dir_name'];?>/administration/administration-users-types-add-new.php">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['text_add_new_users_type'];?>" />
          <?=$languages['text_add_new_users_type'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="60%" class="text_left"><?=$languages['header_name'];?></th>
            <th width="20%"><?=$languages['header_reorder'];?></th>
            <th width="20%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <input type="hidden" id="text_cannnot_delete_admin" value="<?=$languages['text_cannnot_delete_admin'];?>">
      <div id="users_types_list" class="list_container">
<?php
        list_users_types($current_language_id);
?>
      </div>
    </div>
  </main>
<?php 
    print_html_admin_footer();
?>
</body>
</html>