<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
    
  $page_title = $languages['customers_groups_title'];
  $page_description = "";

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
    $filter_c_is_in_mailist = 0;
    $filter_c_is_blocked = 0;
    $filter_c_is_active = 0;
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
  
//  $result_customers = mysqli_query($db_link,$query_customers);
//  if(!$result_customers) echo mysqli_error($db_link);
//  if(!isset($customers_count)) {
//    $customers_count = mysqli_num_rows($result_customers);
//    $query_customers .= " LIMIT $offset,$page_offset";
//    $result_customers = mysqli_query($db_link,$query_customers);
//    if(!$result_customers) echo mysqli_error($db_link);
//  }
//  else {
//    $query_customers .= " LIMIT $offset,$page_offset";
//    $result_customers = mysqli_query($db_link,$query_customers);
//    if(!$result_customers) echo mysqli_error($db_link);
//  }
//  $customers = array();
//  if(mysqli_num_rows($result_customers) > 0) {
//    
//    // if the results are more then $page_offset
//    // making a pagination, finding how many pages will be needed
//    $current_page = ($offset/$page_offset)+1;
//
//    if($customers_count > $page_offset) {
//      $page_count = ceil($customers_count/$page_offset);
//    }
//
//    while($row = mysqli_fetch_assoc($result_customers)) {
//      $customers[] = $row;
//    }
//  }
  //echo "<pre>";print_r($customers);echo "</pre>";
  //echo $query_customers."<br>";
  
  $filter_href = "";
  if(isset($filter_c_name)) $filter_href .= "&filter_c_name=$filter_c_name";
  if(isset($filter_c_group_id)) $filter_href .= "&filter_c_group_id=$filter_c_group_id";
  if(isset($filter_c_email)) $filter_href .= "&filter_c_email=$filter_c_email";
  if(isset($filter_c_phone)) $filter_href .= "&filter_c_phone=$filter_c_phone";
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
      <!--begin of left_col-->
      <div id="left_column">
        <table id="choose_customer_group" class="list_container margin_bottom">
          <thead>
            <tr><th><?=$languages['header_choose_customer_group'];?></th></tr>
          </thead>
          <tbody>
<?php
          $query_customers_groups = "SELECT `customers_groups`.`customer_group_id`,`customers_groups`.`customer_group_code`,`cgl`.`customer_group_name`
                                       FROM `customers_groups`
                                 INNER JOIN `customers_groups_languages` as `cgl` ON `cgl`.`customer_group_id` = `customers_groups`.`customer_group_id`
                                      WHERE `cgl`.`language_id` = '$current_language_id'
                                   ORDER BY `customers_groups`.`customer_group_sort_order` ASC";
          $result_customers_groups = mysqli_query($db_link,$query_customers_groups);
          if(!$result_customers_groups) echo mysqli_error($db_link);
          $customer_groups_count = mysqli_num_rows($result_customers_groups);
          if(mysqli_num_rows($result_customers_groups) > 0) {

            $key = 0;
            $class = ($key == 0) ? "even" : "$class";

            while($customers_groups = mysqli_fetch_assoc($result_customers_groups)) {

              $customer_group_id = $customers_groups['customer_group_id'];
              $customer_group_code = $customers_groups['customer_group_code'];
              $customer_group_name = stripslashes($customers_groups['customer_group_name']);

             echo "<tr><td class='text_left'><a data-id='$customer_group_id' data-code='$customer_group_code' class='red_link'>$customer_group_name</a></td></tr>";
            }
          }
          else {   
?>
            <tr><td><?=$languages['no_customer_types_yet'];?></td></tr>
<?php    
          }
?>
          </tbody>
        </table>
      </div>
      <!--end of left_col-->

      <div id="right_column" class="list_container" style="display: none;">
        <table>
          <thead>
            <tr>
              <th style="width:15%" class="text_left">
                <a href="<?php echo "$page_href_for_sort&sort_by=fullname&order="; if($sort_by == "fullname") echo ($order == "ASC") ? "DESC" : "ASC"; else echo "ASC";?>" class="<?php if($sort_by == "fullname") echo $link_order_class;?>">
                  <?=$languages['header_fullname'];?>
                </a>
              </th>
              <th style="width:15%" class="text_left">
                <a href="<?="$page_href_for_sort&sort_by=email&order="; if($sort_by == "email") echo ($order == "ASC") ? "DESC" : "ASC"; else echo "ASC";?>" class="<?php if($sort_by == "email") echo $link_order_class;?>">
                  <?=$languages['header_email'];?>
                </a>
              </th>
              <th style="width:10%" class="text_left">
                <a href="<?="$page_href_for_sort&sort_by=phone&order="; if($sort_by == "phone") echo ($order == "ASC") ? "DESC" : "ASC"; else echo "ASC";?>" class="<?php if($sort_by == "phone") echo $link_order_class;?>">
                  <?=$languages['header_phone'];?>
                </a>
              </th>
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
                <td style="width:15%" class="text_left"><input type="text" name="filter_c_name" id="filter_c_name" value="<?php if(isset($filter_c_name)) echo $filter_c_name;?>" /></td>
                <td style="width:15%" class="text_left">
                  <input type="text" name="filter_c_email" id="filter_c_email" value="<?php if(isset($filter_c_email)) echo $filter_c_email;?>" />
                </td>
                <td style="width:10%" class="text_left">
                  <input type="text" name="filter_c_phone" id="filter_c_phone" value="<?php if(isset($filter_c_phone)) echo $filter_c_phone;?>" />
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

        </div>
        <script type="text/javascript">
          $(document).ready(function() {
            $("#choose_customer_group a").click(function() {
              $("#choose_customer_group td").removeClass("selected_customer_group")
              $(this).parent().addClass("selected_customer_group");
              GetCustomersForGroup();
            });
          });
        </script>
        <div class="clearfix"></div>
        
      </div>
      <!--id="right_column"-->
      
    </div>
    <?php //close_page_build_time_measure($print_time = true);?>
  </main>
<!--contents list-->

<?php
  
  print_html_admin_footer();
  
?>
</body>
</html>