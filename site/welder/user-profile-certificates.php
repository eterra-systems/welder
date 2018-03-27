<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_FILES);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  $display_path = SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/certificates/$customer_id/";
  
  $query_certificates = "SELECT `customers_welder_certificates`.* FROM `customers_welder_certificates` WHERE `customer_id` = '$customer_id'";
  //echo $query_certificates;
  $result_certificates = mysqli_query($db_link, $query_certificates);
  if(!$result_certificates) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_certificates) > 0) {
    while($certificate_row = mysqli_fetch_assoc($result_certificates)) {
      $certificates[] = $certificate_row;
    }
  }
?>
  <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">  
<?php
  if(!isset($certificates)) {
?>
  <p class="alert alert-info">Все още нямате добавени серификати</p>
  <div id="current_certificates" class="row">

  </div>
<?php
  }
  else {
?>
  <h3 class="title-style2"><?=$languages['header_user_certificates'];?></h3>
  <div id="current_certificates" class="row">
<?php
    //print_array_for_debug($certificates);

    $display_path = SITEFOLDERSL.DIRECTORY_SEPARATOR.$_SESSION['customer_group_code']."/certificates/$customer_id/";

    foreach($certificates as $certificate) {
      $certificate_name = $certificate['certificate_name'];
      $certificate_exstension = $certificate['certificate_exstension'];

      $img_ext = array("jpg", "jpeg", "png", "gif");
      $files_ext = array("pdf", "docx", "doc");

      if(in_array($certificate_exstension, $img_ext)) {
        $file = "<a href='$display_path$certificate_name' target='_blank'><img src='$display_path$certificate_name' width='auto' height='200' alt='$certificate_name'></a>";
      }
      else {
        if($certificate_exstension == "doc" || $certificate_exstension == "docx") $file_fa = "word";
        if($certificate_exstension == "pdf") $file_fa = "pdf";
        $file = "<i class='fa fa-file-$file_fa-o fa-lg'></i> <a href='$display_path$certificate_name' target='_blank' class='file'>$certificate_name</a>";
      }
?>
      <div class="certificate col-lg-3 col-md-4 col-sm-12 col-xs-12">
        <?=$file;?>
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