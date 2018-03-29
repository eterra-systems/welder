<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $return_link = "content-types.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $return_link");
  }
  
  $languages_array = get_languages();

  if(isset($_POST['add_content_type'])) {
   
//    echo"<pre>";print_r($_POST);print_r($_FILES);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $content_type_errors = array();
    $all_queries = "";
    
    foreach($_POST['content_type_name'] as $language_id => $content_type_name) {
      if(empty($content_type_name)) $content_type_errors['content_type_name'][$language_id] = $languages['required_field_error'];
      
      $content_type_names_array[$language_id] = $_POST['content_type_name'][$language_id];
    }
    
    $content_type = $_POST['content_type'];
      if(empty($content_type)) $content_type_errors['content_type'] = $languages['required_field_error'];
    
    if(empty($content_type_errors)) {
      //if there are no form errors we can insert the information
      
      $content_type_is_active = 0;
        if(isset($_POST['content_type_is_active'])) $content_type_is_active = 1;
      
      $content_type_sort_order = get_last_sort_order("contents_types","content_type_sort_order")+1;
      $query_insert_ct = "INSERT INTO `contents_types`(`content_type_id`, `content_type`, `content_type_is_active`, `content_type_sort_order`) 
                                               VALUES (NULL,'$content_type','$content_type_is_active','$content_type_sort_order')";
      //echo $query_insert_ct;
      $all_queries .= "<br>".$query_insert_ct;
      $result_insert_ct = mysqli_query($db_link, $query_insert_ct);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $content_type_id = mysqli_insert_id($db_link);
    
      foreach($content_type_names_array as $language_id => $content_type_name) {
        
        $content_type_name = mysqli_real_escape_string($db_link, $content_type_name);

        $query_content_type = "INSERT INTO `contents_types_languages`(`content_type_id`,`language_id`,`content_type_name`) 
                                                              VALUES ('$content_type_id','$language_id','$content_type_name')";
        //echo $query_content_type;
        $all_queries .= "<br>".$query_content_type;
        $result_inser_content_type_name = mysqli_query($db_link, $query_content_type);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." 2 contents_types_languages - insert ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
       
      }
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $return_link");
    }//if(empty($content_type_errors))
    
  }//if(isset($_POST['add_content_type']))
  
  $page_title = $languages['text_content_type_edit'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$return_link;?>" title="<?=$languages['text_content_types'];?>"><?=$languages['text_content_types'];?></a>
        <span>&raquo;</span>
        <?=$languages['text_content_type_edit'];?>
      </div>
      
<?php if(isset($content_type_errors) && !empty($content_type_errors)) echo '<div class="alert alert-danger">Моля проверете дали всички задължителни полета са попълнени</div>';?>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <form method="post" name="edit_content_type" id="edit_content_type" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="content_type" class="title"><?=$languages['header_content_type'];?><span class="red">*</span></label>
          <?php
            if(isset($content_type_errors['content_type'])) {
              echo "<div class='error'>".$content_type_errors['content_type']."</div>";
            }
          ?>
          <input type="text" name="content_type" value="<?php if(isset($content_type)) echo $content_type;?>" />
        </div>
        <p class="clearfix"></p>
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
              <label for="content_type_name" class="title"><?=$languages['header_name'];?>
                <span class="red">*</span>
              </label>
            <?php
              }
              if(isset($content_type_errors['content_type_name'][$language_id])) {
                echo "<div class='error'>".$content_type_errors['content_type_name'][$language_id]."</div>";
              }
              if(!isset($content_type_names_array[$language_id])) {
                /*
                 * no record for this language, because the language was added after the first time the status was created
                 */
            ?>
              <input type="hidden" name="new_entry[<?=$language_id;?>]" value="1" />
            <?php 
              }
            ?>
            <input type="text" name="content_type_name[<?=$language_id;?>]" class="content_type_name" value="<?php if(isset($content_type_names_array[$language_id])) echo $content_type_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
          </div>
          <p class="clearfix"></p>
<?php
          $key++;
        }
      }
?>
        <div>
          <label for="content_type_is_active" class="title"><?=$languages['header_status'];?></label>
          <?php
            if(isset($content_type_is_active)) {
              if($content_type_is_active == 0) echo '<input type="checkbox" name="content_type_is_active" id="content_type_is_active" />';
              else echo '<input type="checkbox" name="content_type_is_active" id="content_type_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="content_type_is_active" id="content_type_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        <div>
          <button type="submit" name="add_content_type" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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