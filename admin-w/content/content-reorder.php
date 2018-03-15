<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $back_link = "content.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  if(isset($_POST['revert_changes'])) {
    header('Location: content-reorder.php');
  }
  
  if(isset($_POST['submit'])) {
    
    if(isset($_POST['contents'])) {
      $contents = $_POST['contents'];
    }

    mysqli_query($db_link,"BEGIN");

    $all_queries = "";
    $user_id = $_SESSION['admin']['user_id'];

    foreach($contents as $content_parent_id => $content) {

      foreach($content as $content_menu_order => $content_id) {

        $content_menu_order++;

        $query_update_content = "UPDATE `contents` SET `content_menu_order`='$content_menu_order' WHERE `content_id` = '$content_id'";
        $all_queries .= "<br>".$query_update_content;
        //echo $query_update_content;
        $result_update_content = mysqli_query($db_link, $query_update_content);
        if(!$result_update_content) {
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
  
  $page_title = $languages['page_title_reorder_contents'];
  $page_description = $languages['company_name']." администрация";
  
  $additional_script = '<script src="../js/jquery.mjs.nestedSortable.js" type="text/javascript"></script>';
  
  print_html_admin_header($page_title, $page_description,$additional_css = false, $additional_script);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_pages'];?>"><?=$languages['header_contents'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_reorder_pages'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_reorder_pages'];?></h1>
      
      <form method="post" name="reorder_contents" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages['btn_revert_changes'];?></button>
        </div>
        <div class="clearfix"></div>

        <div id="reorder_pages">
<?php
          list_contents_for_reorder($parent_id = 0,$path_number = 0);
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
<!--contents list-->

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
  });
</script>
</body>
</html>