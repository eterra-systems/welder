<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
 // print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  if(isset($_POST['cancel'])) {
    header("Location: user-profile-dashboard.php");
  }
  
  if(isset($_POST['update_profile'])) {
    print_array_for_debug($_POST);exit;
    
    $customer_firstname = trim($_POST['customer_firstname']);
    $customer_surname = trim($_POST['customer_surname']);
    $customer_lastname = trim($_POST['customer_lastname']);
    $customer_gender = $_POST['customer_gender'];
    $customer_age = intval($_POST['customer_age']);
    $customer_address_site_id = $_POST['customer_address_site_id'];
    $customer_address_site_name = $_POST['customer_address_site_name'];
    $customer_address_site_postcode = $_POST['customer_address_site_postcode'];
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
      
      $query_update_user = "UPDATE `customers` SET `customer_email`='$customer_email',
                                                  `customer_phone`='$customer_phone',
                                                  `customer_is_in_mailist`='$customer_is_in_mailist' 
                        WHERE `customer_id` = '$customer_id'";
      //echo $query_update_user."<br>";
      $result_update_user = mysqli_query($db_link, $query_update_user);
      if(!$result_update_user) {
        echo $languages['sql_error_update']." - 2 ".mysqli_error($db_link);
      }
      else $success = true;
    }
  }
  else {
    $query_customer = "SELECT `customers`.*, `customers_company`.*
                         FROM `customers` 
                   INNER JOIN `customers_company` ON `customers_company`.`customer_id` = `customers`.`customer_id`
                        WHERE `customers`.`customer_id` = '$customer_id'";
    //echo $query_customer;
    $result_customer = mysqli_query($db_link, $query_customer);
    if(!$result_customer) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_customer) > 0) {
      $customer = mysqli_fetch_assoc($result_customer);
      $customer_email = $customer['customer_email'];
      $customer_phone = $customer['customer_phone'];
      $customer_companyname = $customer['company_name'];
      $customer_firstname = $customer['first_name'];
      //$customer_surname = $customer['surname'];
      $customer_lastname = $customer['last_name'];
      //$customer_age = $customer['age'];
      //$customer_gender = $customer['gender'];
      //$customer_work_abroad = $customer['work_abroad'];
      //$customer_work_abroad_short_term = $customer['short_term'];
      //$customer_work_abroad_long_term = $customer['long_term'];
      $customer_explanation_text = $customer['explanation_text'];
      $customer_is_in_mailist = $customer['customer_is_in_mailist'];
    }
    //echo "<pre>";print_r($_SERVER);
  }
?>
    <form name="user_profile_data" id="user_profile_data" class="form-group" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<?php
    if(isset($success)) {
?>
  <div class="row">
    <p class="alert alert-success">Промерните бяха запазени успешно</p>
  </div>
<?php
    }
    if(!empty($errors)) {

      //foreach($errors as $error) echo "<div class='warning_field'>$error</div>";
    }
