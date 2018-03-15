<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $return_link = "administration-users-types.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $return_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_GET['user_type_id'])) {
    $current_user_type_id = $_GET['user_type_id'];
  }
  else {
    exit("Error");
  }

  if(isset($_POST['submit_user_type'])) {
   
//    echo"<pre>";print_r($_POST);print_r($_FILES);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $user_type_errors = array();
    $all_queries = "";
    
    foreach($_POST['user_type_name'] as $language_id => $user_type_name) {
      if(empty($user_type_name)) $user_type_errors['user_type_name'][$language_id] = $languages['required_field_error'];
      
      $user_type_names_array[$language_id] = $_POST['user_type_name'][$language_id];
    }

    if(empty($user_type_errors)) {
      //if there are no form errors we can insert the information
      
      foreach($user_type_names_array as $language_id => $user_type_name) {
        
        $user_type_name = mysqli_real_escape_string($db_link, $user_type_name);

        if(isset($_POST['new_entry'][$language_id])) {
          /*
           * this means a new language was added after the first status insertion
           * so we have to make a new record for the language, and not update an old one
           */
          
          $query_user_type = "INSERT INTO `users_types_descriptions`(`user_type_id`,`language_id`,`user_type_name`) 
                                                VALUES ('$current_user_type_id','$language_id','$user_type_name')";
          //echo $query_user_type;
          $all_queries .= "<br>".$query_user_type;
          $result_inser_user_type_name = mysqli_query($db_link, $query_user_type);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." users_types_descriptions - insert ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_update_user_type_name = "UPDATE `users_types_descriptions` SET `user_type_name` = '$user_type_name'
                                            WHERE `user_type_id` = '$current_user_type_id' AND `language_id` = '$language_id'";
          //echo $query_update_user_type_name;
          $all_queries .= "<br>".$query_update_user_type_name;
          $result_update_user_type_name = mysqli_query($db_link, $query_update_user_type_name);
          if(!$result_update_user_type_name) {
            echo $languages['sql_error_update']." users_types_descriptions - update ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $return_link");
    }//if(empty($user_type_errors))
    
  }//if(isset($_POST['submit_user_type']))
  
  $page_title = $languages['text_user_type_edit'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$return_link;?>" title="<?=$languages['text_users_types'];?>"><?=$languages['text_users_types'];?></a>
        <span>&raquo;</span>
        <?=$languages['text_user_type_edit'];?>
      </div>
      
<?php if(isset($user_type_errors) && !empty($user_type_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <form method="post" name="edit_user_type" id="edit_user_type" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $key => $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];

            
            if(!isset($_POST['submit_user_type'])) {
              $query_user_type = "SELECT `user_type_name` FROM `users_types_descriptions` 
                                  WHERE `user_type_id` = '$current_user_type_id' AND `language_id` = '$language_id'";
              //echo $query_user_type;
              $result_user_type = mysqli_query($db_link, $query_user_type);
              if(!$result_user_type) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_user_type) > 0) {
                $user_type_array = mysqli_fetch_assoc($result_user_type);
                //echo"<pre>";print_r($attribute_group_array);
                $user_type_names_array[$language_id] = $user_type_array['user_type_name'];
              }
            }
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
              <input type="hidden" name="new_entry[<?=$language_id;?>]" value="1" />
            <?php 
              }
            ?>
            <input type="text" name="user_type_name[<?=$language_id;?>]" class="user_type_name" value="<?php if(isset($user_type_names_array[$language_id])) echo $user_type_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
          </div>
          <p class="clearfix"></p>
<?php
    }
  }
?>
        
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        <div>
          <button type="submit" name="submit_user_type" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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