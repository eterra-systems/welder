<?php
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $user_rights = get_admin_user_rights($menu_url = str_replace("/".$_SESSION['admin_dir_name'], "", $_SERVER['PHP_SELF']));
  
  $users_rights_edit = $user_rights['users_rights_edit'];
  $users_rights_delete = $user_rights['users_rights_delete'];
  
  if(isset($_GET['banner_id'])) {
    $current_banner_id = $_GET['banner_id'];
    
    include_once 'banners-details.php';
  }
  else {
    
    $page_title = $languages['page_title_banners'];
    $page_description = $languages['company_name']." администрация";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--banners list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_banners'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
 
      <section class="options">
        <a class="pageoptions add_new_link" href="banners-add-new.php" title="<?=$languages['title_add_new_banner'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_banner'];?>" />
          <?=$languages['link_add_new_banner'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&num;</th>
            <th width="63%" class="text_left"><?=$languages['header_image'];?></th>
            <!--<th width="30%" class="text_left"><?=$languages['header_header'];?></th>-->
            <th width="10%"><?=$languages['header_status'];?></th>
            <th width="10%"><?=$languages['header_reorder'];?></th>
            <th width="15%"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="banners_list" class="list_container">
<?php
        list_banners();
?>
      </div>
    </div>
  </main>
<!--banners list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }