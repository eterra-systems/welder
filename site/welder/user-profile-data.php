<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  if(isset($_POST['cancel'])) {
    header("Location: user-profile-data.php");
  }
  
  if(isset($_POST['update_profile'])) {
    //print_array_for_debug($_POST);exit;
    
    $profile_image = $_POST['profile_image_preview'];
    $customer_firstname = trim($_POST['customer_firstname']);
    $customer_surname = trim($_POST['customer_surname']);
    $customer_lastname = trim($_POST['customer_lastname']);
    $customer_gender = $_POST['customer_gender'];
    $customer_age = intval($_POST['customer_age']);
    $customer_site_id = $_POST['customer_site_id'];
    $customer_site_name = $_POST['customer_site_name'];
    $customer_site_postcode = $_POST['customer_site_postcode'];
    $customer_work_abroad = 0;
      if(isset($_POST['customer_work_abroad'])) $customer_work_abroad = 1;
    $customer_work_abroad_long_term = 0;
      if(isset($_POST['customer_work_abroad_long_term'])) $customer_work_abroad_long_term = 1;
    $customer_work_abroad_short_term = 0;
      if(isset($_POST['customer_work_abroad_short_term'])) $customer_work_abroad_short_term = 1;
    $customer_explanation_text = $_POST['customer_explanation_text'];
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $customer_is_in_mailist = 0;
      if(isset($_POST['customer_is_in_mailist'])) $customer_is_in_mailist = 1;
      
    if(empty($errors)) {
      
      $query_update_user = "UPDATE `customers` SET `customer_email`='$customer_email',`customer_phone`='$customer_phone' WHERE `customer_id` = '$customer_id'";
      //echo $query_update_user."<br>";
      $result_update_user = mysqli_query($db_link, $query_update_user);
      if(!$result_update_user) {
        echo $languages['sql_error_update']." - 1 update `customers` ".mysqli_error($db_link);
      }
      
      $customer_firstname = mysqli_real_escape_string($db_link,$customer_firstname);
      $customer_surname = mysqli_real_escape_string($db_link,$customer_surname);
      $customer_lastname = mysqli_real_escape_string($db_link,$customer_lastname);
      $customer_age = mysqli_real_escape_string($db_link,$customer_age);
      $explanation_text_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $customer_explanation_text));
      
      $query_update_user = "UPDATE `customers_welder` SET `site_id`='$customer_site_id',
                                                          `first_name`='$customer_firstname',
                                                          `surname`='$customer_surname',
                                                          `surname`='$customer_surname',
                                                          `last_name`='$customer_lastname',
                                                          `age`='$customer_age',
                                                          `gender`='$customer_gender',
                                                          `work_abroad`='$customer_work_abroad',
                                                          `long_term`='$customer_work_abroad_long_term',
                                                          `short_term`='$customer_work_abroad_short_term',
                                                          `explanation_text`=$explanation_text_db 
                        WHERE `customer_id` = '$customer_id'";
      //echo $query_update_user."<br>";
      $result_update_user = mysqli_query($db_link, $query_update_user);
      if(!$result_update_user) {
        echo $languages['sql_error_update']." - 2 update `customers_welder` ".mysqli_error($db_link);
      }
      $success = true;
    }
  }
  else {
    $query_customer = "SELECT `customers`.*, `customers_welder`.*
                         FROM `customers` 
                   INNER JOIN `customers_welder` ON `customers_welder`.`customer_id` = `customers`.`customer_id`
                        WHERE `customers`.`customer_id` = '$customer_id'";
    //echo $query_customer;
    $result_customer = mysqli_query($db_link, $query_customer);
    if(!$result_customer) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_customer) > 0) {
      $customer = mysqli_fetch_assoc($result_customer);
      $customer_site_id = $customer['site_id'];
      $customer_image = $customer['customer_image'];
      $profile_image = (empty($customer_image)) ? SITEFOLDERSL."/images/no-profile-man-medium.jpg" : 
                                                  SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/profile-images/$customer_id/$customer_image";
      $customer_email = $customer['customer_email'];
      $customer_phone = $customer['customer_phone'];
      $customer_firstname = $customer['first_name'];
      $customer_surname = $customer['surname'];
      $customer_lastname = $customer['last_name'];
      $customer_age = $customer['age'];
      $customer_gender = $customer['gender'];
      $customer_work_abroad = $customer['work_abroad'];
      $customer_work_abroad_short_term = $customer['short_term'];
      $customer_work_abroad_long_term = $customer['long_term'];
      $customer_explanation_text = $customer['explanation_text'];
      $customer_is_in_mailist = $customer['customer_is_in_mailist'];
      if($customer_site_id != 0) {
        $query_customer_site = "SELECT `site_type`, `site_name`, `site_postcode` FROM `sites` WHERE `site_id` = '$customer_site_id'";
        $result_customer_site = mysqli_query($db_link, $query_customer_site);
        if(!$result_customer_site) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_customer_site) > 0) {
          $site = mysqli_fetch_assoc($result_customer_site);

          $customer_site_type = $site['site_type'];
          $customer_site_name = mb_convert_case($site['site_name'], MB_CASE_TITLE, "UTF-8");
          $customer_site_postcode = $site['site_postcode'];
        }
      }
    }
  }
  //echo "<pre>";print_r($_COOKIE);
?>
    <form name="user_profile_data" id="user_profile_data" class="form-group" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<?php
    if(isset($success)) {
?>
    <p class="alert alert-success mb-15">Промерните бяха запазени успешно</p>
<?php
    }
    if(!empty($errors)) {

      //foreach($errors as $error) echo "<div class='warning_field'>$error</div>";
    }
?>
      <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <div class="form-group bootstrap-fileinput-style-01">
            <label for="profile_image"><?=$languages['header_image'];?></label>
            <input type="hidden" name="profile_image_preview" id="profile_image_preview" value="<?=$profile_image;?>">
            <input type="hidden" id="upload_url" value="<?=SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/ajax/upload-profile-image.php";?>">
            <input type="file" name="profile_image" id="profile_image">
            <span class="font12 font-italic hidden"><i class="fa fa-info-circle"></i> photo must not bigger than 250kb</span>
          </div>
        </div>
      </div>
      <p class="clearfix"></p>
      
      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_firstname"><?=$languages['header_firstname'];?><span class="text-danger">*</span></label>
          <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
          <?php if(!empty($errors['customer_firstname'])) { ?><div class="alert alert-danger"><?=$errors['customer_firstname'];?></div><?php } ?>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_surname"><?=$languages['header_surname'];?><span class="text-danger">*</span></label>
          <input type="text" name="customer_surname" id="customer_surname" class="form-control" value="<?php if(isset($customer_surname)) echo $customer_surname;?>" />
          <?php if(!empty($errors['customer_surname'])) { ?><div class="alert alert-danger"><?=$errors['customer_surname'];?></div><?php } ?>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_lastname"><?=$languages['header_lastname'];?><span class="text-danger">*</span></label>
          <input type="text" name="customer_lastname" id="customer_lastname" class="form-control" value="<?php if(isset($customer_lastname)) echo $customer_lastname;?>" />
          <?php if(!empty($errors['customer_lastname'])) { ?><div class="alert alert-danger"><?=$errors['customer_lastname'];?></div><?php } ?>
        </div>
        
      </div>
      <div class="clearfix">&nbsp;</div>
    
      <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
          <label for="customer_age"><?=$languages['header_customer_age'];?><span class="text-danger">*</span></label>
          <input type="text" name="customer_age" id="customer_age" class="form-control" value="<?php if(isset($customer_age)) echo $customer_age;?>" />
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
          <label for="customer_gender"><?=$languages['header_gender'];?><span class="text-danger">*</span></label>
          <select name="customer_gender" id="customer_gender" class="form-control">
            <option value="male"<?php if(isset($customer_gender) && $customer_gender == "male") echo " selected";?>><?=$languages['option_male'];?></option>
            <option value="female"<?php if(isset($customer_gender) && $customer_gender == "female") echo " selected";?>><?=$languages['option_female'];?></option>
          </select>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_phone"><?=$languages['header_customer_phone'];?><span class="text-danger">*</span></label>
          <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
          <?php if(!empty($errors['customer_phone'])) { ?><div class="alert alert-danger"><?=$errors['customer_phone'];?></div><?php } ?>
          <span class="font12 font-italic"><i class="fa fa-info-circle"></i> <?=$languages['text_phone_example'];?></span>
        </div>
      </div>
      
      <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding: 0">
            <label for="customer_city" style="display: block"><?=$languages['header_customer_address_site_name'];?><span class="text-danger">*</span></label>
            <input type="text" name="customer_site_type" class="pull-left form-control" id="customer_site_type" disabled="disabled" value="<?php if(isset($customer_site_type)) echo $customer_site_type;else echo $languages['header_customer_address_site_type'];?>" style="width: 25%; margin-right: 1%;padding: 8px 10px" />
            <input type="text" name="customer_site_name_label" id="customer_site_name_label" class="form-control" autocomplete="off" value="<?php if(isset($customer_site_name)) echo $customer_site_name;?>" style="width: 74%;" />
            <?php if(!empty($errors['customer_site_name'])) { ?><div class="alert alert-danger"><?=$errors['customer_site_name'];?></div><?php } ?>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0 0 0 1%">
            <label for="customer_postcode"><?=$languages['header_customer_address_postcode'];?></label>
            <input type="text" name="customer_site_postcode_label" class="form-control" disabled="disabled" id="customer_site_postcode_label" value="<?php if(isset($customer_site_postcode)) echo $customer_site_postcode;?>" />
            <input type="hidden" name="customer_site_id" id="customer_site_id" value="<?php if(isset($customer_site_id)) echo $customer_site_id;?>" />
            <input type="hidden" name="customer_site_name" id="customer_site_name" value="<?php if(isset($customer_site_name)) echo $customer_site_name;?>" />
            <input type="hidden" name="customer_site_postcode" id="customer_site_postcode" value="<?php if(isset($customer_site_postcode)) echo $customer_site_postcode;?>" />
          </div>
        </div>
      </div>
      <p class="clearfix">&nbsp;</p>
      
      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_work_abroad"><?=$languages['header_preferences'];?>Предпочитания</label>
          <?php
            if(isset($customer_work_abroad)) {
              if($customer_work_abroad == 0) {echo '<input type="checkbox" name="customer_work_abroad" id="customer_work_abroad" />';}
              else {echo '<input type="checkbox" name="customer_work_abroad" id="customer_work_abroad" checked="checked" />';}
            }
            else echo '<input type="checkbox" name="customer_work_abroad" id="customer_work_abroad" />';
          ?>
          <label for="customer_work_abroad" style="display: inline-block;margin: 0;"><?=$languages['header_customer_work_abroad'];?></label>
          <br>
          <?php
            if(isset($customer_work_abroad_long_term)) {
              if($customer_work_abroad_long_term == 0) {echo '<input type="checkbox" name="customer_work_abroad_long_term" id="customer_work_abroad_long_term" />';}
              else {echo '<input type="checkbox" name="customer_work_abroad_long_term" id="customer_work_abroad_long_term" checked="checked" />';}
            }
            else echo '<input type="checkbox" name="customer_work_abroad_long_term" id="customer_work_abroad_long_term" />';
          ?>
          <label for="customer_work_abroad_long_term" style="display: inline-block;margin: 0;"><?=$languages['header_customer_work_abroad_long_term'];?></label>
          <br>
          <?php
            if(isset($customer_work_abroad_short_term)) {
              if($customer_work_abroad_short_term == 0) {echo '<input type="checkbox" name="customer_work_abroad_short_term" id="customer_work_abroad_short_term" />';}
              else {echo '<input type="checkbox" name="customer_work_abroad_short_term" id="customer_work_abroad_short_term" checked="checked" />';}
            }
            else echo '<input type="checkbox" name="customer_work_abroad_short_term" id="customer_work_abroad_short_term" />';
          ?>
          <label for="customer_work_abroad_short_term" style="display: inline-block;margin: 0;"><?=$languages['header_customer_work_abroad_short_term'];?></label>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <label for="customer_explanation_text"><?=$languages['header_customer_work_abroad_explanation_text'];?></label>
          <textarea name="customer_explanation_text" class="form-control"><?php if(isset($customer_explanation_text)) echo $customer_explanation_text;?></textarea>
        </div>

      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div class="row">
        <div class="col-lg-12 col-md-8 col-sm-12 col-xs-12">
          <label for="customer_email"><?=$languages['header_customer_email'];?></label>
          <input type="text" name="customer_email" id="customer_email" class="form-control" value="<?php if(isset($customer_email)) echo $customer_email;?>" onBlur="CheckIfUserEmailIsValidForUpdate(this.value,'<?=$current_lang;?>')" />
          <input type="hidden" name="customer_email_status" id="customer_email_status" value="<?php if(!empty($errors['customer_email_status'])) echo "error"; else echo "ok"?>" />
          <span id="customer_email_is_valid"></span>
          <?php if(!empty($errors['customer_email'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_email'];?></span><?php } ?>
          <?php if(!empty($errors['customer_email_status'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_email_status'];?></span><?php } ?>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row hidden">
        <?php
          if(isset($customer_is_in_mailist)) {
            if($customer_is_in_mailist == 0) {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" />';}
            else {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';}
          }
        ?>
        <label for="customer_is_in_mailist" style="display: inline-block;"><?=$languages['header_customer_is_in_mailist'];?></label>
        <div class="clearfix">&nbsp;</div>
      </div>

      <div class="row">
        <div class="col-sm-12 mt-10">
          <button type="submit" name="update_profile" class="btn btn-primary"><?=$languages['btn_save'];?></button>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
    </form>
    <script>
      $(function() {
        //autocomplete sites
        $("#customer_site_name_label").autocomplete({
          source: "<?=SITEFOLDERSL;?>/ajax/get-sites-autocomplete.php",
          minLength: 2,
          select: function( event, ui ) {
            //alert(ui.item.site_name);
            $('#customer_site_id').val(ui.item.site_id);
            $('#customer_site_type').val(ui.item.site_type);
            $('#customer_site_name').val(ui.item.site_name);
            $('#customer_site_postcode').val(ui.item.site_postcode);
          },
          close: function( event, ui ) {
            $('#customer_site_postcode_label').val($('#customer_site_postcode').val());
            $('#customer_site_name_label').val($('#customer_site_name').val());
          }
        });

      });
    </script>