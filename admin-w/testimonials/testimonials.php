<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['testimonial_id'])) {
    $current_testimonial_id = $_GET['testimonial_id'];
    
    include_once 'testimonials-details.php';
  }
  else {
    
    $page_title = $languages['page_title_testimonials'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--testimonials list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_testimonials'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
 
      <section class="options">
        <a class="pageoptions add_new_link" href="testimonials-add-new.php" title="<?=$languages['title_add_new_testimonial'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_testimonial'];?>" />
          <?=$languages['link_add_new_testimonial'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&num;</th>
            <th width="43%" class="text_left"><?=$languages['header_image'];?></th>
            <th width="30%" class="text_left"><?=$languages['header_author'];?></th>
            <th width="5%"><?=$languages['header_status'];?></th>
            <th width="10%"><?=$languages['header_reorder'];?></th>
            <th width="10%"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>

      <div id="testimonials_list" class="list_container">
<?php
        list_testimonials();
?>
      </div>
    </div>
  </main>
<!--testimonials list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }