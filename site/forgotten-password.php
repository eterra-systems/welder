<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
 
  $success = false;
  
  if(isset($_POST['update_profile'])) {
    //echo "<pre>";print_r($_POST);
    
    $customer_email = mysqli_real_escape_string($db_link, $_POST['customer_email']);
    
    if(filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
      
      $query_customer_email = "SELECT `customer_id` FROM `customers` WHERE `customer_email` = '$customer_email'";
      //echo $query_customer_email;
      $result_customer_email = mysqli_query($db_link, $query_customer_email);
      if(!$result_customer_email) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_customer_email) > 0) {

        $row = mysqli_fetch_assoc($result_customer_email);
        $customer_id = $row['customer_id'];

        $customer_password = generate_strong_password();
        //echo $customer_password;
        $customer_hashed_password = password_hash($customer_password , PASSWORD_DEFAULT);

        mysqli_query($db_link,"BEGIN");

        $query_update_user = "UPDATE `customers` SET `customer_salted_password`='$customer_hashed_password' WHERE `customer_id` = '$customer_id'";
        //echo $query_update_user."<br>";
        $result_update_user = mysqli_query($db_link, $query_update_user);
        if(!$result_update_user) {
          echo $languages['sql_error_update']." - 2 ".mysqli_error($db_link);
        }
        else {

          $to      = $customer_email;
          $subject = $languages['forgotten_pass_subject_text'];
//          $logo_image = "http://".$_SERVER['SERVER_NAME'].SITEFOLDERSL."/images/logo.png";
//          $logo_image_params = getimagesize($logo_image);
//          $logo_image_dimensions = $logo_image_params[3];
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
          $message .= "<tr><td>&nbsp;</td></tr>";
          $message .= "<tr><td>".$languages['forgotten_pass_message_text_1']." $customer_password</td></tr>";
          $message .= "<tr><td>&nbsp;</td></tr>";
          $message .= "<tr><td>".$languages['forgotten_pass_message_text_2']."</td></tr>";
          $message .= "<tr><td>&nbsp;</td></tr>";
          $message .= "<tr><td>&nbsp;</td></tr>";
          $message .= "</table>";
          $headers = $languages['email_headers_text'];

          if(mail($to, $subject, $message, $headers,'-fidimitrov@eterrasystems.com')) {
            mysqli_commit($db_link);
            $success = true;
          }
          else {
            print_r(error_get_last());
            echo $languages['error_registration_customer_send_email_fail'];
            mysqli_query($db_link, "ROLLBACK");
          }
        }
      }
      else {
        $success = false;
        $errors['customer_email'] = $languages['error_user_profile_forgotten_password'];
      }
    } //if(filter_var($customer_email, FILTER_VALIDATE_EMAIL))
    else {
      $success = false;
      $errors['customer_email'] = $languages['error_email_is_not_valid'];
    }
  }

?>
  <form name="user_profile_settings" id="user_profile_settings" class="form-group form-horizontal" method="post" action="/<?=$_GET['page'];?>">
    
<?php
  if($success) {
?>
    <p class="alert alert-success"><?=$languages['text_user_profile_forgotten_password_success']?></p>
<?php
  }
  else {
    echo "<p></p>";
?>
    <p class="alert alert-info"><?=$languages['text_user_profile_forgotten_password']?></p>
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12<?php if(!empty($errors['customer_email'])) echo ' error_field';?>">
        <label for="customer_email"><?=$languages['header_customer_email'];?></label>
        <input type="email" name="customer_email" id="customer_email" class="form-control" value="<?php if(isset($customer_email)) echo $customer_email;?>" required="required" />
      </div>
    </div>
    <?php if(!empty($errors['customer_email'])) { ?><br><p class="alert alert-danger"><?=$errors['customer_email'];?></p><?php } ?>

    <div class="clearfix">&nbsp;</div>

    <div>
      <button type="submit" name="update_profile" class="btn btn-primary"><?=$languages['btn_generate_password'];?></button>
    </div>   
<?php
  }
?>
    <div class="clearfix"></div>
  </form>