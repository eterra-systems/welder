<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['content_type_id'])) {
    $current_content_type_id = $_GET['content_type_id'];
    
    include_once 'content-types-details.php';
  }
  else {
    
    $page_title = $languages['page_title_content_types'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--content_types list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_content_types'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
 
      <section class="options">
        <a class="pageoptions add_new_link" href="content-types-add-new.php">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new'];?>" />
          <?=$languages['link_add_new_content_type'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="3%" class="text_left">&num;</th>
            <th width="40%" class="text_left"><?=$languages['header_name'];?></th>
            <th width="30%" class="text_left"><?=$languages['header_alias'];?></th>
            <th width="5%"><?=$languages['header_status'];?></th>
            <th width="10%"><?=$languages['header_reorder'];?></th>
            <th width="12%"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>

      <div id="content_types_list" class="list_container">
<?php
        list_content_types();
?>
      </div>
    </div>
  </main>
<!--content_types list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }