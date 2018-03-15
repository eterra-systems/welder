<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $return_link = "administration-menu.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $return_link");
  }

  if(isset($_POST['submit'])) {
    
//    echo"<pre>";print_r($_POST);echo"</pre>";exit;
  
    if(isset($_POST['menus'])) {
      $menus = $_POST['menus'];
    }
  
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    $user_id = $_SESSION['admin']['user_id'];
  
    foreach($menus as $menu_parent_id => $menu) {
    
      foreach($menu as $menu_sort_order => $menu_id) {

        $menu_sort_order++;

        $query_update_menu = "UPDATE `menus` SET `menu_sort_order`='$menu_sort_order' WHERE `menu_id` = '$menu_id'";
        $all_queries .= "<br>".$query_update_menu;
        //echo $query_update_menu;
        $result_update_menu = mysqli_query($db_link, $query_update_menu);
        if(!$result_update_menu) {
          echo $languages['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      } 
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  
    header("Location: $return_link");
  }
  
  $page_title = $languages['page_title_reorder_menus'];
  $page_description = $languages['company_name']." администрация";
  
  $additional_script = '<script src="../js/jquery.mjs.nestedSortable.js" type="text/javascript"></script>';
  
  print_html_admin_header($page_title, $page_description, $additional_css = false, $additional_script);
  
?>

<!--menus list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$return_link;?>" title="<?=$languages['title_breadcrumbs_menu'];?>"><?=$languages['header_menu'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_reorder_menu'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_reorder_pages'];?></h1>
      
      <form method="post" name="reorder_menus" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <a href="<?=$_SERVER['PHP_SELF'];?>" class="button red"><?=$languages['btn_revert_changes'];?></a>
        </div>
        <div class="clearfix"></div>

        <div id="reorder_pages">
<?php
          list_menus_for_reorder($parent_id = 0,$path_number = 0);
?>
        </div>

        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <a href="<?=$_SERVER['PHP_SELF'];?>" class="button red"><?=$languages['btn_revert_changes'];?></a>
        </div>
        <div class="clearfix"></div>
      </form>
    </div>
  </main>
<!--menus list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
<script type="text/javascript">
  $(document).ready(function() {
    
    $('ul.sortable').nestedSortable({
      disableParentChange: true,
      disableNesting: 'no-nest',
      forcePlaceholderSize: true,
      handle: 'div',
      items: 'li',
      opacity: .6,
      placeholder: 'placeholder',
      startCollapsed : true,
      tabSize: 25,
      tolerance: 'pointer',
      listType: 'ul',
      toleranceElement: '> div'
    });
    $('.disclose').on('click', function() {
        $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
        $(this).toggleClass('ui-icon-plusthick').toggleClass('ui-icon-minusthick');
    });

  });
</script>
</body>
</html>