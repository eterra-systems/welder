<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  if(isset($_GET['search_param'])) {
    $search_param =  $_GET['search_param'];
  }
  if(isset($_GET['search_term'])) {
    $search_term =  $_GET['search_term'];
  }
  if(isset($_GET['current_lang'])) {
    $current_lang =  $_GET['current_lang'];
  }
  if(isset($_GET['offset'])) {
    $offset =  $_GET['offset'];
  }
  else $offset = 0;
  //echo "<pre>";print_r($_GET);echo "</pre>";

  if($search_param == "products") {
    
    /*
     * full text search
     */
//    $query_category_ids = "SELECT `category_id`,`cd_name` FROM `categories_descriptions` WHERE MATCH(`cd_name`,`cd_description`) AGAINST('$search_param' IN BOOLEAN MODE )";
//    $result_category_ids = mysqli_query($db_link, $query_category_ids);
//    if(!$result_category_ids) echo mysqli_error($db_link);
//    $options_count = mysqli_num_rows($result_category_ids);
//    if($options_count > 0) {
//      while($row_category_ids = mysqli_fetch_assoc($result_category_ids)) {
//        echo"<pre>";print_r($row_category_ids);
//        $options[] = $row_category_ids;
//      }
//    }
    
    /*
     * normal LIKE search for InnoDB tables, that don't have full text search
     * first we gonna check the categories and if no results we gonna check the products
     */
   
    $page_offset = 12;
    $query_limit = "";
      
    $query_category_ids = "SELECT `category_id`,`cd_hierarchy_path` FROM `categories_descriptions` 
                            WHERE `cd_name` LIKE '%$search_term%' OR `cd_description` LIKE '%$search_term%'";
    //echo $query_category_ids;
    $result_category_ids = mysqli_query($db_link, $query_category_ids);
    if(!$result_category_ids) echo mysqli_error($db_link);
    $categories_count = mysqli_num_rows($result_category_ids);
    if($categories_count > 0) {
  
      while($row_category_ids = mysqli_fetch_assoc($result_category_ids)) {
        
        $category_id = $row_category_ids['category_id'];
        $cd_hierarchy_path = $row_category_ids['cd_hierarchy_path'];
        
        $query_products = "SELECT `products`.`product_id`
                             FROM `products`
                       INNER JOIN `product_to_category` USING(`product_id`)
                            WHERE `products`.`product_is_active` = '1' AND `product_to_category`.`category_id` = '$category_id'";
        //echo $query_products."<br>";
        $result_products = mysqli_query($db_link, $query_products);
        if (!$result_products) echo mysqli_error($db_link);
        $products_count = mysqli_num_rows($result_products);
        
        $query_products = "SELECT `products`.`product_id`,`products`.`product_ean`,`products`.`product_quantity`,`products`.`product_price`,
                                  `products_discounts`.`pd_price`,`products`.`product_viewed`,`products_descriptions`.`pd_name`,`products_descriptions`.`pd_description`
                             FROM `products`
                       INNER JOIN `product_to_category` ON `product_to_category`.`product_id` = `products`.`product_id`
                       INNER JOIN `products_descriptions` ON `products_descriptions`.`product_id` = `products`.`product_id`
                        LEFT JOIN `products_discounts` ON `products_discounts`.`product_id` = `products`.`product_id`
                        LEFT JOIN `product_option_value` ON `product_option_value`.`product_id` = `products`.`product_id`
                            WHERE `products`.`product_is_active` = '1' AND `product_to_category`.`category_id` = '$category_id'
                              AND `products_descriptions`.`language_id` = '$current_language_id'
                         GROUP BY `products`.`product_id`
                         ORDER BY `products`.`product_price` ASC";
        $result_products = mysqli_query($db_link, $query_products);
        if (!$result_products) echo mysqli_error($db_link);
        if (mysqli_num_rows($result_products) > 0) {
          //echo mysqli_num_rows($result_products);
          ?>
          <div class="catalog-grid">
            <ul class="product-grid products"> 
            <?php
            // if the results are more then $page_offset
            // making a pagination, finding how many pages will be needed
            $current_page = ($offset / $page_offset) + 1;

            if ($products_count > $page_offset) {
              $page_count = ceil($products_count / $page_offset);
            }
            // echo $page_count;

            while ($product_row = mysqli_fetch_assoc($result_products)) {

              $product_id = $product_row['product_id'];
              $pd_name = $product_row['pd_name'];
              $pd_description = stripslashes($product_row['pd_description']);
              $product_ean = $product_row['product_ean'];
              $product_viewed = $product_row['product_viewed'];
              $product_review_text = ($product_viewed == 1) ? $languages['text_review'] : $languages['text_reviews'];
              $product_quantity = $product_row['product_quantity'];

              $pd_images_folder = "/frontstore/images/products/";

              $pi_names_array = get_product_images($product_id);
              if ((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
                $default_img = $pi_names_array['default']['pi_name'];
                $default_img_exploded = explode(".", $default_img);
                $default_img_name = $default_img_exploded[0];
                $default_img_exstension = $default_img_exploded[1];
                $gallery_img_home_default = $pd_images_folder . $default_img_name . "_gal_zoom." . $default_img_exstension;
                $gallery_img_cart = $pd_images_folder . $default_img_name . "_gal_thumb." . $default_img_exstension;
                $full_path = $_SERVER['DOCUMENT_ROOT'].$pd_images_folder;

                $file = $full_path.$default_img;

                list($width,$height) = getimagesize($file);

                if($width > $height) {
                  $default_img_style = "";
                }
                else {
                  $default_img_style = "style='height:100%;width:auto;'";
                }
              } else {
                $gallery_img_path_large = $pd_images_folder . "no_image.jpg";
              }

              $additional_img = "";
              if (isset($pi_names_array['gallery'])) {
                //echo"<pre>";print_r($pi_names_array['gallery']);
                $gallery_img = $pi_names_array['gallery'][0]['pi_name'];
                $gallery_img_exploded = explode(".", $gallery_img);
                $gallery_img_name = $gallery_img_exploded[0];
                $gallery_img_exstension = $gallery_img_exploded[1];
                $full_path = $_SERVER['DOCUMENT_ROOT'].$pd_images_folder;

                $file = $full_path.$gallery_img;

                list($width,$height) = getimagesize($file);

                if($width > $height) {
                  $img_style = "";
                }
                else {
                  $img_style = "style='height:100%;width:auto;'";
                }

                $additional_img = "<img class='slider_img' src='" . $pd_images_folder . $gallery_img_name . "_gal_zoom." . $gallery_img_exstension . "' $img_style>";
              }

              $product_price = $product_row['product_price'];
              $product_price_text = $product_price . "лв.";
              if (!empty($product_row['pd_price'])) {
                $pd_price = $product_row['pd_price'];
                $pd_price_text = $product_price_text;
                $product_price_text = $pd_price . "лв.";
                $pd_price_percent_digit = ceil(100 - $pd_price * 100 / $product_price);
                $pd_price_percent = '<span class="price-reduction">' . $pd_price_percent_digit . '%</span>';
                if ($pd_price_percent_digit > 50) {
                  $sale_sign_class = " hidden";
                  $hot_sign_class = "";
                } else {
                  $sale_sign_class = "";
                  $hot_sign_class = " hidden";
                }
              } else {
                $pd_price = "";
                $pd_price_text = "";
                $pd_price_percent = "";
                $pd_price_percent_digit = "";
                $sale_sign_class = " hidden";
                $hot_sign_class = " hidden";
              }

              $pd_name_for_link = str_replace(array('\\','?','!','.',',','(',')','%',' - ',' '), array('-','','','','-','-','-','-','-','-'), mb_convert_case($pd_name, MB_CASE_LOWER, "UTF-8"));
              $product_link = "/$current_lang/$cd_hierarchy_path/$pd_name_for_link?pid=$product_id";
              $quick_view_link = "/$current_lang/product-quick-view?product_id=$product_id&cd_hierarchy_path=$cd_hierarchy_path";
              $quick_view_link = "/frontstore/product-quick-view.php?product_id=$product_id&cd_hierarchy_path=$cd_hierarchy_path";
              $current_cat_href_final = (empty($cd_pretty_url)) ? $cd_hierarchy_path : "$cd_hierarchy_path/$cd_pretty_url";
              $loggin_required_for_wishlist = $languages['text_need_to_login_for_wishlist'];
              $onclick_wishlist_fn = (user_is_loged()) ? "AddProductToWishlist('$product_id','$pd_name')" : "OpenModalWindow('$loggin_required_for_wishlist')";

              /*
               * we gonna check every product if is in or out of stock
               */

              $there_are_quantity_from_products = false;
              $query_options = "SELECT `options`.`option_id`,`products_options`.`po_is_required`
                                  FROM `options` 
                            INNER JOIN `products_options` ON `products_options`.`option_id` = `options`.`option_id`
                                 WHERE `products_options`.`product_id` = '$product_id' AND `po_is_required` = '1'";
              //echo "<input type='hidden' value='$query_options' />";
              $result_options = mysqli_query($db_link, $query_options);
              if (!$result_options)
                echo mysqli_error($db_link);
              $options_count = mysqli_num_rows($result_options);
              if ($options_count > 0) {

                $there_are_quantity_from_products = true;
                $there_is_at_least_one_quantity_for_product = false;

                while ($row_options = mysqli_fetch_assoc($result_options)) {

                  $current_option_id = $row_options['option_id'];

                  $query_ovd_names = "SELECT `product_option_value`.`product_option_value_id`
                               FROM `product_option_value`
                               WHERE `product_option_value`.`product_id` = '$product_id' AND `product_option_value`.`option_id` = '$current_option_id' 
                                 AND `product_option_value`.`pov_quantity` <> '0' LIMIT 1";
                  //echo $query_ovd_names."<br>";
                  $result_ovd_names = mysqli_query($db_link, $query_ovd_names);
                  if (!$result_ovd_names)
                    echo mysqli_error($db_link);
                  $count_option_values = mysqli_num_rows($result_ovd_names);
                  if ($count_option_values > 0) {
                    $there_is_at_least_one_quantity_for_product = true;
                  }
                }
              }

              if ((!$there_are_quantity_from_products && $product_quantity > 0) || ($there_are_quantity_from_products && $there_is_at_least_one_quantity_for_product)) {
                $not_avail_class = " hidden";
              } else {
                $not_avail_class = "";
                $sale_sign_class = " hidden";
                $hot_sign_class = " hidden";
              }
              ?>
              <li id="product_block_<?= $product_id; ?>" class="animated" data-animation="bounceInUp" >
                <input type="hidden" name="product_ean" class="product_ean" value="<?= $product_ean; ?>">
                <input type="hidden" name="product_price" class="product_price" value="<?= $product_price; ?>">
                <input type="hidden" name="pd_price" class="pd_price" value="<?= $pd_price; ?>">
                <input type="hidden" name="product_name" class="product_name" value="<?= $pd_name; ?>">
                <input type="hidden" name="product_url" class="product_url" value="<?= $product_link; ?>">
                <input type="hidden" name="product_qty" class="product_qty" value="1">
                <input type="hidden" name="product_img" class="product_img" value="<?= $gallery_img_cart; ?>">
                <div class="product">
                  <div class="product-thumb">
                    <span class="label-sale<?= $sale_sign_class; ?>"><?= $languages['text_sale']; ?></span>
                    <span class="label-hot<?= $hot_sign_class; ?>"><?= $languages['text_hot']; ?></span>
                    <span class="label-not-available<?= $not_avail_class; ?>"><?= $languages['text_not_in_stock']; ?></span>
                    <div class="btn-action-item product-button">
              <a href="javascript:;" onclick="<?= $onclick_wishlist_fn; ?>"> <i class="icomoon-heart"></i></a> 
              <a href="<?= $quick_view_link; ?>" rel="nofollow" data-toggle="modal" data-target="#quick-view-id<?= $product_id; ?>" > 
                <i class="icomoon-search"></i>
              </a> 
              <a href="<?="$pd_images_folder$default_img";?>" class="magnific"> <i class="icomoon-images"></i></a> 
              <a href="<?= $product_link; ?>" > <i class="icomoon-eye-open"></i></a>
          </div>
                    <a href="<?= $product_link; ?>">
                      <img src="<?= $gallery_img_home_default; ?>" class="default_img" <?=$default_img_style;?> alt="<?= $pd_name; ?>" title="<?= $pd_name; ?>" itemprop="image" />
                    </a>
                    <a href="<?= $product_link; ?>"><?= $additional_img; ?></a> 
                  </div>
                  <div class="info-product">
            <div class="star-rating">
              <?php
                $product_rating_params = get_product_rating($product_id);

                $product_rating = $product_rating_params['product_rating'];
                $product_rating_imgs = $product_rating_params['rating_imgs'];

                echo "$product_rating_imgs";
              ?>
            </div>
            <h3 class="product-name x-hover">
                  <a href="<?= $product_link; ?>"><span class="x-hover-text"><?= $pd_name; ?></span></a>
                </h3>
            <div class="price-box"> 
              <span class="price-regular"><?= $product_price; ?> лв.</span> 

            </div>
            <div class="only-list-view">
              <div class="product-desc">
                <p><?= $pd_description; ?></p>
              </div>
              <div class="btn-group"> <a class="btn"  href="<?= $product_link; ?>">View more</a> </div>
            </div>
          </div>
                </div>
              </li>
              <!--Modal box-->
              <div class="quick-view-modal modal fade" id="quick-view-id<?= $product_id; ?>">
                <div class="modal-content"> </div>
              </div>
<?php
            } //while($product_row)
?>
            <div class="clearfix"></div> 
<?php
            // if the results are more then $page_offset make pagination
            if (isset($page_count)) {
              if (!empty($colors_ids) || !empty($option_value_ids) || ((isset($price_min) && $price_min != $min_product_price) || (isset($price_max) && $price_max != $max_product_price))) {
                ?>
              </ul>
            </div>
            <nav class="pagination">
              <ul class="js_pagination">
                <?php
                while ($current_page <= $page_count) {
                  if ($current_page == 1) {
                    $a_current = " btn-primary";
                    echo '<li class="disabled btn_prev_page"><a href="javascript:;" class="btn">&laquo; </a></li>';
                  } else {
                    $a_current = " btn-default";
                  }

                  echo "<li id='pag_$current_page'><a href='javascript:;' class='btn$a_current' data'$current_page'>$current_page</a></li>";

                  $current_page++;
                }
                ?>
                <li class="btn_next_page"><a href="javascript:;" class="btn btn-default" data="2"> &raquo;</a></li>
              </ul>
              <input type="hidden" class="page_count" value="<?= $page_count; ?>" >
            </nav>
              <?php
            } else {
              ?>
          </ul>
          </div>
          <nav class="pagination">
            <ul class="php_pagination">
              <?php
              $pages = 1;
              $current_offset = $offset;
              $offset = 0;

              if ($current_page == 1) {
                echo '<li class="disabled btn_prev_page"><a href="javascript:;" class="btn" data="">&laquo; </a></li>';
              } else {
                $prev_offset = $current_offset - $page_offset;
                echo '<li class="btn_prev_page"><a href="javascript:;" class="btn" data="' . $prev_offset . '">&laquo; </a></li>';
              }

              while ($pages <= $page_count) {


                $a_current = ($current_page == $pages) ? " btn-primary" : " btn-default";

                echo "<li id='pag_$pages'><a href='javascript:;' class='btn $a_current' data=\"$offset\">$pages</a></li>";

                $pages++;
                $offset += $page_offset;
              }
              if ($current_page == $page_count) {
                echo '<li class="disabled btn_next_page"><a href="javascript:;" class="btn" data=""> &raquo;</a></li>';
              } else {
                $next_offset = $current_offset + $page_offset;
                echo '<li class="btn_next_page"><a href="javascript:;" class="btn" data="' . $next_offset . '">&raquo; </a></li>';
              }
              ?>
            </ul>
            <input type="hidden" class="products_count" value="<?= $products_count; ?>" >
            <input type="hidden" class="language_id" value="<?= $current_language_id; ?>" >
          </nav>
          <?php
            }
          } // if(isset($page_count))
        } //if (mysqli_num_rows($result_products) > 0)
      }
    } //if($categories_count > 0)
    else {
       
      $cd_hierarchy_path = "";
      
      $query_products = "SELECT `products`.`product_id`,`products`.`product_ean`,`products`.`product_quantity`,`products`.`product_price`,
                                `products_discounts`.`pd_price`,`products`.`product_viewed`,`products_descriptions`.`pd_name`,`products_descriptions`.`pd_description`
                           FROM `products`
                     INNER JOIN `product_to_category` ON `product_to_category`.`product_id` = `products`.`product_id`
                     INNER JOIN `products_descriptions` ON `products_descriptions`.`product_id` = `products`.`product_id`
                      LEFT JOIN `products_discounts` ON `products_discounts`.`product_id` = `products`.`product_id`
                      LEFT JOIN `product_option_value` ON `product_option_value`.`product_id` = `products`.`product_id`
                          WHERE `products`.`product_is_active` = '1' 
                            AND `products_descriptions`.`pd_name` LIKE '%$search_term%' OR `products_descriptions`.`pd_description` LIKE '%$search_term%'
                             OR `products`.`product_ean` LIKE '%$search_term%'
                            AND `products_descriptions`.`language_id` = '$current_language_id'
                       GROUP BY `products`.`product_id`
                       ORDER BY `products`.`product_price` ASC";
      //echo $query_products;
      $result_products = mysqli_query($db_link, $query_products);
      if (!$result_products) echo mysqli_error($db_link);
      $products_count = mysqli_num_rows($result_products);
      if ($products_count > 0) {
        //echo mysqli_num_rows($result_products);
        ?>
        <div class="catalog-grid">
          <ul class="product-grid products"> 
          <?php
          // if the results are more then $page_offset
          // making a pagination, finding how many pages will be needed
          $current_page = ($offset / $page_offset) + 1;

          if ($products_count > $page_offset) {
            $page_count = ceil($products_count / $page_offset);
          }
          // echo $page_count;

          while ($product_row = mysqli_fetch_assoc($result_products)) {

            $product_id = $product_row['product_id'];
            $pd_name = $product_row['pd_name'];
            $pd_description = stripslashes($product_row['pd_description']);
            $product_ean = $product_row['product_ean'];
            $product_viewed = $product_row['product_viewed'];
            $product_review_text = ($product_viewed == 1) ? $languages['text_review'] : $languages['text_reviews'];
            $product_quantity = $product_row['product_quantity'];

            $pd_images_folder = "/frontstore/images/products/";

            $pi_names_array = get_product_images($product_id);
            if ((isset($pi_names_array['default']['pi_name']) && !empty($pi_names_array['default']['pi_name']))) {
              $default_img = $pi_names_array['default']['pi_name'];
              $default_img_exploded = explode(".", $default_img);
              $default_img_name = $default_img_exploded[0];
              $default_img_exstension = $default_img_exploded[1];
              $gallery_img_home_default = $pd_images_folder . $default_img_name . "_gal_zoom." . $default_img_exstension;
              $gallery_img_cart = $pd_images_folder . $default_img_name . "_gal_thumb." . $default_img_exstension;
              $full_path = $_SERVER['DOCUMENT_ROOT'].$pd_images_folder;

              $file = $full_path.$default_img;

              list($width,$height) = getimagesize($file);

              if($width > $height) {
                $default_img_style = "";
              }
              else {
                $default_img_style = "style='height:100%;width:auto;'";
              }
            } else {
              $gallery_img_path_large = $pd_images_folder . "no_image.jpg";
            }

            $additional_img = "";
            if (isset($pi_names_array['gallery'])) {
              //echo"<pre>";print_r($pi_names_array['gallery']);
              $gallery_img = $pi_names_array['gallery'][0]['pi_name'];
              $gallery_img_exploded = explode(".", $gallery_img);
              $gallery_img_name = $gallery_img_exploded[0];
              $gallery_img_exstension = $gallery_img_exploded[1];
              $full_path = $_SERVER['DOCUMENT_ROOT'].$pd_images_folder;

              $file = $full_path.$gallery_img;

              list($width,$height) = getimagesize($file);

              if($width > $height) {
                $img_style = "";
              }
              else {
                $img_style = "style='height:100%;width:auto;'";
              }

              $additional_img = "<img class='slider_img' src='" . $pd_images_folder . $gallery_img_name . "_gal_zoom." . $gallery_img_exstension . "' $img_style>";
            }

            $product_price = $product_row['product_price'];
            $product_price_text = $product_price . "лв.";
            if (!empty($product_row['pd_price'])) {
              $pd_price = $product_row['pd_price'];
              $pd_price_text = $product_price_text;
              $product_price_text = $pd_price . "лв.";
              $pd_price_percent_digit = ceil(100 - $pd_price * 100 / $product_price);
              $pd_price_percent = '<span class="price-reduction">' . $pd_price_percent_digit . '%</span>';
              if ($pd_price_percent_digit > 50) {
                $sale_sign_class = " hidden";
                $hot_sign_class = "";
              } else {
                $sale_sign_class = "";
                $hot_sign_class = " hidden";
              }
            } else {
              $pd_price = "";
              $pd_price_text = "";
              $pd_price_percent = "";
              $pd_price_percent_digit = "";
              $sale_sign_class = " hidden";
              $hot_sign_class = " hidden";
            }

            $pd_name_for_link = str_replace(array('\\','?','!','.',',','(',')','%',' - ',' '), array('-','','','','-','-','-','-','-','-'), mb_convert_case($pd_name, MB_CASE_LOWER, "UTF-8"));
            $product_link = "/$current_lang/$cd_hierarchy_path/$pd_name_for_link?pid=$product_id";
            $quick_view_link = "/$current_lang/product-quick-view?product_id=$product_id&cd_hierarchy_path=$cd_hierarchy_path";
            $quick_view_link = "/frontstore/product-quick-view.php?product_id=$product_id&cd_hierarchy_path=$cd_hierarchy_path";
            $current_cat_href_final = (empty($cd_pretty_url)) ? $cd_hierarchy_path : "$cd_hierarchy_path/$cd_pretty_url";
            $loggin_required_for_wishlist = $languages['text_need_to_login_for_wishlist'];
            $onclick_wishlist_fn = (user_is_loged()) ? "AddProductToWishlist('$product_id','$pd_name')" : "OpenModalWindow('$loggin_required_for_wishlist')";

            /*
             * we gonna check every product if is in or out of stock
             */

            $there_are_quantity_from_products = false;
            $query_options = "SELECT `options`.`option_id`,`products_options`.`po_is_required`
                       FROM `options` 
                       INNER JOIN `products_options` ON `products_options`.`option_id` = `options`.`option_id`
                       WHERE `products_options`.`product_id` = '$product_id' AND `po_is_required` = '1'";
            //echo "<input type='hidden' value='$query_options' />";
            $result_options = mysqli_query($db_link, $query_options);
            if (!$result_options)
              echo mysqli_error($db_link);
            $options_count = mysqli_num_rows($result_options);
            if ($options_count > 0) {

              $there_are_quantity_from_products = true;
              $there_is_at_least_one_quantity_for_product = false;

              while ($row_options = mysqli_fetch_assoc($result_options)) {

                $current_option_id = $row_options['option_id'];

                $query_ovd_names = "SELECT `product_option_value`.`product_option_value_id`
                             FROM `product_option_value`
                             WHERE `product_option_value`.`product_id` = '$product_id' AND `product_option_value`.`option_id` = '$current_option_id' 
                               AND `product_option_value`.`pov_quantity` <> '0' LIMIT 1";
                //echo $query_ovd_names."<br>";
                $result_ovd_names = mysqli_query($db_link, $query_ovd_names);
                if (!$result_ovd_names)
                  echo mysqli_error($db_link);
                $count_option_values = mysqli_num_rows($result_ovd_names);
                if ($count_option_values > 0) {
                  $there_is_at_least_one_quantity_for_product = true;
                }
              }
            }

            if ((!$there_are_quantity_from_products && $product_quantity > 0) || ($there_are_quantity_from_products && $there_is_at_least_one_quantity_for_product)) {
              $not_avail_class = " hidden";
            } else {
              $not_avail_class = "";
              $sale_sign_class = " hidden";
              $hot_sign_class = " hidden";
            }
            ?>
            <li id="product_block_<?= $product_id; ?>" class="animated" data-animation="bounceInUp" >
              <input type="hidden" name="product_ean" class="product_ean" value="<?= $product_ean; ?>">
              <input type="hidden" name="product_price" class="product_price" value="<?= $product_price; ?>">
              <input type="hidden" name="pd_price" class="pd_price" value="<?= $pd_price; ?>">
              <input type="hidden" name="product_name" class="product_name" value="<?= $pd_name; ?>">
              <input type="hidden" name="product_url" class="product_url" value="<?= $product_link; ?>">
              <input type="hidden" name="product_qty" class="product_qty" value="1">
              <input type="hidden" name="product_img" class="product_img" value="<?= $gallery_img_cart; ?>">
              <div class="product">
                <div class="product-thumb">
                  <span class="label-sale<?= $sale_sign_class; ?>"><?= $languages['text_sale']; ?></span>
                  <span class="label-hot<?= $hot_sign_class; ?>"><?= $languages['text_hot']; ?></span>
                  <span class="label-not-available<?= $not_avail_class; ?>"><?= $languages['text_not_in_stock']; ?></span>
                  <div class="btn-action-item product-button">
              <a href="javascript:;" onclick="<?= $onclick_wishlist_fn; ?>"> <i class="icomoon-heart"></i></a> 
              <a href="<?= $quick_view_link; ?>" rel="nofollow" data-toggle="modal" data-target="#quick-view-id<?= $product_id; ?>" > 
                <i class="icomoon-search"></i>
              </a> 
              <a href="<?="$pd_images_folder$default_img";?>" class="magnific"> <i class="icomoon-images"></i></a> 
              <a href="<?= $product_link; ?>" > <i class="icomoon-eye-open"></i></a>
          </div>
                  <a href="<?= $product_link; ?>">
                    <img src="<?= $gallery_img_home_default; ?>" class="default_img" <?=$default_img_style;?> alt="<?= $pd_name; ?>" title="<?= $pd_name; ?>" itemprop="image" />
                  </a>
                  <a href="<?= $product_link; ?>"><?= $additional_img; ?></a> 
                </div>
                <div class="info-product">
            <div class="star-rating">
              <?php
                $product_rating_params = get_product_rating($product_id);

                $product_rating = $product_rating_params['product_rating'];
                $product_rating_imgs = $product_rating_params['rating_imgs'];

                echo "$product_rating_imgs";
              ?>
            </div>
            <h3 class="product-name x-hover">
                  <a href="<?= $product_link; ?>"><span class="x-hover-text"><?= $pd_name; ?></span></a>
                </h3>
            <div class="price-box"> 
              <span class="price-regular"><?= $product_price; ?> лв.</span> 

            </div>
            <div class="only-list-view">
              <div class="product-desc">
                <p><?= $pd_description; ?></p>
              </div>
              <div class="btn-group"> <a class="btn"  href="<?= $product_link; ?>">View more</a> </div>
            </div>
          </div>
              </div>
            </li>
            <!--Modal box-->
            <div class="quick-view-modal modal fade" id="quick-view-id<?= $product_id; ?>">
              <div class="modal-content"> </div>
            </div>
<?php

        }
      } //if (mysqli_num_rows($result_products) > 0) 
    }
    
  }
?>
