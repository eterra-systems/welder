<?php
  $registration_was_successfull = false;
  $sitekey = "6Le3IEYUAAAAAPssLvABf4DmEfxX5RLwb04bIRHw";
  $secretkey = "6Le3IEYUAAAAAE47DgddVeqGkccpLkbh-7fnFYs9";
    
  if(isset($_POST['sign_up'])) {
    
    //print_array_for_debug($_POST);exit;
  
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    $errors = array(); //defining an array errors, wich will collect them, if any
    
     //we will check if all the fields are filled in at all
    $exclude_fields_arr = array(
        "sign_up",
        "customer_surname",
        "customer_site_name_label",
        "customer_site_name",
        "customer_site_type",
        "customer_site_postcode",
        "customer_site_id",
        "customer_certificates",
        "categories",
        "category_ids",
        "category_hierarchy_ids",
        "g-recaptcha-response",
    );
    foreach($_POST as $name => $value) {
      if(!in_array($name, $exclude_fields_arr)) {
        if(empty(trim($value))) {
          $field_name = "header_".$name;
          $field_name_text = mb_convert_case($languages[$field_name], MB_CASE_LOWER, "UTF-8");
          $errors[$name] = $languages['error_registration_empty_field'].$field_name_text;
        }
      }
    }
    
    $customer_group_id = 2; // regular user
    $customer_companyname = trim($_POST['customer_companyname']);
    $customer_firstname = trim($_POST['customer_firstname']);
    $customer_lastname = trim($_POST['customer_lastname']);
    $current_country_id = $_POST['country_id'];
    $customer_site_id = $_POST['customer_site_id'];
    $customer_site_name = $_POST['customer_site_name'];
    $site_name_not_bg = (!empty($site_name)) ? $site_name : $_POST['site_name_not_bg'];
    $customer_site_postcode = $_POST['customer_site_postcode'];
    $customer_email = trim($_POST['customer_email']);
    $customer_email_retype = trim($_POST['customer_email_retype']);
    if($customer_email != $customer_email_retype) {
      $errors['customer_emails_mismatch'] = $languages['error_create_customer_emails_mismatch'];
    }
    if(isset($_POST['customer_email_status'])) {
      $customer_email_status =  $_POST['customer_email_status'];
      if($customer_email_status == "ok") {
        // check again if email is already taken if the form was autofilled 
        if(!check_if_user_email_is_valid($customer_email)) $errors['customer_email'] = $languages['error_create_customer_email_taken'];
      }
      else {
        $errors['customer_email_status'] = $languages['error_create_customer_email_taken'];
      }
    }
    $customer_password = $_POST['customer_password'];
    $customer_hashed_password = password_hash($customer_password , PASSWORD_DEFAULT);
    $customer_password_retype = $_POST['customer_password_retype'];
    $customer_phone = trim($_POST['customer_phone']);
    $customer_is_in_mailist = 0;
      if(isset($_POST['customer_is_in_mailist'])) $customer_is_in_mailist = 1;

    $recaptcha_response = false;
    if(isset($_POST['g-recaptcha-response'])) {
      $g_recaptcha_response = $_POST['g-recaptcha-response'];
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = array('secret' => "$secretkey", 'response' => $g_recaptcha_response);

      // use key 'http' even if you send the request to https://...
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data),
          ),
      );
      $context  = stream_context_create($options);
      $result = json_decode(file_get_contents($url, false, $context));

      $recaptcha_response = $result->success;
      if(!$recaptcha_response) {
        $errors['recaptcha_response_field'] = "<span class='red'>".$languages['error_create_customer_recaptcha']."</span>";  
      }
    }
    
    $uppercase = preg_match('@[A-Z]@', $customer_password);
    $lowercase = preg_match('@[a-z]@', $customer_password);
    $number    = preg_match('@[0-9]@', $customer_password);

    if(!$uppercase || !$lowercase || !$number || strlen($customer_password) < 8) {
      // tell the user something went wrong
      $errors['customer_password'] = $languages['error_registration_password_is_not_valid'];
    }
    
    $customer_passwords_mismatch = check_if_users_passwords_match($customer_password,$customer_password_retype);
    if(!empty($customer_passwords_mismatch)) {
      $errors['customer_passwords_mismatch'] = $customer_passwords_mismatch;
    }
    //echo"<pre>";print_r($errors);
    
    //if all the requered fields was filled in correct by the user make a database record
    if(empty($errors)) {
      
      $customer_email = mysqli_real_escape_string($db_link,$customer_email);
      $customer_phone = mysqli_real_escape_string($db_link,$customer_phone);
    
      $query_insert_customer = "INSERT INTO `customers`(`customer_id`, 
                                                        `customer_group_id`,  
                                                        `customer_salted_password`,
                                                        `customer_email`,  
                                                        `customer_phone`,   
                                                        `customer_is_in_mailist`,
                                                        `customer_is_blocked`,
                                                        `customer_is_active`, 
                                                        `customer_registration_date`) 
                                                VALUES (NULL,
                                                        '$customer_group_id',
                                                        '$customer_hashed_password',
                                                        '$customer_email',
                                                        '$customer_phone',
                                                        '$customer_is_in_mailist',
                                                        '0',
                                                        '0',
                                                        NOW())";
      //echo $query_insert_customer;
      $all_queries = "<br>".$query_insert_customer;
      $result_insert_customer = mysqli_query($db_link, $query_insert_customer);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 1 `customers` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $customer_id = mysqli_insert_id($db_link);
      
      $customer_firstname = mysqli_real_escape_string($db_link,$customer_firstname);
      $customer_lastname = mysqli_real_escape_string($db_link,$customer_lastname);
      
      $query_insert_welder = "INSERT INTO `customers_company`(`customer_company_id`,
                                                            `customer_id`,
                                                            `country_id`,
                                                            `site_id`,
                                                            `site_name`,
                                                            `company_name`,
                                                            `first_name`,
                                                            `last_name`) 
                                                    VALUES (NULL,
                                                            '$customer_id',
                                                            '$current_country_id',
                                                            '$customer_site_id',
                                                            '$site_name_not_bg',
                                                            '$customer_companyname',
                                                            '$customer_firstname',
                                                            '$customer_lastname')";
      //echo $query_insert_welder;
      $all_queries .= "<br>".$query_insert_welder;
      $result_insert_welder = mysqli_query($db_link, $query_insert_welder);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 2 `customers_company` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
      
      $to      = $_POST['customer_email'];
      $subject = $languages['email_subject_text'];
