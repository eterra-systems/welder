<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
    
  $page_title = $languages['customers_groups_title'];
  $page_description = "";

  $query_customers_groups = "SELECT `customers_groups`.`customer_group_id`, customers_groups_translations.`customer_group_translation_text` 
                               FROM `customers_groups`
                         INNER JOIN `customers_groups_translations` ON `customers_groups_translations`.`customer_group_id` = `customers_groups`.`customer_group_id`
                              WHERE `customers_groups_translations`.`language_id` = '$current_language_id'
                           ORDER BY `customers_groups`.`customer_group_sort_order` ASC";
  $result_customers_groups = mysqli_query($db_link,$query_customers_groups);
  if (!$result_customers_groups) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customers_groups) > 0) {
    while($row = mysqli_fetch_assoc($result_customers_groups)) {

      $customers_groups[] = $row;
    }
  }

  $page_offset = 18;
  $offset = (isset($_GET['offset'])) ? $_GET['offset'] : 0;

  if(!isset($_GET['customers_count']) && !isset($_GET['filter_cs'])) {
    $query_customers = "SELECT `customer_id` FROM `customers`";
    $result_customers = mysqli_query($db_link,$query_customers);
    if (!$result_customers) echo mysqli_error($db_link);
    $customers_count = mysqli_num_rows($result_customers);
  }

  if(isset($_GET['customers_count'])) $customers_count = $_GET['customers_count'];
  
  if(isset($_GET['filter_cs'])) {
    //echo"<pre>";print_r($_GET);echo"</pre>";
    
    if(isset($_GET['filter_c_name'])) {
      $customer_fullname = explode(" ", $_GET['filter_c_name']);
      if(count($customer_fullname) > 1) {
        $filter_c_firstname = $customer_fullname['0'];
        $filter_c_lastname = $customer_fullname['1'];
      }
      else $filter_c_name = $customer_fullname['0'];
    }
    if(isset($_GET['filter_c_group_id'])) {
      $filter_c_group_id =  $_GET['filter_c_group_id'];
    }
    if(isset($_GET['filter_c_email'])) {
      $filter_c_email =  $_GET['filter_c_email'];
    }
    if(isset($_GET['filter_c_phone'])) {
      $filter_c_phone =  $_GET['filter_c_phone'];
    }
    $filter_c_has_orders = 0;
    $filter_c_is_in_mailist = 0;
    $filter_c_is_blocked = 0;
    $filter_c_is_active = 0;
    if(isset($_GET['filter_c_has_orders']) && !empty($_GET['filter_c_has_orders'])) {
      $filter_c_has_orders = 1;
    }
    if(isset($_GET['filter_c_is_in_mailist']) && !empty($_GET['filter_c_is_in_mailist'])) {
      $filter_c_is_in_mailist = 1;
    }
    if(isset($_GET['filter_c_is_blocked']) && !empty($_GET['filter_c_is_blocked'])) {
      $filter_c_is_blocked = 1;
    }
    if(isset($_GET['filter_c_is_active']) && !empty($_GET['filter_c_is_active'])) {
      $filter_c_is_active = 1;
    }
    if(isset($_GET['filter_c_registration_date'])) {
      $filter_c_registration_date =  $_GET['filter_c_registration_date'];
    }
    $order_by = "";
    if(isset($_GET['sort_by']) && isset($_GET['order'])) {
      $sort_by =  $_GET['sort_by'];
      $order =  $_GET['order'];
      $order_by .= "`customers`.`customer_$sort_by` $order";
      if($sort_by == "fullname") {
        $order_by = "`customer_firstname` $order,`customer_lastname` $order";
      }
    }
    else {
      $order = "ASC";
      $sort_by = "fullname";
      $order_by = "`customer_firstname` $order,`customer_lastname` $order";
    }

    $where = false;
    $and_or = false;
    $filter_or_and = "AND";

    /*
     * if $filter_c_has_orders == 1 we gonna query only for customers
     * that have at least one order
     */
    if($filter_c_has_orders == 1) {
      $query_c_with_orders = "SELECT DISTINCT `customer_id` FROM `orders`";
      //echo $query_c_with_orders;
      $result_c_with_orders = mysqli_query($db_link, $query_c_with_orders);
      if(!$result_c_with_orders) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_c_with_orders) > 0) {
        
        $customers_with_orders = "";
        $key = 0;
        while($row = mysqli_fetch_assoc($result_c_with_orders)) {

          $customer_id = $row['customer_id'];
          $customers_with_orders .= ($key == 0) ? $customer_id : ",$customer_id";
          
          $key++;
        }
        $where_customers = "`customers`.`customer_id` IN($customers_with_orders)";
      }
    }
    else {
      $where_customers = "`customers`.`customer_id` IS NOT NULL";
    }
    
    $query_customers = "SELECT `customers`.* FROM `customers` 
                        WHERE $where_customers";
    if(!empty($filter_c_group_id)) {
      $query_customers .= " $filter_or_and (";
      $query_customers .= " `customers`.`customer_group_id` = '$filter_c_group_id'";
      $and_or = true;
    }
    if(isset($filter_c_name) && !empty($filter_c_name)) {
      $query_customers .= ($and_or) ? " $filter_or_and" : " $filter_or_and (";
      $query_customers .= " (`customers`.`customer_firstname` LIKE '%$filter_c_name%' || `customers`.`customer_lastname` LIKE '%$filter_c_name%')";
      $and_or = true;
    }
    if(isset($filter_c_firstname) && !empty($filter_c_firstname)) {
      $query_customers .= ($and_or) ? " $filter_or_and" : " $filter_or_and (";
      $query_customers .= " `customers`.`customer_firstname` LIKE '%$filter_c_firstname%'";
      $and_or = true;
    }
    if(isset($filter_c_lastname) && !empty($filter_c_lastname)) {
      $query_customers .= ($and_or) ? " $filter_or_and" : " $filter_or_and (";
      $query_customers .= " `customers`.`customer_lastname` LIKE '%$filter_c_lastname%'";
      $and_or = true;
    }
    if(isset($filter_c_email) && !empty($filter_c_email)) {
      $query_customers .= ($and_or) ? " $filter_or_and" : " $filter_or_and (";
      $query_customers .= " `customers`.`customer_email` LIKE '%$filter_c_email%'";
      $and_or = true;
    }
    if(isset($filter_c_phone) && !empty($filter_c_phone)) {
      $query_customers .= ($and_or) ? " $filter_or_and" : " $filter_or_and (";
      $query_customers .= " `customers`.`customer_phone` LIKE '%$filter_c_phone%'";
      $and_or = true;
    }
    $query_customers .= ($and_or) ? " $filter_or_and" : " $filter_or_and (";
    $query_customers .= " `customers`.`customer_is_in_mailist` = '$filter_c_is_in_mailist' 
                      $filter_or_and `customers`.`customer_is_blocked` = '$filter_c_is_blocked' 
                      $filter_or_and `customers`.`customer_is_active` = '$filter_c_is_active'";

    if(isset($filter_c_registration_date) && !empty($filter_c_registration_date)) {
      $filter_c_registration_date_db = date("Y-m-d", strtotime($filter_c_registration_date));
      $query_customers .= " $filter_or_and `customers`.`customer_registration_date` = '$filter_c_registration_date_db'";
    }
    $query_customers .= ")";
    $query_customers .= " ORDER BY $order_by";
  }
  else {
    $order_by = "";
    if(isset($_GET['sort_by']) && isset($_GET['order'])) {
      $sort_by =  $_GET['sort_by'];
      $order =  $_GET['order'];
      $order_by .= "`customers`.`customer_$sort_by` $order";
      if($sort_by == "fullname") {
        $order_by = "`customer_firstname` $order,`customer_lastname` $order";
      }
    }
    else {
      $order = "ASC";
      $sort_by = "fullname";
      $order_by = "`customer_firstname` $order,`customer_lastname` $order";
    }
    $query_customers = "SELECT `customers`.* FROM `customers` ORDER BY $order_by";
  }

  $result_customers = mysqli_query($db_link,$query_customers);
  if(!$result_customers) echo mysqli_error($db_link);
  if(!isset($customers_count)) {
    $customers_count = mysqli_num_rows($result_customers);
    $query_customers .= " LIMIT $offset,$page_offset";
    $result_customers = mysqli_query($db_link,$query_customers);
    if(!$result_customers) echo mysqli_error($db_link);
  }
  else {
    $query_customers .= " LIMIT $offset,$page_offset";
    $result_customers = mysqli_query($db_link,$query_customers);
    if(!$result_customers) echo mysqli_error($db_link);
  }
  $customers = array();
  if(mysqli_num_rows($result_customers) > 0) {
    
    // if the results are more then $page_offset
    // making a pagination, finding how many pages will be needed
    $current_page = ($offset/$page_offset)+1;

    if($customers_count > $page_offset) {
      $page_count = ceil($customers_count/$page_offset);
    }

    while($row = mysqli_fetch_assoc($result_customers)) {
      $customers[] = $row;
    }
  }
  //echo "<pre>";print_r($customers);echo "</pre>";
  //echo $query_customers."<br>";
  
  $filter_href = "";
  if(isset($filter_c_name)) $filter_href .= "&filter_c_name=$filter_c_name";
  if(isset($filter_c_group_id)) $filter_href .= "&filter_c_group_id=$filter_c_group_id";
  if(isset($filter_c_email)) $filter_href .= "&filter_c_email=$filter_c_email";
  if(isset($filter_c_phone)) $filter_href .= "&filter_c_phone=$filter_c_phone";
  if(isset($filter_c_has_orders)) $filter_href .= "&filter_c_has_orders=$filter_c_has_orders";
  if(isset($filter_c_is_in_mailist)) $filter_href .= "&filter_c_is_in_mailist=$filter_c_is_in_mailist";
  if(isset($filter_c_is_blocked)) $filter_href .= "&filter_c_is_blocked=$filter_c_is_blocked";
  if(isset($filter_c_is_active)) $filter_href .= "&filter_c_is_active=$filter_c_is_active";
  if(isset($filter_c_registration_date)) $filter_href .= "&filter_c_registration_date=$filter_c_registration_date";
  if(!empty($filter_href)) $filter_href .= "&filter_cs=1";

  print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages['title_breadcrumbs_homepage'];?>"><?=$languages['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages['header_customers_groups'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      <?php 
        //echo"<pre>";print_r($_GET);echo"</pre>";href="'.$page_href.'?offset='.$prev_offset.'&customers_count='.$customers_count.$filter_href.'"
        $page_href = $_SERVER['PHP_SELF'];
        $page_href .= "?customers_count=$customers_count$filter_href";
        $page_href_for_paging = $page_href;
        $page_href_for_sort = "$page_href&offset=$offset";
        if(isset($_GET['sort_by']) && isset($_GET['order'])) {
          $sort_by =  $_GET['sort_by'];
          $order =  $_GET['order'];
          $page_href_for_paging .= "&sort_by=$sort_by&order=$order";
        }
        $link_order_class = mb_convert_case($order, MB_CASE_LOWER, "UTF-8");
      ?>
      <table>
        <thead>
          <tr>
            <th style="width:5%"><?=$languages['btn_save'];?></th>
            <th style="width:15%" class="text_left">
              <a href="<?php echo "$page_href_for_sort&sort_by=fullname&order="; if($sort_by == "fullname") echo ($order == "ASC") ? "DESC" : "ASC"; else echo "ASC";?>" class="<?php if($sort_by == "fullname") echo $link_order_class;?>">
                <?=$languages['header_fullname'];?>
              </a>
            </th>
            <th style="width:10%"><?=$languages['header_customer_group'];?></th>
            <th style="width:16%" class="text_left">
              <a href="<?="$page_href_for_sort&sort_by=email&order="; if($sort_by == "email") echo ($order == "ASC") ? "DESC" : "ASC"; else echo "ASC";?>" class="<?php if($sort_by == "email") echo $link_order_class;?>">
                <?=$languages['header_email'];?>
              </a>
            </th>
            <th style="width:10%" class="text_left">
              <a href="<?="$page_href_for_sort&sort_by=phone&order="; if($sort_by == "phone") echo ($order == "ASC") ? "DESC" : "ASC"; else echo "ASC";?>" class="<?php if($sort_by == "phone") echo $link_order_class;?>">
                <?=$languages['header_phone'];?>
              </a>
            </th>
            <th style="width:4%"><?=$languages['header_orders'];?></th>
            <th style="width:10%"><?=$languages['header_is_in_mailist'];?></th>
            <th style="width:5%"><?=$languages['header_is_blocked'];?></th>
            <th style="width:5%"><?=$languages['header_is_active'];?></th>
            <th style="width:10%">
              <a href="<?="$page_href_for_sort&sort_by=registration_date&order="; if($sort_by == "registration_date") echo ($order == "ASC") ? "DESC" : "ASC"; else echo "ASC";?>" class="<?php if($sort_by == "registration_date") echo $link_order_class;?>">
                <?=$languages['header_registration_date'];?>
              </a>
            </th>
            <th style="width:10%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <form method="get" name="filter_cs_form" action="<?=$_SERVER['PHP_SELF'];?>">
        <table>
          <tbody>
            <tr class="filter">
              <td style="width:5%">
              </td>
              <td style="width:15%" class="text_left"><input type="text" name="filter_c_name" id="filter_c_name" value="<?php if(isset($filter_c_name)) echo $filter_c_name;?>" /></td>
              <td style="width:10%">
                <select name="filter_c_group_id" id="filter_c_group_id">
                  <option value="0"></option>
