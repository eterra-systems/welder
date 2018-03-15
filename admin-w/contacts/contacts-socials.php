<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
    
  if(isset($_GET['contact_social_id'])) {
    $current_contact_social_id = $_GET['contact_social_id'];
    
    include_once 'contacts-socials-details.php';
  }
  else {
  
    $page_title = $languages['page_title_social_contacts'];
    $page_description = $languages['company_name']." администрация";

    print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_contact_socials'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="contacts-socials-add-new.php" title="<?=$languages['title_add_new_contact_social'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_contact_social'];?>" />
          <?=$languages['link_add_new_contact_social'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">ID</th>
            <th width="20%" class="text_left"><?=$languages['header_social_network'];?></th>
            <th width="48%" class="text_left"><?=$languages['header_address'];?></th>
            <th width="10%"><?=$languages['header_is_active'];?></th>
            <th width="10%"><?=$languages['header_reorder'];?></th>
            <th width="10%"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>

      <div id="contact_socials_list" class="list_container">
<?php
      list_contacts_socials();
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