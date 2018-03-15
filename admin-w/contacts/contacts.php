<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['contact_id'])) {
    $current_contact_id = $_GET['contact_id'];
    
    include_once 'contacts-details.php';
  }
  else {
    
    $page_title = $languages['page_title_contacts'];
    $page_description = $languages['company_name']." администрация";

    print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_contacts'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="contacts-add-new.php" title="<?=$languages['title_add_new_contact'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_contact'];?>" />
          <?=$languages['link_add_new_contact'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">ID</th>
            <th width="20%" class="text_left"><?=$languages['header_site'];?></th>
            <th width="40%" class="text_left"><?=$languages['header_address'];?></th>
            <th width="13%"><?=$languages['header_use_by_default'];?></th>
            <th width="5%"><?=$languages['header_is_active'];?></th>
            <th width="10%"><?=$languages['header_reorder'];?></th>
            <th width="10%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>

      <div id="contacts_list" class="list_container">
<?php
        list_contacts();
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