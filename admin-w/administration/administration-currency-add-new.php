<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $return_link = "administration-currency.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $return_link");
  }
  
  if(isset($_POST['submit'])) {
    
    //echo"<pre>";print_r($_POST);
    
    $currency_title = mysqli_real_escape_string($db_link, $_POST['currency_title']);
    $currency_is_default = 0;
    if(isset($_POST['currency_is_default'])) 1;
    $currency_code = $_POST['currency_code'];
    $currency_symbol_left = $_POST['currency_symbol_left'];
    $currency_symbol_right = $_POST['currency_symbol_right'];
    $currency_decimal_place = $_POST['currency_decimal_place'];
    $currency_exchange_rate = $_POST['currency_exchange_rate'];
    $currency_is_active = 0;
    if(isset($_POST['currency_is_active'])) $currency_is_active = 1;
    
    $query_update_currency = "INSERT INTO `currencies`(`currency_id`, 
                                                      `currency_title`, 
                                                      `currency_is_default`, 
                                                      `currency_code`, 
                                                      `currency_symbol_left`, 
                                                      `currency_symbol_right`, 
                                                      `currency_decimal_place`, 
                                                      `currency_exchange_rate`, 
                                                      `currency_is_active`, 
                                                      `currency_date_modified`) 
                                              VALUES ('',
                                                      '$currency_title',
                                                      '$currency_is_default',
                                                      '$currency_code',
                                                      '$currency_symbol_left',
                                                      '$currency_symbol_right',
                                                      '$currency_decimal_place',
                                                      '$currency_exchange_rate',
                                                      '$currency_is_active', 
                                                      NOW())";
    //echo $query_update_currency;
    $result_update_currency = mysqli_query($db_link, $query_update_currency);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    header("Location: $return_link");
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages['currency_edit_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$return_link;?>" title="<?=$languages['title_breadcrumbs_currencies'];?>"><?=$languages['header_currency'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_currency_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_currency_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
          
        <div>
          <p class="title"><?=$languages['header_currency_code'];?><span class="red">*</span></p>
          <input type="text" name="currency_code" id="currency_code" value="<?php if(isset($_POST['currency_code'])) echo $_POST['currency_code'];?>" style="width: 100px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages['header_name'];?><span class="red">*</span></p>
          <input type="text" name="currency_title" id="currency_title" value="<?php if(isset($_POST['currency_title'])) echo $_POST['currency_title'];?>" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages['header_currency_exchange_rate'];?><span class="red">*</span></p>
          <input type="text" name="currency_exchange_rate" id="currency_exchange_rate" value="<?php if(isset($_POST['currency_exchange_rate'])) echo $_POST['currency_exchange_rate'];?>" maxlength="10" style="width: 100px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages['header_currency_decimal_place'];?><span class="red">*</span></p>
          <input type="text" name="currency_decimal_place" id="currency_decimal_place" value="<?php if(isset($_POST['currency_decimal_place'])) echo $_POST['currency_decimal_place'];else echo "2"?>" maxlength="10" style="width: 50px;" />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_currency_symbol_left'];?></p>
          <input type="text" name="currency_symbol_left" id="currency_symbol_left" value="<?php if(isset($_POST['currency_symbol_left'])) echo $_POST['currency_symbol_left'];?>" style="width: 50px;" />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_currency_symbol_right'];?></p>
          <input type="text" name="currency_symbol_right" id="currency_symbol_right" value="<?php if(isset($_POST['currency_symbol_right'])) echo $_POST['currency_symbol_right'];?>" style="width: 50px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages['header_is_default'];?></p>
          <?php
            if(isset($_POST['currency_is_default'])) {
              if($_POST['currency_is_default'] == 0) {
                echo '<input type="checkbox" name="currency_is_default" class="currency_is_default" />';
              }
              else {
                echo '<input type="checkbox" name="currency_is_default" class="currency_is_default" checked="checked" />';
              }
            }
            else {
              echo '<input type="checkbox" name="currency_is_default" class="currency_is_default" />';
            }
          ?>
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages['header_is_active'];?></p>
          <?php
            if(isset($_POST['currency_is_active']) && $_POST['currency_is_active'] == 0) {
              echo '<input type="checkbox" name="currency_is_active" class="currency_is_active" />';
            }
            else {
              echo '<input type="checkbox" name="currency_is_active" class="currency_is_active" checked="checked" />';
            }
          ?>
        </div>
        
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
        <?php $users_rights_edit = 1; if(isset($users_rights_edit) && $users_rights_edit == 1) { ?>
          <button type="submit" name="submit" class="button green"><i class="icon icon_save_sign"></i><?=$languages['btn_save'];?></button>
        <?php } else { ?>
          <a href="javascript:;"  onclick="alert('<?php echo $languages['no_rights_for_edit'];?>')" class="save_product_tab button red">
            <i class="icon icon_save_sign"></i><?=$languages['btn_save'];?>
          </a>
        <?php } ?>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>