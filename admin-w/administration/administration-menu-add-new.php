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
  
  $menu_parent_id = 0;
  if(isset($_POST['add_menu'])) {
    
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
    foreach($_POST['menu_names'] as $language_id => $menu_name) {
      if(empty($menu_name)) $menu_errors['menu_names'][$language_id] = $languages['required_field_error'];
      
      $menu_names_array[$language_id] = $_POST['menu_names'][$language_id];
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
    $menu_is_collapsed = 0;
    $menu_sort_order = get_last_sort_order("menus", "menu_sort_order", "menu_parent_id", $menu_parent_id)+1;

    if(!isset($menu_errors)) {
      //if there are no form errors we can insert the information
      
      $menu_css_id = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_css_id']));
      $menu_image_url = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_image_url']));
      $menu_include_block_fn = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_include_block_fn']));
      $menu_include_block_class = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_include_block_class']));

      $query = "INSERT INTO `menus`(`menu_id`, 
                                   `menu_parent_id`, 
                                   `menu_hierarchy_level`, 
                                   `menu_has_children`, 
                                   `menu_path_name`,
                                   `menu_url`, 
                                   `menu_css_id`, 
                                   `menu_image_url`, 
                                   `menu_sort_order`, 
                                   `menu_show_in_menu`, 
                                   `menu_use_as_include_block`, 
                                   `menu_include_block_fn`, 
                                   `menu_include_block_class`, 
                                   `menu_is_collapsed`, 
                                   `menu_is_active`) 
                           VALUES (NULL,
                                   '$menu_parent_id',
                                   '$menu_hierarchy_level',
                                   '$menu_has_children',
                                   '$menu_path_name',
                                   '$menu_url',
                                   $menu_css_id,
                                   $menu_image_url,
                                   '$menu_sort_order',
                                   '$menu_show_in_menu',
                                   '$menu_use_as_include_block',
                                   $menu_include_block_fn,
                                   $menu_include_block_class,
                                   '$menu_is_collapsed',
                                   '$menu_is_active')";
      $all_queries .= "<br>".$query;
      mysqli_query($db_link, $query);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." 1 - `menus` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $menu_id = mysqli_insert_id($db_link);

      foreach($menu_names_array as $language_id => $menu_name) {

        $query_insert_menus_translation = "INSERT INTO `menus_translations`(`menu_id`,
                                                                          `language_id`,
                                                                          `menu_translation_text`) 
                                                                  VALUES ('$menu_id',
                                                                          '$language_id',
                                                                          '$menu_name')";
        $all_queries .= "<br>".$query_insert_menus_translation;
        mysqli_query($db_link, $query_insert_menus_translation);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." 2 - `menus_translations` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      /*
       * give rights to the users for the new menu according to their type
       */
      
      $inserted_user_type_ids = array();
      $query_select_users = "SELECT `users`.`user_id`,`users`.`user_type_id`,`users_types`.`user_type_is_superuser` 
                              FROM `users` 
                              INNER JOIN `users_types` USING(`user_type_id`)";
      $result_select_users = mysqli_query($db_link, $query_select_users);
      if(!$result_select_users) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_select_users) > 0) {
        while($row_select_users = mysqli_fetch_assoc($result_select_users)) {
          
          $user_id = $row_select_users['user_id'];
          $user_type_id = $row_select_users['user_type_id'];
          $user_type_is_superuser = $row_select_users['user_type_is_superuser'];
      
          $users_rights_access = 0;
          $users_rights_add = 0;
          $users_rights_edit = 0;
          $users_rights_delete = 0;
      
          if($user_type_is_superuser == 1) {
            $users_rights_access = 1;
            $users_rights_add = 1;
            $users_rights_edit = 1;
            $users_rights_delete = 1;
          }

          $query_insert_users_rights = "INSERT INTO `users_rights`(`users_rights_id`, 
                                                                  `user_id`, 
                                                                  `menu_id`,  
                                                                  `users_rights_access`,  
                                                                  `users_rights_add`,  
                                                                  `users_rights_edit`, 
                                                                  `users_rights_delete`)
                                                          VALUES (NULL,
                                                                  '$user_id',
                                                                  '$menu_id',
                                                                  '$users_rights_access',
                                                                  '$users_rights_add',
                                                                  '$users_rights_edit',
                                                                  '$users_rights_delete')";
          $all_queries .= "<br>".$query_insert_users_rights;
          mysqli_query($db_link, $query_insert_users_rights);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." 3 - `users_rights` ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
          
          if($user_type_is_superuser == 0 && !in_array($user_type_id, $inserted_user_type_ids)) {
            
            $inserted_user_type_ids[] = $user_type_id;
            
            $query_users_types_rights = "INSERT INTO `users_types_rights`(`users_rights_id`, 
                                                                          `user_type_id`, 
                                                                          `menu_id`,  
                                                                          `users_rights_access`,  
                                                                          `users_rights_add`,  
                                                                          `users_rights_edit`, 
                                                                          `users_rights_delete`)
                                                                  VALUES (NULL,
                                                                          '$user_type_id',
                                                                          '$menu_id',
                                                                          '$users_rights_access',
                                                                          '$users_rights_add',
                                                                          '$users_rights_edit',
                                                                          '$users_rights_delete')";
            $all_queries .= "<br>".$query_users_types_rights;
            mysqli_query($db_link, $query_users_types_rights);
            if(mysqli_affected_rows($db_link) <= 0) {
              echo $languages['sql_error_insert']." 3 - `users_types_rights` ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }
          }
          
        }
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
      mysqli_query($db_link,"COMMIT");
      header("Location: $return_link");
    
    } //if(!isset($menu_errors))
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages['menu_add_new_title'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
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
          <button type="submit" name="add_menu" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>
<?php
      if(!empty($languages_array)) {
        
        $key = 0;
              
        foreach($languages_array as $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          $language_menu_name = $row_languages['language_menu_name'];
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
            <input type="text" name="menu_names[<?=$language_id;?>]" class="input_with_language" id="menu_name_<?=$language_id;?>" value="<?php if(isset($menu_names_array[$language_id])) echo $menu_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
          </div>
          <p class="clearfix"></p>
<?php
          $key++;
        }
      }
?>
        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_parent'];?></p>
          <select name="menu_parent_id_level" id="menu_parent_id_level">
            <option value="0|0"></option>
            <?php list_menu_for_select_a_parent($parent_id = 0, $path_number = 0, $menu_parent_id); ?>
          </select>
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_url_address'];?></p>
          <input type="text" name="menu_url" id="menu_url" value="<?php if(isset($menu_url)) echo $menu_url;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_css_id'];?></p>
          <input type="text" name="menu_css_id" id="menu_css_id" value="<?php if(isset($menu_css_id)) echo $menu_css_id;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_directory_path'];?></p>
          <input type="text" name="menu_path_name" id="menu_path_name" value="<?php if(isset($menu_path_name)) echo $menu_path_name;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_image'];?></p>
          <input type="text" name="menu_image_url" id="menu_image_url" value="<?php if(isset($menu_image_url)) echo $menu_image_url;?>" />
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
          <?php
            if(isset($menu_has_children)) {
              if($menu_has_children == 0) {
                echo '<input type="checkbox" name="menu_has_children" id="menu_has_children" />';
              }
              else {
                echo '<input type="checkbox" name="menu_has_children" id="menu_has_children" checked="checked" />';
              }
            }
            else echo '<input type="checkbox" name="menu_has_children" id="menu_has_children" />';
          ?>
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_menu_use_as_include_block'];?></p>
          <?php
            if(isset($menu_use_as_include_block)) {
              if($menu_use_as_include_block == 0) {
                echo '<input type="checkbox" name="menu_use_as_include_block" id="menu_use_as_include_block" />';
              }
              else {
                echo '<input type="checkbox" name="menu_use_as_include_block" id="menu_use_as_include_block" checked="checked" />';
              }
            }
            else echo '<input type="checkbox" name="menu_use_as_include_block" id="menu_use_as_include_block" />';
          ?>
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_include_block_fn'];?></p>
          <input type="text" name="menu_include_block_fn" id="menu_include_block_fn" value="<?php if(isset($menu_include_block_fn)) echo $menu_include_block_fn;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
          <p class="title"><?=$languages['header_menu_include_block_class'];?></p>
          <input type="text" name="menu_include_block_class" id="menu_include_block_class" value="<?php if(isset($menu_include_block_class)) echo $menu_include_block_class;?>" />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_menu_visible'];?></p>
          <?php
            if(isset($menu_show_in_menu) && $menu_show_in_menu == 0) echo '<input type="checkbox" name="menu_show_in_menu" id="menu_show_in_menu" />';
            else echo '<input type="checkbox" name="menu_show_in_menu" id="menu_show_in_menu" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"></div>
        
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
          <button type="submit" name="add_menu" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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