<?php
  //echo"<pre>";print_r($_SERVER);exit;

//  unset($_SESSION);
//  session_destroy();
//  echo"<pre>Session<br>";print_r($_SESSION);
//  echo"<pre>Post<br>";print_r($_POST);
?>
    <div class="modal-header">
      <h4 class="modal-title text-center"><?=$languages['header_login'];?></h4>
    </div>

    <form method="post" name="login_form" id="login_form">
      
      <div class="modal-body">
        <div class="row gap-20">

          <div class="col-md-12 mb-5">
            <button class="btn btn-facebook btn-block">Log-in with Facebook</button>
          </div>
          <div class="col-md-12">
            <button class="btn btn-google-plus btn-block">Log-in with Google+</button>
          </div>

          <div class="col-md-12">
            <div class="login-modal-or">
              <div><span><?=$languages['text_or'];?></span></div>
            </div>
          </div>

          <?php if(isset($_SESSION['error_login']['text'])) { ?>
          <div class="col-md-12">
            <div class="alert alert-danger"><?=$_SESSION['error_login']['text'];?></div>
          </div>
          <?php } ?>

          <div class="col-sm-12 col-md-12">

            <div class="form-group"> 
              <label><?=$languages['header_customer_username'];?></label>
              <input autofocus name="customer_email" class="form-control" autocomplete="off" type="text" required="required"> 
            </div>

          </div>

          <div class="col-sm-12 col-md-12">

            <div class="form-group"> 
              <label><?=$languages['header_customer_password'];?></label>
              <input class="form-control" name="customer_password" type="password" required="required"> 
            </div>

          </div>

          <div class="col-sm-12 col-md-12">

            <div class="g-recaptcha form-group" data-sitekey="<?=$sitekey?>"></div>
            <?php if(isset($errors['recaptcha_response_field']) && !empty($errors['recaptcha_response_field'])) { ?>
              <div class="alert alert-danger"><?=$errors['recaptcha_response_field'];?></div>
            <?php } ?>

          </div>

          <div class="col-sm-6 col-md-6 hidden">
            <div class="checkbox-block"> 
              <input id="remember_me_checkbox" name="remember_me" class="checkbox" type="checkbox"> 
              <label for="remember_me_checkbox"><?= $languages['text_remember_me']; ?></label>
            </div>
          </div>

          <div class="col-sm-6 col-md-6">
            <!--<div class="login-box-link-action">-->
            <div>
              <a href="/<?= $current_lang; ?>/forgotten-password" rel="nofollow"><?= $languages['link_forgotten_password']; ?></a>
            </div>
          </div>

          <div class="col-sm-12 col-md-12">
            <div class="login-box-box-action">
              <?= $languages['text_no_account']; ?> <a href="/<?= $current_lang; ?>/registration" rel="nofollow"><?= $languages['login_sign_up']; ?></a>
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer text-center">
        <input type="submit" name="login" class="btn btn-primary" value="<?=$languages['btn_login'];?>">
      </div>
    </form>