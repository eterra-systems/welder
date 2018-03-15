<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_POST['cancel'])) {
    header('Location: categories.php');
  }
  if(isset($_POST['revert_changes'])) {
    header('Location: categories-reorder.php');
  }
  
  if(isset($_POST['submit'])) {
    
    //print_array_for_debug($_POST);exit;
  
    if(isset($_POST['categories'])) {
      $categories = $_POST['categories'];
    }
  
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    $user_id = $_SESSION['admin']['user_id'];
  
    $root_sort_order = 1;
    
    foreach($categories as $category_root_id => $categories_in_root) {
      
      foreach($categories_in_root as $category_parent_id => $categories_by_parent) {

        $and_root = ($category_parent_id == 0) ? "" : "AND `category_root_id` = '$category_root_id'";
        
        foreach($categories_by_parent as $category_sort_order => $category_id) {

          $category_sort_order++;
          if($category_parent_id == 0) $category_sort_order = $root_sort_order;

          $query_update_category = "UPDATE `category_to_category` SET `category_sort_order`='$category_sort_order' 
                                     WHERE `category_id` = '$category_id' AND `category_parent_id` = '$category_parent_id' $and_root";
          $all_queries .= "<br>".$query_update_category;
          //echo $query_update_category;
          $result_update_category = mysqli_query($db_link, $query_update_category);
          if(!$result_update_category) {
            echo $languages['sql_error_update']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        } 
      }
      
      $root_sort_order++;
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  
    header('Location: categories.php');
  }
  
  $page_title = $languages['page_title_reorder_categories'];
  $page_description = $languages['company_name']." администрация";
  
  $additional_script = '<script src="../js/jquery.mjs.nestedSortable.js" type="text/javascript"></script>';
  
  print_html_admin_header($page_title, $page_description,$additional_css = false , $additional_script);
  
?>

<!--categories list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/<?=$_SESSION['admin_dir_name'];?>/welding/categories.php" title="<?=$languages['title_breadcrumbs_categories'];?>"><?=$languages['header_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_reorder_categories'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_reorder_categories'];?></h1>
      
      <form method="post" name="reorder_categories" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages['btn_revert_changes'];?></button>
          <input type="hidden" name="url_for_rights" id="url_for_rights" value="/categories/categories.php" />
        </div>
        <div class="clearfix"></div>

        <div id="reorder_pages">
<?php
          list_categories_for_reorder($parent_id = 0,$root_id = 0,$path_number = 0);
?>
        </div>

        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages['btn_revert_changes'];?></button>
        </div>
        <div class="clearfix"></div>
      </form>
    </div>
  </main>
<!--categories list-->

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