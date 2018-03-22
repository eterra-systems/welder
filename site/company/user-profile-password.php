<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  if(isset($_POST['cancel'])) {
    header("Location: user-profile-dashboard.php");
  }
  
  if(isset($_POST['update_profile'])) {
    print_array_for_debug($_POST);exit;
    
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
    else {
      $errors['error_required_field'] = $languages['error_required_field'];
    }
      
    if(empty($errors)) {
      
      $customer_hashed_password = password_hash($customer_password , PASSWORD_DEFAULT);
      
      $query_update_user = "UPDATE `customers` SET `customer_salted_password`='$customer_hashed_password' WHERE `customer_id` = '$customer_id'";
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
        <?php if(!empty($errors['error_required_field'])) { ?><br><span class="alert alert-danger"><?=$errors['error_required_field'];?></span><?php } ?>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
          <label for="customer_password"><?=$languages['header_customer_new_password'];?></label>
          <input type="password" name="customer_password" id="customer_password" class="form-control" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
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

      <div class="row">
        <div class="col-sm-12 mt-10">
          <button type="submit" name="update_profile" class="btn btn-primary">Save</button>
          <button type="submit" name="cancel" class="btn btn-primary btn-inverse">Cancel</button>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
    </form>