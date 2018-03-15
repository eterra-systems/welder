<?php
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
      
  if(isset($_GET['menu_id'])) {
    $current_menu_id = $_GET['menu_id'];
  }
  
  $return_link = "administration-menu.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $return_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_POST['edit_menu'])) {
    
//    echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if(isset($_POST['menu_parent_id_level'])) {
      /*
      *  $_POST['menu_parent_id_level'] has three parameters - menu_id, menu_hierarchy_level and menu_root_id
      */
      $menu_parent_id_level = explode("|", $_POST['menu_parent_id_level']);
      $menu_parent_id = $menu_parent_id_level[0];
      $menu_hierarchy_level = $menu_parent_id_level[1]+1;
    }
    foreach($_POST['menu_names'] as $language_id => $menu_author) {
      if(empty($menu_author)) $menu_errors['menu_names'][$language_id] = $languages['required_field_error'];
      
      $menu_names_array[$language_id] = $_POST['menu_names'][$language_id];
      $menu_has_record_array[$language_id] = $_POST['menu_has_record_in_gb'][$language_id];
    }
    if(isset($_POST['menu_url'])) {
      $menu_url = mysqli_real_escape_string($db_link,$_POST['menu_url']);
    }
    if(isset($_POST['menu_css_id'])) {
      $menu_css_id = mysqli_real_escape_string($db_link,$_POST['menu_css_id']);
    }
    if(isset($_POST['menu_image_url'])) {
      $menu_image_url = mysqli_real_escape_string($db_link,$_POST['menu_image_url']);
    }
    if(isset($_POST['menu_path_name'])) {
      $menu_path_name = $_POST['menu_path_name'];
    }
    $menu_has_children = 0;
      if(isset($_POST['menu_has_children'])) $menu_has_children = 1;
    $menu_use_as_include_block = 0;
      if(isset($_POST['menu_use_as_include_block'])) $menu_use_as_include_block = 1;
    if(isset($_POST['menu_include_block_fn'])) {
      $menu_include_block_fn = $_POST['menu_include_block_fn'];
    }
    if(isset($_POST['menu_include_block_class'])) {
      $menu_include_block_class = $_POST['menu_include_block_class'];
    }
    $menu_show_in_menu = 0;
      if(isset($_POST['menu_show_in_menu'])) $menu_show_in_menu = 1;
    $menu_is_active = 0;
      if(isset($_POST['menu_is_active'])) $menu_is_active = 1;

    if(!isset($menu_errors)) {
      //if there are no form errors we can insert the information
      
      $menu_css_id = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_css_id']));
      $menu_image_url = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_image_url']));
      $menu_include_block_fn = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_include_block_fn']));
      $menu_include_block_class = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_include_block_class']));

      $query = "UPDATE `menus` SET `menu_parent_id` = '$menu_parent_id', 
                                    `menu_hierarchy_level` = '$menu_hierarchy_level',
                                    `menu_has_children` = '$menu_has_children',
                                    `menu_path_name` = '$menu_path_name',
                                    `menu_url` = '$menu_url', 
                                    `menu_css_id` = $menu_css_id, 
                                    `menu_image_url` = $menu_image_url,  
                                    `menu_use_as_include_block` = '$menu_use_as_include_block', 
                                    `menu_include_block_fn` = $menu_include_block_fn, 
                                    `menu_include_block_class` = $menu_include_block_class, 
                                    `menu_show_in_menu` = '$menu_show_in_menu', 
                                    `menu_is_active` = '$menu_is_active'
                              WHERE `menu_id` = '$current_menu_id'";
      $all_queries .= "<br>".$query;
      $result = mysqli_query($db_link, $query);
      if(!$result) {
        echo $languages['sql_error_update']." - 1 menus ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($menu_names_array as $language_id => $menu_name) {
        
        $menu_has_record = $menu_has_record_array[$language_id];
        if($menu_has_record == 1) {
          $query_update_menus_translation = "UPDATE `menus_translations` SET `menu_translation_text`='$menu_name'
                                            WHERE `menu_id` = '$current_menu_id' AND `language_id` = '$language_id'";
          $all_queries .= "<br>".$query_update_menus_translation;
          $result_update_menus_translation = mysqli_query($db_link, $query_update_menus_translation);
          if(!$result_update_menus_translation) {
            echo $languages['sql_error_update']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_insert_menus_translation = "INSERT INTO `menus_translations`(`menu_id`,
                                                                            `language_id`,
                                                                            `menu_translation_text`) 
                                                                    VALUES ('$current_menu_id',
                                                                            '$language_id',
                                                                            '$menu_name')";
          $all_queries .= "<br>".$query_insert_menus_translation;
          $result_insert_menus_translation = mysqli_query($db_link, $query_insert_menus_translation);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
    }
    
//    echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    header("Location: $return_link");
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages['menu_details_new_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
  
  $query_menu = "SELECT `menu_id`,`menu_parent_id`,`menu_hierarchy_level`,`menu_has_children`,`menu_path_name`,`menu_url`,`menu_css_id`,
                        `menu_image_url`,`menu_sort_order`,`menu_show_in_menu`,`menu_use_as_include_block`,`menu_include_block_fn`,`menu_include_block_class`,
                        `menu_is_collapsed`,`menu_is_active` 
                FROM `menus`
                WHERE `menu_id` = '$current_menu_id'";
  //echo $query_menu;
  $result_menu = mysqli_query($db_link, $query_menu);
  if(!$result_menu) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_menu) > 0) {
    $menu_array = mysqli_fetch_assoc($result_menu);
    $menu_parent_id = $menu_array['menu_parent_id'];
    $menu_hierarchy_level = $menu_array['menu_hierarchy_level'];
    $menu_has_children = $menu_array['menu_has_children'];
    $menu_path_name = $menu_array['menu_path_name'];
    $menu_url = stripslashes($menu_array['menu_url']);
    $menu_css_id = stripslashes($menu_array['menu_css_id']);
    $menu_image_url = $menu_array['menu_image_url'];
    $menu_use_as_include_block = $menu_array['menu_use_as_include_block'];
    $menu_include_block_fn = $menu_array['menu_include_block_fn'];
    $menu_include_block_class = $menu_array['menu_include_block_class'];
    $menu_show_in_menu = $menu_array['menu_show_in_menu'];
    $menu_is_active = $menu_array['menu_is_active'];
  }
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$return_link;?>" title="<?=$languages['title_breadcrumbs_menu'];?>"><?=$languages['header_menu'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_menu_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_menu_edit'];?></h1>
      
      <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
        <p>
          <button type="submit" name="edit_menu" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>
<?php
      if(!empty($languages_array)) {
        
        $key = 0;
              
        foreach($languages_array as $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          $menu_has_record_in_gb[$language_id] = 0;
          
          if(!isset($_POST['edit_menu'])) {
            $query_menu_translation_text = "SELECT `menus_translations`.`menu_translation_text`
                                            FROM `menus_translations`
                                            WHERE `menus_translations`.`menu_id` = '$current_menu_id' AND `menus_translations`.`language_id` = '$language_id'";
            //echo "$query_menu_translation_text<br>";
            $result_menu_translation_text = mysqli_query($db_link, $query_menu_translation_text);
            if(!$result_menu_translation_text) { echo mysqli_error($db_link); }
            if(mysqli_num_rows($result_menu_translation_text) > 0) {
              $menu_translation_text = mysqli_fetch_assoc($result_menu_translation_text);

              $menu_names_array[$language_id] = $menu_translation_text['menu_translation_text'];
              $menu_has_record_in_gb[$language_id] = 1;
            }
            else {
              $menu_names_array[$language_id] = "";
              $menu_has_record_in_gb[$language_id] = 0;
            }
          } 
?>
          <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <?php
            if($key == 0) {
          ?>
            <p class="title"><?=$languages['header_menu_name'];?><span class="red">*</span></p>
          <?php
            }
            if(isset($menu_errors['menu_names'][$language_id])) {
              echo "<div class='error'>".$menu_errors['menu_names'][$language_id]."</div>";
            }
          ?>
            <input type="text" name="menu_names[<?=$language_id;?>]" id="menu_name_<?=$language_id;?>" value="<?=$menu_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
            <input type="hidden" name="menu_has_record_in_gb[<?=$language_id;?>]" id="menu_has_record_in_gb_<?=$language_code;?>" value="<?=$menu_has_record_in_gb[$language_id];?>" >
          </div>
          <p class="clearfix"></p>
<?php
          $key++;
        }
      }
?>
        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_parent'];?><span class="red">*</span></p>
          <select name="menu_parent_id_level" id="menu_parent_id_level">
            <option value="0|0"></option>
            <?php list_menu_for_select_a_parent($parent_id = 0, $path_number = 0, $menu_parent_id); ?>
          </select>
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_url_address'];?></p>
          <input type="text" name="menu_url" id="menu_url" value="<?=$menu_url;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_css_id'];?></p>
          <input type="text" name="menu_css_id" id="menu_css_id" value="<?=$menu_css_id;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_directory_path'];?></p>
          <input type="text" name="menu_path_name" id="menu_path_name" value="<?=$menu_path_name;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_image'];?></p>
          <input type="text" name="menu_image_url" id="menu_image_url" value="<?=$menu_image_url;?>" />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_menu_is_active'];?></p>
          <?php
            if(isset($menu_is_active) && $menu_is_active == 0) echo '<input type="checkbox" name="menu_is_active" id="menu_is_active" />';
            else echo '<input type="checkbox" name="menu_is_active" id="menu_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_menu_has_children'];?></p>
          <input type="checkbox" name="menu_has_children" class="menu_has_children" <?php if ($menu_has_children == 1) echo 'checked="checked"';?> />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_menu_use_as_include_block'];?></p>
          <input type="checkbox" name="menu_use_as_include_block" id="menu_use_as_include_block" <?php if ($menu_use_as_include_block == 1) echo 'checked="checked"';?> />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_include_block_fn'];?></p>
          <input type="text" name="menu_include_block_fn" id="menu_include_block_fn" value="<?=$menu_include_block_fn;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_include_block_class'];?></p>
          <input type="text" name="menu_include_block_class" id="menu_include_block_class" value="<?=$menu_include_block_class;?>" />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_menu_visible'];?></p>
          <input type="checkbox" name="menu_show_in_menu" class="menu_show_in_menu" <?php if ($menu_show_in_menu == 1) echo 'checked="checked"';?> />
        </div>
        <div class="clearfix"></div>
        
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
          <button type="submit" name="edit_menu" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>