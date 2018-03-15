<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  //echo "<pre>";print_r($_SERVER);
  //start_page_build_time_measure();
  
  if(isset($_GET['news_id'])) {
    $current_news_id = $_GET['news_id'];
    
    include_once 'news-details.php';
  }
  else {
    
    $news_cat_id = 0;
    $filters_array = "";
    $order_by = "`news`.`news_post_date` DESC";
    $page_limit = 25;
    if(isset($_POST['submit_filter'])) {
      //echo "<pre>";print_r($_POST);echo "</pre>";exit;

      $filters_array = $_POST;

      $category = $filters_array['news_cat_parent_params'];
      if($category != "all") {
        // $_POST['news_cat_parent_params'] has three parameters - parent_id, hierarchy_ids and hierarchy_level
        $news_cat_parent_params = explode("+", $_POST['news_cat_parent_params']);
        $news_cat_id = $news_cat_parent_params[0];
        $news_cat_hierarchy_ids = $news_cat_parent_params[1];
        $news_cat_hierarchy_level = $news_cat_parent_params[2]+1;
      }
      $order_by = $filters_array['order_by'];
      $page_limit = $filters_array['page_limit'];
    }
  
    $page_title = $languages['page_title_news'];
    $page_description = $languages['company_name']." администрация";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--newss list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['menu_news'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_news'];?></h1>

      <fieldset>
      <legend><?=$languages['header_filters'];?></legend>
        <form method="post" name="filter_news" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
          <div>
            <label for="news_cat_parent_params" class="title"><?=$languages['header_categories'];?>:</label>
            <select name="news_cat_parent_params" style="width: auto;">
              <option value="all" selected="selected"><?=$languages['option_choose_categories_for_product'];?></option>
              <?php
                  list_news_categories_for_select_for_news($news_cat_id);
              ?>
            </select>
            <span class="hidden"><?=$languages['text_show_children_cat'];?>: <input type="checkbox" class="cms_checkbox" name="show_subcategories" /></span>
          </div>
          <div>
            <label for="order_by" class="title"><?=$languages['header_sort_order'];?>:</label>
            <select name="order_by" style="width: auto;">
              <option value="`news_post_date` DESC" <?php if($order_by == "`news_post_date` DESC") echo 'selected="selected"';?>><?=$languages['option_post_date_desc'];?></option>
              <option value="`news_post_date` ASC" <?php if($order_by == "`news_post_date` ASC") echo 'selected="selected"';?>><?=$languages['option_post_date_asc'];?></option>
              <option value="`news_end_time` DESC" <?php if($order_by == "`news_end_time` DESC") echo 'selected="selected"';?>><?=$languages['option_expiry_date_desc'];?></option>
              <option value="`news_end_time` ASC" <?php if($order_by == "`news_end_time` DESC") echo 'selected="selected"';?>><?=$languages['option_expiry_date_asc'];?></option>
              <option value="`news_title` ASC" <?php if($order_by == "`news_title` ASC") echo 'selected="selected"';?>><?=$languages['option_title_asc'];?></option>
              <option value="`news_title` DESC" <?php if($order_by == "`news_title` DESC") echo 'selected="selected"';?>><?=$languages['option_title_desc'];?></option>
              <option value="`news_is_active` DESC" <?php if($order_by == "`news_is_active` DESC") echo 'selected="selected"';?>><?=$languages['option_status_desc'];?></option>
              <option value="`news_is_active` ASC" <?php if($order_by == "`news_is_active` ASC") echo 'selected="selected"';?>><?=$languages['option_status_asc'];?></option>
            </select>
          </div>
          <div>
            <label for="page_limit" class="title"><?=$languages['header_page_limit'];?>:</label>
            <select name="page_limit" style="width: auto;">
              <option value="5" <?php if($page_limit == 5) echo 'selected="selected"';?>>5</option>
              <option value="25" <?php if($page_limit == 25) echo 'selected="selected"';?>>25</option>
              <option value="50" <?php if($page_limit == 50) echo 'selected="selected"';?>>50</option>
              <option value="100" <?php if($page_limit == 100) echo 'selected="selected"';?>>100</option>
              <option value="500" <?php if($page_limit == 500) echo 'selected="selected"';?>>500</option>
              <option value="1000" <?php if($page_limit == 1000) echo 'selected="selected"';?>>1000</option>
              <option value="0" <?php if($page_limit == 0) echo 'selected="selected"';?>><?=$languages['option_unlimited'];?></option>
            </select>
          </div>
          <p>&nbsp;</p>
          <div>
            <button type="submit" name="submit_filter" id="submit_filter" class="button blue"><i class="fa fa-filter" aria-hidden="true"></i> <?=$languages['btn_filter'];?></button>
            <!--<input type="submit" name="submit_filter" id="submit_filter" class="button blue" value="<?=$languages['btn_filter'];?>" style="width: auto;" />-->
          </div>
        </form>

      </fieldset>
      
      <section class="options margin_bottom">
        <a class="pageoptions add_new_link" href="news-add-new.php" title="<?=$languages['title_add_new_news'];?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['alt_add_new_news'];?>" />
          <?=$languages['link_add_new_news'];?>
        </a>
      </section>

      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&num;</th>
            <th width="33%" class="text_left"><?=$languages['header_news_title'];?></th>
            <th width="12%" class="text_left"><?=$languages['header_news_post_date'];?></th>
            <th width="8%" class="text_left"><?=$languages['header_news_start_time'];?></th>
            <th width="8%" class="text_left"><?=$languages['header_news_end_time'];?></th>
            <th width="20%"><?=$languages['header_news_category'];?></th>
            <th width="5%"><?=$languages['header_news_status'];?></th>
            <th width="9%"><?=$languages['header_actions'];?></th>
            <th width="4%" title="<?=$languages['title_toggle_checkbox_all'];?>">
              <input id="selectall" type="checkbox" onclick="SelectAllCheckboxes(this)" />
            </th>
          </tr>
        </thead>
      </table>

      <div id="news_list" class="list_container">
<?php
        list_news($filters_array);
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