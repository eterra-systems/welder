<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_FILES);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  $display_path = SITEFOLDERSL."/welder/certificates/$customer_id/";
  
  $query_certificates = "SELECT `customers_welder_certificates`.* FROM `customers_welder_certificates` WHERE `customer_id` = '$customer_id'";
  //echo $query_certificates;
  $result_certificates = mysqli_query($db_link, $query_certificates);
  if(!$result_certificates) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_certificates) > 0) {
    while($certificate_row = mysqli_fetch_assoc($result_certificates)) {
      $certificates[] = $certificate_row;
    }
  }

  if(!empty($errors)) {

    //foreach($errors as $error) echo "<div class='warning_field'>$error</div>";
  }
?>
  <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
  <div class="row">
      
<?php
    if(!isset($certificates)) {
?>
    <p class="alert alert-info">Все още нямате добавени серификати</p>
<?php
    }
    else {
?>
    <h3 class="title-style2"><?=$languages['header_user_certificates'];?></h3>
    <div id="current_certificates">
<?php
      //print_array_for_debug($certificates);
      foreach($certificates as $certificate) {
        $certificate_name = $certificate['certificate_name'];
        $img = "<img src='$display_path$certificate_name' width='auto' height='200' alt='$certificate_name'>";
?>
        <div class="certificate col-lg-3 col-md-4 col-sm-12 col-xs-12">
          <?=$img;?>
        </div>
<?php
      }
?>
    </div>
<?php
    }
?>
    <p class="clearfix">&nbsp;</p>
    <div id="certificates" class="m-b-20">
      <h3 class="title-style2"><?=$languages['header_add_certificates'];?></h3>
      <form action="<?=SITEFOLDERSL;?>/<?=$_SESSION['customer_group_code'];?>/ajax/upload-file.php" id="filedrop" class="dropzone">
        <div id="dropzone">
          <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_tab_success" value="<?=$languages['ajaxmessage_update_tab_success'];?>" >
          <input type="hidden" name="current_lang" id="current_lang" value="<?=$current_lang;?>" >
          <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>" >
          <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages['text_drag_and_drop_upload'];?>" >
        </div>
      </form>
      <div class="clearfix"></div>
    </div>
  </div>
  <div class="clearfix">&nbsp;</div>

  <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/dropzone.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      Dropzone.options.filedrop = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            this.removeFile(file);
          });
          this.on("success", function(file, responseImage) {
            if(responseImage == "" || responseImage == " ") {
              
            }
            else {
              $("#current_certificates").append(responseImage);
              //alert(responseImage);
              //this.removeFile(file);
            }
          });
        }
      };
    });
  </script>