<?php
                  if(count($customers_groups) > 0) {
                    foreach($customers_groups as $customers_group) {

                      $customer_group_id = $customers_group['customer_group_id'];
                      $customer_group_translation_text = $customers_group['customer_group_translation_text'];
                      if(isset($filter_c_group_id)) {
                        $class_selected = ($customer_group_id == $filter_c_group_id) ? 'selected="selected"' : "";
                      }
                      else $class_selected = "";

                      echo "<option value='$customer_group_id' $class_selected>$customer_group_translation_text</option>";
                    }
                  }
?>
                </select>
              </td>
              <td style="width:16%" class="text_left">
                <input type="text" name="filter_c_email" id="filter_c_email" value="<?php if(isset($filter_c_email)) echo $filter_c_email;?>" />
              </td>
              <td style="width:10%" class="text_left">
                <input type="text" name="filter_c_phone" id="filter_c_phone" value="<?php if(isset($filter_c_phone)) echo $filter_c_phone;?>" />
              </td>
              <td style="width:4%">
                <div class="checkbox">
                  <input type="checkbox" name="filter_c_has_orders" id="filter_c_has_orders" onClick="Checkbox(this)" <?php if(isset($filter_c_has_orders) && $filter_c_has_orders == 1) echo 'checked="checked"';?> />
                </div>
              </td>
              <td style="width:10%">
                <div class="checkbox">
                  <input type="checkbox" name="filter_c_is_in_mailist" id="filter_c_is_in_mailist" onClick="Checkbox(this)" <?php if(isset($filter_c_is_in_mailist) && $filter_c_is_in_mailist == 1) echo 'checked="checked"';?> />
                </div>
              </td>
              <td style="width:5%">
                <div class="checkbox">
                  <input type="checkbox" name="filter_c_is_blocked" id="filter_c_is_blocked" onClick="Checkbox(this)" <?php if(isset($filter_c_is_blocked) && $filter_c_is_blocked == 1) echo 'checked="checked"';?> />
                </div>
              </td>
              <td style="width:5%">
                <div class="checkbox">
                  <input type="checkbox" name="filter_c_is_active" id="filter_c_is_active" onClick="Checkbox(this)" <?php if(isset($filter_c_is_active)) { if($filter_c_is_active == 1) echo 'checked="checked"';} else echo 'checked="checked"';?> />
                </div>
              </td>
              <td style="width:10%"><input type="text" name="filter_c_registration_date" id="filter_c_registration_date" class="datepicker" value="<?php if(isset($filter_c_registration_date)) echo $filter_c_registration_date;?>" /></td>
              <td style="width:10%" colspan="2">
                <button type="submit" name="filter_cs" class="button green"><?=$languages['btn_filter'];?></button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <input type="hidden" id="current_page" value="<?=$_SERVER['PHP_SELF'];?>">
      <div id="customers_list" class="margin_bottom">
