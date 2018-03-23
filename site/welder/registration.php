<?php
  $registration_was_successfull = false;
  $sitekey = "6Le3IEYUAAAAAPssLvABf4DmEfxX5RLwb04bIRHw";
  $secretkey = "6Le3IEYUAAAAAE47DgddVeqGkccpLkbh-7fnFYs9";
    
  $category_ids_tree = array();
  
  if(isset($_POST['sign_up'])) {
    
    //print_array_for_debug($_POST);exit;
  
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    $errors = array(); //defining an array errors, wich will collect them, if any
    
    //we will check if all the fields are filled in at all
    $exclude_fields_arr = array(
        "sign_up",
        "customer_surname",
        "customer_address_info",
        "customer_address_site_name_label",
        "customer_address_site_id",
        "customer_certificates",
        "customer_work_abroad",
        "customer_work_abroad_long_term",
        "customer_work_abroad_short_term",
        "customer_address_info",
        "customer_explanation_text",
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
    
    $customer_group_id = 1; // regular user
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
    if(isset($_POST['categories'])) {
      $categories = $_POST['categories'];
      $category_ids_tree = $categories;
    }
    else $errors['categories'] = $languages['error_choosen_category'];
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
    
    //handling certificates if any
    if(isset($_FILES['customer_certificates'])) {
      //1048576 bytes = 1MB
      $max_file_size = 8; //8MB
      $max_file_size_bytes = 1048576*$max_file_size;
      foreach($_FILES['customer_certificates']['error'] as $key_file => $error) {
        if($error != 4) {
          $extension = pathinfo($_FILES['customer_certificates']['name'][$key_file], PATHINFO_EXTENSION);
          $file_exstension = mb_convert_case($extension, MB_CASE_LOWER, "UTF-8");
          if(!is_file_valid_format($file_exstension)) {
            $errors['customer_certificates'][$key_file] = $languages['error_file_extension']."$file_exstension<br>";
          }

          if(($_FILES['customer_certificates']['size'][$key_file] < $max_file_size_bytes) && ($_FILES['customer_certificates']['error'][$key_file] == 0)) {
            // no error
          }
          elseif(($_FILES['customer_certificates']['size'][$key_file] > $max_file_size_bytes) || ($_FILES['customer_certificates']['error'][$key_file] == 1 || $_FILES['customer_certificates']['error'][$key_file] == 2)) {
            $errors['customer_certificates'][$key_file] = $languages['error_file_size'].$max_file_size."MB<br>";
          }
          else {
            if($_FILES['customer_certificates']['error'][$key_file] != 4) { // error 4 means no file was uploaded
              $errors['customer_certificates'][$key_file] = $languages['error_file_uploading']."<br>";
            }
          }
        }  
      }
    }
    
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
      $customer_surname = mysqli_real_escape_string($db_link,$customer_surname);
      $customer_lastname = mysqli_real_escape_string($db_link,$customer_lastname);
      $customer_age = mysqli_real_escape_string($db_link,$customer_age);
      $explanation_text_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $customer_explanation_text));
      
      $query_insert_welder = "INSERT INTO `customers_welder`(`customer_id`,
                                                            `site_id`,
                                                            `first_name`,
                                                            `surname`,
                                                            `last_name`,
                                                            `age`,
                                                            `gender`,
                                                            `work_abroad`,
                                                            `long_term`,
                                                            `short_term`,
                                                            `explanation_text`) 
                                                    VALUES ('$customer_id',
                                                            '$customer_address_site_id',
                                                            '$customer_firstname',
                                                            '$customer_surname',
                                                            '$customer_lastname',
                                                            '$customer_age',
                                                            '$customer_gender',
                                                            '$customer_work_abroad',
                                                            '$customer_work_abroad_long_term',
                                                            '$customer_work_abroad_short_term',
                                                            $explanation_text_db)";
      //echo $query_insert_welder;
      $all_queries .= "<br>".$query_insert_welder;
      $result_insert_welder = mysqli_query($db_link, $query_insert_welder);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 2 `customers_welder` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($categories as $key) {
        
        $category_hierarchy_ids = $_POST['category_hierarchy_ids'][$key];
        
        $q_insert_ctc = "INSERT INTO `customers_to_categories`(`customer_id`, `category_hierarchy_ids`) VALUES ('$customer_id','$category_hierarchy_ids')";
        $all_queries .= "<br>".$q_insert_ctc;
        $result_insert_ctc = mysqli_query($db_link, $q_insert_ctc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 3 `customers_to_categories` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      if(isset($_FILES['customer_certificates'])) {
        
        $upload_path = $_SERVER['DOCUMENT_ROOT'].SITEFOLDERSL."/welder/certificates/$customer_id/";
        mkdir($upload_path, 0777);
        chmod($upload_path, 0777);
        
        foreach($_FILES['customer_certificates']['error'] as $key_file => $error) {

          if($error != 4) { // if not empty
            $customer_certificate_tmp_name  = $_FILES['customer_certificates']['tmp_name'][$key_file];
            $customer_certificate_name = $_FILES['customer_certificates']['name'][$key_file];
            $extension = pathinfo($customer_certificate_name, PATHINFO_EXTENSION);
            $file_exstension = mb_convert_case($extension, MB_CASE_LOWER, "UTF-8");
            $file_name = str_replace(".$extension", "", $customer_certificate_name);
            $customer_certificate_name = "$file_name.$file_exstension";

            $q_insert_ctc = "INSERT INTO `customers_welder_certificates`(`certificate_id`, `customer_id`, `certificate_name`, `certificate_exstension`) 
                                                                 VALUES (NULL,'$customer_id','$customer_certificate_name','$file_exstension')";
            //echo $q_insert_ctc;
            $result_insert_ctc = mysqli_query($db_link, $q_insert_ctc);
            if(mysqli_affected_rows($db_link) <= 0) {
              echo $languages['sql_error_insert']." - 1 `customers_to_certificates` ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }

            if(is_uploaded_file($customer_certificate_tmp_name)) {
              move_uploaded_file($customer_certificate_tmp_name, $upload_path.$customer_certificate_name);
            }
            else {
              echo $languages['error_file_uploading']." - 2 file $customer_certificate_name ($customer_certificate_tmp_name) - ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
            }
          }
        }
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
  //
  //if not all the requered fields was filled in correct by the user make the sign up form showing the errors
  
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
      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <label for="customer_firstname"><?=$languages['header_firstname'];?><span class="text-danger">*</span></label>
        <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
        <?php if(!empty($errors['customer_firstname'])) { ?><div class="alert alert-danger"><?=$errors['customer_firstname'];?></div><?php } ?>
      </div>
      <p class="clearfix hidden-lg hidden-md"></p>
      
      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <label for="customer_surname"><?=$languages['header_surname'];?></label>
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
        <p class="alert alert-info"><i class="fa fa-info-circle"></i> <i><?=$languages['text_phone_example'];?></i></p>
      </div>
    </div>
      
    <div class="row">
      
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding: 0">
          <label for="customer_address_city" style="display: block"><?=$languages['header_customer_address_site_name'];?><span class="text-danger">*</span></label>
          <input type="text" name="customer_address_site_type" class="pull-left form-control" id="customer_address_site_type" disabled="disabled" value="<?php if(isset($customer_address_site_type)) echo $customer_address_site_type;else echo $languages['header_customer_address_site_type'];?>" style="width: 25%; margin-right: 1%;padding: 8px 10px" />
          <input type="text" name="customer_address_site_name_label" id="customer_address_site_name_label" class="form-control" autocomplete="off" value="<?php if(isset($customer_address_site_name)) echo $customer_address_site_name;?>" style="width: 74%;" />
          <?php if(!empty($errors['customer_address_site_name'])) { ?><div class="alert alert-danger"><?=$errors['customer_address_site_name'];?></div><?php } ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0 0 0 1%">
          <label for="customer_address_postcode"><?=$languages['header_customer_address_postcode'];?></label>
          <input type="text" name="customer_address_site_postcode_label" class="form-control" disabled="disabled" id="customer_address_site_postcode_label" value="<?php if(isset($customer_address_site_postcode)) echo $customer_address_site_postcode;?>" />
          <input type="hidden" name="customer_address_site_id" id="customer_address_site_id" value="<?php if(isset($customer_address_site_id)) echo $customer_address_site_id;?>" />
          <input type="hidden" name="customer_address_site_name" id="customer_address_site_name" value="<?php if(isset($customer_address_site_name)) echo $customer_address_site_name;?>" />
          <input type="hidden" name="customer_address_site_postcode" id="customer_address_site_postcode" value="<?php if(isset($customer_address_site_postcode)) echo $customer_address_site_postcode;?>" />
        </div>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>
      
    <div class="row">
      <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
        <label for="customer_explanation_text">Предпочитания</label>
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
      
      <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
        <label for="customer_explanation_text"><?=$languages['header_customer_work_abroad_explanation_text'];?></label>
        <textarea name="customer_explanation_text" id="customer_explanation_text" class="form-control"><?php if(isset($customer_explanation_text)) echo $customer_explanation_text;?></textarea>
      </div>
      
    </div>
    <div class="clearfix">&nbsp;</div>
    
    <label for="customer_skills"><?=$languages['header_skills'];?><span class="text-danger">*</span></label>
    <?php if(!empty($errors['categories'])) { ?><div class="alert alert-danger"><?=$errors['categories'];?></div><?php } ?>
    <div class="tree row">
      <ul>
        <?php list_categories_with_checkboxes($category_parent_id = 0, $category_root_id = 0, $category_ids_tree) ;?>
      </ul>
    </div>
    <div class="clearfix">&nbsp;</div>
    
    <?php print_categories_info(); ?>
    
    <div class="clearfix">&nbsp;</div>

    <div class="row email">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_email"><?=$languages['header_customer_email'];?><span class="text-danger">*</span></label>
        <input type="text" name="customer_email" id="customer_email" class="form-control" value="<?php if(isset($customer_email)) echo $customer_email;?>" onBlur="CheckIfUserEmailIsValid(this.value,'<?=$current_lang;?>')" />
        <input type="hidden" name="customer_email_status" id="customer_email_status" value="<?php if(!empty($errors['customer_email_status'])) echo "error"; else echo "ok"?>" />
        <span id="customer_email_is_valid"></span>
        <?php if(!empty($errors['customer_email'])) { ?><div class="alert alert-danger"><?=$errors['customer_email'];?></div><?php } ?>
        <?php if(!empty($errors['customer_email_status'])) { ?><div class="alert alert-danger"><?=$errors['customer_email_status'];?></div><?php } ?>
      </div>
      <p class="clearfix hidden-lg hidden-md"></p>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_email_retype"><?=$languages['header_customer_email_retype'];?><span class="text-danger">*</span></label>
        <input type="text" name="customer_email_retype" id="customer_email_retype" class="form-control" value="<?php if(isset($customer_email_retype)) echo $customer_email_retype;?>" />
        <?php if(!empty($errors['customer_emails_mismatch'])) { ?><div class="alert alert-danger"><?=$errors['customer_emails_mismatch'];?></div><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_password"><?=$languages['header_customer_password'];?><span class="text-danger">*</span></label>
        <input type="password" name="customer_password" id="customer_password" class="form-control" value="<?php if(isset($customer_password)) echo $customer_password;?>" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
        <p class="alert alert-info" style="margin: 0"><i class="fa fa-info-circle"></i> <i><?=$languages['text_email_specs'];?></i></p>
        <span id="customer_password_is_valid"></span>
        <?php if(!empty($errors['customer_password'])) { ?><div class="alert alert-danger"><?=$errors['customer_password'];?></div><?php } ?>
      </div>
      <p class="clearfix hidden-lg hidden-md"></p>
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="customer_password_retype"><?=$languages['header_customer_password_retype'];?><span class="text-danger">*</span></label>
        <input type="password" name="customer_password_retype" id="customer_password_retype" class="form-control" value="<?php if(isset($customer_password_retype)) echo $customer_password_retype;?>" />
        <?php if(!empty($errors['customer_passwords_mismatch'])) { ?><div class="alert alert-danger"><?=$errors['customer_passwords_mismatch'];?></div><?php } ?>
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>
    
    <div>
      <div id="certificates" class="m-b-20">
        <label for="customer_certificates"><?=$languages['header_certificates'];?></label>
        <?php
          if(isset($errors['customer_certificates'])) {
            foreach($errors['customer_certificates'] as $customer_certificate_error) {
              echo "<div class='error'>$customer_certificate_error</div>";
            }
          }
        ?>
        <p><input type="file" name="customer_certificates[]" class="customer_certificate"  style="width: auto;" /></p>
        <p><input type="file" name="customer_certificates[]" class="customer_certificate"  style="width: auto;" /></p>
        <p><input type="file" name="customer_certificates[]" class="customer_certificate"  style="width: auto;" /></p>
        <p id="more_certificates_container">

        </p>
      </div>

      <a href="javascript:;" class="btn btn-success" onClick="ShowOneMoreCertificateInput('10')">
        <i class="icon icon_plus_sign"></i>
        <?=$languages['btn_add_certificates_inputs'];?>
      </a>
      <input type="hidden" id="more_certificates_id" value="3" />
      <input type="hidden" id="alt_delete" value="<?=$languages['alt_delete'];?>" />
    </div>
    <div class="clearfix">&nbsp;</div>
    
    <div class="hidden">
      <?php
        if(isset($customer_is_in_mailist)) {
          if($customer_is_in_mailist == 0) {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" />';}
          else {echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';}
        }
        else echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';
      ?>
      <label for="customer_is_in_mailist" style="display: inline-block;"><?=$languages['header_customer_is_in_mailist'];?></label>
      <div class="clearfix">&nbsp;</div>
    </div>
    
    <div>
      <div class="g-recaptcha" data-sitekey="<?=$sitekey;?>"></div>
      <?php if(!empty($errors['recaptcha_response_field'])) { ?>
        <div class="alert alert-danger"><?=$errors['recaptcha_response_field'];?></div>
      <?php } ?>
    </div>
    <p class="clearfix"></p>

    <div>
      <button type="submit" name="sign_up" class="btn btn-primary"><span><?=$languages['btn_sign_up'];?></span></button>
    </div>

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
    
    //start family tree
    $('.select_all').on('click', function (e) {
      var state = true;
      var root = $(this).attr("data-root");
      if($(this).hasClass("active")) {
        $(this).removeClass("active")
        state = false;
      }
      else {
        $(this).addClass("active")
        state = true;
      }
      var checkboxes = document.getElementsByClassName("categories_"+root);
      for(var i=0; i<checkboxes.length ; i++) {
        if(checkboxes[i].type == "checkbox") {
          checkboxes[i].checked = state;
        }
      }
    });
    $('.tree li.expandable label').on('click', function (e) {
        var current_tree_parent = $(this).parent('.expandable');
        var current_tree_id = current_tree_parent.attr('id');
        var child_ul = $(this).parent('.expandable').find(".expandable_ul_"+current_tree_id);
        if(child_ul.is(":visible")) {
          child_ul.hide('fast');
          current_tree_parent.removeClass("active_parent_tree");
          current_tree_parent.find(".fa_"+current_tree_id).removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
        }
        else {
          child_ul.show('fast');
          current_tree_parent.addClass("active_parent_tree");
          current_tree_parent.find(".fa_"+current_tree_id).removeClass("fa-plus-square-o").addClass("fa-minus-square-o");
        }
        e.stopPropagation();
    });
    //end family tree
      
  });
  </script>
<?php
  }