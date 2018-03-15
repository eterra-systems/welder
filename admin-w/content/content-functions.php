<?php

function list_sliders_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_sliders = "SELECT `sliders`.`slider_id`,`sliders`.`slider_image`,`sliders_descriptions`.`slider_header`,`sliders_descriptions`.`slider_text`
                      FROM `sliders`
                INNER JOIN `sliders_descriptions` ON `sliders_descriptions`.`slider_id` = `sliders`.`slider_id`
                     WHERE `sliders_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `slider_sort_order` ASC";
  //echo $query_sliders;exit;
  $result_sliders = mysqli_query($db_link, $query_sliders);
  if(!$result_sliders) echo mysqli_error($db_link);
  $sliders_count = mysqli_num_rows($result_sliders);
  if($sliders_count > 0) {
    
    while($slider_row = mysqli_fetch_assoc($result_sliders)) {
      $slider_id = $slider_row['slider_id'];
      $slider_header = stripslashes($slider_row['slider_header']);
      $slider_text = stripslashes($slider_row['slider_text']);
      $slider_image = $slider_row['slider_image'];
      if(!empty($slider_image)) {
        $slider_image_exstension = pathinfo($slider_image, PATHINFO_EXTENSION);
        $slider_image_name = str_replace(".$slider_image_exstension", "", $slider_image);
        $slider_image_thumb = "/site/images/sliders/".$slider_image_name."_admin_thumb.".$slider_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $slider_image_thumb = "/site/images/no_image_172x120.jpg";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if($blocks_ids_array) {
        if(in_array($slider_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$slider_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$slider_id;?>">
          </td>
          <td width="35%" class="text_left">
            <img src="<?=$slider_image_thumb;?>" alt="<?=$slider_header;?>" <?=$thumb_image_dimensions;?>>
          </td>
          <td width="40%" class="text_left"><?=$slider_header;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_sliders);
  }
}

function list_news_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $languages;
  global $current_language_id;
  global $class;
  
  $news_cat_parent_id = 0;
  $query_news_categories = "SELECT `news_categories`.`news_category_id`,`news_categories`.`news_cat_parent_id`,
                                   `news_categories`.`news_cat_has_children`,`news_cat_desc`.`news_cat_name`,`news_cat_desc`.`news_cat_long_name` 
                              FROM `news_categories` 
                        INNER JOIN `news_cat_desc` USING(`news_category_id`)
                             WHERE `news_categories`.`news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_desc`.`language_id` = '$current_language_id'
                          ORDER BY `news_categories`.`news_cat_sort_order` ASC";
  //echo "<pre>$query_news_categories</pre>";exit;
  $result_news_categories = mysqli_query($db_link, $query_news_categories);
  if(!$result_news_categories) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_categories) > 0) {

    while($news_categories_row = mysqli_fetch_assoc($result_news_categories)) {
      $news_category_id = $news_categories_row['news_category_id'];
      $news_cat_parent_id = $news_categories_row['news_cat_parent_id'];
      $news_cat_name = $news_categories_row['news_cat_name'];
      
      $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_image`,`news_descriptions`.`news_title` 
                       FROM `news` 
                 INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                      WHERE `news`.`news_category_id` = '$news_category_id' AND `news_descriptions`.`language_id` = '$current_language_id'";
      //echo "<pre>$query_news</pre>";exit;
      $result_news = mysqli_query($db_link, $query_news);
      if(!$result_news) echo mysqli_error($db_link);
      $news_count = mysqli_num_rows($result_news);
      if($news_count > 0) {
        while($news_row = mysqli_fetch_assoc($result_news)) {
          $news_id = $news_row['news_id'];
          $news_title = $news_row['news_title'];
          $news_images_folder = "/site/images/news/";
          $news_image = $news_images_folder.$news_row['news_image'];
          $news_image_exstension = pathinfo($news_image, PATHINFO_EXTENSION);
          $news_image_name = str_replace(".$news_image_exstension", "", $news_image);
          $news_image_thumb_name = $news_image_name."_sidebar_thumb.".$news_image_exstension;
          @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image_thumb_name);
          $thumb_image_dimensions = $thumb_image_params[3];
          $checkbox_checked = "";
          if($blocks_ids_array) {
            if(in_array($news_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
          }
          
?>
          <table class="row_over">
            <tbody>
              <tr id="tr_<?=$news_id;?>" class="<?=$class?>">
                <td width="5%" class="text_left">
                  <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$news_id;?>">
                </td>
                <td width="35%" class="text_left">
                  <img src="<?=$news_image_thumb_name;?>" alt="<?=$news_title;?>" <?=$thumb_image_dimensions;?>>
                </td>
                <td width="40%" class="text_left"><?=$news_title;?></td>
                <td width="20%" class="text_left"><?=$news_cat_name;?></td>
              </tr>
            </tbody>
          </table>
<?php
        } //while($news_row)
        mysqli_free_result($result_news);
      } //if($news_count > 0)
    } //while($news_categories_row)
    mysqli_free_result($result_news_categories);
  } //if(mysqli_num_rows($result_news_categories) > 0)
  
}

