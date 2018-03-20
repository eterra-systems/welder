  <main class="main-content">
    
    <section id="s-contact-content">

      <h4 class="title-style3"><?=$languages['header_contact_us'];?>
        <span class="title-block"></span>
      </h4>
<?php
  $contacts_array = get_contacts();
  if(!empty($contacts_array)) {
    foreach($contacts_array as $contact_row) {

      $contact_id = $contact_row['contact_id'];
      $contact_city = $contact_row['contact_city'];
      $contact_address = stripslashes($contact_row['contact_address']);
      $contact_postcode = $contact_row['contact_postcode'];
      $contact_info = stripslashes($contact_row['contact_info']);
      $contact_address .= (!empty($contact_info)) ? " ($contact_info)" : "";
      $contact_address .= ", $contact_postcode $contact_city";
      $contact_is_default = $contact_row['contact_is_default'];
?>
      <div class="contact_address">
        <p>
          <strong>Адрес:</strong> <?=$contact_address;?>
          <br>
          <strong>Телефони:</strong> 
          <span><i class="fa fa-phone"></i></span>
          <a href="tel:<?=str_replace(array("/","-"," "), array("","",""), $_SESSION['contact_home_phone']);?>"><?=$_SESSION['contact_home_phone'];?></a>, 
          <span><i class="fa fa-mobile-phone"></i></span>
          <a href="tel:<?=str_replace(array("/","-"," "), array("","",""), $_SESSION['contact_mobile_phones'][0]);?>"><?=$_SESSION['contact_mobile_phones'][0];?></a>
        </p>
      </div>
<?php
    }
  }
?>
      <p><?=$content_text;?></p>
      
      <hr class="space3" />

      <h4 class="title-style3"><?=$languages['header_email_us'];?>
        <span class="title-block"></span>
      </h4>
      
      <p class="alert alert-success hidden"><?= $languages['text_inquiry_was_sended_successfully']; ?><span id="appointment_date"></span></p>
      <form id="emailform" action="<?=SITEFOLDERSL;?>/inquiry.php" method="post" class="appointment-form form-group">
        
        <div class="row">
          <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <label for="fullname"><?=$languages['header_fullname'];?> <span>*</span></label>
            <input name="fullname" id="fullname" type="text" required="required" class="form-control required_field">
            <div class="alert alert-danger error hidden"><?= $languages['error_required_field']; ?></div>
          </div>
          <p class="clearfix"></p>

          <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <label for="phone"><?=$languages['header_phone'];?> <span>*</span></label>
            <input name="phone" id="phone" type="text" required="required" class="form-control required_field">
            <div class="alert alert-danger error hidden"><?= $languages['error_required_field']; ?></div>
          </div>
          <p class="clearfix"></p>

          <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <label for="email"><?=$languages['header_email'];?> <span>*</span></label>
            <input name="email" id="email" type="email" required="required" class="form-control required_field email">
            <div class="alert alert-danger invalid_email hidden"><?= $languages['error_email_is_not_valid']; ?></div>
          </div>
          <p class="clearfix"></p>

          <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <label for="subject"><?=$languages['header_subject'];?></label>
            <input type="text" name="subject" id="subject" class="form-control">
          </div>
          <p class="clearfix"></p>

          <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <label for="textarea"><?=$languages['header_inquiry'];?></label>
            <textarea name="message" id="textarea" required="required" rows="4" class="form-control required_field form-textarea"></textarea>
            <div class="alert alert-danger error hidden"><?= $languages['error_required_field']; ?></div>
          </div>
          <p class="clearfix"></p>

          <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
            <div id="g-recaptcha" data-sitekey="<?=$sitekey;?>"></div>
            <div class="alert alert-danger error hidden recaptcha_error"><?=$languages['error_create_customer_recaptcha'];?></div>
          </div>
          <p class="clearfix"></p>
        </div>
        
        <input type="hidden" name="current_lang" value="<?=$current_lang;?>" >
        <input class="btn btn-primary" type="submit" value="<?=$languages['btn_submit_inquiry'];?>" id="submit" name="submit_inquiery">
        
      </form>
      
   </section>
  </main>

  <aside class="sidebar-content">

    <section class="widget hidden clearfix">

      <h4 class="title-style3"><?=$languages['header_contact_us'];?>
        <span class="title-block"></span>
      </h4>
      
      <ul class="contact-widget">
        <li class="phone-icon">
          <strong><?=$languages['header_phone'];?>:</strong> 
          <a href="tel:<?=str_replace(array("/","-"," "), array("","",""), $_SESSION['contact_mobile_phones'][0]);?>"><?=$_SESSION['contact_mobile_phones'][0];?></a>
        </li>
        <li class="fax-icon">
          <strong><?=$languages['header_fax'];?>:</strong> 
          <a href="tel:<?=str_replace(array("/","-"," "), array("","",""), $_SESSION['contact_home_phone']);?>"><?=$_SESSION['contact_home_phone'];?></a>
        </li>
        <li class="email-icon">
          <strong><?=$languages['header_email'];?>:</strong> 
          <a href="mailto:<?=$_SESSION['contact_email'];?>"><?=$_SESSION['contact_email'];?></a>
        </li>
        <li class="address-icon">
          <strong><?=$languages['header_address'];?>:</strong> 
          <?=$_SESSION['contact_address'].", ".$_SESSION['contact_postcode']." ".$_SESSION['contact_city'];?>
        </li>
      </ul>
      
    </section>
    
    <section class="widget clearfix">
      <h4 class="title-style3"><?= $languages['header_latest_news']; ?>
        <span class="title-block"></span>
      </h4>
      <ul class="latest-posts-list clearfix">
        <?php list_latest_news_for_category($news_category_id = 1, $news_count = 5) ?>
      </ul>
    </section>
  
  </aside>

  <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
  <script type="text/javascript">
    var onloadCallback = function() {
      grecaptcha.render('g-recaptcha', {'sitekey' : '<?=$sitekey;?>'});
    };
  </script>