<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  $back_link = "contacts-socials.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $contact_social_network = 0;
  
  if(isset($_POST['add_contact_social'])) {
    
    //echo"<pre>";print_r($_POST);echo"</pre>";exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if(isset($_POST['contact_social_network'])) $contact_social_network = $_POST['contact_social_network'];
    if(isset($_POST['contact_social_address'])) $contact_social_address = $_POST['contact_social_address'];
    if(empty($contact_social_address)) {
      $contact_social_errors['contact_social_address'] = $languages['required_field_error'];
    }
    $contact_social_is_active = (isset($_POST['contact_social_is_active'])) ? 1 : 0;
    
    /*
     * custom image icon is not used yet
     */
    $input_name = "contact_social_image";
    $max_image_size = "2048000"; //2MB
    $contact_social_image_set = false;
    if(isset($_FILES[$input_name]) && ($_FILES[$input_name]['error'] != 4)) {
      $contact_social_image_set = true;
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/contact-socials/";
      $image_params = validate_upload_image($input_name, $upload_path, $max_image_size);
      //echo "<pre>";print_r($image_params);exit;
      if(!empty($image_params['error'])) $contact_social_errors[$input_name] = $image_params['error']; // array that may contain extension, size, upload
      $image_tmp_name = $image_params['image_tmp_name'];
      $image_name = $image_params['image_name'];
      $image_exstension = $image_params['image_exstension'];
      $image_name_full = $image_params['image_name_full'];
    }
        
    if(!isset($contact_social_errors)) {
      //if there are no form errors we can insert the information
      
      $contact_social_sort_order = get_last_sort_order("contacts_socials","contact_social_sort_order")+1;
      $contact_social_icon = ($contact_social_image_set) ? "'$image_name_full'" : "NULL";
      
      $query_insert_contact_social = "INSERT INTO `contacts_socials`(`contact_social_id`, 
                                                                    `social_network_id`, 
                                                                    `contact_social_address`, 
                                                                    `contact_social_icon`, 
                                                                    `contact_social_is_active`, 
                                                                    `contact_social_sort_order`) 
                                                            VALUES (NULL,
                                                                    '$contact_social_network',
                                                                    '$contact_social_address',
                                                                    $contact_social_icon,
                                                                    '$contact_social_is_active',
                                                                    '$contact_social_sort_order')";
      $all_queries .= "<br>".$query_insert_contact_social;
      $result_insert_contact_social = mysqli_query($db_link, $query_insert_contact_social);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 1 `contacts_socials`".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      if($contact_social_image_set) {

        if(is_uploaded_file($image_tmp_name)) {
          move_uploaded_file($image_tmp_name, $upload_path.$image_name_full);
        }
        else {
          echo $languages['image_uploading_error'];
          exit;
        }

        $file = $upload_path.$image_name_full;

        list($width,$height) = getimagesize($file);

        $image = new SimpleImage();
        $image->load($file);

        $image_site_name = $image_name."_site.".$image_exstension;
        $image_site = $upload_path.$image_site_name;

        switch($image_exstension) {
          case "gif" : $image_type = 1;
            break;
          case "jpg" : $image_type = 2;
            break;
          case "jpeg" : $image_type = 2;
            break;
          case "png" : $image_type = 3;
            break;
        }

        if($width > $height) {
          $image->resizeToWidth(170);

          $image->save($image_site,$image_type);

        }
        else {
          $image->resizeToHeight(43);

          $image->save($image_site,$image_type);
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
      
    } //if(!isset($contact_social_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages['page_title_add_new_contact_social'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>"><?=$languages['menu_contact_socials'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_add_new_contact_social'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_add_new_contact_social'];?></h1>
      
      <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        <p>
          <button type="submit" name="add_contact_social" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>

        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="contact_social_network" class="title"><?=$languages['header_social_network'];?></label>
          <?php
            if(isset($contact_social_errors['contact_social_network'])) {
              echo "<div class='error'>".$contact_social_errors['contact_social_network']."</div>";
            }
          ?>
          <select name="contact_social_network" id="contact_social_network" class="social_networks" style="width: auto;">
            <option value="0"><?=$languages['option_choose_social_network'];?></option>
            <?php list_social_networks_for_select($contact_social_network); ?> 
          </select>
        </div>
        <div class="clearfix"></div>
          
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="contact_social_address" class="title"><?=$languages['header_address'];?><span class="red">*</span></label>
          <?php
            if(isset($contact_social_errors['contact_social_address'])) {
              echo "<div class='error'>".$contact_social_errors['contact_social_address']."</div>";
            }
          ?>
          <input type="text" name="contact_social_address" value="<?php if(isset($_POST['contact_social_address'])) echo $_POST['contact_social_address'];?>">
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="contact_social_is_active" class="title"><?=$languages['header_status'];?></label>
          <?php
            if(isset($contact_social_is_active)) {
              if($contact_social_is_active == 0) echo '<input type="checkbox" name="contact_social_is_active" id="contact_social_is_active" />';
              else echo '<input type="checkbox" name="contact_social_is_active" id="contact_social_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="contact_social_is_active" id="contact_social_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix">&nbsp;</div>
          
        <div class="hidden">
          <label for="contact_social_image" class="title"><?=$languages['header_contact_social_image'];?><span class="red">*</span> (64x64px)</label>
          <?php
            if(!empty($contact_social_errors['contact_social_image'])) {
              foreach($contact_social_errors['contact_social_image'] as $error) {
                echo "<div class='error'>$error</div>";
              }
            }
          ?>
          <p><input type="file" name="contact_social_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="add_contact_social" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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