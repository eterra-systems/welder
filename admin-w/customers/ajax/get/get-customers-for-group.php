<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf_in_reports();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['customer_group_id'])) {
    $customer_group_id = $_POST['customer_group_id'];
  }
  if(isset($_POST['customer_group_code'])) {
    $customer_group_code = $_POST['customer_group_code'];
  }

  
  $cg_table = "customers_$customer_group_code";
  $query_customers = "SELECT `customers`.*,`$cg_table`.*
                        FROM `customers`
                  INNER JOIN `$cg_table` ON `$cg_table`.`customer_id` = `customers`.`customer_id`
                       WHERE `customers`.`customer_group_id` = '$customer_group_id'
                    ORDER BY `$cg_table`.`first_name` ASC, `$cg_table`.`last_name` ASC";
  //echo $query_customers;
  $result_customers = mysqli_query($db_link,$query_customers);
  if (!$result_customers) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customers) > 0) {
    
    while($customer_details = mysqli_fetch_assoc($result_customers)) {
      
      $customer_id = $customer_details['customer_id'];
      $customer_phone = $customer_details['customer_phone'];
      $customer_email = $customer_details['customer_email'];
      $customer_is_in_mailist = $customer_details['customer_is_in_mailist'];
      $customer_is_blocked = $customer_details['customer_is_blocked'];
      $customer_registration_date = $customer_details['customer_registration_date'];
      $customer_firstname = $customer_details['first_name'];
      $customer_lastname = $customer_details['last_name'];
      $customer_customername = "$customer_firstname $customer_lastname";
      $customer_is_active = $customer_details['customer_is_active'];
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? " even" : " odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/customers/customers-details.php?customer_id=$customer_id";
?>
    <form method="post" name="edit_customers_rights_<?=$customer_id; ?>" action="javascript:;">
      <div id="customer<?=$customer_id; ?>">
        <table>
          <tbody>
            <tr class="row_over<?=$class;?>">
              <td width="15%" class="text_left"><?=$customer_customername;?></td>
              <td width="15%" class="text_left"><?=$customer_email;?></td>
              <td width="10%" class="text_left"><?=$customer_phone;?></td>
              <td width="10%">
                <div class="checkbox<?php if ($customer_is_in_mailist == 1) echo ' checkbox_checked'; ?>">
                  <input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" onClick="Checkbox(this)" <?php if ($customer_is_in_mailist == 1) echo 'checked="checked"'; ?> />
                </div>
              </td>
              <td width="5%">
                <div class="checkbox<?php if ($customer_is_blocked == 1) echo ' checkbox_checked'; ?>">
                  <input type="checkbox" name="customer_is_blocked" id="customer_is_blocked" onClick="Checkbox(this)" <?php if ($customer_is_blocked == 1) echo 'checked="checked"'; ?> />
                </div>
              </td>
              <td width="5%">
                <div class="checkbox<?php if ($customer_is_active == 1) echo ' checkbox_checked'; ?>">
                  <input type="checkbox" name="customer_is_active" id="customer_is_active" onClick="Checkbox(this)" <?php if ($customer_is_active == 1) echo 'checked="checked"'; ?> />
                </div>
              </td>
              <td width="10%"><?=$customer_registration_date;?></td>
              <td width="5%">
                <a href="<?=$edit_link;?>" class="edit_link">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
                </a>
              </td>
              <td width="5%">
                <a href="javascript:;" class="delete_customer_link" data-id="<?=$customer_id;?>">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </form>
<?php
    }
    mysqli_free_result($result_customers);
  }
?>
  <p></p>
  
  <!--modal_confirm-->
  <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_customer']?></p>
    <input type="hidden" id="cannnot_delete_admin" value="<?=$languages['warning_cannnot_delete_admin']?>" />
  </div>
  <script>
  $(function() {
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
    $(".access_rights").click(function() {
        var customer_id = $(this).attr("button-id");
        if($(".details"+customer_id).hasClass("access_rights_edit")) {
          $(".customers_details").removeClass("access_rights_edit");
        } else {
          $(".customers_details").removeClass("access_rights_edit");
          $(".details"+customer_id).addClass("access_rights_edit");
        }
      });
      $(".menu_header").click(function() {
        if($(this).hasClass("active_header")) {
          var header_id = $(this).attr("button-id");
          $(this).html("+");
          $(this).removeClass("active_header")
          $(".children"+header_id).hide();
        }
        else {
          $(".menu_header").removeClass("active_header");
          $(this).addClass("active_header");
          $(this).html("-");
          var header_id = $(this).attr("button-id");
          $(".children").hide();
          $(".children"+header_id).show();
        }
      });
  });
  </script>