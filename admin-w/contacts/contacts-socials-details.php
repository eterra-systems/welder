<?php
  $back_link = "contacts-socials.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_POST['update_contact_social'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if(isset($_POST['contact_social_network'])) $contact_social_network = $_POST['contact_social_network'];
    if(isset($_POST['contact_social_address'])) $contact_social_address = $_POST['contact_social_address'];
    if(empty($contact_social_address)) {
      $contact_social_errors['contact_social_address'] = $languages['required_field_error'];
    }
    $contact_social_is_active = 0;
      if(isset($_POST['contact_social_is_active'])) $contact_social_is_active = 1;
    
    if(!isset($contact_social_errors)) {
      //if there are no form errors we can insert the information
      
      $contact_social_icon = "NULL";
      
      $query_update_contact_social = "UPDATE `contacts_socials` SET `social_network_id` = '$contact_social_network',
                                                                    `contact_social_address` = '$contact_social_address',
                                                                    `contact_social_icon` = $contact_social_icon,
                                                                    `contact_social_is_active`='$contact_social_is_active'
                                                              WHERE `contact_social_id` = '$current_contact_social_id'";
      $all_queries .= "<br>".$query_update_contact_social;
      $result_update_contact_social = mysqli_query($db_link, $query_update_contact_social);
      if(!$result_update_contact_social) {
        echo $languages['sql_error_update']." - 1 `contact_socials` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      if(isset($_FILES['contact_social_image']) && $_FILES['contact_social_image']['error'] != 4) {

        $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/images/contact-socials/";
        if(!is_dir($upload_path)) {
          mkdir($upload_path, 0777);
          chmod($upload_path, 0777);
        }

        if(is_uploaded_file($contact_social_image_tmp_name)) {
          move_uploaded_file($contact_social_image_tmp_name, $upload_path.$contact_social_image_name);
        }
        else {
          echo $languages['image_uploading_error'];
          exit;
        }

        $file = $upload_path.$contact_social_image_name;

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
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages['page_title_edit_contact_social'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
  
  $query_contact_social = "SELECT `contacts_socials`.`social_network_id`,`contacts_socials`.`contact_social_address`,`contacts_socials`.`contact_social_icon`,
                                  `contacts_socials`.`contact_social_is_active`,`contacts_socials`.`contact_social_sort_order`,
                                  `social_networks`.`social_network_name`,`social_networks`.`social_network_icon`
                          FROM `contacts_socials`
                          INNER JOIN `social_networks` ON `social_networks`.`social_network_id` = `contacts_socials`.`social_network_id`
                          WHERE `contacts_socials`.`contact_social_id` = '$current_contact_social_id'";
  //echo $query_contact_social;
  $result_contact_social = mysqli_query($db_link, $query_contact_social);
  if(!$result_contact_social) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_contact_social) > 0) {
    
    $contact_social_row = mysqli_fetch_assoc($result_contact_social);
    
    $social_network_id = $contact_social_row['social_network_id'];
    $contact_social_address = $contact_social_row['contact_social_address'];
    $contact_social_icon = $contact_social_row['contact_social_icon'];
    $social_network_name = $contact_social_row['social_network_name'];
    $social_network_icon = $contact_social_row['social_network_icon'];
    $contact_social_is_active = $contact_social_row['contact_social_is_active'];
  }
?>
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>"><?=$languages['menu_contact_socials'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_edit_contact_social'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_edit_contact_social'];?></h1>
      
      <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        <p>
          <button type="submit" name="update_contact_social" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
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
            <?php list_social_networks_for_select($social_network_id); ?> 
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
          <input type="text" name="contact_social_address" value="<?=$contact_social_address;?>">
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
        <div class="clearfix"><p>&nbsp;</p></div>
        
        <div>
          <button type="submit" name="update_contact_social" class="button green"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="fa fa-undo" aria-hidden="true"></i> <?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--main section-->

<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>