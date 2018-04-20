<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  if(isset($_POST['cancel'])) {
    header("Location: user-profile-data.php");
  }

  $query_customer = "SELECT `customers`.*, `customers_company`.*
                       FROM `customers` 
                 INNER JOIN `customers_company` ON `customers_company`.`customer_id` = `customers`.`customer_id`
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
    $customer_companyname = $customer['company_name'];
    $customer_firstname = $customer['first_name'];
    $customer_lastname = $customer['last_name'];
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
  //echo "<pre>";print_r($_SERVER);
  
  if(isset($_POST['update_profile'])) {
    //print_array_for_debug($_POST);exit;
    
    $customer_companyname = mysqli_real_escape_string($db_link, trim($_POST['customer_companyname']));
    $customer_firstname = trim($_POST['customer_firstname']);
    $customer_lastname = trim($_POST['customer_lastname']);
    $customer_site_id = $_POST['customer_site_id'];
    $customer_site_name = $_POST['customer_site_name'];
    $customer_site_postcode = $_POST['customer_site_postcode'];
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
        echo $languages['sql_error_update']." - 1 update `customers`".mysqli_error($db_link);
      }
      
      $query_update_user = "UPDATE `customers_company` SET `site_id`='$customer_site_id',
                                                           `company_name`='$customer_companyname',
                                                           `first_name`='$customer_firstname',
                                                           `last_name`='$customer_lastname'
                                                     WHERE `customer_id` = '$customer_id'";
      //echo $query_update_user."<br>";
      $result_update_user = mysqli_query($db_link, $query_update_user);
      if(!$result_update_user) {
        echo $languages['sql_error_update']." - 2 update `customers_company`".mysqli_error($db_link);
      }
      else $success = true;
      
      $_SESSION['customer_name'] = "$customer_firstname $customer_lastname";
      $_SESSION['company']['company_name'] = $customer_companyname;
      unset($_POST);
    }
  }
?>
    <form name="user_profile_data" id="user_profile_data" class="form-group" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<?php
    if(isset($success)) {
?>
    <p class="alert alert-success mb-15"><?=$languages['text_update_was_successfull'];?></p>
    <script>
      // this script will prevent resubmitting the form on refresh
      if( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
      }
    </script>
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
            <label for="profile_image"><?=$languages['header_image_company'];?></label>
            <input type="hidden" name="profile_image_preview" id="profile_image_preview" value="<?=$profile_image;?>">
            <input type="hidden" id="upload_url" value="<?=SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/ajax/upload-profile-image.php";?>">
            <input type="file" name="profile_image" id="profile_image">
            <span class="font12 font-italic hidden"><i class="fa fa-info-circle"></i> photo must not bigger than 250kb</span>
          </div>
        </div>
      </div>
      <p class="clearfix"></p>
      
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_companyname"><?=$languages['header_customer_companyname'];?><span class="red">*</span></label>
          <input type="text" name="customer_companyname" id="customer_companyname" class="form-control" value="<?php if(isset($customer_companyname)) echo $customer_companyname;?>" />
          <?php if(!empty($errors['customer_companyname'])) { ?><span class="alert alert-danger"><?=$errors['customer_companyname'];?></span><?php } ?>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_firstname"><?=$languages['header_customer_contactperson'].' / '.$languages['header_customer_firstname'];?><span class="red">*</span></label>
          <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
          <?php if(!empty($errors['customer_firstname'])) { ?><div class="alert alert-danger"><?=$errors['customer_firstname'];?></div><?php } ?>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_lastname"><?=$languages['header_lastname'];?><span class="red">*</span></label>
          <input type="text" name="customer_lastname" id="customer_lastname" class="form-control" value="<?php if(isset($customer_lastname)) echo $customer_lastname;?>" />
          <?php if(!empty($errors['customer_lastname'])) { ?><div class="alert alert-danger"><?=$errors['customer_lastname'];?></div><?php } ?>
        </div>
        
      </div>
      <div class="clearfix">&nbsp;</div>
      
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
      <div class="clearfix">&nbsp;</div>
      
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_phone"><?=$languages['header_customer_phone'];?><span class="red">*</span></label>
          <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
          <?php if(!empty($errors['customer_phone'])) { ?><div class="alert alert-danger"><?=$errors['customer_phone'];?></div><?php } ?>
          <span class="font12 font-italic"><i class="fa fa-info-circle"></i> <?=$languages['text_phone_example'];?></span>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>
        
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_email"><?=$languages['header_customer_email'];?></label>
          <input type="text" name="customer_email" id="customer_email" class="form-control" value="<?php if(isset($customer_email)) echo $customer_email;?>" onBlur="CheckIfUserEmailIsValidForUpdate(this.value,'<?=$current_lang;?>')" />
          <input type="hidden" name="customer_email_status" id="customer_email_status" value="<?php if(!empty($errors['customer_email_status'])) echo "error"; else echo "ok"?>" />
          <span id="customer_email_is_valid"></span>
          <?php if(!empty($errors['customer_email'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_email'];?></span><?php } ?>
          <?php if(!empty($errors['customer_email_status'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_email_status'];?></span><?php } ?>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>

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