?>
      <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
      
      <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_companyname"><?=$languages['header_customer_companyname'];?><span class="red">*</span></label>
        <input type="text" name="customer_companyname" id="customer_copanyname" class="form-control" value="<?php if(isset($customer_companyname)) echo $customer_companyname;?>" />
        <?php if(!empty($errors['customer_companyname'])) { ?><span class="alert alert-danger"><?=$errors['customer_companyname'];?></span><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>

      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_firstname"><?=$languages['header_firstname'];?><span class="red">*</span></label>
          <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
          <?php if(!empty($errors['customer_firstname'])) { ?><div class="alert alert-danger"><?=$errors['customer_firstname'];?></div><?php } ?>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_lastname"><?=$languages['header_lastname'];?><span class="red">*</span></label>
          <input type="text" name="customer_lastname" id="customer_lastname" class="form-control" value="<?php if(isset($customer_lastname)) echo $customer_lastname;?>" />
          <?php if(!empty($errors['customer_lastname'])) { ?><div class="alert alert-danger"><?=$errors['customer_lastname'];?></div><?php } ?>
        </div>
        
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div class="row">
      
      <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding-right: 0px">
          <label for="customer_address_city" style="display: block"><?=$languages['header_customer_address_site_name'];?><span class="red">*</span></label>
          <input type="text" name="customer_address_site_type" class="pull-left form-control" id="customer_address_site_type" disabled="disabled" value="<?php if(isset($customer_address_site_type)) echo $customer_address_site_type;else echo $languages['header_customer_address_site_type'];?>" style="width: 25%; margin-right: 1%;padding: 8px 10px" />
          <input type="text" name="customer_address_site_name_label" id="customer_address_site_name_label" class="form-control" autocomplete="off" value="<?php if(isset($customer_address_site_name)) echo $customer_address_site_name;?>" style="width: 74%;" />
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding-left: 1%">
          <label for="customer_address_postcode"><?=$languages['header_customer_address_postcode'];?></label>
          <input type="text" name="customer_address_site_postcode_label" class="form-control" disabled="disabled" id="customer_address_site_postcode_label" value="<?php if(isset($customer_address_site_postcode)) echo $customer_address_site_postcode;?>" />
          <input type="hidden" name="customer_address_site_id" id="customer_address_site_id" value="<?php if(isset($customer_address_site_id)) echo $customer_address_site_id;?>" />
          <input type="hidden" name="customer_address_site_name" id="customer_address_site_name" value="<?php if(isset($customer_address_site_name)) echo $customer_address_site_name;?>" />
          <input type="hidden" name="customer_address_site_postcode" id="customer_address_site_postcode" value="<?php if(isset($customer_address_site_postcode)) echo $customer_address_site_postcode;?>" />
          <?php if(!empty($errors['customer_address_site_name'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_address_site_name'];?></span><?php } ?>
        </div>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>
    
      <p class="alert alert-info"><i><?=$languages['text_email_specs'];?></i></p>
    <div class="row<?php if(!empty($errors['customer_password'])) echo ' form-error';?>">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_password"><?=$languages['header_customer_password'];?><span class="red">*</span></label>
        <input type="password" name="customer_password" id="customer_password" class="form-control" value="<?php if(isset($customer_password)) echo $customer_password;?>" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
        <span id="customer_password_is_valid"></span>
        <?php if(!empty($errors['customer_password'])) { ?><span class="alert alert-danger"><?=$errors['customer_password'];?></span><?php } ?>
      </div>
      <p class="clearfix hidden-lg hidden-md"></p>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12<?php if(!empty($errors['customer_password_retype'])) echo ' form-error';?>">
        <label for="customer_password_retype"><?=$languages['header_customer_password_retype'];?><span class="red">*</span></label>
        <input type="password" name="customer_password_retype" id="customer_password_retype" class="form-control" value="<?php if(isset($customer_password_retype)) echo $customer_password_retype;?>" />
        <?php if(!empty($errors['customer_passwords_mismatch'])) { ?><span class="alert alert-danger"><?=$errors['customer_passwords_mismatch'];?></span><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="row<?php if(!empty($errors['customer_phone'])) echo ' form-error';?>">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_phone"><?=$languages['header_customer_phone'];?><span class="red">*</span></label>
        <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
        <?php if(!empty($errors['customer_phone'])) { ?><span class="alert alert-danger"><?=$errors['customer_phone'];?></span><?php } ?>
      </div>
    </div>
    <p><i class="fa fa-info-circle"></i> <i><?=$languages['text_phone_example'];?></i></p>

    <div>
      <?php
        if(isset($customer_is_in_mailist)) {
          if($customer_is_in_mailist == 0) {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" />';}
          else {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';}
        }
        else echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';
      ?>
      <label for="customer_is_in_mailist" style="display: inline-block;"><?=$languages['header_customer_company_is_in_mailist'];?></label>
    </div>
    <div class="clearfix">&nbsp;</div>
      
    <div class="<?php if(!empty($errors['recaptcha_response_field'])) echo "form-error";?>">
      <?php if(!empty($errors['recaptcha_response_field'])) { ?>
        <div class="alert alert-danger"><?=$errors['recaptcha_response_field'];?></div>
      <?php } ?>
      <div class="g-recaptcha" data-sitekey="<?=$sitekey;?>"></div>
    </div>
    <p class="clearfix"></p>

      <div class="clearfix">&nbsp;</div>

      <div class="row">
        <div class="col-sm-12 mt-10">
          <button type="submit" name="update_profile" class="btn btn-primary">Save</button>
          <button type="submit" name="cancel" class="btn btn-primary btn-inverse">Cancel</button>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
    </form>

  <script>
  $(function() {
    //autocomplete sites
    $("#customer_address_site_name_label").autocomplete({
      source: "<?=SITEFOLDERSL;?>/ajax/get-sites-autocomplete.php",
      minLength: 2,
      select: function( event, ui ) {
        //alert(ui.item.site_name);
        $('#customer_address_site_id').val(ui.item.site_id);
        $('#customer_address_site_type').val(ui.item.site_type);
        $('#customer_address_site_name').val(ui.item.site_name);
        $('#customer_address_site_postcode').val(ui.item.site_postcode);
      },
      close: function( event, ui ) {
        $('#customer_address_site_postcode_label').val($('#customer_address_site_postcode').val());
        $('#customer_address_site_name_label').val($('#customer_address_site_name').val());
      }
    });
  });
  </script>