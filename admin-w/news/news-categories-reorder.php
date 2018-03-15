<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $back_link = "news-categories.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  if(isset($_POST['revert_changes'])) {
    header('Location: content-reorder.php');
  }
  
  if(isset($_POST['submit'])) {
    
    if(isset($_POST['news_categories'])) {
      $news_categories = $_POST['news_categories'];
    }

    mysqli_query($db_link,"BEGIN");

    $all_queries = "";
    $user_id = $_SESSION['admin']['user_id'];

    foreach($news_categories as $news_categories_parent_id => $news_categories) {

      foreach($news_categories as $news_cat_sort_order => $news_category_id) {

        $news_cat_sort_order++;

        $query_update_news_cat = "UPDATE `news_categories` SET `news_cat_sort_order`='$news_cat_sort_order' WHERE `news_category_id` = '$news_category_id'";
        $all_queries .= "<br>".$query_update_news_cat;
        //echo $query_update_news_cat;
        $result_update_news_cat = mysqli_query($db_link, $query_update_news_cat);
        if(!$result_update_news_cat) {
          echo $languages['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      } 
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  
    header("Location: $back_link");
  
  }
  
  $page_title = $languages['page_title_reorder_news_categories'];
  $page_description = $languages['company_name']." администрация";
  
  $additional_script = '<script src="../js/jquery.mjs.nestedSortable.js" type="text/javascript"></script>';
  
  print_html_admin_header($page_title, $page_description, $additional_css = false, $additional_script);
  
?>

<!--news_categories list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_news_categories'];?>"><?=$languages['header_news_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_reorder_news_categories'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_reorder_news_categories'];?></h1>
      
      <form method="post" name="reorder_news_categories" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <div class="reorder_news_categories_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages['btn_revert_changes'];?></button>
          <input type="hidden" name="url_for_rights" id="url_for_rights" value="/news_categories/news_categories.php" />
        </div>
        <div class="clearfix"></div>

        <div id="reorder_news_categories">
<?php
          list_news_categories_for_reorder($news_cat_parent_id = 0,$path_number = 0);
?>
        </div>

        <div class="reorder_news_categories_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages['btn_revert_changes'];?></button>
        </div>
        <div class="clearfix"></div>
      </form>
    </div>
  </main>
<!--news_categories list-->

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