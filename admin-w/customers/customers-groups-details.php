<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $return_link = "customers-groups.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $return_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_GET['customer_group_id'])) {
    $current_customer_group_id = $_GET['customer_group_id'];
  }
  else {
    exit("Error");
  }

  if(isset($_POST['submit_customer_group'])) {
   
//    echo"<pre>";print_r($_POST);print_r($_FILES);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $customer_group_errors = array();
    $all_queries = "";
    
    foreach($_POST['customer_group_name'] as $language_id => $customer_group_name) {
      if(empty($customer_group_name)) $customer_group_errors['customer_group_name'][$language_id] = $languages['required_field_error'];
      
      $customer_group_names_array[$language_id] = $_POST['customer_group_name'][$language_id];
    }

    if(empty($customer_group_errors)) {
      //if there are no form errors we can insert the information
      
      $customer_group_code = $_POST['customer_group_code'];
      $query_update = "UPDATE `customers_groups` SET `customer_group_code` = '$customer_group_code' WHERE `customer_group_id` = '$current_customer_group_id'";
      //echo $query_update;
      $all_queries .= "<br>".$query_update;
      $result_update = mysqli_query($db_link, $query_update);
      if(!$result_update) {
        echo $languages['sql_error_update']." 1 customers_groups - update ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    
      foreach($customer_group_names_array as $language_id => $customer_group_name) {
        
        $customer_group_name = mysqli_real_escape_string($db_link, $customer_group_name);
        $customer_group_text = mysqli_real_escape_string($db_link, $_POST['customer_group_text']);

        if(isset($_POST['new_entry'][$language_id])) {
          /*
           * this means a new language was added after the first status insertion
           * so we have to make a new record for the language, and not update an old one
           */
          
          $query_customer_group = "INSERT INTO `customers_groups_languages`(`customer_group_id`,`language_id`,`customer_group_name`,`customer_group_text`) 
                                                                    VALUES ('$current_customer_group_id','$language_id','$customer_group_name','$customer_group_text')";
          //echo $query_customer_group;
          $all_queries .= "<br>".$query_customer_group;
          $result_inser_customer_group_name = mysqli_query($db_link, $query_customer_group);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." 2 customers_groups_languages - insert ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_update_customer_group = "UPDATE `customers_groups_languages` SET `customer_group_name` = '$customer_group_name',`customer_group_text` = '$customer_group_text'
                                           WHERE `customer_group_id` = '$current_customer_group_id' AND `language_id` = '$language_id'";
          //echo $query_update_customer_group;
          $all_queries .= "<br>".$query_update_customer_group;
          $result_update_customer_group = mysqli_query($db_link, $query_update_customer_group);
          if(!$result_update_customer_group) {
            echo $languages['sql_error_update']." 3 customers_groups_languages - update ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $return_link");
    }//if(empty($customer_group_errors))
    
  }//if(isset($_POST['submit_customer_group']))
  else {
    $query_customer_group = "SELECT `customer_group_code` FROM `customers_groups` WHERE `customer_group_id` = '$current_customer_group_id'";
    //echo $query_customer_group;
    $result_customer_group = mysqli_query($db_link, $query_customer_group);
    if(!$result_customer_group) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_customer_group) > 0) {
      $customer_group_row = mysqli_fetch_assoc($result_customer_group);
      $customer_group_code = $customer_group_row['customer_group_code'];
    }
  }
  
  $page_title = $languages['text_customer_group_edit'];
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
        <?=$languages['text_customer_group_edit'];?>
      </div>
      
<?php if(isset($customer_group_errors) && !empty($customer_group_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <form method="post" name="edit_customer_group" id="edit_customer_group" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="customer_group_name" class="title"><?=$languages['header_customer_group_code'];?></label>
          <input type="text" name="customer_group_code" value="<?php if(isset($customer_group_code)) echo $customer_group_code;?>" />
        </div>
        <p class="clearfix"></p>
<?php
        if(!empty($languages_array)) {
        
          $key = 0;
        
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];

            
            if(!isset($_POST['submit_customer_group'])) {
              $query_customer_group = "SELECT `customer_group_name`,`customer_group_text` FROM `customers_groups_languages` 
                                        WHERE `customer_group_id` = '$current_customer_group_id' AND `language_id` = '$language_id'";
              //echo $query_customer_group;
              $result_customer_group = mysqli_query($db_link, $query_customer_group);
              if(!$result_customer_group) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_customer_group) > 0) {
                $customer_group_array = mysqli_fetch_assoc($result_customer_group);
                //echo"<pre>";print_r($attribute_group_array);
                $customer_group_texts_array[$language_id] = $customer_group_array['customer_group_text'];
                $customer_group_names_array[$language_id] = $customer_group_array['customer_group_name'];
              }
            }
?>
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <?php
              if($key == 0) {
            ?>
              <label for="customer_group_name" class="title"><?=$languages['header_name'];?>
                <span class="red">*</span>
              </label>
            <?php
              }
              if(isset($customer_group_errors['customer_group_name'][$language_id])) {
                echo "<div class='error'>".$customer_group_errors['customer_group_name'][$language_id]."</div>";
              }
              if(!isset($customer_group_names_array[$language_id])) {
                /*
                 * no record for this language, because the language was added after the first time the status was created
                 */
            ?>
              <input type="hidden" name="new_entry[<?=$language_id;?>]" value="1" />
            <?php 
              }
            ?>
            <input type="text" name="customer_group_name[<?=$language_id;?>]" class="customer_group_name" value="<?php if(isset($customer_group_names_array[$language_id])) echo $customer_group_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
          </div>
          <p class="clearfix"></p>
<?php
          $key++;
        }
      }
?>
        
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        <div>
          <button type="submit" name="submit_customer_group" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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