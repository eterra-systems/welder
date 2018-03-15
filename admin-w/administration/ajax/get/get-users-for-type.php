<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf_in_reports();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['user_type_id'])) {
    $user_type_id = $_POST['user_type_id'];
  }
  if(isset($_POST['user_type'])) {
    $user_type = $_POST['user_type'];
  }

  $query = "SELECT `users`.`user_id`, `users`.`user_username`, `users`.`user_is_active`, `users`.`user_is_ip_in_use`, 
                   `users`.`user_firstname`,`users`.`user_lastname`,`users_types`.`user_type_is_superuser`
              FROM `users`
        INNER JOIN `users_types` ON `users_types`.`user_type_id` = `users`.`user_type_id`
             WHERE `users`.`user_type_id` = '$user_type_id'
          ORDER BY `users`.`user_firstname` ASC";
  //echo $query;exit;
  $users_result = mysqli_query($db_link, $query);
  if (!$users_result) echo mysqli_error($db_link);
  if(mysqli_num_rows($users_result) > 0) {
  $key = 0;
    while ($user_details = mysqli_fetch_assoc($users_result)) {
      $user_id = $user_details['user_id'];
      $user_type_is_superuser = $user_details['user_type_is_superuser'];
      $user_username = $user_details['user_username'];
      $user_firstname = $user_details['user_firstname'];
      $user_lastname = $user_details['user_lastname'];
      $user_is_ip_in_use = $user_details['user_is_ip_in_use'];
      $user_is_active = $user_details['user_is_active'];
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? " even" : " odd");
      $class = ((($key % 2) == 1) ? " even" : " odd");
?>
    <form method="post" name="edit_users_rights_<?=$user_id; ?>" action="javascript:;">
      <div id="user<?=$user_id; ?>">
        <table>
          <tbody>
            <tr class="row_over<?=$class;?>">
              <td width="5%">
                <a href="javascript:;" class="btn_save" onClick="EditUserRights('<?=$user_id; ?>')"><?=$languages['btn_save'];?></a>
              </td>
              <td width="15%"><input type="text" name="user_username" id="user_username" class="user_username" value="<?=$user_username; ?>" ></td>
              <td width="10%"><input type="password" name="user_password" id="user_password" class="user_password" placeholder="******" ></td>
              <td width="10%"><?=$user_firstname; ?></td>
              <td width="10%"><?=$user_lastname; ?></td>
              <td width="9%"><a href="javascript:;" class="access_rights button blue" button-id="<?=$user_id; ?>">Access rights</a></td>
              <td width="5%">
                <div class="checkbox<?php if ($user_is_active == 1) echo ' checkbox_checked'; ?>">
                  <input type="checkbox" name="user_is_active" id="user_is_active" onClick="Checkbox(this)" <?php if ($user_is_active == 1) echo 'checked="checked"'; ?> />
                </div>
              </td>
              <td width="5%"><a href="javascript:;" class="get_user_log button blue" button-id="<?=$user_id; ?>" onclick="GetUserLog(<?=$user_id; ?>)">Check</a></td>
              <td width="5%">
                <div class="checkbox<?php if ($user_is_ip_in_use == 1) echo ' checkbox_checked'; ?>">
                  <input type="checkbox" name="user_is_ip_in_use" id="user_is_ip_in_use" onClick="Checkbox(this)" <?php if ($user_is_ip_in_use == 1) echo 'checked="checked"'; ?> />
                </div>
              </td>
              <td width="5%"><a href="javascript:;" class="reset_ip button blue" button-id="<?=$user_id; ?>" onclick="ResetIP(<?=$user_id; ?>)">Reset</a></td>
              <td width="5%">
                <a href="javascript:;" class="delete_user_link" data-id="<?=$user_id;?>" data-su="<?=$user_type_is_superuser;?>">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
                </a>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="users_details details<?=$user_id; ?>">
          
          <table>
            <thead>
              <tr>
                <th width="5%"></th>
                <th width="20%" style="text-align: left;"><?=$languages['header_user_rights_page'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_access'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_add'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_edit'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_delete'];?></th>
                <th width="15%"><?=$languages['header_user_rights_page_subpages'];?></th>
              </tr>
            </thead>
            <tbody>
              <?php list_user_menu_rights($menu_id = 0, $user_id);?>
            </tbody>
          </table>
          
        </div>
      </div>
    </form>
<?php
      $key++;
    }
    mysqli_free_result($users_result);
  }
?>
  <p></p>
  
  <!--modal_confirm-->
  <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_user']?></p>
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
          DeleteUser();
        },
        "<?=$languages['btn_cancel'];?>": function() {
          $(".delete_user_link").removeClass("active");
          $(this).dialog("close");
        }
      }
    });
    $(".delete_user_link").click(function() {
      $(".delete_user_link").removeClass("active");
      $(this).addClass("active");
      $("#modal_confirm").dialog("open");
    });
    $(".access_rights").click(function() {
        var user_id = $(this).attr("button-id");
        if($(".details"+user_id).hasClass("access_rights_edit")) {
          $(".users_details").removeClass("access_rights_edit");
        } else {
          $(".users_details").removeClass("access_rights_edit");
          $(".details"+user_id).addClass("access_rights_edit");
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