<?php
      if(count($customers) > 0) {
        foreach($customers as $customer) {
          
          $customer_id = $customer['customer_id'];
          $current_customer_group_id = $customer['customer_group_id'];
          $customer_firstname = $customer['customer_firstname'];
          $customer_lastname = $customer['customer_lastname'];
          $customer_email = $customer['customer_email'];
          $customer_phone = $customer['customer_phone'];
          $customer_is_in_mailist = $customer['customer_is_in_mailist'];
          $customer_is_blocked = $customer['customer_is_blocked'];
          $customer_is_active = $customer['customer_is_active'];
          $customer_registration_date = date("m-d-Y",  strtotime($customer['customer_registration_date']));
          $customer_has_orders_text = $languages['no'];
          $customer_has_orders = false;
          
          if(!isset($filter_c_has_orders) || (isset($filter_c_has_orders) && $filter_c_has_orders == 0)) {
            $query_customer_orders = "SELECT `order_id`  
                                      FROM `orders` 
                                      WHERE `customer_id` = '$customer_id' LIMIT 1";
            //echo $query_customer_orders;
            $result_customer_orders = mysqli_query($db_link, $query_customer_orders);
            if(!$result_customer_orders) echo mysqli_error($db_link);
            if(mysqli_num_rows($result_customer_orders) > 0) {
              $customer_has_orders_text = $languages['yes'];
              $customer_has_orders = true;
            }
          }
          else {
            $customer_has_orders_text = $languages['yes'];
            $customer_has_orders = true;
          }
          
          if(!isset($class)) $class = "even";
          $class = (($class == "odd") ? "even" : "odd");
?>
        <div id="customer_<?=$customer_id;?>" class="row_over">
          <table>
            <tr class="<?=$class;?>">
              <td style="width:5%">
                <button class="btn_save" onClick="EditCustomer('<?=$customer_id;?>')">
                  <?=$languages['btn_save'];?>
                </button>
              </td>
              <td style="width:15%" class="text_left"><?="$customer_firstname $customer_lastname";?></td>
              <td style="width:10%">
                <select name="select_customer_group_id" class="select_customer_group_id">
<?php
                if(count($customers_groups) > 0) {
                  foreach($customers_groups as $customers_group) {

                    $customer_group_id = $customers_group['customer_group_id'];
                    $customer_group_translation_text = $customers_group['customer_group_translation_text'];
                    $class_selected = ($customer_group_id == $current_customer_group_id) ? 'selected="selected"' : "";

                    echo "<option value='$customer_group_id' $class_selected>$customer_group_translation_text</option>";
                  }
                }
?>
                </select>
              </td>
              <td style="width:16%" class="text_left"><?=$customer_email;?></td>
              <td style="width:10%" class="text_left"><?=$customer_phone;?></td>
              <td style="width:4%"><?=$customer_has_orders_text;?></td>
              <td style="width:10%">
                <div class="checkbox<?php if ($customer_is_in_mailist == 1) echo ' checkbox_checked';?>">
                  <input type="checkbox" name="customer_is_in_mailist" class="customer_is_in_mailist" onClick="Checkbox(this)" <?php if ($customer_is_in_mailist == 1) echo 'checked="checked"';?> />
                </div>
              </td>
              <td style="width:5%">
                <div class="checkbox<?php if ($customer_is_blocked == 1) echo ' checkbox_checked';?>">
                  <input type="checkbox" name="customer_is_blocked" class="customer_is_blocked" onClick="Checkbox(this)" <?php if ($customer_is_blocked == 1) echo 'checked="checked"';?> />
                </div>
              </td>
              <td style="width:5%">
                <div class="checkbox<?php if ($customer_is_active == 1) echo ' checkbox_checked';?>">
                  <input type="checkbox" name="customer_is_active" class="customer_is_active" onClick="Checkbox(this)" <?php if ($customer_is_active == 1) echo 'checked="checked"';?> />
                </div>
              </td>
              <td style="width:10%"><?=$customer_registration_date;?></td>
              <td style="width:5%">
                <button class="toggle_user_details btn_toggle" onclick="ToggleCustomerDetails('<?=$customer_id;?>')">&plus;</button>
              </td>
              <td style="width:5%">
                <a href="javascript:;" class="delete_customer_link" data-id="<?=$customer_id;?>">
                  <img src="/_admin/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" style="width:16" height="16" />
                </a>
              </td>
            </tr>
          </table>
        </div>
        <div id="customer_details_<?=$customer_id;?>" class="customer_details" style="display:none;">
<?php
      if($customer_has_orders) {
        $query_customer_orders = "SELECT `orders`.`order_id`,`orders`.`order_total`,`orders`.`order_status_id`,`orders`.`order_date_added`,`currency`.`currency_symbol_left`,
                                          `currency`.`currency_symbol_right`,`order_statuses`.`order_status_name`   
                                  FROM `orders` 
                                  INNER JOIN `order_statuses` ON `order_statuses`.`language_id` = '$current_language_id' AND `order_statuses`.`order_status_id` = `orders`.`order_status_id`
                                  INNER JOIN `currency` ON `currency`.`currency_id` = `orders`.`currency_id`
                                  WHERE `customer_id` = '$customer_id'";
        //echo $query_customer_orders;
        $result_customer_orders = mysqli_query($db_link, $query_customer_orders);
        if(!$result_customer_orders) echo mysqli_error($db_link);
        $customer_orders_count = mysqli_num_rows($result_customer_orders);
        if($customer_orders_count > 0) {
?>
          <table>
            <thead>
              <tr>
                <th><?=$languages['header_number'];?></th>
                <th><?=$languages['header_date'];?></th>
                <th><?=$languages['header_status'];?></th>
                <th><?=$languages['header_summary'];?></th>
              </tr>
            </thead>
            <tbody>
<?php
          while($customer_order = mysqli_fetch_assoc($result_customer_orders)) {

            $order_id = $customer_order['order_id'];
            $order_status_id = $customer_order['order_status_id'];
            $status_class = ($order_status_id == 5) ? ' class="complete"' : "";
            $order_status_name = $customer_order['order_status_name'];
            $order_total_format = number_format($customer_order['order_total'],2,".",".");
            $order_date_added = date("d.m.Y",strtotime($customer_order['order_date_added']));
            $currency_symbol_left = $customer_order['currency_symbol_left'];
            $currency_symbol_right = $customer_order['currency_symbol_right'];
            $order_total = $currency_symbol_left.$order_total_format.$currency_symbol_right;
?>
            <tr>
              <td><?=$order_id;?></td>
              <td><?=$order_date_added;?></td>
              <td<?=$status_class;?>><?=$order_status_name;?></td>
              <td><?=$order_total;?></td>
            </tr>
<?php
        }
?>
            </tbody>
          </table>
<?php
        } //if($customer_orders_count > 0)
      } // $customer_has_orders
?> 
<!--          <table>
            <thead>
              <tr>
                <th style="width:5%"><?=$languages['header_user_logs'];?></th>
              </tr>
            </thead>
            <tbody>
              <td style="width:5%">
                <button class="get_user_log button green" onclick="GetCustomerLog('<?=$customer_id;?>')">
                  <i class="icon icon_open_sign"></i>
                </button>
              </td>
            </tbody>
          </table>-->
        </div>
<?php
        } // foreach($customers as $customer)
        
        // if the results are more then $page_offset make pagination
        if(isset($page_count) && $page_count > 1) {
?>
        <div class="text_centered">
          <ul id="pagination_<?=$category_id;?>" class="php_pagination pagination">
<?php
            $pages = 1;
            $current_offset = $offset;
            $offset = 0;
            $links_arround_current = 4;
            $is_gap = false;

            if($current_page == 1) {
              echo '<li class="disabled btn_prev_page"><a href="javascript:;" data="">&laquo;</a></li>';
            }
            else {
              $prev_offset = $current_offset - $page_offset;
              echo "<li class='btn_prev_page'><a href='$page_href_for_paging&offset=$prev_offset'>&laquo;</a></li>";
            }

            for($page = 1; $page < $page_count; $page++) { // Run through pages
              $is_gap = false;
              
              // Are we at a gap?
              // If beyond "$links_arround_current" and not first or last
              if($links_arround_current >= 0 && $page > 1 && $page < $page_count - 1 && abs($page - $current_page) > $links_arround_current) { 
                
                $is_gap    = true;

                // Skip to next linked item (or last if we've already run past the current page)
                $page = ($page < $current_page) ? $current_page - $links_arround_current : $page_count - 1;
                $page = $page-1;
              }

              $offset = ($page_offset*$page) - $page_offset;
              $link = ($is_gap ? '...' : ($page)); // If gap, write ellipsis, else page number
              if($page != $current_page && !$is_gap) {
                echo "<li id='pag_$page'><a href='$page_href_for_paging&offset=$offset'>$link</a></li>";
              }
              else { // Do not link gaps and current
                $li_class = ($current_page == $page) ? "active" : "disabled";
                $a_class = ($current_page == $page) ? "" : ' class="disabled"';
                
                echo "<li class='$li_class'><a href='javascript:;'$a_class>$link</a></li>";
              }
            }
     
            if($current_page == $page_count) {
              echo '<li class="disabled btn_next_page"><a href="javascript:;" data="">&raquo;</a></li>';
            }
            else {
              $next_offset = $current_offset + $page_offset;
              echo "<li class='btn_next_page'><a href='$page_href_for_paging&offset=$next_offset'>&laquo;</a></li>";
            }
?>
          </ul>
        </div>
<?php
        }
        
      } //if(count($customers) > 0) 
      else {
        echo "<h2>".$languages['text_no_customers_with_choosen_filters']."</h2>";
      }
?>
      </div>
      <!--modal_confirm-->
      <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['are_you_sure'];?>">
        <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_customer'];?></p>
      </div>
      <script>
      $(function() {
        $(".datepicker").datepicker({ dateFormat: "dd-mm-yy" });
        $("#modal_confirm").dialog({
          resizable: false,
          width: 400,
          height: 200,
          autoOpen: false,
          modal: true,
          draggable: false,
          closeOnEscape: true,
          dialogClass: "modal_confirm",
          buttons: {
            "<?=$languages['btn_delete'];?>": function() {
              DeleteCustomer();
              $(this).dialog("close");
            },
            "<?=$languages['btn_cancel'];?>": function() {
              $(".delete_customer_link").removeClass("active");
              $(this).dialog("close");
            }
          }
        });
        $(".delete_customer_link").click(function() {
          $(".delete_customer_link").removeClass("active");
          $(this).addClass("active");
          $("#modal_confirm").dialog("open");
        });
      });
      </script>
      <div class="clearfix"></div>
    </div>
    <?php //close_page_build_time_measure($print_time = true);?>
  </main>
<!--contents list-->

<?php
  
  print_html_admin_footer();
  
?>
</body>
</html>