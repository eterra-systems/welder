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
    
    $customer_group_code = $_POST['customer_group_code'];
      if(empty($customer_group_code)) $customer_group_errors['customer_group_code'] = $languages['required_field_error'];
    foreach($_POST['customer_group_name'] as $language_id => $customer_group_name) {
      if(empty($customer_group_name)) $customer_group_errors['customer_group_name'][$language_id] = $languages['required_field_error'];
      
      $customer_group_names_array[$language_id] = $_POST['customer_group_name'][$language_id];
      $customer_group_texts_array[$language_id] = $_POST['customer_group_text'][$language_id];
    }

    if(empty($customer_group_errors)) {
      //if there are no form errors we can insert the information
      
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
        $customer_group_text_db = mysqli_real_escape_string($db_link, $customer_group_texts_array[$language_id]);

        if(isset($_POST['new_entry'][$language_id])) {
          /*
           * this means a new language was added after the first status insertion
           * so we have to make a new record for the language, and not update an old one
           */
          
          $query_customer_group = "INSERT INTO `customers_groups_languages`(`customer_group_id`,`language_id`,`customer_group_name`,`customer_group_text`) 
                                                                    VALUES ('$current_customer_group_id','$language_id','$customer_group_name','$customer_group_text_db')";
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
          $query_update_customer_group = "UPDATE `customers_groups_languages` SET `customer_group_name` = '$customer_group_name',`customer_group_text` = '$customer_group_text_db'
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
          <label for="customer_group_code" class="title"><?=$languages['header_customer_group_code'];?></label>
          <?php
            if(isset($customer_group_errors['customer_group_code'])) {
              echo "<div class='error'>".$customer_group_errors['customer_group_code']."</div>";
            }
          ?>
          <input type="text" name="customer_group_code" id="customer_group_code" value="<?php if(isset($customer_group_code)) echo $customer_group_code;?>" />
        </div>
        <div class="clearfix"></div>
        <p>&nbsp;</p>
        
        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
            $class_error = (isset($testimonial_errors['testimonial_author'][$language_id]) || isset($testimonial_errors['testimonial_text'][$language_id])) ? ' class="red"' : "";
?>
            <li<?=$class_error;?>>
              <a href="#<?=$language_code;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?>
              </a>
            </li>
<?php
          }
        }
?>
        </ul>
<?php
        if(!empty($languages_array)) {
        
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];

            
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
          <div id="<?=$language_code;?>" class="language_tab tab row">
            <div class="col-lg-6 col-md-10 col-sm-12 col-xs-12">
              <label for="customer_group_name" class="title"><?=$languages['header_name'];?><span class="red">*</span></label>
              <?php
                if(isset($customer_group_errors['customer_group_name'][$language_id])) {
                  echo "<div class='error'>".$customer_group_errors['customer_group_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="customer_group_name[<?=$language_id;?>]" class="customer_group_name" value="<?php if(isset($customer_group_names_array[$language_id])) echo $customer_group_names_array[$language_id];?>" />
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="customer_group_text" class="title"><?=$languages['header_text'];?></label>
              <?php
                if(isset($customer_group_errors['customer_group_text'][$language_id])) {
                  echo "<div class='error'>".$customer_group_errors['customer_group_text'][$language_id]."</div>";
                }
              ?>
              <textarea name="customer_group_text[<?=$language_id;?>]" id="ckeditor_customer_group_text_<?=$language_code;?>" class="default_text"><?php if(isset($customer_group_texts_array[$language_id])) echo $customer_group_texts_array[$language_id];?></textarea>
            </div>
            <div class="clearfix"></div>
          </div>
<?php
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
  <script type="text/javascript" src="/modules/ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {
              
          $language_code = $row_languages['language_code'];
?>
          CKEDITOR.replace('ckeditor_customer_group_text_<?=$language_code;?>');
<?php
        }
      }
?>
      // language tab switcher
      $(".language_tabs li").removeClass("active");
      $(".language_tab").hide();
      $(".language_tabs li:first").addClass("active");
      $(".language_tab:first").show();
      $(".language_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".language_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".language_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end language tab switcher
    });
  </script>
</body>
</html>