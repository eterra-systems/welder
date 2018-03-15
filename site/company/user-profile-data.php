<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  $query_customer = "SELECT `customer_email`, `customer_phone`, `customer_is_in_mailist` 
                       FROM `customers` 
                      WHERE `customer_id` = '$customer_id'";
  //echo $query_customer;
  $result_customer = mysqli_query($db_link, $query_customer);
  if(!$result_customer) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer) > 0) {
    $customer = mysqli_fetch_assoc($result_customer);
    $customer_email = $customer['customer_email'];
    $customer_phone = $customer['customer_phone'];
    $customer_is_in_mailist = $customer['customer_is_in_mailist'];
  }
  //echo "<pre>";print_r($_SERVER);
  
  if(isset($_POST['update_profile'])) {
    //echo "<pre>";print_r($_POST);
    $customer_companyname = $_POST['customer_companyname'];
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
?>
    <form name="user_profile_data" id="user_profile_data" class="form-group" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<?php
    if(isset($success)) {
?>
  <div class="row">
    <p class="alert alert-success">Промените бяха запазени успешно</p>
  </div>
<?php
    }
    if(!empty($errors)) {

      //foreach($errors as $error) echo "<div class='warning_field'>$error</div>";
    }
?>
      <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
      
      <div class="row<?php if(!empty($errors['customer_copanyname'])) echo ' form-error';?>">
      
        <label for="customer_copanyname"><?=$languages['header_customer_companyname'];?><span class="red">*</span></label>
        <input type="text" name="customer_copanyname" id="customer_copanyname" class="form-control" value="<?php if(isset($customer_copanyname)) echo $customer_copanyname;?>" />
        <?php if(!empty($errors['customer_copanyname'])) { ?><span class="alert alert-danger"><?=$errors['customer_copanyname'];?></span><?php } ?>
      
    </div>
    <div class="clearfix">&nbsp;</div>
    
      <div class="row <?php if(!empty($errors['customer_email_status']) || !empty($errors['customer_email'])) echo ' class="error_field"';?>">
        <label for="customer_email"><?=$languages['header_customer_email'];?></label>
        <input type="text" name="customer_email" id="customer_email" class="form-control" value="<?php if(isset($customer_email)) echo $customer_email;?>" onBlur="CheckIfUserEmailIsValidForUpdate(this.value,'<?=$current_lang;?>')" />
        <input type="hidden" name="customer_email_status" id="customer_email_status" value="<?php if(!empty($errors['customer_email_status'])) echo "error"; else echo "ok"?>" />
        <span id="customer_email_is_valid"></span>
        <?php if(!empty($errors['customer_email'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_email'];?></span><?php } ?>
        <?php if(!empty($errors['customer_email_status'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_email_status'];?></span><?php } ?>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row <?php if(!empty($errors['customer_firstname'])) echo ' class="error_field"';?>">
        <label for="customer_firstname"><?=$languages['header_customer_firstname'];?></label>
        <input type="text" name="customer_firstname" id="customer_firstname" class="form-control" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
        <?php if(!empty($errors['customer_firstname'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_firstname'];?></span><?php } ?>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row <?php if(!empty($errors['customer_lastname'])) echo ' class="error_field"';?>">
        <label for="customer_lastname"><?=$languages['header_customer_lastname'];?></label>
        <input type="text" name="customer_lastname" id="customer_lastname" class="form-control" value="<?php if(isset($customer_lastname)) echo $customer_lastname;?>" />
        <?php if(!empty($errors['customer_lastname'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_lastname'];?></span><?php } ?>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row <?php if(!empty($errors['customer_phone'])) echo ' class="error_field"';?>">
        <label for="customer_phone"><?=$languages['header_customer_phone'];?></label>
        <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
        <?php if(!empty($errors['customer_phone'])) { ?>&nbsp;&nbsp;<span class="alert alert-danger"><?=$errors['customer_phone'];?></span><?php } ?>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row <?php if(!empty($errors['customer_password'])) echo ' class="error_field"';?>">
        <p><i class="fa fa-info-circle"></i> <i class="info"><?=$languages['text_password_hint'];?></i></p>
        <label for="customer_password"><?=$languages['header_customer_new_password'];?></label>
        <input type="password" name="customer_password" id="customer_password" class="form-control" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
        <span id="customer_password_is_valid"></span>
        <?php if(!empty($errors['customer_password'])) { ?><br><span class="alert alert-danger"><?=$errors['customer_password'];?></span><?php } ?>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row <?php if(!empty($errors['customer_password_retype'])) echo ' class="error_field"';?>">
        <label for="customer_password_retype"><?=$languages['header_customer_password_retype'];?></label>
        <input type="password" name="customer_password_retype" id="customer_password_retype" class="form-control" />
        <?php if(!empty($errors['customer_passwords_mismatch'])) { ?><br><span class="alert alert-danger"><?=$errors['customer_passwords_mismatch'];?></span><?php } ?>
      </div>
      <div class="clearfix">&nbsp;</div>

      <div class="row">
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
        <button type="submit" name="update_profile" class="button2"><?=$languages['btn_save'];?></button>
      </div>
      <div class="clearfix">&nbsp;</div>
    </form>