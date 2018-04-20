<?php
  
  //print_array_for_debug($_SESSION);
  $ad_id = $_POST['ad_id'];
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  //print_array_for_debug($_POST);
  
  $query_ad = "SELECT `cca`.* FROM `customers_company_ads` as `cca` WHERE `cca`.`ad_id` = '$ad_id'";
  //echo $query_ad;exit;
  $result_ad = mysqli_query($db_link, $query_ad);
  if (!$result_ad) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_ad)) {

    $ad_row = mysqli_fetch_assoc($result_ad);

    $ad_title = $ad_row['ad_title'];
    $current_country_id = $ad_row['country_id'];
    $site_id = $ad_row['site_id'];
    $site_name = $ad_row['site_name'];
    $site_name_not_bg = $site_name;
    $ad_summary = $ad_row['ad_summary'];
    $ad_description = $ad_row['ad_description'];
    $ad_start_date = $ad_row['ad_start_date'];
  }
    
  //if(false) {
  if(isset($_POST['post_ad'])) {
    
    $ad_title = $_POST['ad_title'];
        if(empty($ad_title)) $errors['ad_title'] = $languages['error_required_field'];
    $current_country_id = $_POST['country_id'];
    $site_id = $_POST['site_id'];
    $site_name = $_POST['site_name'];
    $site_name_not_bg = $_POST['site_name_not_bg'];
    $site_postcode = $_POST['site_postcode'];
    $ad_summary = $_POST['ad_summary'];
        if(empty($ad_title)) $errors['ad_summary'] = $languages['error_required_field'];
    $ad_description = $_POST['ad_description'];
        if(empty($ad_title)) $errors['ad_description'] = $languages['error_required_field'];
    $ad_start_date = $_POST['ad_start_date'];
    
    if(isset($_POST['categories'])) {
      $categories = $_POST['categories'];
      $category_ids_tree = $categories;
    }
    else $errors['categories'] = $languages['error_choosen_category'];
    
    if(empty($errors)) {
      
      mysqli_query($db_link,"BEGIN");
      $all_queries = "";
      
      $ad_views = 0;
      $ad_is_active = 1;
      $ad_start_date_db = prepare_for_null_row($ad_start_date);
      
      $q_insert_ad = "INSERT INTO `customers_company_ads`(`ad_id`, 
                                                          `customer_id`, 
                                                          `country_id`, 
                                                          `site_id`, 
                                                          `site_name`, 
                                                          `ad_title`, 
                                                          `ad_summary`, 
                                                          `ad_description`, 
                                                          `ad_start_date`,
                                                          `ad_views`,
                                                          `ad_is_active`,
                                                          `ad_publish_date`) 
                                                  VALUES (NULL,
                                                          '$customer_id',
                                                          '$current_country_id',
                                                          '$site_id',
                                                          '$site_name_not_bg',
                                                          '$ad_title',
                                                          '$ad_summary',
                                                          '$ad_description',
                                                          $ad_start_date_db,
                                                          '$ad_views',
                                                          '$ad_is_active',
                                                          NOW())";
      $all_queries .= "<br>".$q_insert_ad;
      $result_insert_ad = mysqli_query($db_link, $q_insert_ad);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 1 `customers_to_categories` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $ad_id = mysqli_insert_id($db_link);
        
      foreach($categories as $key) {
        
        $category_hierarchy_ids = $_POST['category_hierarchy_ids'][$key];
        
        $q_insert_ctc = "INSERT INTO `ads_to_categories`(`ad_id`, `category_hierarchy_ids`) VALUES ('$ad_id','$category_hierarchy_ids')";
        $all_queries .= "<br>".$q_insert_ctc;
        $result_insert_ctc = mysqli_query($db_link, $q_insert_ctc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 3 `customers_to_categories` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
      
      mysqli_commit($db_link);
      
      ?><script>window.location="<?="/$current_lang/company/user-profile-ads-list";?>"</script><?php
    }
  }
  
  $bg_form_style = 'style="display:none"';
  $not_bg_form_style = 'style="display:none"';

  //if(isset($_POST['customer_address_country_id'])) echo $_POST['customer_address_country_id']."<br>";
  if($current_country_id == 33) {
    $bg_form_style = "";
  }
  else {
    $not_bg_form_style = "";
  }
?>
  <form name="company_post_ad" id="company_post_ad" class="form-group" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
    <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
    <input type="hidden" name="ad_id" value="<?=$ad_id;?>">
    
    <div class="row gap-20">
      
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="ad_title"><?=$languages['header_ad_title'];?><span class="text-danger">*</span></label>
        <input type="text" name="ad_title" class="form-control" value="<?php if(isset($ad_title)) echo $ad_title;?>">
        <?php if(!empty($errors['ad_title'])) { ?><div class="alert alert-danger"><?=$errors['ad_title'];?></div><?php } ?>
      </div>
      <p class="clearfix"></p>
      
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <label for="country_id"><?=$languages['header_customer_address_country'];?><span class="text-danger">*</span></label>
        <select name="country_id" id="country_id" class="form-control" onChange="DisplayCountryAddressForm(this.value)">
        <?php
          $country_list = "WHERE `country_id` IN(14,21,33,53,55,56,57)";
          $query_countries = "SELECT `country_id`,`country_name` FROM  `countries` ORDER BY `country_name` ASC ";
          //echo $query_countries;
          $result_countries = mysqli_query($db_link, $query_countries);
          if (!$result_countries) echo mysqli_error($db_link);
          if(mysqli_num_rows($result_countries) > 0) {

            while ($country = mysqli_fetch_assoc($result_countries)) {

              $country_id = $country['country_id'];
              $country_name = stripslashes($country['country_name']);
              $selected = ($current_country_id == $country_id) ? 'selected="selected"' : ""; //$country_id_db

              echo "<option value='$country_id' $selected>$country_name</option>";

            }
            mysqli_free_result($result_countries);
          }
        ?> 
        </select>
      </div>
      <p class="clearfix"></p>
      
      <div id="bg_form" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" <?=$bg_form_style;?>>
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding: 0 !important">
          <label for="customer_city" style="display: block"><?=$languages['header_customer_address_site_name'];?><span class="text-danger">*</span></label>
          <input type="text" name="site_type" class="pull-left form-control" id="site_type" disabled="disabled" value="<?php if(isset($site_type)) echo $site_type;else echo $languages['header_customer_address_site_type'];?>" style="width: 25%; margin-right: 1%;padding: 8px 10px" />
          <input type="text" name="site_name_label" id="site_name_label" class="form-control" autocomplete="off" value="<?php if(isset($site_name)) echo $site_name;?>" style="width: 74%;" />
          <?php if(!empty($errors['site_name'])) { ?><div class="alert alert-danger"><?=$errors['site_name'];?></div><?php } ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0 0 0 1% !important">
          <label for="customer_postcode"><?=$languages['header_customer_address_postcode'];?></label>
          <input type="text" name="site_postcode_label" class="form-control" disabled="disabled" id="site_postcode_label" value="<?php if(isset($site_postcode)) echo $site_postcode;?>" />
          <input type="hidden" name="site_id" id="site_id" value="<?php if(isset($site_id)) echo $site_id;?>" />
          <input type="hidden" name="site_name" id="site_name" value="<?php if(isset($site_name)) echo $site_name;?>" />
          <input type="hidden" name="site_postcode" id="site_postcode" value="<?php if(isset($site_postcode)) echo $site_postcode;?>" />
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div id="not_bg_form" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" <?=$not_bg_form_style;?>>
        <label for="site_name_not_bg"><?=$languages['header_customer_address_site_name'];?><span class="text-danger">*</span></label>
        <input type="text" name="site_name_not_bg" id="site_name_not_bg" class="form-control" value="<?php if(isset($site_name_not_bg)) echo $site_name_not_bg;?>" />
        <?php if(!empty($errors['site_name_not_bg'])) { ?><div class="alert alert-danger"><?=$errors['site_name_not_bg'];?></div><?php } ?>
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="bootstrap3-wysihtml5-wrapper">
          <label for="ad_summary"><?=$languages['header_ad_summary'];?></label>
          <textarea name="ad_summary" id="ad_summary" class="bootstrap3-wysihtml5 form-control" style="height: 200px;"><?php if(isset($ad_summary)) echo $ad_summary;?></textarea>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="bootstrap3-wysihtml5-wrapper">
          <label for="ad_description"><?=$languages['header_ad_description'];?><span class="text-danger">*</span></label>
          <textarea name="ad_description" id="ad_description" class="bootstrap3-wysihtml5 form-control" style="height: 200px;"><?php if(isset($ad_description)) echo $ad_description;?></textarea>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <label for="ad_start_date"><?=$languages['header_ad_start_date'];?></label>
        <input type="text" name="ad_start_date" id="ad_start_date" class="form-control datepicker" value="<?php if(isset($ad_start_date)) echo $ad_start_date;?>" />
      </div>
      <div class="clearfix">&nbsp;</div>
      
    </div>
    <p class="clearfix">&nbsp;</p>

    <?php if(!empty($errors['categories'])) { ?><div class="alert alert-danger"><?=$errors['categories'];?></div><?php } ?>
    <label for="ad_description"><?=$languages['header_categories'];?><span class="text-danger">*</span></label>
    <div class="tree">
      <ul class="recent-job-wrapper">
        <?php list_categories_with_checkboxes($category_parent_id = 0, $category_root_id = 0, $category_ids_tree = array()) ;?>
        <li class="level_1 clearfix"></li>
      </ul>
    </div>
    <p class="clearfix">&nbsp;</p>

    <?php print_categories_info(); ?>

    <div class="clearfix">&nbsp;</div>

    <button type="submit" name="post_ad" class="btn btn-primary"><?=$languages['btn_save'];?></button>

    <div class="clearfix">&nbsp;</div>
   
  </form>
  <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap3-wysihtml5.min.js"></script>
  <script>
    $(function() {

      $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
      
      //autocomplete sites
      $("#site_name_label").autocomplete({
        source: "<?=SITEFOLDERSL;?>/ajax/get-sites-autocomplete.php",
        minLength: 2,
        select: function( event, ui ) {
          //alert(ui.item.site_name);
          $('#site_id').val(ui.item.site_id);
          $('#site_type').val(ui.item.site_type);
          $('#site_name').val(ui.item.site_name);
          $('#site_postcode').val(ui.item.site_postcode);
        },
        close: function( event, ui ) {
          $('#site_postcode_label').val($('#site_postcode').val());
          $('#site_name_label').val($('#site_name').val());
        }
      });

      //start family tree
      $('.select_all').on('click', function (e) {
        var state = true;
        var root = $(this).attr("data-root");
        if($(this).hasClass("active")) {
          $(this).removeClass("active")
          state = false;
        }
        else {
          $(this).addClass("active")
          state = true;
        }
        var checkboxes = document.getElementsByClassName("categories_"+root);
        for(var i=0; i<checkboxes.length ; i++) {
          if(checkboxes[i].type == "checkbox") {
            checkboxes[i].checked = state;
          }
        }
      });
      $('.tree li.expandable label').on('click', function (e) {
          var current_tree_parent = $(this).parent('.expandable');
          var current_tree_id = current_tree_parent.attr('id');
          var child_ul = $(this).parent('.expandable').find(".expandable_ul_"+current_tree_id);
          if(child_ul.is(":visible")) {
            child_ul.hide('fast');
            current_tree_parent.removeClass("active_parent_tree");
          }
          else {
            child_ul.show('fast');
            current_tree_parent.addClass("active_parent_tree");
          }
          e.stopPropagation();
      });
      $('.tree input[type="checkbox"]').on('click', function (e) {
          CalculateSelectedSubcategories();
      });
      //end family tree

    });
  </script>