//      $logo_image = "http://".$_SERVER['SERVER_NAME'].SITEFOLDERSL."/images/welder-logo.jpg";
//      $logo_image_params = getimagesize($logo_image);
//      $logo_image_dimensions = $logo_image_params[3];
      $message = "<table>";
      $message .= "<tr>
                    <td>
                      <a href='".$_SERVER['SERVER_NAME']."' target='_blank'><font style='color:#424242;font-size:34px;'>Welder</a>
                    </td>
                  </tr>
                  <tr>
                    <td>
                    </td>
                  </tr>";
      $message .= "<tr><td>".$languages['email_message_text_1']."</td></tr>";
      $message .= "<tr><td>".$languages['email_message_text_2']." www.".$_SERVER['SERVER_NAME']."/$current_lang/confirm-account/$customer_id</td></tr>";
      $message .= "<tr><td>".$languages['email_message_text_3']."</td></tr>";
      $message .= "<tr><td>".$languages['email_message_text_4']." ".$_SERVER['SERVER_NAME']."/$current_lang/confirm-account/$customer_id</td></tr>";
      $message .= "<tr><td>&nbsp;</td></tr>";
      $message .= "<tr><td>&nbsp;</td></tr>";
      $message .= "</table>";
      $headers = $languages['email_headers_text'];
      $headers .= 'Cc: idimitrov@eterrasystems.com' . "\r\n";

      if(mail($to, $subject, $message, $headers,'-fidimitrov@eterrasystems.com')) {
        mysqli_commit($db_link);
        $registration_was_successfull = true;
      }
      else {
        print_r(error_get_last());
        echo $languages['error_registration_customer_send_email_fail'];
        mysqli_query($db_link, "ROLLBACK");
      }
    }
    
  }//if(isset($_POST['sign_up'])
  //if not all the requered fields was filled in correct by the user make the sign up form showing the errors
  
  $bg_form_style = 'style="display:none"';
  $not_bg_form_style = 'style="display:none"';

  if(isset($current_country_id)) {
    if($current_country_id == 33) {
      $bg_form_style = "";
    }
    else {
      $not_bg_form_style = "";
    }
  }
  else {
    $bg_form_style = "";
  }
  
  if($registration_was_successfull) {
?>
  <div class="form-group">&nbsp;</div>
  <div class="row">
    <div class="alert alert-success">
      <h2 class="no_margin"><?=$languages['header_registration_was_successfull'];?></h2>
    </div>
  </div>

  <h4><?=$languages['create_customer_success_text'];?></h4>
  <div class="form-group">&nbsp;</div>
<?php
  }
  else {
?>
  <form name="sign_up_form" id="sign_up_form" class="form-group form-horizontal" method="post" action="/<?=strip_tags($_GET['page']);?>">
      
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_companyname"><?=$languages['header_customer_companyname'];?><span class="red">*</span></label>
        <input type="text" name="customer_companyname" id="customer_copanyname" class="form-control" value="<?php if(isset($customer_companyname)) echo $customer_companyname;?>" />
        <?php if(!empty($errors['customer_companyname'])) { ?><div class="alert alert-danger"><?=$errors['customer_companyname'];?></div><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>
      
    <div class="row<?php if(!empty($errors['customer_firstname'])) echo ' form-error';?>">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_firstname"><?=$languages['header_customer_contactperson'].' / '.$languages['header_customer_firstname'];?><span class="red">*</span></label>
        <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
        <?php if(!empty($errors['customer_firstname'])) { ?><div class="alert alert-danger"><?=$errors['customer_firstname'];?></div><?php } ?>
      </div>
      <p class="clearfix hidden-lg hidden-md"></p>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_lastname"><?=$languages['header_customer_lastname'];?><span class="red">*</span></label>
        <input type="text" name="customer_lastname" id="customer_lastname" class="form-control" value="<?php if(isset($customer_lastname)) echo $customer_lastname;?>" />
        <?php if(!empty($errors['customer_lastname'])) { ?><div class="alert alert-danger"><?=$errors['customer_lastname'];?></div><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>
      
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label for="country_id"><?=$languages['header_customer_address_country'];?><span class="text-danger">*</span></label>
        <select name="country_id" id="country_id" class="form-control" onChange="DisplayCountryAddressForm(this.value)">
        <?php
          $country_list = "WHERE `country_id` IN(14,21,33,53,55,56,57)";
          $query_countries = "SELECT `country_id`,`country_name` FROM  `countries` ORDER BY `country_name` ASC ";
          //echo $query_countries;
          $result_countries = mysqli_query($db_link, $query_countries);
          if (!$result_countries) echo mysqli_error($db_link);
          if(mysqli_num_rows($result_countries) > 0) {

            while ($country = mysqli_fetch_assoc($result_countries)) {

              $country_id = $country['country_id'];
              $country_name = stripslashes($country['country_name']);
              $selected = (isset($current_country_id) && $current_country_id == $country_id) ? 'selected="selected"' : ""; //$country_id_db

              echo "<option value='$country_id' $selected>$country_name</option>";

            }
            mysqli_free_result($result_countries);
          }
        ?> 
        </select>
      </div>
      <p class="clearfix"></p>
      
      <div id="bg_form" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" <?=$bg_form_style;?>>
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
      
      <div id="not_bg_form" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" <?=$not_bg_form_style;?>>
        <label for="site_name_not_bg"><?=$languages['header_customer_address_site_name'];?><span class="text-danger">*</span></label>
        <input type="text" name="site_name_not_bg" id="site_name_not_bg" class="form-control" value="<?php if(isset($site_name_not_bg)) echo $site_name_not_bg;?>" />
        <?php if(!empty($errors['site_name_not_bg'])) { ?><div class="alert alert-danger"><?=$errors['site_name_not_bg'];?></div><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="row email<?php if(!empty($errors['customer_email_status']) || !empty($errors['customer_email'])) echo ' form-error';?>">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_email"><?=$languages['header_customer_email'];?><span class="red">*</span></label>
        <input type="text" name="customer_email" id="customer_email" class="form-control" value="<?php if(isset($customer_email)) echo $customer_email;?>" onBlur="CheckIfUserEmailIsValid(this.value,'<?=$current_lang;?>')" />
        <input type="hidden" name="customer_email_status" id="customer_email_status" value="<?php if(!empty($errors['customer_email_status'])) echo "error"; else echo "ok"?>" />
        <span id="customer_email_is_valid"></span>
        <?php if(!empty($errors['customer_email'])) { ?><div class="alert alert-danger"><?=$errors['customer_email'];?></div><?php } ?>
        <?php if(!empty($errors['customer_email_status'])) { ?><div class="alert alert-danger"><?=$errors['customer_email_status'];?></div><?php } ?>
      </div>
      <p class="clearfix hidden-lg hidden-md"></p>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12<?php if(!empty($errors['customer_email_retype']) || !empty($errors['customer_passwords_mismatch'])) echo ' form-error';?>">
        <label for="customer_email_retype"><?=$languages['header_customer_email_retype'];?><span class="red">*</span></label>
        <input type="text" name="customer_email_retype" id="customer_email_retype" class="form-control" value="<?php if(isset($customer_email_retype)) echo $customer_email_retype;?>" />
        <?php if(!empty($errors['customer_emails_mismatch'])) { ?><div class="alert alert-danger"><?=$errors['customer_emails_mismatch'];?></div><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>

    <p class="alert alert-info"><i><?=$languages['text_email_specs'];?></i></p>
    <div class="row<?php if(!empty($errors['customer_password'])) echo ' form-error';?>">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_password"><?=$languages['header_customer_password'];?><span class="red">*</span></label>
        <input type="password" name="customer_password" id="customer_password" class="form-control" value="<?php if(isset($customer_password)) echo $customer_password;?>" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
        <span id="customer_password_is_valid"></span>
        <?php if(!empty($errors['customer_password'])) { ?><div class="alert alert-danger"><?=$errors['customer_password'];?></div><?php } ?>
      </div>
      <p class="clearfix hidden-lg hidden-md"></p>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12<?php if(!empty($errors['customer_password_retype'])) echo ' form-error';?>">
        <label for="customer_password_retype"><?=$languages['header_customer_password_retype'];?><span class="red">*</span></label>
        <input type="password" name="customer_password_retype" id="customer_password_retype" class="form-control" value="<?php if(isset($customer_password_retype)) echo $customer_password_retype;?>" />
        <?php if(!empty($errors['customer_passwords_mismatch'])) { ?><div class="alert alert-danger"><?=$errors['customer_passwords_mismatch'];?></div><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="row<?php if(!empty($errors['customer_phone'])) echo ' form-error';?>">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_phone"><?=$languages['header_customer_phone'];?><span class="red">*</span></label>
        <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
        <?php if(!empty($errors['customer_phone'])) { ?><div class="alert alert-danger"><?=$errors['customer_phone'];?></div><?php } ?>
      </div>
    </div>
    <p><i class="fa fa-info-circle"></i> <i><?=$languages['text_phone_example'];?></i></p>

    <div class="hidden">
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

    <div>
      <button type="submit" name="sign_up" class="btn btn-primary"><span><?=$languages['btn_sign_up'];?></span></button>
    </div>

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
<?php
  }