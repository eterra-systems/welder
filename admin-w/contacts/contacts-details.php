<?php
  $back_link = "contacts.php";
  
  if(isset($_POST['cancel'])) {
    header("Location: $back_link");
  }
  
  $languages_array = get_languages();
  
  if(isset($_POST['update_contact'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['contact_addresses'] as $language_id => $contact_address) {
      if(empty($contact_address)) $contact_errors['contact_address'][$language_id] = $languages['required_field_error'];
      if(empty($_POST['contact_cities'][$language_id])) $contact_errors['contact_citiy'][$language_id] = $languages['required_field_error'];
      if(empty($_POST['contact_postcodes'][$language_id])) $contact_errors['contact_postcode'][$language_id] = $languages['required_field_error'];
      
      $contact_addressеs_array[$language_id] = $_POST['contact_addresses'][$language_id];
      $contact_cities_array[$language_id] = $_POST['contact_cities'][$language_id];
      $contact_postcodes_array[$language_id] = $_POST['contact_postcodes'][$language_id];
      $contact_infos_array[$language_id] = $_POST['contact_infos'][$language_id];
      $contact_has_record_array[$language_id] = $_POST['contact_has_record_in_db'][$language_id];
    }
    
    $contact_is_active = 0;
      if(isset($_POST['contact_is_active'])) $contact_is_active = 1;
    $contact_is_default = 0;
      if(isset($_POST['contact_is_default'])) $contact_is_default = 1;
    if(isset($_POST['contact_map_lat'])) $contact_map_lat = $_POST['contact_map_lat'];
    if(isset($_POST['contact_map_lng'])) $contact_map_lng = $_POST['contact_map_lng'];
    if(isset($_POST['contact_phones'])) $contact_phones_array = $_POST['contact_phones'];
    if(isset($_POST['contact_email'])) $contact_email = $_POST['contact_email'];
    
    if(!isset($contact_errors)) {
      //if there are no form errors we can insert the information
      
      $contact_email_db = prepare_for_null_row($contact_email);
      
      $query_update_contact = "UPDATE `contacts` SET `contact_map_lat` = '$contact_map_lat',
                                                      `contact_map_lng` = '$contact_map_lng',
                                                      `contact_email` = $contact_email_db,
                                                      `contact_is_active`='$contact_is_active',
                                                      `contact_is_default`='$contact_is_default'
                                                WHERE `contact_id` = '$current_contact_id'";
      $all_queries .= "<br>".$query_update_contact;
      $result_update_contact = mysqli_query($db_link, $query_update_contact);
      if(!$result_update_contact) {
        echo $languages['sql_error_update']." - 1 `contacts` ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($contact_addressеs_array as $language_id => $contact_address) {
        
        $contact_has_record = $contact_has_record_array[$language_id];
        $contact_city_db = $contact_cities_array[$language_id];
        $contact_postcode_db = $contact_postcodes_array[$language_id];
        $contact_address_db = mysqli_real_escape_string($db_link, $contact_address);
        $contact_info_db = prepare_for_null_row($contact_infos_array[$language_id]);
      
        if($contact_has_record == 1) {
          $query_update_contact_desc = "UPDATE `contacts_descriptions` 
                                          SET `contact_city`='$contact_city_db',
                                              `contact_postcode`='$contact_postcode_db',
                                              `contact_address`='$contact_address_db',
                                              `contact_info`=$contact_info_db
                                        WHERE `contact_id` = '$current_contact_id' AND `language_id` = '$language_id'";
          $all_queries .= "<br>".$query_update_contact_desc;
          $result_update_contact_desc = mysqli_query($db_link, $query_update_contact_desc);
          if(!$result_update_contact_desc) {
            echo $languages['sql_error_update']." - 2 `contacts_descriptions`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_insert_contact_desc = "INSERT INTO `contacts_descriptions`(`contact_id`,
                                                                            `language_id`,
                                                                            `contact_city`,
                                                                            `contact_postcode`,
                                                                            `contact_address`,
                                                                            `contact_info`) 
                                                                    VALUES ('$current_contact_id',
                                                                            '$language_id',
                                                                            '$contact_city_db',
                                                                            '$contact_postcode_db',
                                                                            '$contact_address_db',
                                                                            $contact_info_db)";
          $all_queries .= "<br>".$query_insert_contact_desc;
          mysqli_query($db_link, $query_insert_contact_desc);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - 2 `contacts_descriptions` ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
      
      foreach($contact_phones_array as $key => $contact_phone_row) {
        
        $contact_phone_id = 0;
          if(isset($contact_phone_row['contact_phone_id'])) $contact_phone_id = $contact_phone_row['contact_phone_id'];
        $contact_phone = $contact_phone_row['phone'];
        $contact_phone_is_home = 0;
          if(isset($contact_phone_row['is_home'])) $contact_phone_is_home = 1;

        if($contact_phone_id == 0) {
          $query_insert_contacts_phone = "INSERT INTO `contacts_phones`(`contact_phone_id`,`contact_id`,`contact_phone`,`contact_phone_is_home`) 
                                                                  VALUES (NULL,'$current_contact_id','$contact_phone','$contact_phone_is_home')";
          $all_queries .= "<br>".$query_insert_contacts_phone;
          mysqli_query($db_link, $query_insert_contacts_phone);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages['sql_error_insert']." - 2 `contacts_phones`".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_update_contacts_phone = "UPDATE `contacts_phones` SET `contact_phone` = '$contact_phone',`contact_phone_is_home`='$contact_phone_is_home'
                                          WHERE `contact_phone_id` = '$contact_phone_id'";
          $all_queries .= "<br>".$query_update_contacts_phone;
          $result_update_contacts_phone = mysqli_query($db_link, $query_update_contacts_phone);
          if(!$result_update_contacts_phone) {
            echo $languages['sql_error_update']." - 1 `contacts_phones` ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header("Location: $back_link");
      
    } //if(!isset($contact_errors))
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages['page_title_contacts_details'];
  $page_description = $languages['company_name']." администрация";
  
  print_html_admin_header($page_title, $page_description);
  
  $query_contact = "SELECT `contact_map_lat`,`contact_map_lng`,`contact_email`,`contact_is_active`,`contact_is_default`
                      FROM `contacts`
                     WHERE `contacts`.`contact_id` = '$current_contact_id'";
  //echo $query_contact;
  $result_contact = mysqli_query($db_link, $query_contact);
  if(!$result_contact) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_contact) > 0) {
    $contact_array = mysqli_fetch_assoc($result_contact);
    $contact_map_lat = $contact_array['contact_map_lat'];
    $contact_map_lng = $contact_array['contact_map_lng'];
    $contact_email = $contact_array['contact_email'];
    $contact_is_active = $contact_array['contact_is_active'];
    $contact_is_default = $contact_array['contact_is_default'];
  }
?>
<!--main section-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/<?=$_SESSION['admin_dir_name'];?>/index.php" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['header_home'];?></a>
        <span>&raquo;</span>
        <a href="<?=$back_link;?>" title="<?=$languages['title_breadcrumbs_contacts'];?>"><?=$languages['header_contacts'];?></a>
        <span>&raquo;</span>
        <?=$languages['text_contacts_details'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages['header_contacts_details'];?></h1>
      
      <form method="post" class="input_form row" action="<?=htmlspecialchars($_SERVER['REQUEST_URI']);?>" enctype="multipart/form-data">
        <p>
          <button type="submit" name="update_contact" class="button green"><i class="icon icon_save_sign"></i><?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages['text_required_fields'];?></i></p>

        <ul id="contacts" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
?>
            <li>
              <a href="#<?=$language_code;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?>
              </a>
            </li>
<?php
          }
        }
?>
        </ul>
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $key => $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          $language_menu_name = $row_languages['language_menu_name'];
          
          $query_contact_desc = "SELECT `contact_city`, `contact_postcode`, `contact_address`, `contact_info`
                                   FROM `contacts_descriptions` 
                                  WHERE `language_id` = '$language_id' AND `contact_id` = '$current_contact_id'";
          //echo $query_contact_desc;
          $result_contact_desc = mysqli_query($db_link, $query_contact_desc);
          if(!$result_contact_desc) echo mysqli_error($db_link);
          if(mysqli_num_rows($result_contact_desc) > 0) {
            $contact_desc = mysqli_fetch_assoc($result_contact_desc);
            
            $contact_cities_array[$language_id] = $contact_desc['contact_city'];
            $contact_postcodes_array[$language_id] = $contact_desc['contact_postcode'];
            $contact_addresses_array[$language_id] = stripslashes($contact_desc['contact_address']);
            $contact_infos_array[$language_id] = stripslashes($contact_desc['contact_info']);
            $contact_has_record_in_db[$language_id] = 1;
          }
          else {
            $contact_has_record_in_db[$language_id] = 0;
          }
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="contact_city" class="title"><?=$languages['header_site'];?><span class="red">*</span></label>
            <?php
              if(isset($contact_errors['contact_city'][$language_id])) {
                echo "<div class='error'>".$contact_errors['contact_city'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="contact_cities[<?=$language_id;?>]" value='<?php if(isset($contact_cities_array[$language_id])) echo $contact_cities_array[$language_id];?>'>
            <input type="hidden" name="contact_has_record_in_db[<?=$language_id;?>]" value="<?=$contact_has_record_in_db[$language_id];?>" >
          </div>
          <div class="clearfix"></div>
          
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="contact_postcode" class="title"><?=$languages['header_postcode'];?><span class="red">*</span></label>
            <?php
              if(isset($contact_errors['contact_postcode'][$language_id])) {
                echo "<div class='error'>".$contact_errors['contact_postcode'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="contact_postcodes[<?=$language_id;?>]" value='<?php if(isset($contact_postcodes_array[$language_id])) echo $contact_postcodes_array[$language_id];?>'>
          </div>
          <div class="clearfix"></div>
          
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="contact_address" class="title"><?=$languages['header_address'];?><span class="red">*</span></label>
            <?php
              if(isset($contact_errors['contact_address'][$language_id])) {
                echo "<div class='error'>".$contact_errors['contact_address'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="contact_addresses[<?=$language_id;?>]" value='<?php if(isset($contact_addresses_array[$language_id])) echo $contact_addresses_array[$language_id];?>'>
          </div>
          <div class="clearfix"></div>
          
          <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
            <label for="contact_info" class="title"><?=$languages['header_address_info'];?></label>
            <input type="text" name="contact_infos[<?=$language_id;?>]" value='<?php if(isset($contact_infos_array[$language_id])) echo $contact_infos_array[$language_id];?>'>
          </div>
          <div class="clearfix"></div>
          
        </div>
<?php
        } //foreach($languages_array as $key => $row_contacts)
      }
?>
        <input type="hidden" id="warning_address_cant_be_empty" value="<?=$languages['warning_address_cant_be_empty'];?>">
        <input type="hidden" id="warning_google_cant_find_coords_for_this_address" value="<?=$languages['warning_google_cant_find_coords_for_this_address'];?>">
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12 margin_bottom">
          <label for="map_address" class="title"><?=$languages['header_address'];?></label>
          <input type="text" name="map_address" id="map_address" value="">
          <div><i class="info"><?=$languages['text_google_maps_address'];?></i></div>
        </div>
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="map_address" class="title hidden-md hidden-sm hidden-xs">&nbsp;</label>
          <a id="get_coords" class="button green" onClick="SetMapCoords()"><?=$languages['btn_get_map_coords'];?></a>
          <!--<a href="http://www.latlong.net/" target="_blank" class="button blue">Вземи координати за карта (от сайт)</a>-->
        </div>
        <p class="clearfix"></p>
        
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="contact_map_lat" class="title">Latitude</label> 
          <input type="text" name="contact_map_lat" id="contact_map_lat" value="<?php if(isset($contact_map_lat)) echo $contact_map_lat; ?>">
        </div>
        <p class="clearfix"></p>
        
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="contact_map_lng" class="title">Longitude</label>
          <input type="text" name="contact_map_lng" id="contact_map_lng" value="<?php if(isset($contact_map_lng)) echo $contact_map_lng; ?>">
        </div>
        <p class="clearfix"></p>
        
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="contact_email" class="title"><?=$languages['header_email'];?></label>
          <input type="text" name="contact_email" id="contact_email" value="<?php if(isset($contact_email)) echo $contact_email;?>">
        </div>
        <p class="clearfix"></p>
<?php
      $query_phones = "SELECT `contact_phone_id`,`contact_phone`,`contact_phone_is_home` FROM `contacts_phones` 
                        WHERE `contact_id` = '$current_contact_id'";
      $result_phones = mysqli_query($db_link, $query_phones);
      if(!$result_phones) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_phones) > 0) {
        
        $key = 1;
        
        while($phones = mysqli_fetch_assoc($result_phones)) {
          
          $contact_phone_id = $phones['contact_phone_id'];
          $contact_phone = $phones['contact_phone'];
          $contact_phone_is_home = $phones['contact_phone_is_home'];         
?>
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="contact_phone" class="title"><?=$languages['header_phone'];?></label>
          <input type="text" name="contact_phones[<?=$key;?>][phone]" class="col-lg-7 col-md-7 col-sm-12 col-xs-12" value="<?=$contact_phone;?>">
          <input type="hidden" name="contact_phones[<?=$key;?>][contact_phone_id]" value="<?=$contact_phone_id;?>">
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            &nbsp;&nbsp;<input type="checkbox" name="contact_phones[<?=$key;?>][is_home]" <?php if($contact_phone_is_home == 1) echo 'checked="checked"';?>> Стационарен
          </div>
        </div>
        <p class="clearfix"></p>
<?php
          $key++;
        } //while($phones)
      } //if(mysqli_num_rows($result_phones) > 0)
      else {
        $key = 2;
?>
        <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
          <label for="contact_phone" class="title"><?=$languages['header_phone'];?></label>
          <input type="text" name="contact_phones[1][phone]" class="col-lg-7 col-md-7 col-sm-12 col-xs-12" value="<?php if(isset($contact_phones[1]['phone'])) echo $contact_phones[1]['phone'];?>">
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            &nbsp;<input type="checkbox" name="contact_phones[1][is_home]" <?php if(isset($contact_phones[1]['is_home'])) echo 'checked="checked"';?>> Стационарен
          </div>
        </div>
        <p class="clearfix"></p>
<?php
      }
?>
        <div id="more_contact_phones_container" class="margin_bottom">

        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <div class="margin_bottom">
            <a class="button green" onClick="AddContactPhoneRow()">
              <i class="icon icon_plus_sign"></i>
              <?=$languages['btn_add_phone_row'];?>
            </a>
          </div>
          <input type="hidden" id="current_phones_count" value="<?=$key;?>" />
          <input type="hidden" id="title_delete" value="<?=$languages['title_delete'];?>" />
        </div>
        
        <div>
          <label for="contact_is_default" class="title"><?=$languages['header_contact_use_by_default'];?></label>
          <?php
            if(isset($contact_is_default)) {
              if($contact_is_default == 0) echo '<input type="checkbox" name="contact_is_default" id="contact_is_default" />';
              else echo '<input type="checkbox" name="contact_is_default" id="contact_is_default" checked="checked" />';
            }
            else echo '<input type="checkbox" name="contact_is_default" id="contact_is_default" checked="checked" />';
          ?>
        </div>
        
        <div>
          <label for="contact_is_active" class="title"><?=$languages['header_status'];?></label>
          <?php
            if(isset($contact_is_active)) {
              if($contact_is_active == 0) echo '<input type="checkbox" name="contact_is_active" id="contact_is_active" />';
              else echo '<input type="checkbox" name="contact_is_active" id="contact_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="contact_is_active" id="contact_is_active" checked="checked" />';
          ?>
        </div>
        
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="update_contact" class="button green"><i class="icon icon_save_sign"></i><?=$languages['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--main section-->

<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUHQWYkA9yeW1eC3QXX4lastGoE4FvmjE&v=3.exp&signed_in=true"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      // language tab switcher
      $(".language_tabs li").removeClass("active");
      $(".language_tab").hide();
      $(".language_tabs li:first").addClass("active");
      $(".language_tab:first").show();
      $(".language_tabs a").click(function(event) {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".language_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".language_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end language tab switcher
    });
  </script>
</body>
</html>