function list_clients_include_block($menu_id, $blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_clients = "SELECT `clients`.`client_id`,`clients`.`client_image`,`clients_descriptions`.`client_name`
                      FROM `clients`
                INNER JOIN `clients_descriptions` ON `clients_descriptions`.`client_id` = `clients`.`client_id`
                     WHERE `clients_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `client_sort_order` ASC";
  //echo $query_clients;exit;
  $result_clients = mysqli_query($db_link, $query_clients);
  if(!$result_clients) echo mysqli_error($db_link);
  $clients_count = mysqli_num_rows($result_clients);
  if($clients_count > 0) {
    
    while($client_row = mysqli_fetch_assoc($result_clients)) {
      
      $client_id = $client_row['client_id'];
      $client_name = stripslashes($client_row['client_name']);
      $client_image = $client_row['client_image'];
      if(!empty($client_image)) {
        $client_image_exstension = pathinfo($client_image, PATHINFO_EXTENSION);
        $client_image_name = str_replace(".$client_image_exstension", "", $client_image);
        $client_image_thumb = "/site/images/clients/".$client_image_name."_admin_thumb.".$client_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$client_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $client_image_thumb = "/site/images/no_image_172x120.jpg";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$client_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if($blocks_ids_array) {
        if(in_array($client_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$client_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$client_id;?>">
          </td>
          <td width="35%" class="text_left">
            <img src="<?=$client_image_thumb;?>" alt="<?=$client_name;?>" <?=$thumb_image_dimensions;?>>
          </td>
          <td width="40%" class="text_left"><?=$client_name;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_clients);

  }
}

function list_team_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_team_members = "SELECT `tm`.`team_member_id`,`tm`.`team_member_image`,`tmd`.`team_member_name`,`tmd`.`team_member_position`,`tmd`.`team_member_desc`
                           FROM `team_members` as `tm`
                     INNER JOIN `team_members_descriptions` as `tmd` ON `tmd`.`team_member_id` = `tm`.`team_member_id`
                          WHERE `tmd`.`language_id` = '$current_language_id'
                       ORDER BY `tm`.`team_member_sort_order` ASC";
  //echo $query_team_members;exit;
  $result_team_members = mysqli_query($db_link, $query_team_members);
  if(!$result_team_members) echo mysqli_error($db_link);
  $team_members_count = mysqli_num_rows($result_team_members);
  if($team_members_count > 0) {
    
    while($team_member_row = mysqli_fetch_assoc($result_team_members)) {
      
      $team_member_id = $team_member_row['team_member_id'];
      $team_member_name = stripslashes($team_member_row['team_member_name']);
      $team_member_desc = stripslashes($team_member_row['team_member_desc']);
      $team_member_image = $team_member_row['team_member_image'];
      if(!empty($team_member_image)) {
        $team_member_image_thumb = "/site/images/team/".$team_member_image;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$team_member_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $team_member_image_thumb = "/site/images/no_image_172x120.jpg";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$team_member_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if($blocks_ids_array) {
        if(in_array($team_member_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$team_member_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$team_member_id;?>">
          </td>
          <td width="35%" class="text_left">
            <img src="<?=$team_member_image_thumb;?>" alt="<?=$team_member_name;?>" <?=$thumb_image_dimensions;?>>
          </td>
          <td width="40%" class="text_left"><?=$team_member_name;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_team_members);
  }
}

function list_partners_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_partners = "SELECT `partners`.`partner_id`,`partners`.`partner_image`,`partners_descriptions`.`partner_name`
                       FROM `partners`
                 INNER JOIN `partners_descriptions` ON `partners_descriptions`.`partner_id` = `partners`.`partner_id`
                      WHERE `partners_descriptions`.`language_id` = '$current_language_id'
                   ORDER BY `partner_sort_order` ASC";
  //echo $query_partners;exit;
  $result_partners = mysqli_query($db_link, $query_partners);
  if(!$result_partners) echo mysqli_error($db_link);
  $partners_count = mysqli_num_rows($result_partners);
  if($partners_count > 0) {
    
    while($partner_row = mysqli_fetch_assoc($result_partners)) {
      
      $partner_id = $partner_row['partner_id'];
      $partner_name = stripslashes($partner_row['partner_name']);
      $partner_image = $partner_row['partner_image'];
      if(!empty($partner_image)) {
        $partner_image_exstension = pathinfo($partner_image, PATHINFO_EXTENSION);
        $partner_image_name = str_replace(".$partner_image_exstension", "", $partner_image);
        $partner_image_thumb = "/site/images/partners/".$partner_image_name."_admin_thumb.".$partner_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$partner_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $partner_image_thumb = "/site/images/no_image_172x120.jpg";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$partner_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if(!empty($blocks_ids_array)) {
        if(in_array($partner_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$partner_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$partner_id;?>">
          </td>
          <td width="35%" class="text_left">
            <img src="<?=$partner_image_thumb;?>" alt="<?=$partner_name;?>" <?=$thumb_image_dimensions;?>>
          </td>
          <td width="40%" class="text_left"><?=$partner_name;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_partners);
  }
}

function list_testimonials_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_testimonials = "SELECT `testimonials`.`testimonial_id`,`testimonials`.`testimonial_image`,`testimonials_descriptions`.`testimonial_author`,
                                `testimonials_descriptions`.`testimonial_text`
                           FROM `testimonials`
                     INNER JOIN `testimonials_descriptions` ON `testimonials_descriptions`.`testimonial_id` = `testimonials`.`testimonial_id`
                          WHERE `testimonials_descriptions`.`language_id` = '$current_language_id'
                       ORDER BY `testimonial_sort_order` ASC";
  //echo $query_testimonials;exit;
  $result_testimonials = mysqli_query($db_link, $query_testimonials);
  if(!$result_testimonials) echo mysqli_error($db_link);
  $testimonials_count = mysqli_num_rows($result_testimonials);
  if($testimonials_count > 0) {
    
    while($testimonial_row = mysqli_fetch_assoc($result_testimonials)) {
      $testimonial_id = $testimonial_row['testimonial_id'];
      $testimonial_author = stripslashes($testimonial_row['testimonial_author']);
      $testimonial_image = $testimonial_row['testimonial_image'];
      if(!empty($testimonial_image)) {
        $testimonial_image_exstension = pathinfo($testimonial_image, PATHINFO_EXTENSION);
        $testimonial_image_name = str_replace(".$testimonial_image_exstension", "", $testimonial_image);
        $testimonial_image_thumb = "/site/images/testimonials/".$testimonial_image_name."_site.".$testimonial_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$testimonial_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $testimonial_image_thumb = "/site/images/no_image_172x120.jpg";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$testimonial_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if($blocks_ids_array) {
        if(in_array($testimonial_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$testimonial_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$testimonial_id;?>">
          </td>
          <td width="35%" class="text_left">
            <img src="<?=$testimonial_image_thumb;?>" alt="<?=$testimonial_author;?>" <?=$thumb_image_dimensions;?>>
          </td>
          <td width="40%" class="text_left"><?=$testimonial_author;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_testimonials);
  }
}

function list_galleries_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_galleries = "SELECT `galleries`.`gallery_id`,`galleries_descriptions`.`gallery_name`,`galleries_images`.`gi_name` as `album_cover`
                        FROM `galleries` 
                  INNER JOIN `galleries_descriptions` USING(`gallery_id`)
                  INNER JOIN `galleries_images` USING(`gallery_id`)
                       WHERE `galleries_descriptions`.`language_id` = '$current_language_id' AND `galleries_images`.`gi_is_album_cover` = '1'
                    ORDER BY `gallery_sort_order` ASC";
  //echo $query_galleries;exit;
  $result_galleries = mysqli_query($db_link, $query_galleries);
  if(!$result_galleries) echo mysqli_error($db_link);
  $galleries_count = mysqli_num_rows($result_galleries);
  if($galleries_count > 0) {
    
    $gallery_cover_images_folder = "/site/images/galleries/";
    
    while($gallery_row = mysqli_fetch_assoc($result_galleries)) {

      $gallery_id = $gallery_row['gallery_id'];
      $gallery_name = stripslashes($gallery_row['gallery_name']);
      $gallery_cover_img = $gallery_row['album_cover'];
      $gallery_cover_img_exstension = pathinfo($gallery_cover_img, PATHINFO_EXTENSION);
      $gallery_cover_img_name = str_replace(".$gallery_cover_img_exstension", "", $gallery_cover_img);
      $gallery_cover_img_path_small = $gallery_cover_images_folder.$gallery_id."/".$gallery_cover_img_name."_big_thumb.".$gallery_cover_img_exstension;
      @$gallery_cover_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_cover_img_path_small);
      $gallery_cover_img_dimensions = $gallery_cover_img_params[3];
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if($blocks_ids_array) {
        if(in_array($gallery_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$gallery_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$gallery_id;?>">
          </td>
          <td width="35%" class="text_left">
            <img src="<?=$gallery_cover_img_path_small?>" alt="<?=$gallery_name;?>">
          </td>
          <td width="40%" class="text_left"><?=$gallery_name;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_galleries);
  }
}

function list_banners_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_banners = "SELECT `banner_id`,`banner_image`,`banners_links`.`banner_name` 
                      FROM `banners` 
                INNER JOIN `banners_links` USING(`banner_id`)
                     WHERE `banners_links`.`language_id` = '$current_language_id'
                  ORDER BY `banner_sort_order` ASC";
  //echo $query_banners;exit;
  $result_banners = mysqli_query($db_link, $query_banners);
  if(!$result_banners) echo mysqli_error($db_link);
  $banners_count = mysqli_num_rows($result_banners);
  if($banners_count > 0) {
    
    while($banner_row = mysqli_fetch_assoc($result_banners)) {

      $banner_id = $banner_row['banner_id'];
      $banner_image = "/site/images/banners/".$banner_row['banner_image'];
      @$image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$banner_image);
      $image_dimensions = $image_params[3];
      $banner_name = $banner_row['banner_name'];
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if($blocks_ids_array) {
        if(in_array($banner_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$banner_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$banner_id;?>">
          </td>
          <td width="35%" class="text_left">
            <img src="<?=$banner_image?>" alt="" <?=$image_dimensions;?>>
          </td>
          <td width="40%" class="text_left"><?=$banner_name;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_banners);
  }
}

function list_contacts_include_block($menu_id,$blocks_ids_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $class;
  
  $query_contacts = "SELECT `contacts`.`contact_id`,`contacts`.`contact_is_active`,`contacts`.`contact_sort_order`,`contacts`.`contact_is_default`,
                            `contacts_descriptions`.`contact_city`,`contacts_descriptions`.`contact_postcode`,`contacts_descriptions`.`contact_address`,
                            `contacts_descriptions`.`contact_info`
                       FROM `contacts`
                 INNER JOIN `contacts_descriptions` ON `contacts_descriptions`.`contact_id` = `contacts`.`contact_id`
                      WHERE `contacts_descriptions`.`language_id` = '$current_language_id'
                   ORDER BY `contact_sort_order` ASC";
  //echo $query_contacts;exit;
  $result_contacts = mysqli_query($db_link, $query_contacts);
  if(!$result_contacts) echo mysqli_error($db_link);
  $contacts_count = mysqli_num_rows($result_contacts);
  if($contacts_count > 0) {
    
    while($contact_row = mysqli_fetch_assoc($result_contacts)) {
      
      $contact_id = $contact_row['contact_id'];
      $contact_city = $contact_row['contact_city'];
      $contact_address = stripslashes($contact_row['contact_address']);
      $contact_postcode = $contact_row['contact_postcode'];
      $contact_info = stripslashes($contact_row['contact_info']);
      $contact_address .= (!empty($contact_info)) ? " ($contact_info)" : "";
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $checkbox_checked = "";
      if($blocks_ids_array) {
        if(in_array($contact_id, $blocks_ids_array)) $checkbox_checked = 'checked="checked"';
      }
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$contact_id;?>" class="<?=$class?>">
          <td width="5%" class="text_left">
            <input type="checkbox" name="include_blocks[<?=$menu_id;?>][]" class="multicontent_<?=$menu_id;?>" <?=$checkbox_checked;?> value="<?=$contact_id;?>">
          </td>
          <td width="35%" class="text_left">
            <?=$contact_city;?>
          </td>
          <td width="40%" class="text_left"><?=$contact_address;?></td>
          <td width="20%" class="text_left"></td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_contacts);
  }
}