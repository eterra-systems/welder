<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  if(isset($_POST['update_profile'])) {
    //echo "<pre>";print_r($_POST);
    
    $customer_firstname = $_POST['customer_firstname'];
    $customer_lastname = $_POST['customer_lastname'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $customer_is_in_mailist = 0;
      if(isset($_POST['customer_is_in_mailist'])) $customer_is_in_mailist = 1;
    if(!empty($_POST['customer_password'])) {
      $customer_password = $_POST['customer_password'];
      $customer_password_retype = $_POST['customer_password_retype'];
      
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
    } 
      
    if(empty($errors)) {
      
      $customer_hashed_password = password_hash($customer_password , PASSWORD_DEFAULT);
      
      $query_update_user = "UPDATE `customers` SET ";
      
      if(!empty($_POST['customer_password'])) {
        $query_update_user .= "`customer_salted_password`='$customer_hashed_password',";
      }
      $query_update_user .= " `customer_email`='$customer_email',
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
    $query_customer = "SELECT `customers`.*, `customers_welder`.*
                         FROM `customers` 
                   INNER JOIN `customers_welder` ON `customers_welder`.`customer_id` = `customers`.`customer_id`
                        WHERE `customers`.`customer_id` = '$customer_id'";
    //echo $query_customer;
    $result_customer = mysqli_query($db_link, $query_customer);
    if(!$result_customer) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_customer) > 0) {
      $customer = mysqli_fetch_assoc($result_customer);
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
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_firstname"><?=$languages['header_firstname'];?><span class="red">*</span></label>
          <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
          <?php if(!empty($errors['customer_firstname'])) { ?><div class="alert alert-danger"><?=$errors['customer_firstname'];?></div><?php } ?>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <label for="customer_surname"><?=$languages['header_surname'];?><span class="red">*</span></label>
          <input type="text" name="customer_surname" id="customer_surname" class="form-control" value="<?php if(isset($customer_surname)) echo $customer_surname;?>" />
          <?php if(!empty($errors['customer_surname'])) { ?><div class="alert alert-danger"><?=$errors['customer_surname'];?></div><?php } ?>
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
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
          <label for="customer_age"><?=$languages['header_customer_age'];?><span class="red">*</span></label>
          <input type="text" name="customer_age" id="customer_age" class="form-control" value="<?php if(isset($customer_age)) echo $customer_age;?>" />
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
          <label for="customer_gender"><?=$languages['header_gender'];?><span class="red">*</span></label>
          <select name="customer_gender" id="customer_gender" class="form-control">
            <option value="male"<?php if(isset($customer_gender) && $customer_gender == "male") echo " selected";?>><?=$languages['option_male'];?></option>
            <option value="female"<?php if(isset($customer_gender) && $customer_gender == "female") echo " selected";?>><?=$languages['option_female'];?></option>
          </select>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_phone"><?=$languages['header_customer_phone'];?><span class="red">*</span></label>
          <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
          <?php if(!empty($errors['customer_phone'])) { ?><div class="alert alert-danger"><?=$errors['customer_phone'];?></div><?php } ?>
          <p class="alert alert-info"><i class="fa fa-info-circle"></i> <i class="info"><?=$languages['text_phone_example'];?></i></p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
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

        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
          <label for="customer_explanation_text"><?=$languages['header_customer_work_abroad_explanation_text'];?></label>
          <textarea name="customer_explanation_text" id="customer_explanation_text" class="form-control">
            <?php if(isset($customer_explanation_text)) echo $customer_explanation_text;?>
          </textarea>
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

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_password"><?=$languages['header_customer_new_password'];?></label>
          <input type="password" name="customer_password" id="customer_password" class="form-control" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
          <p class="alert alert-info" style="margin: 0"><i class="fa fa-info-circle"></i> <i class="info"><?=$languages['text_password_hint'];?></i></p>
          <span id="customer_password_is_valid"></span>
          <?php if(!empty($errors['customer_password'])) { ?><br><span class="alert alert-danger"><?=$errors['customer_password'];?></span><?php } ?>
        </div>
        <p class="clearfix hidden-lg hidden-md"></p>
        
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_password_retype"><?=$languages['header_customer_password_retype'];?></label>
          <input type="password" name="customer_password_retype" id="customer_password_retype" class="form-control" />
          <?php if(!empty($errors['customer_passwords_mismatch'])) { ?><br><span class="alert alert-danger"><?=$errors['customer_passwords_mismatch'];?></span><?php } ?>
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
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="clearfix">&nbsp;</div>

      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <button type="submit" name="update_profile" class="button2"><?=$languages['btn_save'];?></button>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
    </form>