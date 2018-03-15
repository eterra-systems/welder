<?php
  //echo"<pre>";print_r($_SERVER);exit;

//  unset($_SESSION);
//  session_destroy();
//  echo"<pre>Session<br>";print_r($_SESSION);
//  echo"<pre>Post<br>";print_r($_POST);
  
  $sitekey = "6Le3IEYUAAAAAPssLvABf4DmEfxX5RLwb04bIRHw";
  $secretkey = "6Le3IEYUAAAAAE47DgddVeqGkccpLkbh-7fnFYs9";
  if(isset($_SERVER['HTTP_REFERER'])) {
    if(!isset($_POST['login'])) {
      if(strstr($_SERVER['HTTP_REFERER'], "confirm-account")) $_SESSION['redirect_link'] = "/index.php";
      else $_SESSION['redirect_link'] = $_SERVER['HTTP_REFERER'];
    }
  }
  else {
    $_SESSION['redirect_link'] = $_SERVER['PHP_SELF'];
  }

  if(isset($_POST['login'])) {

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
        $errors['recaptcha_response_field'] = $languages['error_create_customer_recaptcha'];  
      }
    }
    
    if($recaptcha_response) {
      
      $customer_password = $_POST['customer_password'];
      $customer_email = $_POST['customer_email'];
      $user_is_active = true;

      $query_user_is_active = "SELECT `customer_id` 
                                 FROM `customers` 
                                WHERE `customer_email` = '$customer_email' AND `customers`.`customer_is_active` = '0'";
      //echo $query_user_is_active."<br>";
      $result_user = mysqli_query($db_link,$query_user_is_active);
      if(!$result_user) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_user) > 0) {
        $user_is_active = false;
      }
      mysqli_free_result($result_user);

      if($user_is_active) {
        $query_user = "SELECT `customers`.`customer_id`,`customers`.`customer_salted_password`,`customers_groups`.`customer_group_code`
                         FROM `customers` 
                   INNER JOIN `customers_groups` USING(`customer_group_id`)
                        WHERE `customer_email` = '$customer_email' AND `customers`.`customer_is_active` = '1' AND `customers`.`customer_is_blocked` = '0'";
        //echo $query_user."<br>";
        $result_user = mysqli_query($db_link,$query_user);
        if (!$result_user) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_user) > 0) {
          $customer = mysqli_fetch_assoc($result_user);

          $customer_id = $customer['customer_id'];
          $customer_group_code = $customer['customer_group_code'];
          $password_hash = $customer['customer_salted_password'];
          $customer_ip = get_client_ip_env();

          if(password_verify($customer_password, $password_hash)) {
            // password is correct

            $user_type_table = "`customers_$customer_group_code`";
            $query_user_type = "SELECT $user_type_table.* FROM $user_type_table WHERE `customer_id` = '$customer_id'";
            //echo $query_user_type;
            $result_user_type = mysqli_query($db_link,$query_user_type);
            if (!$result_user_type) echo mysqli_error($db_link);
            if(mysqli_num_rows($result_user_type) > 0) {
              
              $user_type = mysqli_fetch_assoc($result_user_type);
              $customer_firstname = $user_type['first_name'];
              $customer_lastname = $user_type['last_name'];
              
            }
            
            
            //make record for table users_log
            $query = "INSERT INTO `customers_logs`(`customer_log_id`, 
                                                  `customer_id`,
                                                  `customer_ip`, 
                                                  `customer_log_date`)
                                          VALUES (NULL,
                                                  '$customer_id',
                                                  '$customer_ip',
                                                  NOW())";
            $result = mysqli_query($db_link, $query);
            if (!$result) echo mysqli_error($db_link);

            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['customer_group_code'] = $customer_group_code;
            $_SESSION['customer_name'] = "$customer_firstname $customer_lastname";
            $_SESSION[$customer_group_code] = $user_type;
            $redirect_link = $_SESSION['redirect_link'];
            unset($_SESSION['redirect_link']);
            ?>
              <script>window.location.href="<?=$redirect_link;?>"</script>
            <?php
          }
          else {
            $_SESSION['error_login']['text'] = $languages['error_login'];
          }

          mysqli_free_result($result_user);
        } // if(mysqli_num_rows($result_user) > 0)
        else {
          $_SESSION['error_login']['text'] = $languages['error_login'];
        }
      } //if($user_is_active)
      else {
        $_SESSION['error_login']['text'] = $languages['error_profile_not_active'];
      }
    } //if($recaptcha_response) 
  }
?>
    <h3 class="text-center"> <span> <i class="fa fa-user"></i> <?=$languages['header_registered_users'];?></span></h3>

    <div class="form-wrapper row text-center">              
      <form method="post" name="login_form" id="login_form" class="form-group col-lg-4 col-md-6 col-sm-10 col-xs-12">
        <p>
          <input value="<?php if(isset($_POST['customer_email'])) echo $_POST['customer_email'];?>" autofocus name="customer_email" class="form-control" autocomplete="off" type="text" required="required" placeholder="<?=$languages['header_customer_username'];?>">
        </p>
        <?php if(isset($_SESSION['error_login']['text'])) { ?>
          <div class="alert alert-danger"><?=$_SESSION['error_login']['text'];?></div>
        <?php } ?>
        <p>
          <input name="customer_password" type="password" class="form-control" placeholder="<?=$languages['header_customer_password'];?>" required="required">
        </p>
        <div class="g-recaptcha m-b-10" data-sitekey="<?=$sitekey?>"></div>
        <?php if(isset($errors['recaptcha_response_field']) && !empty($errors['recaptcha_response_field'])) { ?>
          <div class="alert alert-danger m-b-10"><?=$errors['recaptcha_response_field'];?></div>
        <?php } ?>
        <p class="text-left">
          <a href="/<?= $current_lang; ?>/forgotten-password" class="f_pass" rel="nofollow"><?= $languages['link_forgotten_password']; ?></a>
        </p>
        <input type="submit" name="login" class="button2" value="<?=$languages['btn_login'];?>">   
      </form>
    </div>