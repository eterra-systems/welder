<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['language_id'])) {
    $current_language_id = $_GET['language_id'];
    
    include_once 'language-details.php';
  }
  else {
    
    $page_title = $languages['customers_groups_title'];
    $page_description = "";

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
      
      <section class="contents_options">
        <a class="pageoptions add_new_link" href="customers-groups-add-new.php">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages['text_add_new_users_type'];?>" />
          <?=$languages['text_add_new_users_type'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="60%" class="text_left"><?=$languages['header_name'];?></th>
            <th width="20%"><?=$languages['header_reorder'];?></th>
            <th width="20%" colspan="2"><?=$languages['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <input type="hidden" id="current_page" value="<?=$_SERVER['PHP_SELF'];?>">
      <div id="customers_groups_list" class="list_container">
<?php

  $query_customers_groups = "SELECT `customers_groups`.`customer_group_id`,`customers_groups`.`customer_group_sort_order`,`cgl`.`customer_group_name`
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
      $customer_group_name = stripslashes($customers_groups['customer_group_name']);
      $customer_group_sort_order = $customers_groups['customer_group_sort_order'];
      $edit_link = "customers-groups-details.php?customer_group_id=$customer_group_id";
      $class = (($class == "odd") ? "even" : "odd");
?>
      <table id="customer_group_<?=$customer_group_id;?>" class="row_over">
        <tbody>
          <tr class="<?=$class?>">
            <td width="60%" class="text_left">
              <span class="red_link"><?=$customer_group_name;?></span>
            </td>
            <td width="20%">
              <?php
                // if($customer_groups_count > 1) we gonna give the appropriate moving options
                // else we gonna leave this empty
                if($customer_groups_count > 1) {
                  if($key == 0) {
              ?>
                  <a href="javascript:;" onclick="MoveCustomerGroupForwardBackward('<?=$customer_group_id;?>','<?=$customer_group_sort_order;?>','backward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                  </a>
                <?php } elseif($key == $customer_groups_count-1) { ?>
                  <a href="javascript:;" onclick="MoveCustomerGroupForwardBackward('<?=$customer_group_id;?>','<?=$customer_group_sort_order;?>','forward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                  </a>
                <?php } else { ?>
                  <a href="javascript:;" onclick="MoveCustomerGroupForwardBackward('<?=$customer_group_id;?>','<?=$customer_group_sort_order;?>','backward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                  </a>
                  <a href="javascript:;" onclick="MoveCustomerGroupForwardBackward('<?=$customer_group_id;?>','<?=$customer_group_sort_order;?>','forward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                  </a>
              <?php 
                  }
                } // if($customer_groups_count > 1)
              ?>
            </td>
            <td width="10%">
              <a href="<?=$edit_link;?>" class="edit_link">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="10%">
              <a href="javascript:;" class="delete_customer_group delete_link" data-id="<?=$customer_group_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
              </a>
            </td>
          </tr>
        </tbody>
      </table>
<?php
      $key++;
    }
    mysqli_free_result($result_customers_groups);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
      <!--modal_confirm-->
      <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['are_you_sure']?>">
        <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_customer_group']?></p>
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
              DeleteCustomerGroup();
            },
            "<?=$languages['btn_cancel'];?>": function() {
              $(".delete_customer_group_link").removeClass("active");
              $(this).dialog("close");
            }
          }
        });
        $(".delete_customer_group_link").click(function() {
          $(".delete_customer_group_link").removeClass("active");
          $(this).addClass("active");
          $("#modal_confirm").dialog("open");
        });
      });
      </script>
<?php
    }
  }
?>
      <div class="clearfix"></div>
    </div>
  </main>
<!--contents list-->

<?php
  
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }