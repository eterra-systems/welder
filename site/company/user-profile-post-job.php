<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  //print_array_for_debug($_SESSION);
  $customer_id = $_SESSION['customer_id'];
  $customer_fullname = $_SESSION['customer_name'];
  
  //print_array_for_debug($_POST);
  
  //if(false) {
  if(isset($_POST['post_job'])) {
    
    $job_title = $_POST['job_title'];
    $job_summary = $_POST['job_summary'];
    $job_description = $_POST['job_description'];
    
    if(isset($_POST['categories'])) {
      $categories = $_POST['categories'];
      $category_ids_tree = $categories;
    }
    else $errors['categories'] = $languages['error_choosen_category'];
    
    if(empty($errors)) {
      
      $q_insert_job = "INSERT INTO `customers_company_jobs`(`job_id`, 
                                                            `country_id`, 
                                                            `site_id`, 
                                                            `job_title`, 
                                                            `job_summary`, 
                                                            `job_description`) 
                                                    VALUES ('$job_id',
                                                            '$country_id',
                                                            '$site_id',
                                                            '$job_title',
                                                            '$job_summary',
                                                            '$job_description')";
      $all_queries .= "<br>".$q_insert_job;
      $result_insert_job = mysqli_query($db_link, $q_insert_job);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages['sql_error_insert']." - 1 `customers_to_categories` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $job_id = mysqli_insert_id($db_link);
        
      foreach($categories as $key) {
        
        $category_hierarchy_ids = $_POST['category_hierarchy_ids'][$key];
        
        $q_insert_ctc = "INSERT INTO `jobs_to_categories`(`job_id`, `category_hierarchy_ids`) VALUES ('$job_id','$category_hierarchy_ids')";
        $all_queries .= "<br>".$q_insert_ctc;
        $result_insert_ctc = mysqli_query($db_link, $q_insert_ctc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages['sql_error_insert']." - 3 `customers_to_categories` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      $success = true;
    }
  }
  
  $bg_form_style = 'style="display:none"';
  $not_bg_form_style = 'style="display:none"';

  //if(isset($_POST['customer_address_country_id'])) echo $_POST['customer_address_country_id']."<br>";
  if(isset($_POST['country_id'])) {
    if($_POST['country_id'] == 33) {
      $bg_form_style = "";
    }
    else {
      $not_bg_form_style = "";
    }
  }
  else {
    $bg_form_style = "";
  }
?>
  <form name="company_post_job" id="company_post_job" class="form-group" method="post" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<?php
    if(isset($success)) {
?>
    <p class="alert alert-success mb-15"><?=$languages['text_update_was_successfull'];?></p>
<?php
    }
    if(!empty($errors)) {

      //foreach($errors as $error) echo "<div class='warning_field'>$error</div>";
    }
?>
    <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
    
    <div class="row gap-20">
      
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label for="job_title"><?=$languages['header_job_title'];?><span class="text-danger">*</span></label>
        <input type="text" name="job_title" class="form-control" value="<?php if(isset($job_title)) echo $job_title;?>">
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
              if(isset($_POST['country_id'])) {
                $selected = ($country_id == $_POST['country_id']) ? 'selected="selected"' : "";
              }
              else {
                $selected = ($country_id == 33) ? 'selected="selected"' : ""; //$country_id_db
              }

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
          <input type="text" name="customer_site_type" class="pull-left form-control" id="customer_site_type" disabled="disabled" value="<?php if(isset($customer_site_type)) echo $customer_site_type;else echo $languages['header_customer_address_site_type'];?>" style="width: 25%; margin-right: 1%;padding: 8px 10px" />
          <input type="text" name="customer_site_name_label" id="customer_site_name_label" class="form-control" autocomplete="off" value="<?php if(isset($customer_site_name)) echo $customer_site_name;?>" style="width: 74%;" />
          <?php if(!empty($errors['customer_site_name'])) { ?><div class="alert alert-danger"><?=$errors['customer_site_name'];?></div><?php } ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0 0 0 1% !important">
          <label for="customer_postcode"><?=$languages['header_customer_address_postcode'];?></label>
          <input type="text" name="customer_site_postcode_label" class="form-control" disabled="disabled" id="customer_site_postcode_label" value="<?php if(isset($customer_site_postcode)) echo $customer_site_postcode;?>" />
          <input type="hidden" name="customer_site_id" id="customer_site_id" value="<?php if(isset($customer_site_id)) echo $customer_site_id;?>" />
          <input type="hidden" name="customer_site_name" id="customer_site_name" value="<?php if(isset($customer_site_name)) echo $customer_site_name;?>" />
          <input type="hidden" name="customer_site_postcode" id="customer_site_postcode" value="<?php if(isset($customer_site_postcode)) echo $customer_site_postcode;?>" />
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div id="not_bg_form" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" <?=$not_bg_form_style;?>>
        <label for="customer_site_name_not_bg"><?=$languages['header_customer_address_site_name'];?><span class="text-danger">*</span></label>
        <input type="text" name="customer_site_name_not_bg" id="customer_site_name_not_bg" class="form-control" value="<?php if(isset($customer_site_name_not_bg)) echo $customer_site_name_not_bg;?>" />
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group bootstrap3-wysihtml5-wrapper">
          <label for="job_summary"><?=$languages['header_job_summary'];?><span class="text-danger">*</span></label>
          <textarea name="job_summary" id="job_summary" class="bootstrap3-wysihtml5 form-control" style="height: 200px;"><?php if(isset($job_summary)) echo $job_summary;?></textarea>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
      
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="form-group bootstrap3-wysihtml5-wrapper">
          <label for="job_description"><?=$languages['header_job_description'];?><span class="text-danger">*</span></label>
          <textarea name="job_description" id="job_description" class="bootstrap3-wysihtml5 form-control" style="height: 200px;"><?php if(isset($job_description)) echo $job_description;?></textarea>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
      
    </div>
    <p class="clearfix">&nbsp;</p>

    <?php if(!empty($errors['categories'])) { ?><span class="alert alert-danger"><?=$errors['categories'];?></span><?php } ?>
    <label for="job_description"><?=$languages['header_categories'];?><span class="text-danger">*</span></label>
    <div class="tree">
      <ul class="recent-job-wrapper">
        <?php list_categories_with_checkboxes($category_parent_id = 0, $category_root_id = 0, $category_ids_tree = array()) ;?>
        <li class="level_1 clearfix"></li>
      </ul>
    </div>
    <p class="clearfix">&nbsp;</p>

    <?php print_categories_info(); ?>

    <div class="clearfix">&nbsp;</div>

    <button type="submit" name="post_job" class="btn btn-primary"><?=$languages['btn_save'];?></button>

    <div class="clearfix">&nbsp;</div>
   
  </form>
  <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/bootstrap3-wysihtml5.min.js"></script>
  <script>
    $(function() {

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