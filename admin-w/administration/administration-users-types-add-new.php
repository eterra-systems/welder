<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $return_link = "administration-users-types.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $return_link");
  }
  
  $languages_array = get_languages();

  if(isset($_POST['add_user_type'])) {
   
    //echo"<pre>";print_r($_POST);print_r($_FILES);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $user_type_errors = array();
    $all_queries = "";
    
    foreach($_POST['user_type_name'] as $language_id => $user_type_name) {
      if(empty($user_type_name)) $user_type_errors['user_type_name'][$language_id] = $languages['required_field_error'];
      
      $user_type_names_array[$language_id] = $_POST['user_type_name'][$language_id];
    }

    if(empty($user_type_errors)) {
      //if there are no form errors we can insert the information
      
      $user_type_is_superuser = 0;
      $user_type_sort_order = get_last_sort_order($table_name = 'users_types', $column_name = 'user_type_sort_order')+1;

      $query_user_type = "INSERT INTO `users_types`(`user_type_id`,`user_type_is_superuser`,`user_type_sort_order`) 
                                            VALUES (NULL,'$user_type_is_superuser','$user_type_sort_order')";
      //echo $query_user_type;
      $all_queries .= "<br>".$query_user_type;
      mysqli_query($db_link, $query_user_type);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." users_types - insert ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $user_type_id = mysqli_insert_id($db_link);
      
      foreach($user_type_names_array as $language_id => $user_type_name) {
        
        $user_type_name = mysqli_real_escape_string($db_link, $user_type_name);
        
        $query_users_type_desc = "INSERT INTO `users_types_descriptions`(`user_type_id`,`language_id`,`user_type_name`) 
                                                          VALUES ('$user_type_id','$language_id','$user_type_name')";
        //echo $query_users_type_desc;
        $all_queries .= "<br>".$query_users_type_desc;
        mysqli_query($db_link, $query_users_type_desc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." users_types_descriptions - insert ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      $query_users_rights = "SELECT `menu_id` FROM `menus`";
      $all_queries .= "<br>\n".$query_users_rights;
      $result_users_rights = mysqli_query($db_link, $query_users_rights);
      if(!$result_users_rights) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_users_rights) > 0) {

        while($default_rights = mysqli_fetch_assoc($result_users_rights)) {

          $menu_id = $default_rights['menu_id'];
          $users_rights_access = 0;
          $users_rights_add = 0;
          $users_rights_edit = 0;
          $users_rights_delete = 0;

          $query = "INSERT INTO `users_types_rights`(`users_rights_id`, 
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
          $all_queries .= "<br>\n".$query;
          $result = mysqli_query($db_link, $query);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }

        }

      }
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $return_link");
    }//if(empty($user_type_errors))
    
  }//if(isset($_POST['add_user_type']))
  
  $page_title = $languages['text_user_type_add_new'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$return_link;?>"><?=$languages['text_users_types'];?></a>
        <span>&raquo;</span>
        <?=$languages['text_user_type_add_new'];?>
      </div>
      
<?php if(isset($user_type_errors) && !empty($user_type_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <form method="post" name="add_user_type" id="add_user_type" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
<?php
      if(!empty($languages_array)) {
        
        $key = 0;
        
        foreach($languages_array as $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          $language_menu_name = $row_languages['language_menu_name'];
?>
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <?php
              if($key == 0) {
            ?>
              <label for="user_type_name" class="title"><?=$languages['header_name'];?>
                <span class="red">*</span>
              </label>
            <?php
              }
              if(isset($user_type_errors['user_type_name'][$language_id])) {
                echo "<div class='error'>".$user_type_errors['user_type_name'][$language_id]."</div>";
              }
              if(!isset($user_type_names_array[$language_id])) {
                /*
                 * no record for this language, because the language was added after the first time the status was created
                 */
            ?>
              <input type="hidden" name="user_type_sort_order" value="<?=$user_type_sort_order?>" />
              <input type="hidden" name="new_entry[<?=$language_id;?>]" value="1" />
            <?php 
              }
            ?>
            <input type="text" name="user_type_name[<?=$language_id;?>]" class="user_type_name" value="<?php if(isset($user_type_names_array[$language_id])) echo $user_type_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
          </div>
          <p class="clearfix"></p>
<?php
          $key++;
        }
      }
?>
        <div class="clearfix"></div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        <div>
          <button type="submit" name="add_user_type" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix">&nbsp;</div>
        
      </form>
      <div class="clearfix">&nbsp;</div>
    </div>
  </main>
<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>