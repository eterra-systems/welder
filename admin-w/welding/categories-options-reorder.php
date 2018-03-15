<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_POST['cancel'])) {
    header('Location: categories-options.php');
  }
  if(isset($_POST['revert_changes'])) {
    header('Location: categories-options-reorder.php');
  }
  
  if(isset($_POST['submit'])) {
    
    //echo"<pre>";print_r($_POST);echo"</pre>";exit;
  
    if(isset($_POST['categories_options'])) {
      $categories_options = $_POST['categories_options'];
    }
  
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    $user_id = $_SESSION['admin']['user_id'];
  
    foreach($categories_options as $option_sort_order => $option_id) {

      $option_sort_order++;

      $query_update_option = "UPDATE `options` SET `option_sort_order`='$option_sort_order' WHERE `option_id` = '$option_id'";
      $all_queries .= "<br>".$query_update_option;
      //echo $query_update_option;
      $result_update_option = mysqli_query($db_link, $query_update_option);
      if(!$result_update_option) {
        echo $languages['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  
    header('Location: categories-options.php');
  }
  
  $page_title = $languages['page_title_reorder_product_options'];
  $page_description = $languages['company_name']." администрация";
  
  $additional_script = '<script src="../js/jquery.mjs.nestedSortable.js" type="text/javascript"></script>';
  
  print_html_admin_header($page_title, $page_description, $additional_css = false, $additional_script);
  
?>

<!--categories-options list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/<?=$_SESSION['admin_dir_name'];?>/welding/categories-options.php" title="<?=$languages['header_categories_options'];?>"><?=$languages['header_categories_options'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_reorder_pages'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_reorder_pages'];?></h1>
      
      <form method="post" name="reorder_categories_options" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages['btn_revert_changes'];?></button>
          <input type="hidden" name="url_for_rights" id="url_for_rights" value="/welding/categories-options.php" />
        </div>
        <div class="clearfix"></div>

        <div id="reorder_pages">
<?php
          list_categories_options_for_reorder();
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
<!--categories_options list-->

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