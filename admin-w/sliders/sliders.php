<?php
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['slider_id'])) {
    $current_slider_id = $_GET['slider_id'];
    
    include_once 'sliders-details.php';
  }
  else {
    
    $page_title = $languages['page_title_sliders'];
    $page_description = $languages['company_name']." администрация";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--sliders list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_sliders'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
 
      <section class="options">
        <a class="pageoptions add_new_link" href="sliders-add-new.php" title="<?=$languages['title_add_new_slider'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_slider'];?>" />
          <?=$languages['link_add_new_slider'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&num;</th>
            <th width="43%" class="text_left"><?=$languages['header_image'];?></th>
            <th width="30%" class="text_left"><?=$languages['header_header'];?></th>
            <th width="5%"><?=$languages['header_status'];?></th>
            <th width="10%"><?=$languages['header_reorder'];?></th>
            <th width="10%"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      
      <div id="sliders_list" class="list_container">
<?php
        list_sliders();
?>
      </div>
    </div>
  </main>
<!--sliders list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }