<?php
//  session_destroy();
//  echo"<pre>";print_r($_SESSION);print_r($_SERVER);
$sitekey = "6Le3IEYUAAAAAPssLvABf4DmEfxX5RLwb04bIRHw";
$secretkey = "6Le3IEYUAAAAAE47DgddVeqGkccpLkbh-7fnFYs9";
if(isset($_SERVER['HTTP_REFERER'])) {
  if(!isset($_POST['login'])) $_SESSION['redirect_link'] = $_SERVER['HTTP_REFERER'];
}
else {
  $_SESSION['redirect_link'] = $_SERVER['PHP_SELF'];
}

$form_is_submitted = false;
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
    $errors['recaptcha_response_field'] = "<h2 class='red'>".$languages['error_recaptcha']."</h2>";  
  }
}

if(!isset($_SESSION['login_error']['count'])){
  $_SESSION['login_error'] = array();
  $_SESSION['login_error']['count'] = 0;
}

if(isset($_POST['login'])) {
  
  $form_is_submitted = true;

  if($recaptcha_response) {
//  if(true){
    //unset($_SESSION['captcha_error']);
    $password = $_POST['password'];
    $user_username = $_POST['user_username'];
    $post_user_ip = $_SERVER['REMOTE_ADDR'];

    $query_user = "SELECT `user_id`,`user_type_id`,`user_is_ip_in_use`,`user_ip` FROM `users` WHERE `user_username` = '$user_username'";
    //$_SESSION['query'] = $query_user."<br>";
    $result_user = mysqli_query($db_link,$query_user);
    if (!$result_user) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_user) > 0) {
      $user = mysqli_fetch_assoc($result_user);

      $db_user_id = $user['user_id'];
      $user_is_ip_in_use = $user['user_is_ip_in_use'];
      $user_ip_in_database = $user['user_ip'];
      //$user_remote_ip = ($user_is_ip_in_use == 1) ? (!empty($user_ip_in_database)) ? $post_user_ip : "" : "";
      $user_remote_ip = "";

      $query_user = "SELECT `users`.*,`users_types`.`user_type_is_superuser`
                       FROM `users`
                 INNER JOIN `users_types` ON `users_types`.`user_type_id` = `users`.`user_type_id`
                      WHERE `users`.`user_id` = '$db_user_id' AND `users`.`user_is_active` = '1'". (!empty($user_remote_ip) ? " 
                        AND `users`.`user_ip` = '$user_remote_ip'" : NULL);
      //$_SESSION['query'] .= $query_user."<br>";
      //echo $query_user;exit;
      $result_user = mysqli_query($db_link, $query_user);
      if(!$result_user) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_user) > 0) {
        $user_details = mysqli_fetch_assoc($result_user);

        $password_hash = $user_details['user_salted_password'];

        if(password_verify($password, $password_hash)) {
          // password is correct

          //update ip if empty START
          if (empty($user_ip_in_database)) {
              $query_update_ip = "UPDATE `users` SET `user_ip`='$post_user_ip' WHERE `user_id` = '$db_user_id'";
              //$_SESSION['$query'] = $query_update_ip;
              mysqli_query($db_link,$query_update_ip);
          }

          //make record for table users_log
          $query = "INSERT INTO `users_logs`(`user_log_id`, 
                                              `user_id`, 
                                              `user_ip`, 
                                              `user_location_city`, 
                                              `user_location_latitude`, 
                                              `user_location_longitude`, 
                                              `user_log_date`)
                                      VALUES (NULL,
                                              '$db_user_id',
                                              '$post_user_ip',
                                              NULL,
                                              NULL,
                                              NULL,
                                              NOW())";
          $result = mysqli_query($db_link, $query);
          if (!$result) echo mysqli_error($db_link);

          $user_username = $user_details['user_username'];
          $contact_first_name = $user_details['user_firstname'];
          $contact_last_name = $user_details['user_lastname'];
          $user_type_id = $user_details['user_type_id'];
          $user_type_is_superuser = $user_details['user_type_is_superuser'];

          $_SESSION['admin']['user_id'] = $db_user_id;
          $_SESSION['admin']['user_type_id'] = $user_type_id;
          $_SESSION['admin']['user_type_is_superuser'] = $user_type_is_superuser;
          $_SESSION['admin']['user_username'] = $user_username;
          $_SESSION['admin']['user_fullname'] = (empty($contact_last_name) ? "$contact_first_name" : "$contact_first_name $contact_last_name");
          unset($_SESSION['login_error']);
          ?>
            <script>window.location.href="<?=$_SESSION['redirect_link'];?>"</script>
          <?php
        }
        else {
          $_SESSION['login_error']['count']++;
          $_SESSION['login_error']['text'] = "<h2 class='red'>Username and password mismatch</h2>";
        }

      } // if(mysqli_num_rows($result_user)
    } // if(mysqli_num_rows($result_user) > 0)
    else {
      $_SESSION['login_error']['count'] ++;
      $_SESSION['login_error']['text'] = "<h2 class='red'>Username and password mismatch</h2>";
    }
  }
}// if(isset($_POST['login']))
//echo $_SESSION['query'];
?>
<main id="login">
  <h1><?=$languages['header_login_page'];?></h1>
  <section>
<?php if(isset($errors['recaptcha_response_field'])) echo $errors['recaptcha_response_field'];?>
<?php if(isset($_SESSION['login_error']['text'])) echo $_SESSION['login_error']['text'];?>
<?php if(isset($_SESSION['login_error']['email'])) echo $_SESSION['login_error']['email'];?>
    <form name="loginform" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" id="loginform">
      <table>
        <tr>
          <td <?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']['count'] > 0) echo 'class="error"';?>>
            <label for="user_username"><?=$languages['text_username'];?>:</label>
            <input name="user_username" autofocus type="text" id="user_username" class="input_text">
          </td>
        </tr>
        <tr>
          <td <?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']['count'] > 0) echo 'class="error"';?>>
            <label for="password"><?=$languages['text_password'];?>:</label>
            <input name="password" type="password" id="password" class="input_text">
          </td>
        </tr>
        <tr>
          <td <?php if($form_is_submitted && !$recaptcha_response) echo 'class="error"';?>>
            <div id="recaptcha_dark" class="g-recaptcha" data-sitekey="<?=$sitekey;?>"></div>
          </td>
        </tr>
        <tr>
          <td>
            <button type="submit" name="login" class="button blue"><?=$languages['btn_login'];?></button>
          </td>
        </tr>
      </table>
    </form>
  </section>
  <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
  <script type="text/javascript">
    var onloadCallback = function() {
      grecaptcha.render('recaptcha_dark', {
        'sitekey' : '<?=$sitekey;?>',
        'theme' : 'dark'
      });
    };
  </script>
</main>