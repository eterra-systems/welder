<?php
  
function print_html_login_header($page_title,$page_description) {
  
  global $languages;
  global $current_lang;
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$page_title; ?></title>
    <meta name="description" content="<?=$page_description; ?>" />
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="author" content="Eterrasystems Ltd.">
    <link href="<?=SITEFOLDERSL;?>/images/ico/favicon.png" rel="shortcut icon">
    <link href="/<?=$_SESSION['admin_dir_name'];?>/css/mainstyle.css" rel="stylesheet" type="text/css"  media="screen" />
</head>
<body>
<?php    
}
  
function print_html_admin_header($page_title,$page_description = "",$additional_css = false,$additional_script = false) {
  
  global $db_link;
  global $languages;
  global $current_lang;
    
  if(!isset($_SESSION['admin']['user_id']) || (isset($_SESSION['admin']['user_id']) && empty($_SESSION['admin']['user_id']))) {
    header('Location: /'.$_SESSION['admin_dir_name']);
  }
  
  $_SESSION['users_rights_add'] = 0;
  $_SESSION['users_rights_edit'] = 0;
  $_SESSION['users_rights_delete'] = 0;
  $user_id = $_SESSION['admin']['user_id'];

  $url_exploded = explode("/", substr($_SERVER['PHP_SELF'], 1));
  $_SESSION['admin_dir_name'] = array_shift($url_exploded);
  $menu_directory = array_shift($url_exploded);
  if($menu_directory != "index.php") {
    //echo $url_exploded[0];
    $current_url = str_replace(array("-add-new","-copy-from-existing","-reorder","-details"), array("","","",""), $url_exploded[0]);
    $menu_url = "/$menu_directory/$current_url";
    $user_rights = get_admin_user_rights($menu_url);
    //print_r($user_rights);
    $_SESSION['users_rights_add'] = $user_rights['users_rights_add'];
    $_SESSION['users_rights_edit'] = $user_rights['users_rights_edit'];
    $_SESSION['users_rights_delete'] = $user_rights['users_rights_delete'];

    $_SESSION['add_link_fn'] = ($_SESSION['users_rights_add'] == 0) ? 'onclick="NoRightsToAdd();return false;"' : "";
    $_SESSION['edit_link_fn'] = ($_SESSION['users_rights_edit'] == 0) ? 'onclick="NoRightsToEdit();return false;"' : "";
    $_SESSION['delete_link_fn'] = ($_SESSION['users_rights_delete'] == 0) ? 'onclick="NoRightsToDelete();return false;"' : "";
  }
  
  $body_nav_class = "opened_nav";
  if(isset($_COOKIE['nav'])) $body_nav_class = $_COOKIE['nav'];
  //echo "<pre>";print_r($_SESSION);
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$page_title;?></title>
    <meta name="description" content="<?=$page_description;?>" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width">
    <meta name="robots" content="noindex, nofollow" />
    <meta name="author" content="Eterrasystems Ltd.">
    <link href="<?=SITEFOLDERSL;?>/images/ico/favicon.png" rel="shortcut icon">
    <link href="/<?=$_SESSION['admin_dir_name'];?>/css/bootstrap.min.css" rel="stylesheet" type="text/css"  media="screen" />
    <link href="/<?=$_SESSION['admin_dir_name'];?>/css/mainstyle.css" rel="stylesheet" type="text/css"  media="screen" />
    <link href="/<?=$_SESSION['admin_dir_name'];?>/css/responsive.css" rel="stylesheet" type="text/css"  media="screen" />
    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"  media="screen" />
    <link href="/js/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css" />
    <?php if($additional_css) echo "$additional_css\n";?>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script type="text/javascript">
      var admin_dir_name = '<?=$_SESSION['admin_dir_name'];?>';
      var base_url = '<?=PROTOCOL.DOMAIN."/";?>';
    </script>
    <script src="/js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <script src="/<?=$_SESSION['admin_dir_name'];?>/js/dropzone.min.js" type="text/javascript"></script>
    <script src="/<?=$_SESSION['admin_dir_name'];?>/js/functions.js" type="text/javascript"></script>
    <?php if($additional_script) echo "$additional_script\n";?>
</head>
<body class="<?=$body_nav_class;?>">
  <div id="modal_window_backgr"></div>
  <div id="modal_window"></div>
  <div id="ajax_loader_backgr"></div>
  <div id="ajax_loader">
    <div class="sk-cube-grid">
      <div class="sk-cube sk-cube1"></div>
      <div class="sk-cube sk-cube2"></div>
      <div class="sk-cube sk-cube3"></div>
      <div class="sk-cube sk-cube4"></div>
      <div class="sk-cube sk-cube5"></div>
      <div class="sk-cube sk-cube6"></div>
      <div class="sk-cube sk-cube7"></div>
      <div class="sk-cube sk-cube8"></div>
      <div class="sk-cube sk-cube9"></div>
    </div>
  </div>
  <div id="ajax_notification">
    <span class="close_warning">Close</span>
    <p class="ajaxmessage"></p>
  </div>
  <div class="hidden images_act_inact">
    <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
    <div class="act">
      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
    </div>
    <div class="inact">
      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
    </div>
  </div>
  <input type="hidden" name="ajaxmessage" id="ajaxmessage_changes_was_saved_successfully" value="<?=$languages['ajaxmessage_changes_was_saved_successfully'];?>" >
  
<!--header-->
  <header>
    <div class="inside_container">
      <aside>
        <span class="hidden-xs"><?=$languages['welcome'];?></span> <?=$_SESSION['admin']['user_fullname'];?> | 
        <a href="<?=$_SERVER['PHP_SELF'];?>?logout=yes" class="logout"><?=$languages['text_logout'];?></a>
      </aside>
      <h1 id="logo">
        <a href="/<?=$_SESSION['admin_dir_name'];?>" title="<?=$languages['title_logo_homepage'];?>">
          <?=$languages['company_name'];?>
        </a>
      </h1>
      <div class="clearfix"></div>
    </div>
  </header>
<!--header-->
<!--navigation-->
  <nav class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
    <ul id="menu">
      <li class="menu_li_1_level"><span title="<?=$languages['title_open_close'];?>" class="open_close_nav">&nbsp;</span></li>
<?php
    $menu_parent_id = 0;
    print_html_admin_menu($menu_parent_id);
    //if($user_id == 1) echo "add: ".$_SESSION['users_rights_add'].", edit: ".$_SESSION['users_rights_edit'].", delete: ".$_SESSION['users_rights_delete'];
?>
      <li class="menu_li_1_level">&nbsp;</li>
    </ul>
    <div class="clearfix"></div>
  </nav>
  <input type="hidden" name="text_no_add_rights" id="text_no_add_rights" value="<?=$languages['text_no_add_rights'];?>" >
  <input type="hidden" name="text_no_edit_rights" id="text_no_edit_rights" value="<?=$languages['text_no_edit_rights'];?>" >
  <input type="hidden" name="text_no_delete_rights" id="text_no_delete_rights" value="<?=$languages['text_no_delete_rights'];?>" >
<!--navigation-->
<?php    
}

function list_user_menu_rights($menu_id, $user_id) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $current_language_id;
  
  $query_menus = "SELECT `menus`.`menu_id`,`menus`.`menu_hierarchy_level`,`menus`.`menu_has_children`,`users_rights`.*,
                         `menus_translations`.`menu_translation_text`
                    FROM `menus`
              INNER JOIN (`users_rights`) ON (`users_rights`.`user_id` = '$user_id' AND `users_rights`.`menu_id` = `menus`.`menu_id`)
              INNER JOIN `menus_translations` ON `menus_translations`.`menu_id` = `menus`.`menu_id`
                   WHERE `menus`.`menu_parent_id` = '$menu_id' AND `menus_translations`.`language_id` = '$current_language_id'
                ORDER BY `menus`.`menu_sort_order` ASC";
  //if($menu_id == 2) {echo $query_menus."<br>";exit;}
  $result_menus = mysqli_query($db_link, $query_menus);
  if(!$result_menus) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_menus) > 0) {
    while($menus = mysqli_fetch_assoc($result_menus)) {
      //print_r($menus);
      $details_btn = "";
      $menu_id = $menus['menu_id'];
      if($_SESSION['admin']['user_type_is_superuser'] == 0 && $menu_id == 4) {
        continue;;
      }
      $menu_translation_text = $menus['menu_translation_text'];
      $menu_hierarchy_level = $menus['menu_hierarchy_level'];
      $menu_has_children = $menus['menu_has_children'];
      $users_rights_access = $menus['users_rights_access'];
      $users_rights_add = $menus['users_rights_add'];
      $users_rights_edit = $menus['users_rights_edit'];
      $users_rights_delete = $menus['users_rights_delete'];
      if($menu_hierarchy_level == 1 && $menu_has_children == 1) {
        $_SESSION['admin']['first_menu_level_id'] = $menu_id;
        $details_btn = '<a href="javascript:;" class="menu_header button blue" button-id="'.$_SESSION['admin']['first_menu_level_id'].'">+</a>';
      }
      $class_menu_hierarchy_level = ($menu_hierarchy_level != 1 && isset($_SESSION['admin']['first_menu_level_id'])) ? " children children".$_SESSION['admin']['first_menu_level_id'] : "";
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
?>
      <tr class="page<?php echo "$menu_id $class$class_menu_hierarchy_level";?>">
        <td>&nbsp;</td>
        <td style="text-align: left;">
<?php
      if ($menu_hierarchy_level == 1) echo "<b>$menu_translation_text</b>";
      if ($menu_hierarchy_level == 2) echo "&nbsp; - $menu_translation_text";
      if ($menu_hierarchy_level == 3) echo "&nbsp;&nbsp; - - $menu_translation_text";
      if ($menu_hierarchy_level == 4) echo "&nbsp;&nbsp;&nbsp; - - - $menu_translation_text";
?>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_access) ? " checkbox_checked" : NULL);?>">
            <input type="hidden" name="menu_rights[<?=$menu_id;?>][menu]" value="1" >
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][access]" onclick="Checkbox(this);" <?=(!empty($users_rights_access) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_add) ? " checkbox_checked" : NULL);?>">
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][add]" onclick="Checkbox(this);" <?=(!empty($users_rights_add) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_edit) ? " checkbox_checked" : NULL);?>">
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][edit]" onclick="Checkbox(this);" <?=(!empty($users_rights_edit) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_delete) ? " checkbox_checked" : NULL);?>">
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][delete]" onclick="Checkbox(this);" <?=(!empty($users_rights_delete) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td><?=$details_btn;?></td>
      </tr>
<?php
      if($menu_has_children == 1 && $menu_id != 22) list_user_menu_rights($menu_id, $user_id);
    }
    //mysqli_free_result($result_menus);
  }
}

function list_users_types($current_language_id) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  
  $user_type_is_superuser = $_SESSION['admin']['user_type_is_superuser'];
  $and_superuser_only = ($user_type_is_superuser == 1) ? "" : " AND `users_types`.`user_type_is_superuser` = '0'";
  
  $query_users_types = "SELECT `users_types`.`user_type_id`,`users_types`.`user_type_sort_order`,`users_types_descriptions`.`user_type_name` 
                          FROM `users_types` 
                    INNER JOIN `users_types_descriptions` ON `users_types_descriptions`.`user_type_id` = `users_types`.`user_type_id`
                         WHERE `users_types_descriptions`.`language_id` = '$current_language_id' $and_superuser_only
                      ORDER BY `users_types`.`user_type_sort_order` ASC";
  //echo $query_users_types;exit;
  $result_users_types = mysqli_query($db_link, $query_users_types);
  if(!$result_users_types) echo mysqli_error($db_link);
  $users_types_count = mysqli_num_rows($result_users_types);
  if($users_types_count > 0) {

    $key = 0;
    $class = ($key == 0) ? "even" : "$class";
    
    while ($status_row = mysqli_fetch_assoc($result_users_types)) {

      $user_type_id = $status_row['user_type_id'];
      $user_type_name = stripslashes($status_row['user_type_name']);
      $user_type_sort_order = $status_row['user_type_sort_order'];
      $edit_link = "/".$_SESSION['admin_dir_name']."/administration/administration-users-types-details.php?user_type_id=$user_type_id";
      $class = (($class == "odd") ? "even" : "odd");
?>
      <table id="user_type_<?=$user_type_id;?>" class="row_over">
        <tbody>
          <tr class="<?=$class?>">
            <td width="60%" class="text_left">
              <span class="red_link"><?=$user_type_name;?></span>
            </td>
            <td width="20%">
              <?php
                // if($users_types_count > 1) we gonna give the appropriate moving options
                // else we gonna leave this empty
                if($users_types_count > 1) {
                  if($key == 0) {
              ?>
                  <a href="javascript:;" onclick="MoveUserTypeForwardBackward('<?=$user_type_id;?>','<?=$user_type_sort_order;?>','backward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                  </a>
                <?php } elseif($key == $users_types_count-1) { ?>
                  <a href="javascript:;" onclick="MoveUserTypeForwardBackward('<?=$user_type_id;?>','<?=$user_type_sort_order;?>','forward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                  </a>
                <?php } else { ?>
                  <a href="javascript:;" onclick="MoveUserTypeForwardBackward('<?=$user_type_id;?>','<?=$user_type_sort_order;?>','backward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                  </a>
                  <a href="javascript:;" onclick="MoveUserTypeForwardBackward('<?=$user_type_id;?>','<?=$user_type_sort_order;?>','forward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                  </a>
              <?php 
                  }
                } // if($users_types_count > 1)
              ?>
            </td>
            <td width="10%">
              <a href="<?=$edit_link;?>" class="edit_link">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="10%">
              <a href="javascript:;" class="delete_user_type delete_link" data-id="<?=$user_type_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
              </a>
            </td>
          </tr>
        </tbody>
      </table>
<?php
      $key++;
    }
    mysqli_free_result($result_users_types);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_user_type_warning']?></p>
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
            DeleteUserType();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_user_type").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_user_type").click(function() {
        $(".delete_user_type").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_users_type_default_rights($menu_parent_id, $user_type_id) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $current_language_id;
    
  $query_users_rights = "SELECT `menus`.`menu_id`,`menus`.`menu_hierarchy_level`,`menus`.`menu_has_children`,`users_types_rights`.*,
                                `menus_translations`.`menu_translation_text`
                           FROM `menus`
                     INNER JOIN (`users_types_rights`) ON (`users_types_rights`.`user_type_id` = '$user_type_id' AND `users_types_rights`.`menu_id` = `menus`.`menu_id`)
                     INNER JOIN `menus_translations` ON `menus_translations`.`menu_id` = `menus`.`menu_id`
                          WHERE `menus`.`menu_parent_id` = '$menu_parent_id' AND `menus_translations`.`language_id` = '$current_language_id'
                       ORDER BY `menus`.`menu_sort_order` ASC";
  $result_users_rights = mysqli_query($db_link, $query_users_rights);
  if(!$result_users_rights) echo mysqli_error($db_link);
  if (mysqli_num_rows($result_users_rights) > 0) {

    $i = -1;
    while ($users_rights_row = mysqli_fetch_assoc($result_users_rights)) {

      $details_btn = "";
      $menu_id = $users_rights_row['menu_id'];
      $menu_translation_text = $users_rights_row['menu_translation_text'];
      $menu_hierarchy_level = $users_rights_row['menu_hierarchy_level'];
      $menu_has_children = $users_rights_row['menu_has_children'];
      $users_rights_access = $users_rights_row['users_rights_access'];
      $users_rights_add = $users_rights_row['users_rights_add'];
      $users_rights_edit = $users_rights_row['users_rights_edit'];
      $users_rights_delete = $users_rights_row['users_rights_delete'];
      if($menu_hierarchy_level == 1 && $menu_has_children == 1) {
        $_SESSION['admin']['first_menu_level_id'] = $menu_id;
        $details_btn = '<button class="menu_header button blue" button-id="'.$_SESSION['admin']['first_menu_level_id'].'">+</button>';
      }
      $class_menu_hierarchy_level = ($menu_hierarchy_level == 1) ? "" : " children children".$_SESSION['admin']['first_menu_level_id'];

      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
?>
      <tr class="single_page page<?="$menu_id $class$class_menu_hierarchy_level";?>" data-id="<?=$menu_id;?>">
        <td style="text-align: left;">
<?php
        if ($menu_hierarchy_level == 1) echo "<b>$menu_translation_text</b>";
        if ($menu_hierarchy_level == 2) echo "&nbsp; - $menu_translation_text";
        if ($menu_hierarchy_level == 3) echo "&nbsp;&nbsp; - - $menu_translation_text";
        if ($menu_hierarchy_level == 4) echo "&nbsp;&nbsp;&nbsp; - - - $menu_translation_text";
?>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_access) ? " checkbox_checked" : NULL);?>">
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][access]" onclick="Checkbox(this);" <?=(!empty($users_rights_access) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_add) ? " checkbox_checked" : NULL);?>">
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][add]" onclick="Checkbox(this);" <?=(!empty($users_rights_add) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_edit) ? " checkbox_checked" : NULL);?>">
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][edit]" onclick="Checkbox(this);" <?=(!empty($users_rights_edit) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td>
          <div class="checkbox<?=(!empty($users_rights_delete) ? " checkbox_checked" : NULL);?>">
            <input type="checkbox" name="menu_rights[<?=$menu_id;?>][delete]" onclick="Checkbox(this);" <?=(!empty($users_rights_delete) ? " checked = 'checked'" : NULL);?> >
          </div>
        </td>
        <td><?=$details_btn;?></td>
      </tr>
<?php
      list_users_type_default_rights($menu_id, $user_type_id);
    }
    mysqli_free_result($result_users_rights);
  }
}

function print_html_admin_menu($menu_parent_id) {
  
  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  
  $user_id = $_SESSION['admin']['user_id'];

  $query_menus = "SELECT `menus`.*, `users_rights`.*, `menus_translations`.`menu_translation_text`
                    FROM `menus`
              INNER JOIN `users_rights` ON (`users_rights`.`menu_id` = `menus`.`menu_id` AND `users_rights`.`users_rights_access` = '1')
              INNER JOIN `menus_translations` ON `menus_translations`.`menu_id` = `menus`.`menu_id`
                   WHERE `menus`.`menu_parent_id` = '$menu_parent_id' AND `users_rights`.`user_id` = '$user_id' 
                     AND `menu_is_active` = '1' AND `menus_translations`.`language_id` = '$current_language_id'
                ORDER BY `menu_sort_order` ASC";
  //if($menu_parent_id == 0) echo $query_menus."<br>";
  $result_menus = mysqli_query($db_link, $query_menus);
  if (!$result_menus) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_menus) > 0) {
    while ($menu_row = mysqli_fetch_assoc($result_menus)) {
      $menu_id = $menu_row['menu_id'];
      if(!empty($menu_id)) {
        $menu_hierarchy_level = $menu_row['menu_hierarchy_level'];
        $menu_has_children = $menu_row['menu_has_children'];
        $menu_path_name = $menu_row['menu_path_name'];
        $menu_translation_text = $menu_row['menu_translation_text'];
        if(empty($menu_row['menu_url']) || $menu_has_children == 1) {
          $menu_url = "javascript:;";
          $menu_attr = 'data-href="/'.$_SESSION['admin_dir_name'].$menu_row['menu_url'].'"';
        }
        else {
          $menu_url = "/".$_SESSION['admin_dir_name'].$menu_row['menu_url'];
          $menu_attr = "";
        }
        $menu_css_id = $menu_row['menu_css_id'];
        $ul_class = "menu_ul_".($menu_hierarchy_level)."_level";
        $li_class = "menu_li_".($menu_hierarchy_level)."_level";
        $a_class = "menu_a_".($menu_hierarchy_level)."_level";
        $user_access_sha1 = sha1($menu_id);
        $menu_image_url = $menu_row['menu_image_url'];
        $menu_css_id = !empty($menu_css_id) ? " id='$menu_css_id'" : "";
        if($menu_hierarchy_level == 1) {
          switch($menu_row['menu_css_id']) {
            case "languages" :      $menu_icon = '<i class="fa fa-language fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "content" :        $menu_icon = '<i class="fa fa-folder-open-o fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "catalog" :        $menu_icon = '<i class="fa fa-cart-plus fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "sales" :          $menu_icon = '<i class="fa fa-credit-card fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "team" :           $menu_icon = '<i class="fa fa-child fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "customers" :        $menu_icon = '<i class="fa fa-users fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "welding" :       $menu_icon = '<i class="fa fa-wrench fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "sliders" :         $menu_icon = '<i class="fa fa-film fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "galleries" :      $menu_icon = '<i class="fa fa-file-image-o fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "testimonials" :   $menu_icon = '<i class="fa fa-comments fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "banners" :        $menu_icon = '<i class="fa fa-object-ungroup fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "news-main" :      $menu_icon = '<i class="fa fa-newspaper-o fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "contacts" :       $menu_icon = '<i class="fa fa-map-marker fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            case "administration" : $menu_icon = '<i class="fa fa-user fa-2x menu_icon" aria-hidden="true"></i>';
              break;
            default: $menu_icon = "";
          }
        }
        else {
          $menu_icon = '<i class="fa fa-caret-right"></i>';
        } 
        $menu_show_in_menu = $menu_row['menu_show_in_menu'];
        $li_class_visible = ($menu_show_in_menu == 1) ? "" : " hidden";
        $menu_sort_order = $menu_row['menu_sort_order'];
        $users_rights_edit = $menu_row['users_rights_edit'];
        $users_rights_delete = $menu_row['users_rights_delete'];
        $menu_has_active_children = false;
        $menu_is_last_child = false;
        if($menu_has_children == 1) {
          $li_class .= " has_children";
          $menu_has_active_children = check_if_menu_has_active_children($menu_id,$user_id);
        }
        if($menu_hierarchy_level > 1) $menu_is_last_child = check_if_this_is_menu_last_child($menu_parent_id,$menu_sort_order,$user_id);
        if($menu_has_children == 1 && $menu_has_active_children) {
          $arrows = "";
          if($menu_hierarchy_level == 1) {
            if(isset($_COOKIE['nav'])) {
              $arrows = ($_COOKIE['nav'] == "opened_nav") ? '<i class="fa fa-chevron-down arrow arrow_down"></i><i class="fa fa-chevron-up arrow arrow_up"></i>' : '<i class="fa fa-chevron-right arrow arrow_right"></i><i class="fa fa-chevron-left arrow arrow_left"></i>';
            }
            else {
              $arrows = '<i class="fa fa-chevron-down arrow arrow_down"></i><i class="fa fa-chevron-up arrow arrow_up"></i>';
            }
          }
          $li_class_active = (strpos($_SERVER['PHP_SELF'],$menu_path_name) !== false && ($menu_hierarchy_level == 1)) ? " active" : "";
?>
          <li<?=$menu_css_id?> class="<?=$li_class.$li_class_active.$li_class_visible;?>">
            <a href="<?=$menu_url;?>" <?=$menu_attr;?> class="<?=$a_class;?>">
              <?=$menu_icon;?>
              <span class="menu_text"><?=$menu_translation_text;?></span>
              <?=$arrows;?>
            </a>
            <ul class="<?=$ul_class;?>">
<?php
    
          print_html_admin_menu($menu_id);
        }
        else {
?>
          <li<?=$menu_css_id?> class="<?=$li_class.$li_class_visible;?><?=(strpos($_SERVER['PHP_SELF'],$menu_url) === false) ? NULL : " active"; ?>">
            <a href="<?=$menu_url;?>" class="<?=$a_class;?>">
              <?=$menu_icon;?>
              <span class="menu_text"><?=$menu_translation_text;?></span>
            </a>
          </li>
<?php 
        }
        
        if($menu_hierarchy_level > 1 && $menu_is_last_child) {
?>
            </ul>
          </li>
<?php 
        }
      }
    }
    mysqli_free_result($result_menus);
  }
}

function list_menus($parent_id, $path_number) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $current_language_id;
  global $class;
  
  $query_menus = "SELECT `menus`.*,`menus_translations`.`menu_translation_text`
                    FROM `menus` 
              INNER JOIN `menus_translations` ON `menus_translations`.`menu_id` = `menus`.`menu_id`
                   WHERE `menu_parent_id` = '$parent_id' AND `menus_translations`.`language_id` = '$current_language_id' 
                ORDER BY `menu_sort_order` ASC";
  //if($parent_id == 4) echo $query_menus;
  $result_menus = mysqli_query($db_link,$query_menus);
  if(!$result_menus) echo mysqli_error($db_link);
  $menu_count = mysqli_num_rows($result_menus);
  if($menu_count > 0) {
    
    $key = 0;
    
    while ($menus = mysqli_fetch_assoc($result_menus)) {
      $menu_id = $menus['menu_id'];
      $menu_parent_id = $menus['menu_parent_id'];
      $menu_path_name = $menus['menu_path_name'];
      $menu_translation_text = stripslashes($menus['menu_translation_text']);
      $menu_url = $menus['menu_url'];
      $menu_hierarchy_level = $menus['menu_hierarchy_level'];
      $menu_has_children = $menus['menu_has_children'];
      $menu_css_id = $menus['menu_css_id'];
      $menu_image_url = $menus['menu_image_url'];
      $menu_sort_order = $menus['menu_sort_order'];
      $menu_show_in_menu = $menus['menu_show_in_menu'];
      $menu_is_collapsed = $menus['menu_is_collapsed'];
      $menu_is_active = $menus['menu_is_active'];
      $set_menu = ($menu_is_active == 1) ? 0 : 1;
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $name_dashes = "";
      if($menu_hierarchy_level == 1) {
          $path_number++;
          $menu_path_number = $path_number;
      } else {
          $menu_path_number = "$path_number.$menu_sort_order";
          if($menu_hierarchy_level == 2) $name_dashes = "- ";
          if($menu_hierarchy_level == 3) $name_dashes = "- - ";
          if($menu_hierarchy_level == 4) $name_dashes = "- - - ";
          if($menu_hierarchy_level == 5) $name_dashes = "- - - - ";
      }
      $edit_link = "/".$_SESSION['admin_dir_name']."/administration/administration-menu-details.php?menu_id=$menu_id";
?>
      <table id="menu_<?=$menu_id;?>" class="row_over">
        <tbody>
          <tr class="<?=$class?>">
            <td width="2%" class="text_left">
              <a href="javascript:;" onclick="ToggleExpandMenu('<?=$menu_id;?>', '<?php if($menu_is_collapsed == 1) echo "expand"; else echo "collapse" ?>');">
                <?php 
                  if($menu_has_children == 0) {
                    // no children, print nothing
                  } else {
                      if($menu_is_collapsed == 0) { ?>
                      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/contract.gif" class="systemicon" alt="<?=$languages['alt_collapse_section'];?>" title="<?=$languages['title_collapse_section'];?>" width="16" height="16" />
                    <?php } else { ?>
                      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/expand.gif" class="systemicon" alt="<?=$languages['alt_open_section'];?>" title="<?=$languages['title_open_section'];?>" width="16" height="16" />
                    <?php 
                    } 
                  }
                ?>
              </a>
            </td>
            <td width="5%" class="text_left"><?=$menu_path_number;?></td>
            <td width="60%" class="text_left">
              <span class="red_link"><?=$name_dashes.$menu_translation_text;?></span>
            </td>
            <td width="5%">
              <a href="javascript:;" class="edit_link" onclick="SetMenuActiveInactive(this,'<?=$menu_id;?>', '<?=$set_menu;?>')">
                <?php if($menu_is_active == 1) { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
                <?php } else { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
                <?php } ?>
              </a>
            </td>
            <td width="14%">
            <?php
              // if($menu_count > 1) we gonna give the appropriate moving options
              // else we gonna leave this empty
              if($menu_count > 1) {
                if($key == 0) {
            ?>
                <a href="javascript:;" class="edit_link" onclick="MoveMenuForwardBackward('<?=$menu_id;?>','<?=$menu_parent_id;?>','<?=$menu_sort_order;?>','<?=$menu_hierarchy_level;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
            <?php } elseif($key == $menu_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveMenuForwardBackward('<?=$menu_id;?>','<?=$menu_parent_id;?>','<?=$menu_sort_order;?>','<?=$menu_hierarchy_level;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveMenuForwardBackward('<?=$menu_id;?>','<?=$menu_parent_id;?>','<?=$menu_sort_order;?>','<?=$menu_hierarchy_level;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveMenuForwardBackward('<?=$menu_id;?>','<?=$menu_parent_id;?>','<?=$menu_sort_order;?>','<?=$menu_hierarchy_level;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php 
                }
              } // if($menu_count > 1)
            ?>
            </td>
            <td width="7%">
              <a href="<?=$edit_link;?>" class="edit_link">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="7%">
            <?php if($menu_has_children == 0) { ?>
              <a href="javascript:;" class="delete_menu_link delete_link" data-id="<?=$menu_id;?>" data-parent="<?=$menu_parent_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
              </a>
            <?php } ?>
            </td>
          </tr>
        </tbody>
      </table>
<?php
      if($menu_is_collapsed == 0 && $menu_has_children == 1) {
        list_menus($menu_id, $menu_path_number, $class);
      }
      $key++;
    }
    mysqli_free_result($result_menus);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_menu_warning']?></p>
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
            DeleteMenuLink();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_menu_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_menu_link").click(function() {
        $(".delete_menu_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
  else {
    if($parent_id == 0) echo "<p>".$languages['no_menus_in_the_database_yet']."</p>";
  }
}

function list_menu_for_select_a_parent($parent_id, $path_number, $menu_parent_id) {
  
    global $db_link;
    global $languages;
    global $current_lang;
    global $current_language_id;
  
    $query = "SELECT `menus`.`menu_id`,`menus`.`menu_hierarchy_level`,`menus`.`menu_sort_order`,`menus_translations`.`menu_translation_text`
                FROM `menus` 
          INNER JOIN `menus_translations` ON `menus_translations`.`menu_id` = `menus`.`menu_id`
               WHERE `menu_parent_id` = '$parent_id' AND `menus_translations`.`language_id` = '$current_language_id'
            ORDER BY `menu_sort_order` ASC";
    //echo $query;
    $result = mysqli_query($db_link, $query);
    if (!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $parent_id = $row['menu_id'];
            $parent_name = stripslashes($row['menu_translation_text']);
            //$parent_name = $languages[$parent_name];
            $parent_level = $row['menu_hierarchy_level'];
            $parent_sort = $row['menu_sort_order'];
            if ($parent_level == 1) {
                $path_number++;
                $option_path_number = (isset($option_path_number)) ? $path_number : $parent_sort;
            } else {
                $option_path_number = "&nbsp;$path_number.$parent_sort";
            }
            if ($menu_parent_id == $parent_id)
                $selected = 'selected="selected"';
            else
                $selected = "";
            echo "<option value='$parent_id|$parent_level' $selected>$option_path_number. $parent_name</option>";
            list_menu_for_select_a_parent($parent_id, $option_path_number, $menu_parent_id);
        }
    }
}

function list_menus_for_reorder($parent_id, $path_number) {
  
  global $db_link;
  global $current_language_id;
  
  $query_menus = "SELECT `menus`.*,`menus_translations`.`menu_translation_text`
                    FROM `menus` 
              INNER JOIN `menus_translations` ON `menus_translations`.`menu_id` = `menus`.`menu_id`
                   WHERE `menu_parent_id` = '$parent_id' AND `menus_translations`.`language_id` = '$current_language_id' 
                ORDER BY `menu_sort_order` ASC";
  //echo $query_menus;
  $result = mysqli_query($db_link, $query_menus);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {
    
    if($parent_id == 0) {
      echo "<ul class='sortable sortable_list'>";
    }
    while ($row = mysqli_fetch_assoc($result)) {
      $menu_id = $row['menu_id'];
      $menu_translation_text = stripslashes($row['menu_translation_text']);
      $menu_hierarchy_level = $row['menu_hierarchy_level'];
      $menu_sort_order = $row['menu_sort_order'];
      $menu_has_children = $row['menu_has_children'];
      if($menu_hierarchy_level == 1) {
          $path_number++;
          $option_path_number = $path_number;
      } else {
          $option_path_number = "&nbsp;$path_number. $menu_sort_order";
      }
      
      $class_expanded_leaf = ($menu_has_children == 1) ? "mjs-nestedSortable-branch mjs-nestedSortable-expanded" : "mjs-nestedSortable-leaf";
      $expand_collapse_node = ($menu_has_children == 1) ? "<span title='Click to show/hide children' class='disclose ui-icon ui-icon-minusthick'><span></span></span>" : "";
      echo "<li id='menu_$menu_id' class='$class_expanded_leaf' data-id='$menu_id'>
        <input type='hidden' name='menus[$parent_id][]' value='$menu_id'>
        <div class='sortable_label'>$expand_collapse_node$option_path_number. $menu_translation_text</div>";
      if($menu_has_children == 1) {
        echo "<ul class='sortable_list'>";
        
        list_menus_for_reorder($menu_id, $option_path_number);
        
        echo "</ul>";
      }
      
      echo "</li>";
    }
    mysqli_free_result($result);
  }
}

function list_content_types() {
  
  global $db_link;
  global $current_lang;
  global $current_language_id;
  global $languages;
  
  $query_content_types = "SELECT `contents_types`.*,`ctl`.`content_type_name`
                            FROM `contents_types` 
                       LEFT JOIN `contents_types_languages` as `ctl` ON (`ctl`.`content_type_id` = `contents_types`.`content_type_id` AND `ctl`.`language_id` = '$current_language_id')
                       ORDER  BY `contents_types`.`content_type_sort_order` ASC";
  $result_content_types = mysqli_query($db_link, $query_content_types);
  if(!$result_content_types) echo mysqli_error($db_link);
  $content_type_count = mysqli_num_rows($result_content_types);
  if($content_type_count > 0) {
    
    $key = 0;
    
    while($row_content_types = mysqli_fetch_assoc($result_content_types)) {

      $content_type_id = $row_content_types['content_type_id'];
      $content_type = $row_content_types['content_type'];
      $content_type_is_active = $row_content_types['content_type_is_active'];
      $content_type_sort_order = $row_content_types['content_type_sort_order'];
      $content_type_name = $row_content_types['content_type_name'];
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/content/content-types-details.php?content_type_id=$content_type_id";
?>
      <table id="ct_<?=$content_type_id;?>" class="row_over">
        <tbody>
          <tr class="<?=$class?>">
            <td width="3%" class="text_left"><?=$content_type_id;?></td>
            <td width="40%" class="text_left">
              <span class="red_link"><?=$content_type_name;?></span>
            </td>
            <td width="30%" class="text_left"><?=$content_type;?></td>
            <td width="5%">
              <a href="javascript:;" class="edit_link" onclick="SetContentTypeActiveInactive(this,'<?=$content_type_id;?>', '<?=$set_content;?>')">
                <?php if($content_type_is_active == 1) { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
                <?php } else { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
                <?php } ?>
              </a>
            </td>
            <td width="10%">
              <?php
                // if($content_type_count > 1) we gonna give the appropriate moveing options
                // else we gonna leave this empty
                if($content_type_count > 1) { 
                  if($key == 0) {
              ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContentTypeForwardBackward('<?=$content_type_id;?>','<?=$content_menu_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_content_backward'];?>" title="<?=$languages['title_move_content_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $content_type_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContentTypeForwardBackward('<?=$content_type_id;?>','<?=$content_menu_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_content_forward'];?>" title="<?=$languages['title_move_content_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContentTypeForwardBackward('<?=$content_type_id;?>','<?=$content_menu_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_content_backward'];?>" title="<?=$languages['title_move_content_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveContentTypeForwardBackward('<?=$content_type_id;?>','<?=$content_menu_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_content_forward'];?>" title="<?=$languages['title_move_content_forward'];?>" width="16" height="16" />
                </a>
              <?php 
                  }
                } // if($content_type_count > 1)
              ?>
            </td>
            <td width="4%">
              <a href="<?=$edit_link;?>" class="edit_link">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit_content'];?>" title="<?=$languages['title_edit_content'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="4%">
              <a href="javascript:;" class="delete_content_type_link delete_link" data-id="<?=$content_type_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete_content'];?>" title="<?=$languages['title_delete_content'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="4%">
              <input type="checkbox" class="multicontent" value="<?=$content_type_id;?>" name="multicontent[]" title="<?=$languages['title_toggle_checkbox'];?>"/>
            </td>
          </tr>
        </tbody>
      </table>
<?php
      $key++;
    }
    mysqli_free_result($result_content_types);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_content_type_warning']?></p>
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
            DeleteContentType();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_content_type_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_content_type_link").click(function() {
        $(".delete_content_type_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_content_types_in_select($cct_id) {
  
  global $db_link;
  global $current_lang;
  global $languages;
  
  //don't show language type beacause language contents are inserted automatically when adding new language WHERE `content_type_id` <> '6'
  $query_content_types = "SELECT `content_type_id`, `content_type` FROM `contents_types` 
                           WHERE `content_type_is_active` = '1'
                       ORDER  BY `content_type_sort_order` ASC";
  $result_content_types = mysqli_query($db_link, $query_content_types);
  if(!$result_content_types) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_types) > 0) {
    while($row_content_types = mysqli_fetch_assoc($result_content_types)) {

      $content_type_id = $row_content_types['content_type_id'];
      $content_type = $row_content_types['content_type'];
      $content_type_lang = $languages[$content_type];
      $selected = ($content_type_id == $cct_id) ? ' selected="selected"' : "";

      echo "<option value='$content_type_id'$selected>$content_type_lang</option>";
    }
    mysqli_free_result($result_content_types);
  }
}

function list_contents($parent_id, $path_number) {
  
  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $class;
  
  $query_content = "SELECT `contents`.`content_id`,`contents`.`content_parent_id`,`content_is_home_page`,`contents`.`content_has_children`,
                           `contents`.`content_hierarchy_level`,`contents`.`content_menu_order`,`contents`.`content_collapsed`,`contents`.`content_is_active`,
                           `contents_descriptions`.`content_name`,`contents_descriptions`.`content_menu_text`,
                           `contents_descriptions`.`content_text`,`contents_descriptions`.`content_pretty_url`,`contents_types`.*
                      FROM `contents`
                INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
                INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                     WHERE `contents`.`content_parent_id` = '$parent_id' AND `contents_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `content_menu_order` ASC";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    $key = 0;
    $warning_cannot_change_language = $languages['warning_cannot_change_language'];
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_parent_id = $content_row['content_parent_id'];
      $content_is_home_page = $content_row['content_is_home_page'];
      $content_type_id = $content_row['content_type_id'];
      $content_type = $content_row['content_type'];
      $content_type_lang = $languages[$content_type];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_has_children = $content_row['content_has_children'];
      $content_name = $content_row['content_name'];
      $content_menu_text = ($content_type_id == 6) ? $content_name : $content_row['content_menu_text'];
      $content_collapsed = $content_row['content_collapsed'];
      $content_text = $content_row['content_text'];
      $content_pretty_url = $content_row['content_pretty_url'];
      $content_url = ($content_type_id == 4) ? $content_text : "/$current_lang/$content_pretty_url";
      $content_menu_order = $content_row['content_menu_order'];
      $content_is_active = $content_row['content_is_active'];
      $set_content = ($content_is_active == 1) ? 0 : 1;
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $name_dashes = "";
      $empty_spaces = "";
      if($content_hierarchy_level == 1) {
          $path_number++;
          $content_path_number = $path_number;
      } else {
          $content_path_number = "$path_number.$content_menu_order";
          if($content_hierarchy_level == 2) {
            $empty_spaces = "&nbsp;";
            $name_dashes = "- ";
          }
          if($content_hierarchy_level == 3) {
            $empty_spaces = "&nbsp;&nbsp;";
            $name_dashes = "- - ";
          }
          if($content_hierarchy_level == 4) {
            $empty_spaces = "&nbsp;&nbsp;&nbsp;";
            $name_dashes = "- - - ";
          }
          if($content_hierarchy_level == 5) {
            $empty_spaces = "&nbsp;&nbsp;&nbsp;&nbsp;";
            $name_dashes = "- - - - ";
          }
      }
      $edit_link = "/".$_SESSION['admin_dir_name']."/content/content-details.php?content_id=$content_id";
?>
      <table class="row_over">
        <tbody>
          <tr id="tr_<?=$content_id;?>" class="<?=$class?>">
            <td width="2%" class="text_left"><?=$empty_spaces;?>
              <a href="javascript:;" onclick="ToggleExpandContent('<?=$content_id;?>', '<?php if($content_collapsed == 1) echo "expand"; else echo "collapse" ?>');">
                <?php 
                  if($content_has_children == 0) {
                    // no children, print nothing
                  } else {
                      if($content_collapsed == 0) { ?>
                      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/contract.gif" class="systemicon" alt="<?=$languages['alt_collapse_section'];?>" title="<?=$languages['title_collapse_section'];?>" width="16" height="16" />
                    <?php } else { ?>
                      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/expand.gif" class="systemicon" alt="<?=$languages['alt_open_section'];?>" title="<?=$languages['title_open_section'];?>" width="16" height="16" />
                    <?php 
                    } 
                  }
                ?>
              </a>
            </td>
            <td width="5%" class="text_left"><?=$content_path_number;?></td>
            <td width="25%" class="text_left">
              <span class="red_link"><?="$name_dashes $content_menu_text";?></span>
            </td>
            <td width="15%" class="text_left"><?=$content_pretty_url;?></td>
            <td width="10%"><?=$content_type_lang;?></td>
            <td width="10%">
              <a href="javascript:;" class="edit_link" onclick="SetContentActiveInactive(this,'<?=$content_id;?>', '<?=$set_content;?>')">
                <?php if($content_is_active == 1) { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
                <?php } else { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
                <?php } ?>
              </a>
            </td>
            <td width="10%">
              <?php if($content_is_home_page == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_content_is_default'];?>" title="<?=$languages['title_home_page_content'];?>" width="16" height="16" />
              <?php } else { ?>
              <a href="javascript:;" class="edit_link" onclick="if(confirm('<?=$languages['set_content_default_warning_1']." $content_menu_text ".$languages['set_content_default_warning_2'];?>')) SetContentAsHomePage('<?=$content_id;?>');">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_set_content_default'];?>" title="<?=$languages['title_set_content_default'];?>" width="16" height="16" />
              </a>
              <?php } ?>
            </td>
            <td width="7%">
              <?php
                // if($content_count > 1) we gonna give the appropriate moveing options
                // else we gonna leave this empty
                if($content_count > 1) { 
                  if($key == 0) {
              ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContentForwardBackward('<?=$content_id;?>','<?=$content_parent_id;?>','<?=$content_menu_order;?>','<?=$content_hierarchy_level;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_content_backward'];?>" title="<?=$languages['title_move_content_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $content_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContentForwardBackward('<?=$content_id;?>','<?=$content_parent_id;?>','<?=$content_menu_order;?>','<?=$content_hierarchy_level;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_content_forward'];?>" title="<?=$languages['title_move_content_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContentForwardBackward('<?=$content_id;?>','<?=$content_parent_id;?>','<?=$content_menu_order;?>','<?=$content_hierarchy_level;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_content_backward'];?>" title="<?=$languages['title_move_content_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveContentForwardBackward('<?=$content_id;?>','<?=$content_parent_id;?>','<?=$content_menu_order;?>','<?=$content_hierarchy_level;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_content_forward'];?>" title="<?=$languages['title_move_content_forward'];?>" width="16" height="16" />
                </a>
              <?php 
                  }
                } // if($content_count > 1)
              ?>
            </td>
            <td width="4%">
              <a href="<?=$content_url;?>" target="_blank">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/view.gif" class="systemicon" alt="<?=$languages['alt_view_content'];?>" title="<?=$languages['title_view_content'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="4%">
              <a href="<?=$edit_link;?>" class="edit_link">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit_content'];?>" title="<?=$languages['title_edit_content'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="4%">
            <?php if($content_has_children == 0) { ?>
              <a href="javascript:;" class="delete_content_link delete_link" data-id="<?=$content_id;?>" data-parent="<?=$content_parent_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete_content'];?>" title="<?=$languages['title_delete_content'];?>" width="16" height="16" />
              </a>
            <?php } ?>
            </td>
            <td width="4%">
              <input type="checkbox" class="multicontent" value="<?=$content_id;?>" name="multicontent[]" title="<?=$languages['title_toggle_checkbox'];?>"/>
            </td>
          </tr>
        </tbody>
      </table>
<?php
      if($content_collapsed == 0 && $content_has_children == 1) {
        list_contents($content_id, $content_path_number);
      }
      $key++;
    }
    mysqli_free_result($result_content);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_content_warning']?></p>
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
            DeleteContent();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_content_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_content_link").click(function() {
        $(".delete_content_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_contents_for_select($parent_id, $path_number, $content_parent_id, $current_content_id) {
  
  global $db_link;
  global $current_language_id;

  $query = "SELECT `contents`.`content_id`,`contents`.`content_type_id`,`contents`.`content_hierarchy_level`,`contents`.`content_menu_order`,
                   `contents_descriptions`.`content_name`,`contents_descriptions`.`content_menu_text` 
              FROM `contents` 
        INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
             WHERE `contents`.`content_parent_id` = '$parent_id' AND `contents_descriptions`.`language_id` = '$current_language_id' 
          ORDER BY `contents`.`content_menu_order` ASC";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $content_id = $row['content_id'];
      $content_type_id = $row['content_type_id'];
      $parent_name = stripslashes($row['content_name']);
      $content_menu_text = ($content_type_id == 6) ? $parent_name : $row['content_menu_text'];
      $content_hierarchy_level = $row['content_hierarchy_level'];
      $content_menu_order = $row['content_menu_order'];
      if($content_hierarchy_level == 1) {
          $path_number++;
          $option_path_number = $path_number;
      } else {
          $option_path_number = "&nbsp;$path_number. $content_menu_order";
      }
      if ($content_parent_id == $content_id)
          $selected = 'selected="selected"';
      else
          $selected = "";
      if($current_content_id != $content_id) {
        
        echo "<option value='$content_id|$content_hierarchy_level' level='$content_hierarchy_level' $selected>$option_path_number. $content_menu_text</option>";
      
        if(!isset($get_only_content_type_language)) list_contents_for_select($content_id, $option_path_number, $content_parent_id, $current_content_id);
      } 
    }
    mysqli_free_result($result);
  }
}

function list_contents_for_reorder($parent_id, $path_number) {
  
  global $db_link;
  global $current_language_id;
  
  $query = "SELECT `contents`.`content_id`,`contents`.`content_hierarchy_level`,`contents`.`content_has_children`,`contents`.`content_menu_order`,
                   `contents_descriptions`.`content_name`,`contents_descriptions`.`content_pretty_url` 
              FROM `contents` 
        INNER JOIN `contents_descriptions` ON `contents_descriptions`.`content_id` = `contents`.`content_id`
             WHERE `contents`.`content_parent_id` = '$parent_id' AND `contents_descriptions`.`language_id` = '$current_language_id'
          ORDER BY `contents`.`content_menu_order` ASC";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {
    
    if($parent_id == 0) {
      echo "<ul class='sortable sortable_list'>";
    }
    while ($row = mysqli_fetch_assoc($result)) {
      $content_id = $row['content_id'];
      $parent_name = stripslashes($row['content_name']);
      $content_hierarchy_level = $row['content_hierarchy_level'];
      $content_has_children = $row['content_has_children'];
      $content_menu_order = $row['content_menu_order'];
      $content_pretty_url = $row['content_pretty_url'];
      if($content_hierarchy_level == 1) {
          $path_number++;
          $option_path_number = $path_number;
      } else {
          $option_path_number = "&nbsp;$path_number.$content_menu_order";
      }
      
      $class_expanded_leaf = ($content_has_children == 1) ? "mjs-nestedSortable-branch mjs-nestedSortable-expanded" : "mjs-nestedSortable-leaf";
      $expand_collapse_node = ($content_has_children == 1) ? "<span title='Click to show/hide children' class='disclose ui-icon ui-icon-minusthick'><span></span></span>" : "";
      echo "<li id='content_$content_id' class='$class_expanded_leaf' data-id='$content_id'>
        <input type='hidden' name='contents[$parent_id][]' value='$content_id'>
        <div class='sortable_label'>$expand_collapse_node$option_path_number. $parent_name<em>($content_pretty_url)</em></div>";
      if($content_has_children == 1) {
        echo "<ul class='sortable_list'>";
        
        list_contents_for_reorder($content_id, $option_path_number);
        
        echo "</ul>";
      }
      
      echo "</li>";
    }
    
    mysqli_free_result($result);
  }
  else {
    echo "</ul>";
  }
}

function list_categories($parent_id,$category_root_id,$path_number) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $current_language_id;
  global $class;
  
  $and_root_id = ($category_root_id == 0) ? "" : " AND `ctc`.`category_root_id` = '$category_root_id'";
  $query_categories = "SELECT `categories`.`category_id`,`ctc`.`category_parent_id`,`ctc`.`category_root_id`,`ctc`.`category_hierarchy_level`,`ctc`.`category_hierarchy_ids`,
                              `ctc`.`category_sort_order`,`ctc`.`category_has_children`,`ctc`.`category_is_active`,`ctc`.`category_is_collapsed`,`cd`.`cd_name`
                         FROM `categories`
                   INNER JOIN `category_to_category` as `ctc` USING(`category_id`)
                   INNER JOIN `categories_descriptions` as `cd` USING(`category_id`)
                        WHERE `ctc`.`category_parent_id` = '$parent_id' $and_root_id
                          AND `cd`.`language_id` = '$current_language_id'
                     ORDER BY `ctc`.`category_sort_order` ASC";
  //echo "$query_categories<br>";exit;;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if($category_count > 0) {
    $key = 0;
    while($category_row = mysqli_fetch_assoc($result_categories)) {
      $category_id = $category_row['category_id'];
      $category_parent_id = $category_row['category_parent_id'];
      $category_root_id = $category_row['category_root_id'];
      $cd_name = $category_row['cd_name'];
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_hierarchy_ids = $category_row['category_hierarchy_ids'];
      $category_sort_order = $category_row['category_sort_order'];
      $category_is_active = $category_row['category_is_active'];
      $category_has_children = $category_row['category_has_children'];
      $category_is_collapsed = $category_row['category_is_collapsed'];
      $set_category = ($category_is_active == 1) ? 0 : 1;
      if($category_hierarchy_level == 1) {
          $path_number++;
          $category_path_number = $path_number;
      } else {
          $category_path_number = "$path_number.$category_sort_order";
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/welding/categories-details.php?category_hierarchy_ids=$category_hierarchy_ids&category_id=$category_id";
?>
      <table id="cat_<?=$category_hierarchy_ids;?>">
        <tbody>
          <tr class="<?=$class?> row_over">
            <td width="5%" class="text_left" style="padding-left:<?=$category_hierarchy_level-1;?>%;">
              <a href="javascript:;" onclick="ToggleExpandCategory('<?=$category_hierarchy_ids;?>', '<?php if($category_is_collapsed == 1) echo "expand"; else echo "collapse" ?>');">
                <?php 
                  if($category_has_children == 0) {
                    // no children, print nothing
                  } else {
                      if($category_is_collapsed == 0) { ?>
                      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/contract.gif" class="systemicon" alt="<?=$languages['alt_collapse_section'];?>" title="<?=$languages['title_collapse_section'];?>" width="16" height="16" />
                    <?php } else { ?>
                      <img src="/<?=$_SESSION['admin_dir_name'];?>/images/expand.gif" class="systemicon" alt="<?=$languages['alt_open_section'];?>" title="<?=$languages['title_open_section'];?>" width="16" height="16" />
                    <?php 
                    } 
                  }
                ?>
              </a>
            </td>
            <td width="65%" class="text_left" style="padding-left:<?=$category_hierarchy_level-1;?>%;">
              <span><?=$category_path_number;?></span> &nbsp;&nbsp;&nbsp;<span class="red_link"><?=$cd_name;?></span>
            </td>
            <td width="5%">
              <a href="javascript:;" class="edit_link" onclick="SetCategoryActiveInactive(this,<?="'$category_hierarchy_ids','$set_category'";?>)">
                <?php if($category_is_active == 1) { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
                <?php } else { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
                <?php } ?>
              </a>
            </td>
            <td width="10%">
              <?php
                // if($category_count > 1) we gonna give the appropriate moving options
                // else we gonna leave this empty
                if($category_count > 1) {
                  if($key == 0) {
              ?>
                  <a href="javascript:;" class="edit_link" onclick="MoveCategoryForwardBackward(<?="'$category_id','$category_parent_id','$category_root_id','$category_sort_order','$category_hierarchy_level','backward'";?>)">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_category_backward'];?>" title="<?=$languages['title_move_category_backward'];?>" width="16" height="16" />
                  </a>
                <?php } elseif($key == $category_count-1) { ?>
                  <a href="javascript:;" class="edit_link" onclick="MoveCategoryForwardBackward(<?="'$category_id','$category_parent_id','$category_root_id','$category_sort_order','$category_hierarchy_level','forward'";?>)">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_category_forward'];?>" title="<?=$languages['title_move_category_forward'];?>" width="16" height="16" />
                  </a>
                <?php } else { ?>
                  <a href="javascript:;" class="edit_link" onclick="MoveCategoryForwardBackward(<?="'$category_id','$category_parent_id','$category_root_id','$category_sort_order','$category_hierarchy_level','backward'";?>)">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_category_backward'];?>" title="<?=$languages['title_move_category_backward'];?>" width="16" height="16" />
                  </a>
                  <a href="javascript:;" class="edit_link" onclick="MoveCategoryForwardBackward(<?="'$category_id','$category_parent_id','$category_root_id','$category_sort_order','$category_hierarchy_level','forward'";?>)">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_category_forward'];?>" title="<?=$languages['title_move_category_forward'];?>" width="16" height="16" />
                  </a>
              <?php 
                  }
                } // if($category_count > 1)
              ?>
            </td>
            <td width="7.5%">
              <a href="<?=$edit_link;?>" class="edit_link">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit_category'];?>" title="<?=$languages['title_edit_category'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="7.5%">
            <?php if($category_has_children == 0) { ?>
              <a href="javascript:;" class="delete_category_link delete_link" data-id="<?=$category_id;?>" data-parent="<?=$category_parent_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete_category'];?>" title="<?=$languages['title_delete_category'];?>" width="16" height="16" />
              </a>
            <?php } ?>
            </td>
          </tr>
        </tbody>
      </table>
<?php
      if($category_is_collapsed == 0 && $category_has_children == 1) {
        list_categories($category_id,$category_root_id,$category_path_number);
      }
      $key++;
    }
    mysqli_free_result($result_categories);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_category_warning']?></p>
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
            DeleteCategory();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_category_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_category_link").click(function() {
        $(".delete_category_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
  else {
    if($parent_id == 0) echo "<p>".$languages['no_categories_in_the_database_yet']."</p>";
  }
}

function list_categories_for_reorder($parent_id,$category_root_id,$path_number) {
  
  global $db_link;
  global $current_language_id;
  
  $and_root_id = ($category_root_id == 0) ? "" : " AND `ctc`.`category_root_id` = '$category_root_id'";
  $query = "SELECT `categories`.`category_id`,`ctc`.`category_parent_id`,`ctc`.`category_root_id`,`ctc`.`category_hierarchy_level`,`ctc`.`category_hierarchy_ids`,
                    `ctc`.`category_sort_order`,`ctc`.`category_has_children`,`ctc`.`category_is_active`,`ctc`.`category_is_collapsed`,`cd`.`cd_name`
               FROM `categories`
         INNER JOIN `category_to_category` as `ctc` USING(`category_id`)
         INNER JOIN `categories_descriptions` as `cd` USING(`category_id`)
              WHERE `ctc`.`category_parent_id` = '$parent_id' $and_root_id
                AND `cd`.`language_id` = '$current_language_id'
           ORDER BY `ctc`.`category_sort_order` ASC";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {
    
    if($parent_id == 0) {
      echo "<ul class='sortable sortable_list'>";
    }
    while ($row = mysqli_fetch_assoc($result)) {
      $category_id = $row['category_id'];
      $parent_name = stripslashes($row['cd_name']);
      $category_root_id = $row['category_root_id'];
      $category_hierarchy_level = $row['category_hierarchy_level'];
      $category_sort_order = $row['category_sort_order'];
      $category_has_children = $row['category_has_children'];
      if($category_hierarchy_level == 1) {
          $path_number++;
          $option_path_number = $path_number;
      } else {
          $option_path_number = "&nbsp;$path_number. $category_sort_order";
      }
      
      $class_expanded_leaf = ($category_has_children == 1) ? "mjs-nestedSortable-branch mjs-nestedSortable-expanded" : "mjs-nestedSortable-leaf";
      $expand_collapse_node = ($category_has_children == 1) ? "<span title='Click to show/hide children' class='disclose ui-icon ui-icon-minusthick'><span></span></span>" : "";
      echo "<li id='category_$category_id' class='$class_expanded_leaf' data-id='$category_id'>
        <input type='hidden' name='categories[$category_root_id][$parent_id][]' value='$category_id'>
        <div class='sortable_label'>$expand_collapse_node$option_path_number. $parent_name<em></em></div>";
      if($category_has_children == 1) {
        echo "<ul class='sortable_list'>";
        
        list_categories_for_reorder($category_id,$category_root_id,$option_path_number);
        
        echo "</ul>";
      }
      
      echo "</li>";
    }
    mysqli_free_result($result);
  }
}

function list_categories_for_select($parent_id, $path_number, $category_parent_id, $current_category_id) {
  
  global $db_link;
  global $current_language_id;
  
  $query = "SELECT `categories`.`category_id`,`categories`.`category_hierarchy_level`,`categories`.`category_sort_order`,`categories_descriptions`.`cd_name` 
              FROM `categories` 
        INNER JOIN `categories_descriptions` USING(`category_id`)
             WHERE `categories`.`category_parent_id` = '$parent_id' AND `categories_descriptions`.`language_id` = '$current_language_id' 
          ORDER BY `category_sort_order` ASC";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $parent_id = $row['category_id'];
      $parent_name = stripslashes($row['cd_name']);
      $category_hierarchy_level = $row['category_hierarchy_level'];
      $category_sort_order = $row['category_sort_order'];
      if($category_hierarchy_level == 1) {
          $path_number++;
          $option_path_number = $path_number;
      } else {
          $option_path_number = "&nbsp;$path_number. $category_sort_order";
      }
      if ($category_parent_id == $parent_id)
          $selected = 'selected="selected"';
      else
          $selected = "";
      if($current_category_id != $parent_id) {
        
        echo "<option value='$parent_id.$category_hierarchy_level' level='$category_hierarchy_level' $selected>$option_path_number. $parent_name</option>";
      
        list_categories_for_select($parent_id, $option_path_number, $category_parent_id, $current_category_id);
      } 
    }
    mysqli_free_result($result);
  }
}

function list_categories_with_checkboxes($category_parent_id,$category_root_id,$category_ids_tree) {

  global $db_link;
  global $current_language_id;
  global $current_lang;

  $and_root_id = ($category_root_id == 0) ? "" : " AND `ctc`.`category_root_id` = '$category_root_id'";
  $query_categories = "SELECT `categories`.`category_id`,`ctc`.`category_root_id`,`ctc`.`category_hierarchy_level`,`ctc`.`category_hierarchy_ids`,
                              `ctc`.`category_sort_order`,`ctc`.`category_has_children`,`cd`.`cd_name`
                         FROM `categories`
                   INNER JOIN `category_to_category` as `ctc` USING(`category_id`)
                   INNER JOIN `categories_descriptions` as `cd` USING(`category_id`)
                        WHERE `ctc`.`category_parent_id` = '$category_parent_id' $and_root_id
                          AND `cd`.`language_id` = '$current_language_id'
                     ORDER BY `ctc`.`category_sort_order` ASC";
  //echo $query_categories;exit;
  $result_categories = mysqli_query($db_link, $query_categories);
  if (!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if ($category_count > 0) {

    while ($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id'];
      $category_root_id = $category_row['category_root_id'];
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_hierarchy_ids = $category_row['category_hierarchy_ids'];
      $category_id_tree = str_replace(".", "", $category_hierarchy_ids);
      $category_sort_order = $category_row['category_sort_order'];
      $category_has_children = $category_row['category_has_children'];
      $cd_name = $category_row['cd_name'];

      $class_li = "";
      $class_ul = "";
      
      $category_is_last_child = false;
      if($category_hierarchy_level > 1) $category_is_last_child = check_if_this_is_category_last_child($category_root_id, $category_parent_id, $category_sort_order);
      if($category_has_children == 1) {
        $class_li = "expandable";
      }
      if(is_array($category_ids_tree)) {
        if(in_array($category_id_tree, $category_ids_tree)) {
          $checkbox_checked = "checked='checked'";
          $input_disabled = "";
        }
        else {
          $checkbox_checked = "";
          $input_disabled = "disabled='disabled'";
        }
      }
      else {
        if($category_ids_tree == $category_id_tree) {
          $checkbox_checked = "checked='checked'";
          $input_disabled = "";
        }
        else {
          $checkbox_checked = "";
          $input_disabled = "disabled='disabled'";
        }
      }

      if ($category_has_children == 1) {
?>
      <li id="<?=$category_id_tree;?>" data-level="<?= $category_hierarchy_level; ?>" class="level_<?= $category_hierarchy_level; ?> <?= "$class_li"; ?>">
        <i class="fa fa-lg fa-plus-square-o icon fa_<?=$category_id_tree;?>" aria-hidden="true"></i>
        <input type="checkbox" value="<?=$category_id_tree;?>" data-level="<?=$category_hierarchy_level;?>" name="categories[]" class="level_<?=$category_hierarchy_level;?> categories" <?=$checkbox_checked;?> />
        <input type="hidden" value="<?=$category_id;?>" name="category_ids[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$category_root_id;?>" name="category_root_ids[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$category_hierarchy_level;?>" name="category_hierarchy_levels[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$category_hierarchy_ids;?>" name="category_hierarchy_ids[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$cd_name;?>" name="category_categories_names[<?=$category_id_tree;?>]" class="category_name_<?=$category_id_tree;?>" <?=$input_disabled;?> />
        <a href="javascript:;" class="dropdown_link dropdown_link_<?= $category_hierarchy_level; ?> level_<?= $category_hierarchy_level; ?>">
          <span class="category_name"><?= "$cd_name"; ?></span>
          <span class="category_count_box">(<span class="category_count_digits"></span> <span class="category_count_text"></span>)</span>
        </a>
        <ul class="expandable_ul expandable_ul_<?=$category_id_tree;?>">
<?php
          list_categories_with_checkboxes($category_id,$category_root_id,$category_ids_tree);
      } else {
?>
      <li id="<?=$category_id_tree;?>" class="level_<?= $category_hierarchy_level; ?> <?= "$class_li"; ?>">
        <i class="fa fa-lg"></i>
        <input type="checkbox" value="<?=$category_id_tree;?>" data-level="<?=$category_hierarchy_level;?>" class="categories" name="categories[]" <?=$checkbox_checked;?> />
        <input type="hidden" value="<?=$category_id;?>" name="category_ids[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$category_root_id;?>" name="category_root_ids[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$category_hierarchy_level;?>" name="category_hierarchy_levels[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$category_hierarchy_ids;?>" name="category_hierarchy_ids[<?=$category_id_tree;?>]" />
        <input type="hidden" value="<?=$cd_name;?>" name="category_categories_names[<?=$category_id_tree;?>]" class="category_name_<?=$category_id_tree;?>" <?=$input_disabled;?> />
        <a href="javascript:;" class="level_<?= $category_hierarchy_level; ?>">
          <span class="category_name"><?= "$cd_name"; ?></span>
        </a>
      </li>
<?php
      }
      if ($category_hierarchy_level > 1 && $category_is_last_child) {
?>
        </ul>
      </li>
<?php
      }
    }
  }
}

function list_sliders() {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  
  $query_sliders = "SELECT `sliders`.`slider_id`,`sliders`.`slider_image`,`sliders`.`slider_is_active`,`sliders`.`slider_sort_order`,
                           `sliders_descriptions`.`slider_header`,`sliders_descriptions`.`slider_text`
                      FROM `sliders`
                INNER JOIN `sliders_descriptions` ON `sliders_descriptions`.`slider_id` = `sliders`.`slider_id`
                     WHERE `sliders_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `sliders`.`slider_sort_order` ASC";
  //echo $query_sliders;exit;
  $result_sliders = mysqli_query($db_link, $query_sliders);
  if(!$result_sliders) echo mysqli_error($db_link);
  $sliders_count = mysqli_num_rows($result_sliders);
  if($sliders_count > 0) {
    $key = 0;
    
    while($slider_row = mysqli_fetch_assoc($result_sliders)) {
      $slider_id = $slider_row['slider_id'];
      $slider_header = stripslashes($slider_row['slider_header']);
      $slider_text = stripslashes($slider_row['slider_text']);
      $slider_is_active = $slider_row['slider_is_active'];
      $slider_sort_order = $slider_row['slider_sort_order'];
      $set_sliders = ($slider_is_active == 1) ? 0 : 1;
      $slider_image = $slider_row['slider_image'];
      if(!empty($slider_image)) {
        $slider_image_exploded = explode(".", $slider_image);
        $slider_image_name = $slider_image_exploded[0];
        $slider_image_exstension = $slider_image_exploded[1];
        $slider_image_thumb = SITEFOLDERSL."/images/sliders/".$slider_image_name."_admin_thumb.".$slider_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $slider_image_thumb = SITEFOLDERSL."/images/no_image_172x120.jpg";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/sliders/sliders.php?slider_id=$slider_id";
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$slider_id;?>" class="<?=$class?>">
          <td width="2%" class="text_left"><?=$slider_id;?></td>
          <td width="43%" class="text_left">
            <span class="red_link"><img src="<?=$slider_image_thumb;?>" alt="<?=$slider_header;?>" <?=$thumb_image_dimensions;?>></span>
          </td>
          <td width="30%" class="text_left"><?=$slider_header;?></td>
          <td width="5%">
            <a href="javascript:;" class="edit_link" onclick="SetSliderActiveInactive(this,'<?=$slider_id;?>', '<?=$set_sliders;?>')">
              <?php if($slider_is_active == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
              <?php } else { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
              <?php } ?>
            </a>
          </td>
          <td width="10%">
            <?php
              // if($sliders_count > 1) we gonna give the appropriate moving options
              // else we gonna leave this empty
              if($sliders_count > 1) {
                if($key == 0) {
            ?>
                <a href="javascript:;" class="edit_link" onclick="MoveSliderForwardBackward('<?=$slider_id;?>','<?=$slider_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $sliders_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveSliderForwardBackward('<?=$slider_id;?>','<?=$slider_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveSliderForwardBackward('<?=$slider_id;?>','<?=$slider_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveSliderForwardBackward('<?=$slider_id;?>','<?=$slider_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php 
                }
              } // if($count_options > 1)
            ?>
          </td>
          <td width="5%">
            <a href="<?=$edit_link;?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="5%">
            <a href="javascript:;" class="delete_slider_link delete_link" data-id="<?=$slider_id;?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
<?php
      $key++;
    }
    mysqli_free_result($result_sliders);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_slider_warning']?></p>
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
            DeleteSlider();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_slider_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_slider_link").click(function() {
        $(".delete_slider_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_testimonials() {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  
  $query_testimonials = "SELECT `testimonials`.`testimonial_id`,`testimonials`.`testimonial_image`,`testimonials`.`testimonial_is_active`,`testimonials`.`testimonial_sort_order`,
                                `testimonials_descriptions`.`testimonial_author`,`testimonials_descriptions`.`testimonial_text`
                           FROM `testimonials`
                     INNER JOIN `testimonials_descriptions` ON `testimonials_descriptions`.`testimonial_id` = `testimonials`.`testimonial_id`
                          WHERE `testimonials_descriptions`.`language_id` = '$current_language_id'
                       ORDER BY `testimonial_sort_order` ASC";
  //echo $query_testimonials;exit;
  $result_testimonials = mysqli_query($db_link, $query_testimonials);
  if(!$result_testimonials) echo mysqli_error($db_link);
  $testimonials_count = mysqli_num_rows($result_testimonials);
  if($testimonials_count > 0) {
    $key = 0;
    
    while($testimonial_row = mysqli_fetch_assoc($result_testimonials)) {
      $testimonial_id = $testimonial_row['testimonial_id'];
      $testimonial_author = stripslashes($testimonial_row['testimonial_author']);
      $testimonial_text = stripslashes($testimonial_row['testimonial_text']);
      $testimonial_is_active = $testimonial_row['testimonial_is_active'];
      $testimonial_sort_order = $testimonial_row['testimonial_sort_order'];
      $set_testimonials = ($testimonial_is_active == 1) ? 0 : 1;
      $testimonial_image = $testimonial_row['testimonial_image'];
      if(!empty($testimonial_image)) {
        $testimonial_image_exstension = pathinfo($testimonial_image, PATHINFO_EXTENSION);
        $testimonial_image_name = str_replace(".$testimonial_image_exstension", "", $testimonial_image);
        $testimonial_image_thumb = SITEFOLDERSL."/images/testimonials/".$testimonial_image_name."_site.".$testimonial_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$testimonial_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $testimonial_image_thumb = SITEFOLDERSL."/images/no_image_172x120.jpg";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$testimonial_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/testimonials/testimonials.php?testimonial_id=$testimonial_id";
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$testimonial_id;?>" class="<?=$class?>">
          <td width="2%" class="text_left"><?=$testimonial_id;?></td>
          <td width="43%" class="text_left">
            <span class="red_link"><img src="<?=$testimonial_image_thumb;?>" alt="<?=$testimonial_author;?>" <?=$thumb_image_dimensions;?>></span>
          </td>
          <td width="30%" class="text_left"><?=$testimonial_author;?></td>
          <td width="5%">
            <a href="javascript:;" class="edit_link" onclick="SetTestimonialActiveInactive(this,'<?=$testimonial_id;?>', '<?=$set_testimonials;?>')">
              <?php if($testimonial_is_active == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
              <?php } else { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
              <?php } ?>
            </a>
          </td>
          <td width="10%">
            <?php
              // if($testimonials_count > 1) we gonna give the appropriate moving options
              // else we gonna leave this empty
              if($testimonials_count > 1) {
                if($key == 0) {
            ?>
                <a href="javascript:;" class="edit_link" onclick="MoveTestimonialForwardBackward('<?=$testimonial_id;?>','<?=$testimonial_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $testimonials_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveTestimonialForwardBackward('<?=$testimonial_id;?>','<?=$testimonial_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveTestimonialForwardBackward('<?=$testimonial_id;?>','<?=$testimonial_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveTestimonialForwardBackward('<?=$testimonial_id;?>','<?=$testimonial_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php 
                }
              } // if($count_options > 1)
            ?>
          </td>
          <td width="5%">
            <a href="<?=$edit_link;?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="5%">
            <a href="javascript:;" class="delete_testimonial_link delete_link" data-id="<?=$testimonial_id;?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
<?php
      $key++;
    }
    mysqli_free_result($result_testimonials);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_testimonial_warning']?></p>
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
            DeleteTestimonial();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_testimonial_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_testimonial_link").click(function() {
        $(".delete_testimonial_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_banners() {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  
  $query_banners = "SELECT `banner_id`, `banner_image`, `banner_is_active`, `banner_sort_order` FROM `banners` ORDER BY `banner_sort_order` ASC";
  //echo $query_banners;exit;
  $result_banners = mysqli_query($db_link, $query_banners);
  if(!$result_banners) echo mysqli_error($db_link);
  $banners_count = mysqli_num_rows($result_banners);
  if($banners_count > 0) {
    $key = 0;
    
    while($banner_row = mysqli_fetch_assoc($result_banners)) {

      $banner_id = $banner_row['banner_id'];
      $banner_image = $banner_row['banner_image'];
      $banner_image_exstension = pathinfo($banner_image, PATHINFO_EXTENSION);
      $banner_image_name = str_replace(".$banner_image_exstension", "", $banner_image);
      $banner_image_thumb = SITEFOLDERSL."/images/banners/".$banner_image_name."_admin_thumb.".$banner_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$banner_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
      @$image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$banner_image);
      $image_dimensions = $image_params[3];
      $banner_is_active = $banner_row['banner_is_active'];
      $banner_sort_order = $banner_row['banner_sort_order'];
      $set_banners = ($banner_is_active == 1) ? 0 : 1;
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/banners/banners.php?banner_id=$banner_id";
?>
    <table id="banner_<?=$banner_id;?>" class="row_over">
      <tbody>
        <tr class="<?=$class?>">
          <td width="2%" class="text_left"><?=$banner_id;?></td>
          <td width="63%" class="text_left">
            <span class="red_link"><img src="<?=$banner_image_thumb;?>" alt="" <?=$image_dimensions;?>></span>
          </td>
          <td width="10%">
            <a href="javascript:;" class="edit_link" onclick="SetBannerActiveInactive(this,'<?=$banner_id;?>', '<?=$set_banners;?>')">
              <?php if($banner_is_active == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
              <?php } else { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
              <?php } ?>
            </a>
          </td>
          <td width="10%">
            <?php
              // if($banners_count > 1) we gonna give the appropriate moving options
              // else we gonna leave this empty
              if($banners_count > 1) {
                if($key == 0) {
            ?>
                <a href="javascript:;" class="edit_link" onclick="MoveBannerForwardBackward('<?=$banner_id;?>','<?=$banner_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $banners_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveBannerForwardBackward('<?=$banner_id;?>','<?=$banner_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveBannerForwardBackward('<?=$banner_id;?>','<?=$banner_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveBannerForwardBackward('<?=$banner_id;?>','<?=$banner_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php 
                }
              } // if($count_options > 1)
            ?>
          </td>
          <td width="7.5%">
            <a href="<?=$edit_link;?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="7.5%">
            <a href="javascript:;" class="delete_banner_link delete_link" data-id="<?=$banner_id;?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
<?php
      $key++;
    }
    mysqli_free_result($result_banners);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_banner_warning']?></p>
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
            DeleteBanner();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_banner_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_banner_link").click(function() {
        $(".delete_banner_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_social_networks_for_select($current_social_network_id) {
  
  global $db_link;
  global $current_language_id;
  
  $query_social_networks = "SELECT `social_networks`.* FROM `social_networks` WHERE `social_network_is_active` = '1' ORDER BY `social_network_sort_order` ASC";
  //echo $query_social_networks;exit;
  $result_social_networks = mysqli_query($db_link, $query_social_networks);
  if(!$result_social_networks) echo mysqli_error($db_link);
  $social_networks_count = mysqli_num_rows($result_social_networks);
  if($social_networks_count > 0) {

    while($social_network_row = mysqli_fetch_assoc($result_social_networks)) {

      $social_network_id = $social_network_row['social_network_id'];
      $social_network_name = $social_network_row['social_network_name'];
      $social_network_unicode = $social_network_row['social_network_unicode'];

      if($current_social_network_id == $social_network_id)
          $selected = 'selected="selected"';
      else
          $selected = "";
        
      echo "<option value='$social_network_id' $selected>$social_network_unicode | $social_network_name</option>";

    }
    mysqli_free_result($result_social_networks);
  }
}

function print_category_name($category_id) {
  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;

  $query_category_name = "SELECT `categories_descriptions`.`cd_name` 
                            FROM `categories`
                      INNER JOIN `categories_descriptions` USING(`category_id`)
                           WHERE `categories`.`category_id` = '$category_id' AND `categories_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_category_name;exit;
  $result_category_name = mysqli_query($db_link, $query_category_name);
  if(!$result_category_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_name)) {
    $category_row = mysqli_fetch_assoc($result_category_name);
    $cd_name = $category_row['cd_name'];
    echo $cd_name;
  }
}

function list_languages() {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $users_rights_edit;
  global $users_rights_delete;
  
  $query_languages = "SELECT `language_id`, `language_code`, `language_name`, `language_menu_order`, 
                             `language_is_default_frontend`,`language_is_default_backend`, `language_is_active` 
                        FROM `languages`
                    ORDER BY `language_menu_order` ASC";
  //echo $query_content;exit;
  $result_languages = mysqli_query($db_link, $query_languages);
  if(!$result_languages) echo mysqli_error($db_link);
  $language_count = mysqli_num_rows($result_languages);
  if($language_count > 0) {
    $key = 0;

    while($language_row = mysqli_fetch_assoc($result_languages)) {
      $language_id = $language_row['language_id'];
      $language_code = $language_row['language_code'];
      $language_name = $language_row['language_name'];
      $language_menu_order = $language_row['language_menu_order'];
      $language_is_default_frontend = $language_row['language_is_default_frontend'];
      $language_is_default_backend = $language_row['language_is_default_backend'];
      $language_is_active = $language_row['language_is_active'];
      $set_language = ($language_is_active == 1) ? 0 : 1;
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/language/languages.php?language_id=$language_id";
?>
      <table class="row_over">
        <tbody>
          <tr id="tr_<?=$language_id;?>" class="<?=$class?>">
            <td width="20%" class="text_left">
              <span class="red_link"><?=$language_code;?></span>
            </td>
            <td width="20%" class="text_left"><?=$language_name;?></td>
            <td width="5%">
              <a href="javascript:;" class="edit_link" onclick="SetLanguageActiveInactive(this,'<?=$language_id;?>', '<?=$set_language;?>')">
                <?php if($language_is_active == 1) { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
                <?php } else { ?>
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
                <?php } ?>
              </a>
            </td>
            <td width="15%">
              <?php if($language_is_default_frontend == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_language_is_default_frontend'];?>" title="<?=$languages['title_language_is_default_frontend'];?>" width="16" height="16" />
              <?php } else { ?>
              <a href="javascript:;" class="edit_link" onclick="if(confirm('<?=$languages['set_language_default_warning_1']." $language_name ".$languages['set_language_default_warning_2'];?>')) SetLanguageDefault('<?=$language_id;?>','_frontend');">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_set_language_default'];?>" title="<?=$languages['title_set_language_default'];?>" width="16" height="16" />
              </a>
              <?php } ?>
            </td>
            <td width="15%">
              <?php if($language_is_default_backend == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_language_is_default_backend'];?>" title="<?=$languages['title_language_is_default_backend'];?>" width="16" height="16" />
              <?php } else { ?>
              <a href="javascript:;" class="edit_link" onclick="if(confirm('<?=$languages['set_language_default_warning_1']." $language_name ".$languages['set_language_default_warning_2'];?>')) SetLanguageDefault('<?=$language_id;?>','_backend');">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_set_language_default'];?>" title="<?=$languages['title_set_language_default'];?>" width="16" height="16" />
              </a>
              <?php } ?>
            </td>
            <td width="10%">
              <?php
                // if($language_count > 1) we gonna give the appropriate moving options
                // else we gonna leave this empty
                if($language_count > 1) {
                  if($key == 0) {
              ?>
                  <a href="javascript:;" class="edit_link" onclick="MoveLanguageForwardBackward('<?=$language_id;?>','<?=$language_menu_order;?>','backward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_language_backward'];?>" title="<?=$languages['title_move_language_backward'];?>" width="16" height="16" />
                  </a>
                <?php } elseif($key == $language_count-1) { ?>
                  <a href="javascript:;" class="edit_link" onclick="MoveLanguageForwardBackward('<?=$language_id;?>','<?=$language_menu_order;?>','forward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_language_forward'];?>" title="<?=$languages['title_move_language_forward'];?>" width="16" height="16" />
                  </a>
                <?php } else { ?>
                  <a href="javascript:;" class="edit_link" onclick="MoveLanguageForwardBackward('<?=$language_id;?>','<?=$language_menu_order;?>','backward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_language_backward'];?>" title="<?=$languages['title_move_language_backward'];?>" width="16" height="16" />
                  </a>
                  <a href="javascript:;" class="edit_link" onclick="MoveLanguageForwardBackward('<?=$language_id;?>','<?=$language_menu_order;?>','forward')">
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_language_forward'];?>" title="<?=$languages['title_move_language_forward'];?>" width="16" height="16" />
                  </a>
              <?php 
                  }
                } // if($language_count > 1)
              ?>
            </td>
            <td width="5%">
              <a href="<?=$edit_link;?>" class="edit_link">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit_language'];?>" title="<?=$languages['title_edit_language'];?>" width="16" height="16" />
              </a>
            </td>
            <td width="5%">
              <a href="javascript:;" class="delete_language_link delete_link" data-id="<?=$language_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete_language'];?>" title="<?=$languages['title_delete_language'];?>" width="16" height="16" />
              </a>
            </td>
          </tr>
        </tbody>
      </table>
<?php
      $key++;
    }
    mysqli_free_result($result_languages);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_language_warning']?></p>
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
            DeleteLanguage('first');
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_language_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_language_link").click(function() {
        $(".delete_language_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");

      });
    });
    </script>
<?php
    }
  }
  else echo "<tr class='even'><td width='20%' class='text_left'>".$languages['no_languages_in_database_yet']."</td><td colspan='6'>&nbsp;</td></tr>";
}

function list_contacts() {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
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
    $key = 0;
    
    while($contact_row = mysqli_fetch_assoc($result_contacts)) {
      
      $contact_id = $contact_row['contact_id'];
      $contact_city = $contact_row['contact_city'];
      $contact_address = stripslashes($contact_row['contact_address']);
      $contact_postcode = $contact_row['contact_postcode'];
      $contact_info = stripslashes($contact_row['contact_info']);
      $contact_address .= (!empty($contact_info)) ? " ($contact_info)" : "";
      $contact_is_active = $contact_row['contact_is_active'];
      $contact_is_default = $contact_row['contact_is_default'];
      $contact_sort_order = $contact_row['contact_sort_order'];
      $set_contacts = ($contact_is_active == 1) ? 0 : 1;
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/contacts/contacts.php?contact_id=$contact_id";
?>
    <table id="contact_<?=$contact_id;?>" class="row_over">
      <tbody>
        <tr class="<?=$class?>">
          <td width="2%" class="text_left"><?=$contact_id;?></td>
          <td width="20%" class="text_left red_link"><?=$contact_city;?></td>
          <td width="40%" class="text_left"><?=$contact_address;?></td>
          <td width="13%">
            <?php if($contact_is_default == 1) { ?>
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_contact_is_default'];?>" title="<?=$languages['title_contact_is_default'];?>" width="16" height="16" />
            <?php } else { ?>
            <a href="javascript:;" class="edit_link set_contact_default" data-id="<?=$contact_id;?>" title="<?=$languages['title_set_contact_default'];?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_set_contact_default'];?>" width="16" height="16" />
            </a>
            <?php } ?>
          </td>
          <td width="5%">
            <a href="javascript:;" class="edit_link" onclick="SetContactActiveInactive(this,'<?=$contact_id;?>', '<?=$set_contacts;?>')">
              <?php if($contact_is_active == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
              <?php } else { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
              <?php } ?>
            </a>
          </td>
          <td width="10%">
            <?php
              // if($contacts_count > 1) we gonna give the appropriate moving options
              // else we gonna leave this empty
              if($contacts_count > 1) {
                if($key == 0) {
            ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContactForwardBackward('<?=$contact_id;?>','<?=$contact_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $contacts_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContactForwardBackward('<?=$contact_id;?>','<?=$contact_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveContactForwardBackward('<?=$contact_id;?>','<?=$contact_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveContactForwardBackward('<?=$contact_id;?>','<?=$contact_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php 
                }
              } // if($count_options > 1)
            ?>
          </td>
          <td width="5%">
            <a href="<?=$edit_link;?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="5%">
            <a href="javascript:;" class="delete_contact_link delete_link" data-id="<?=$contact_id;?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
<?php
      $key++;
    }
    mysqli_free_result($result_contacts);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_contact_warning']?></p>
    </div>
    <div style="display:none;" id="modal_confirm_set_contact_default" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['set_contact_default_warning']?></p>
    </div>
    <script>
    $(function() {
      $("#modal_confirm_set_contact_default").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm",
        buttons: {
          "<?=$languages['btn_yes'];?>": function() {
            var contact_id = $(".set_contact_default.active").attr("data-id");
            SetContactDefault(contact_id);
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".set_contact_default").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".set_contact_default").click(function() {
        $(".set_contact_default").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm_set_contact_default").dialog("open");
      });
      
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
            DeleteContact();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_contact_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_contact_link").click(function() {
        $(".delete_contact_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_contacts_socials() {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  
  $query_contact_socials = "SELECT `contacts_socials`.`contact_social_id`,`contacts_socials`.`contact_social_address`,`contacts_socials`.`contact_social_icon`,
                                   `contacts_socials`.`contact_social_is_active`,`contacts_socials`.`contact_social_sort_order`,`social_networks`.`social_network_name`,
                                   `social_networks`.`social_network_icon`
                              FROM `contacts_socials`
                        INNER JOIN `social_networks` ON `social_networks`.`social_network_id` = `contacts_socials`.`social_network_id`
                          ORDER BY `contacts_socials`.`contact_social_sort_order` ASC";
  //echo $query_contact_socials;exit;
  $result_contact_socials = mysqli_query($db_link, $query_contact_socials);
  if(!$result_contact_socials) echo mysqli_error($db_link);
  $contact_socials_count = mysqli_num_rows($result_contact_socials);
  if($contact_socials_count > 0) {
    $key = 0;

    while($contact_social_row = mysqli_fetch_assoc($result_contact_socials)) {

      $contact_social_id = $contact_social_row['contact_social_id'];
      $contact_social_address = $contact_social_row['contact_social_address'];
      $contact_social_icon = $contact_social_row['contact_social_icon'];
      $social_network_name = $contact_social_row['social_network_name'];
      $social_network_icon = $contact_social_row['social_network_icon'];
      $contact_social_sort_order = $contact_social_row['contact_social_sort_order'];
      $contact_social_is_active = $contact_social_row['contact_social_is_active'];
      $set_contact_socials = ($contact_social_is_active == 1) ? 0 : 1;
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/contacts/contacts-socials.php?contact_social_id=$contact_social_id";
?>
    <table id="contact_social_<?=$contact_social_id;?>" class="row_over">
      <tbody>
        <tr class="<?=$class?>">
          <td width="2%" class="text_left"><?=$contact_social_id;?></td>
          <td width="20%" class="text_left red_link"><?="$social_network_icon - $social_network_name";?></td>
          <td width="48%" class="text_left"><?=$contact_social_address;?></td>
          <td width="10%">
            <a href="javascript:;" class="edit_link" onclick="SetContactSocialActiveInactive(this,'<?=$contact_social_id;?>', '<?=$set_contact_socials;?>')">
              <?php if($contact_social_is_active == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
              <?php } else { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
              <?php } ?>
            </a>
          </td>
          <td width="10%">
          <?php
            // if($contact_socials_count > 1) we gonna give the appropriate moving options
            // else we gonna leave this empty
            if($contact_socials_count > 1) {
              if($key == 0) {
          ?>
              <a href="javascript:;" class="edit_link" onclick="MoveContactSocialForwardBackward('<?=$contact_social_id;?>','<?=$contact_social_sort_order;?>','backward')">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
              </a>
            <?php } elseif($key == $contact_socials_count-1) { ?>
              <a href="javascript:;" class="edit_link" onclick="MoveContactSocialForwardBackward('<?=$contact_social_id;?>','<?=$contact_social_sort_order;?>','forward')">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
              </a>
            <?php } else { ?>
              <a href="javascript:;" class="edit_link" onclick="MoveContactSocialForwardBackward('<?=$contact_social_id;?>','<?=$contact_social_sort_order;?>','backward')">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
              </a>
              <a href="javascript:;" class="edit_link" onclick="MoveContactSocialForwardBackward('<?=$contact_social_id;?>','<?=$contact_social_sort_order;?>','forward')">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
              </a>
          <?php 
              }
            } // if($count_options > 1)
          ?>
          </td>
          <td width="5%">
            <a href="<?=$edit_link;?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="5%">
            <a href="javascript:;" class="delete_contact_social_link delete_link" data-id="<?=$contact_social_id;?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
<?php
      $key++;
    }
    mysqli_free_result($result_contact_socials);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_contact_social_warning']?></p>
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
            DeleteContactSocial();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_contact_social_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_contact_social_link").click(function() {
        $(".delete_contact_social_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_news_categories($news_cat_parent_id, $path_number) {
  
  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $class;
  
  $query_news_categories = "SELECT `news_categories`.`news_category_id`,`news_categories`.`news_cat_parent_id`,`news_categories`.`news_cat_hierarchy_level`,
                                   `news_categories`.`news_cat_has_children`,`news_categories`.`news_cat_sort_order`,`news_categories`.`news_cat_is_collapsed`,
                                   `news_cat_desc`.`news_cat_name`,`news_cat_desc`.`news_cat_long_name` 
                              FROM `news_categories` 
                        INNER JOIN `news_cat_desc` USING(`news_category_id`)
                             WHERE `news_categories`.`news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_desc`.`language_id` = '$current_language_id'
                          ORDER BY `news_categories`.`news_cat_sort_order` ASC";
  //echo "<pre>$query_news_categories</pre>";exit;
  $result_news_categories = mysqli_query($db_link, $query_news_categories);
  if(!$result_news_categories) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news_categories);
  if($news_count > 0) {
    $key = 0;
    while($news_categories_row = mysqli_fetch_assoc($result_news_categories)) {
      $news_category_id = $news_categories_row['news_category_id'];
      $news_cat_parent_id = $news_categories_row['news_cat_parent_id'];
      $news_cat_name = $news_categories_row['news_cat_name'];
      $news_cat_hierarchy_level = $news_categories_row['news_cat_hierarchy_level'];
      $news_cat_has_children = $news_categories_row['news_cat_has_children'];
      $news_cat_sort_order = $news_categories_row['news_cat_sort_order'];
      $news_cat_is_collapsed = $news_categories_row['news_cat_is_collapsed'];
      $name_dashes = "";
      if($news_cat_hierarchy_level == 1) {
          $path_number++;
          $news_cat_path_number = $path_number;
      } else {
          $news_cat_path_number = "$path_number.$news_cat_sort_order";
          if($news_cat_hierarchy_level == 2) $name_dashes = "- ";
          if($news_cat_hierarchy_level == 3) $name_dashes = "- - ";
          if($news_cat_hierarchy_level == 4) $name_dashes = "- - - ";
          if($news_cat_hierarchy_level == 5) $name_dashes = "- - - - ";
      }
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/news/news-categories.php?news_category_id=$news_category_id";
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$news_category_id;?>" class="<?=$class?>">
          <td width="2%" class="text_left">
            <a href="javascript:;" onclick="ToggleExpandNewsCategory('<?=$news_category_id;?>', '<?php if($news_cat_is_collapsed == 1) echo "expand"; else echo "collapse" ?>');">
              <?php 
                if($news_cat_has_children == 0) {
                  // no children, print nothing
                } else {
                    if($news_cat_is_collapsed == 0) { ?>
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/contract.gif" class="systemicon" alt="<?=$languages['alt_collapse_section'];?>" title="<?=$languages['title_collapse_section'];?>" width="16" height="16" />
                  <?php } else { ?>
                    <img src="/<?=$_SESSION['admin_dir_name'];?>/images/expand.gif" class="systemicon" alt="<?=$languages['alt_open_section'];?>" title="<?=$languages['title_open_section'];?>" width="16" height="16" />
                  <?php 
                  } 
                }
              ?>
            </a>
          </td>
          <td width="5%" class="text_left"><?=$news_cat_path_number;?></td>
          <td width="63%" class="text_left">
            <span class="red_link"><?="$name_dashes$news_cat_name";?></span>
          </td>
          <td width="15%">
            <?php
              // if($news_count > 1) we gonna give the appropriate moveing options
              // else we gonna leave this empty
              if($news_count > 1) {
                if($key == 0) {
            ?>
                <a href="javascript:;" class="edit_link" onclick="MoveNewsCategoryForwardBackward('<?=$news_category_id;?>','<?=$news_cat_parent_id;?>','<?=$news_cat_sort_order;?>','<?=$news_cat_hierarchy_level;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $news_count-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveNewsCategoryForwardBackward('<?=$news_category_id;?>','<?=$news_cat_parent_id;?>','<?=$news_cat_sort_order;?>','<?=$news_cat_hierarchy_level;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveNewsCategoryForwardBackward('<?=$news_category_id;?>','<?=$news_cat_parent_id;?>','<?=$news_cat_sort_order;?>','<?=$news_cat_hierarchy_level;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveNewsCategoryForwardBackward('<?=$news_category_id;?>','<?=$news_cat_parent_id;?>','<?=$news_cat_sort_order;?>','<?=$news_cat_hierarchy_level;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php 
                }
              } // if($news_count > 1)
            ?>
          </td>
          <td width="7.5%">
            <a href="<?=$edit_link;?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="7.5%">
            <?php if($news_cat_has_children == 0) { ?>
              <a href="javascript:;" class="delete_news_category_link delete_link" data-id="<?=$news_category_id;?>" data-parent-id="<?=$news_cat_parent_id;?>">
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
              </a>
            <?php } ?>
          </td>
        </tr>
      </tbody>
    </table>
<?php
      if($news_cat_is_collapsed == 0 && $news_cat_has_children == 1) {
        list_news_categories($news_category_id, $news_cat_path_number);
      }
      $key++;
    }
    mysqli_free_result($result_news_categories);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_news_category_warning']?></p>
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
            DeleteNewsCategory();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_news_category_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_news_category_link").click(function() {
        $(".delete_news_category_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_news_categories_for_reorder($news_cat_parent_id, $path_number) {
  
  global $db_link;
  global $current_language_id;
  
  $query_news_categories = "SELECT `news_categories`.`news_category_id`,`news_categories`.`news_cat_parent_id`,`news_categories`.`news_cat_hierarchy_level`,
                                   `news_categories`.`news_cat_has_children`,`news_categories`.`news_cat_sort_order`,`news_cat_desc`.`news_cat_name` 
                              FROM `news_categories` 
                        INNER JOIN `news_cat_desc` USING(`news_category_id`)
                             WHERE `news_categories`.`news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_desc`.`language_id` = '$current_language_id'
                          ORDER BY `news_categories`.`news_cat_sort_order` ASC";
  //echo "<pre>$query_news_categories</pre>";exit;
  $result_news_categories = mysqli_query($db_link, $query_news_categories);
  if(!$result_news_categories) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news_categories);
  if($news_count > 0) {
    
    if($news_cat_parent_id == 0) {
      echo "<ul class='sortable sortable_list'>";
    }
    while($news_categories_row = mysqli_fetch_assoc($result_news_categories)) {
      $news_category_id = $news_categories_row['news_category_id'];
      $news_cat_parent_id = $news_categories_row['news_cat_parent_id'];
      $news_cat_name = $news_categories_row['news_cat_name'];
      $news_cat_hierarchy_level = $news_categories_row['news_cat_hierarchy_level'];
      $news_cat_has_children = $news_categories_row['news_cat_has_children'];
      $news_cat_sort_order = $news_categories_row['news_cat_sort_order'];
      if($news_cat_hierarchy_level == 1) {
          $path_number++;
          $option_path_number = $path_number;
      } else {
          $option_path_number = "&nbsp;$path_number. $news_cat_sort_order";
      }
      
      $class_expanded_leaf = ($news_cat_has_children == 1) ? "mjs-nestedSortable-branch mjs-nestedSortable-expanded" : "mjs-nestedSortable-leaf";
      $expand_collapse_node = ($news_cat_has_children == 1) ? "<span title='Click to show/hide children' class='disclose ui-icon ui-icon-minusthick'><span></span></span>" : "";
      echo "<li id='menu_$news_category_id' class='$class_expanded_leaf' data-id='$news_category_id'>
        <input type='hidden' name='news_categories[$news_cat_parent_id][]' value='$news_category_id'>
        <div class='sortable_label'>$expand_collapse_node$option_path_number. $news_cat_name</div>";
      if($news_cat_has_children == 1) {
        echo "<ul class='sortable_list'>";
        
        list_news_categories_for_reorder($news_category_id, $path_number);
        
        echo "</ul>";
      }
      
      echo "</li>";
    }
    mysqli_free_result($result_news_categories);
  }
}

function list_news($filters_array) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  
  $where = "";
  $order_by = "ORDER BY `news`.`news_post_date` DESC";
  $limit = "";
  if(!empty($filters_array)) {
    $category = $filters_array['news_cat_parent_params'];
    if($category != "all") {
      // $_POST['news_cat_parent_params'] has three parameters - parent_id, hierarchy_ids and hierarchy_level
      $news_cat_parent_params = explode("+", $_POST['news_cat_parent_params']);
      $news_cat_id = $news_cat_parent_params[0];
      $news_cat_hierarchy_ids = $news_cat_parent_params[1];
      $news_cat_hierarchy_level = $news_cat_parent_params[2]+1;
      
      $where = "`news`.`news_category_id` = '$news_cat_id' AND";
    }
    $show_subcategories = (isset($filters_array['show_subcategories'])) ? true : false;
    $order_by = ($filters_array['order_by'] == "`news_title` ASC" || $filters_array['order_by'] == "`news_title` DESC") ? "ORDER BY `news_descriptions`.".$filters_array['order_by'] : "ORDER BY `news`.".$filters_array['order_by'];
    $page_limit = $filters_array['page_limit'];
    $limit = "LIMIT $page_limit";
  }
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                        `news`.`news_is_active`,`news_descriptions`.`news_title`,`news_cat_desc`.`news_cat_long_name` 
                   FROM `news` 
             INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
             INNER JOIN `news_cat_desc` ON `news_cat_desc`.`news_category_id` = `news`.`news_category_id`
                  WHERE $where `news_descriptions`.`language_id` = '$current_language_id' AND `news_cat_desc`.`language_id` = '$current_language_id'
                        $order_by
                        $limit";
  //echo "<pre>$query_news</pre>";exit;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news);
  if($news_count > 0) {
    while($news_row = mysqli_fetch_assoc($result_news)) {
      $news_id = $news_row['news_id'];
      $news_title = $news_row['news_title'];
      $news_cat_long_name = $news_row['news_cat_long_name'];
      $news_post_date = date("d.m.Y H:i:s", strtotime($news_row['news_post_date']));
      $news_start_time = (!is_null($news_row['news_start_time'])) ? date("d.m.Y H:i:s", strtotime($news_row['news_start_time'])) : "";
      $news_end_time = (!is_null($news_row['news_end_time'])) ? date("d.m.Y H:i:s", strtotime($news_row['news_end_time'])) : "";
      $news_is_active = $news_row['news_is_active'];
      $set_news = ($news_is_active == 1) ? 0 : 1;
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/news/news.php?news_id=$news_id";
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$news_id;?>" class="<?=$class?>">
          <td width="2%" class="text_left"><?=$news_id;?></td>
          <td width="33%" class="text_left">
            <span class="red_link"><?=$news_title;?></span>
          </td>
          <td width="12%" class="text_left"><?=$news_post_date;?></td>
          <td width="8%" class="text_left"><?=$news_start_time;?></td>
          <td width="8%" class="text_left"><?=$news_end_time;?></td>
          <td width="20%"><?=$news_cat_long_name;?></td>
          <td width="5%">
            <a href="javascript:;" class="edit_link" onclick="SetNewsActiveInactive(this,'<?=$news_id;?>', '<?=$set_news;?>')">
              <?php if($news_is_active == 1) { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/true.gif" class="systemicon img_active" alt="<?=$languages['alt_deactivate'];?>" title="<?=$languages['title_deactivate'];?>" width="16" height="16" />
              <?php } else { ?>
                <img src="/<?=$_SESSION['admin_dir_name'];?>/images/false.gif" class="systemicon img_inactive" alt="<?=$languages['alt_activate'];?>" title="<?=$languages['title_activate'];?>" width="16" height="16" />
              <?php } ?>
            </a>
          </td>
          <td width="3%">
            <a href="<?="/$current_lang/$news_title?nid=$news_id";?>" target="_blank">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/view.gif" class="systemicon" alt="<?=$languages['alt_view'];?>" title="<?=$languages['title_view'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="3%">
            <a href="<?=$edit_link?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="3%">
            <a href="javascript:;" class="delete_news_link delete_link" data-id="<?=$news_id;?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="4%">
            <input type="checkbox" class="multicontent" value="<?=$news_id;?>" name="multicontent[]" title="<?=$languages['title_toggle_checkbox'];?>"/>
          </td>
        </tr>
      </tbody>
    </table>
<?php
    }
    mysqli_free_result($result_news);

    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_news_warning']?></p>
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
            DeleteNews();
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_news_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_news_link").click(function() {
        $(".delete_news_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
}

function list_news_categories_for_select_for_news($current_news_category_id) {
  
  global $db_link;
  $query_news_cat = "SELECT `news_categories`.`news_category_id`,`news_categories`.`news_cat_hierarchy_ids`,`news_categories`.`news_cat_hierarchy_level`,
                            `news_cat_desc`.`news_cat_name`,`news_cat_desc`.`news_cat_long_name` 
                       FROM `news_categories` 
                 INNER JOIN `news_cat_desc` USING(`news_category_id`)
                 INNER JOIN `languages` USING(`language_id`)
                      WHERE `languages`.`language_is_default_backend` = '1'";
  //echo $query_news_cat."<br>";
  $result_news_cat = mysqli_query($db_link, $query_news_cat);
  if(!$result_news_cat) echo mysqli_error($db_link);
  $count_news_cat = mysqli_num_rows($result_news_cat);
  if($count_news_cat > 0) {
    while ($row = mysqli_fetch_assoc($result_news_cat)) {
      $news_category_id = $row['news_category_id'];
      $news_cat_hierarchy_ids = $row['news_cat_hierarchy_ids'];
      $news_cat_hierarchy_level = $row['news_cat_hierarchy_level'];
      //$news_cat_name = stripslashes($row['news_cat_name']);
      $news_cat_long_name = stripslashes($row['news_cat_long_name']);
      $selected = ($current_news_category_id == $news_category_id) ? 'selected="selected"' : "";
      
      echo "<option value='$news_category_id+$news_cat_hierarchy_ids+$news_cat_hierarchy_level' $selected>$news_cat_long_name</option>";
        
    }
    mysqli_free_result($result_news_cat);
  }
}

function list_news_categories_for_select($parent_id,$path_number,$current_news_category_parent_id,$current_news_category_id) {
  
  global $db_link;
  $query_news_cat = "SELECT `news_categories`.`news_category_id`,`news_categories`.`news_cat_hierarchy_ids`,`news_categories`.`news_cat_hierarchy_level`,
                            `news_categories`.`news_cat_sort_order`,`news_cat_desc`.`news_cat_name`,`news_cat_desc`.`news_cat_long_name` 
                       FROM `news_categories` 
                 INNER JOIN `news_cat_desc` USING(`news_category_id`)
                 INNER JOIN `languages` USING(`language_id`)
                      WHERE `news_categories`.`news_cat_parent_id` = '$parent_id' AND `languages`.`language_is_default_backend` = '1'";
  //echo $query_news_cat."<br>";
  $result_news_cat = mysqli_query($db_link, $query_news_cat);
  if(!$result_news_cat) echo mysqli_error($db_link);
  $count_news_cat = mysqli_num_rows($result_news_cat);
  if($count_news_cat > 0) {
    while ($row = mysqli_fetch_assoc($result_news_cat)) {
      $news_category_id = $row['news_category_id'];
      $news_cat_hierarchy_ids = $row['news_cat_hierarchy_ids'];
      $news_cat_hierarchy_level = $row['news_cat_hierarchy_level'];
      $news_cat_sort_order = $row['news_cat_sort_order'];
      $news_cat_long_name = stripslashes($row['news_cat_long_name']);
      if($news_cat_hierarchy_level == 1) {
          $path_number++;
          $option_path_number = $path_number;
      } else {
          $option_path_number = "&nbsp;$path_number. $news_cat_sort_order";
      }
      $selected = ($current_news_category_parent_id == $news_category_id) ? 'selected="selected"' : "";
      
      if($current_news_category_id != $news_category_id ) {
        echo "<option value='$news_category_id+$news_cat_hierarchy_ids+$news_cat_hierarchy_level' $selected>$option_path_number. $news_cat_long_name</option>";

        list_news_categories_for_select($news_category_id,$option_path_number,$current_news_category_parent_id,$current_news_category_id);
      }
        
    }
    mysqli_free_result($result_news_cat);
  }
}

function list_news_categories_with_checkboxes($news_cat_parent_id,$news_category_ids) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;

  $query_categories = "SELECT `news_categories`.`news_category_id`,`news_categories`.`news_cat_hierarchy_level`,`news_categories`.`news_cat_sort_order`,
                              `news_categories`.`news_cat_has_children`,`news_cat_desc`.`news_cat_name`
                         FROM `news_categories`
                   INNER JOIN `news_cat_desc` USING(`news_category_id`)
                        WHERE `news_categories`.`news_cat_parent_id` = '$news_cat_parent_id'
                          AND `news_cat_desc`.`language_id` = '$current_language_id'
                     ORDER BY `news_categories`.`news_cat_sort_order` ASC";
  //echo $query_categories;exit;
  $result_categories = mysqli_query($db_link, $query_categories);
  if (!$result_categories) echo mysqli_error($db_link);
  $news_cat_count = mysqli_num_rows($result_categories);
  if ($news_cat_count > 0) {

    while ($news_cat_row = mysqli_fetch_assoc($result_categories)) {

      $news_category_id = $news_cat_row['news_category_id'];
      $news_cat_hierarchy_level = $news_cat_row['news_cat_hierarchy_level'];
      $news_cat_sort_order = $news_cat_row['news_cat_sort_order'];
      $news_cat_has_children = $news_cat_row['news_cat_has_children'];
      $news_cat_name = $news_cat_row['news_cat_name'];

      $class_li = "";
      $class_ul = "";
      
      $news_cat_is_last_child = false;
      if($news_cat_hierarchy_level > 1) $news_cat_is_last_child = check_if_this_is_news_category_last_child($news_cat_parent_id, $news_cat_sort_order);
      if($news_cat_has_children == 1) {
        $class_li = "expandable";
      }
      if(is_array($news_category_ids)) {
        if(in_array($news_category_id, $news_category_ids)) {
          $checkbox_checked = "checked='checked'";
          $input_disabled = "";
        }
        else {
          $checkbox_checked = "";
          $input_disabled = "disabled='disabled'";
        }
      }
      else {
        if($news_category_ids == $news_category_id) {
          $checkbox_checked = "checked='checked'";
          $input_disabled = "";
        }
        else {
          $checkbox_checked = "";
          $input_disabled = "disabled='disabled'";
        }
      }

      if ($news_cat_has_children == 1) {
?>
      <li id="<?=$news_category_id;?>" data-level="<?= $news_cat_hierarchy_level; ?>" class="level_<?= $news_cat_hierarchy_level; ?> <?= "$class_li"; ?>">
        <i class="fa fa-lg fa-plus-square-o icon fa_<?=$news_category_id;?>" aria-hidden="true"></i>
        <input type="checkbox" value="<?=$news_category_id;?>" name="categories[]" class="level_<?= $news_cat_hierarchy_level; ?>" <?=$checkbox_checked;?> />
        <input type="hidden" value="<?=$news_cat_name;?>" name="news_categories_names[<?=$news_category_id;?>]" class="category_name_<?=$news_category_id;?>" <?=$input_disabled;?> />
        <a href="javascript:;" class="dropdown_link dropdown_link_<?= $news_cat_hierarchy_level; ?> level_<?= $news_cat_hierarchy_level; ?>">
          <span class="category_name"><?= "$news_cat_name"; ?></span>
          <span class="news_cat_count_box">(<span class="news_cat_count_digits"></span> <span class="news_cat_count_text"></span>)</span>
        </a>
        <ul class="expandable_ul expandable_ul_<?=$news_category_id;?>">
<?php
          list_news_categories_with_checkboxes($news_category_id,$news_category_ids);
      } else {
?>
      <li id="<?=$news_category_id;?>" class="level_<?= $news_cat_hierarchy_level; ?> <?= "$class_li"; ?>">
        <i class="fa fa-lg"></i>
        <input type="checkbox" value="<?=$news_category_id;?>" name="categories[]" <?=$checkbox_checked;?> />
        <input type="hidden" value="<?=$news_cat_name;?>" name="news_categories_names[<?=$news_category_id;?>]" class="category_name_<?=$news_category_id;?>" <?=$input_disabled;?> />
        <a href="javascript:;" class="level_<?= $news_cat_hierarchy_level; ?>">
          <span class="category_name"><?= "$news_cat_name"; ?></span>
        </a>
      </li>
<?php
      }
      if ($news_cat_hierarchy_level > 1 && $news_cat_is_last_child) {
?>
        </ul>
      </li>
<?php
      }
    }
  }
}

function list_categories_options() {
  
  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  
  $query_options = "SELECT `option_description`.`opt_backend_name`,`options`.`option_id`,`options`.`option_sort_order`
                      FROM `option_description` 
                INNER JOIN `options` USING(`option_id`)
                     WHERE `option_description`.`language_id` = '$current_language_id' 
                  ORDER BY `options`.`option_sort_order` ASC";
  //echo $query_options."<br>";
  $result_options = mysqli_query($db_link, $query_options);
  if(!$result_options) echo mysqli_error($db_link);
  $count_options = mysqli_num_rows($result_options);
  if($count_options > 0) {

    $key = 0;
    
    while ($row = mysqli_fetch_assoc($result_options)) {

      $option_id = $row['option_id'];
      $option_sort_order = $row['option_sort_order'];
      $opt_backend_name = stripslashes($row['opt_backend_name']);
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $edit_link = "/".$_SESSION['admin_dir_name']."/welding/categories-options-details.php?option_id=$option_id";
      
      $query_option_values = "SELECT `option_value_id` FROM `option_value` WHERE `option_id` = '$option_id' AND `ov_is_custom` = '0'";
      $result_option_values = mysqli_query($db_link, $query_option_values);
      if(!$result_option_values) echo mysqli_error($db_link);
      $option_values_count = mysqli_num_rows($result_option_values);
      mysqli_free_result($result_option_values);
?>
    <table class="row_over">
      <tbody>
        <tr id="tr_<?=$option_id;?>" class="<?=$class?>">
          <td width="55%" class="text_left">
            <span class="red_link">
            <?=$opt_backend_name;?> (<?=$option_values_count;?>)
            </span>
          </td>
          <td width="10%"><?=$option_id;?></td>
          <td width="10%"><?=$option_sort_order;?></td>
          <td width="10%">
            <?php
              // if($count_options > 1) we gonna give the appropriate moving options
              // else we gonna leave this empty
              if($count_options > 1) {
                if($key == 0) {
            ?>
                <a href="javascript:;" class="edit_link" onclick="MoveOptionForwardBackward('<?=$option_id;?>','<?=$option_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
              <?php } elseif($key == $count_options-1) { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveOptionForwardBackward('<?=$option_id;?>','<?=$option_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
              <?php } else { ?>
                <a href="javascript:;" class="edit_link" onclick="MoveOptionForwardBackward('<?=$option_id;?>','<?=$option_sort_order;?>','backward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
                </a>
                <a href="javascript:;" class="edit_link" onclick="MoveOptionForwardBackward('<?=$option_id;?>','<?=$option_sort_order;?>','forward')">
                  <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
                </a>
            <?php 
                }
              } // if($count_options > 1)
            ?>
          </td>
          <td width="7.5%">
            <a href="<?=$edit_link;?>" class="edit_link">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/edit.gif" class="systemicon" alt="<?=$languages['alt_edit'];?>" title="<?=$languages['title_edit'];?>" width="16" height="16" />
            </a>
          </td>
          <td width="7.5%">
            <a href="javascript:;" class="delete_option_link delete_link" data-id="<?=$option_id;?>">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
        </tr>
      </tbody>
    </table>
<?php
      $key++;
    }
    mysqli_free_result($result_options);
    
    if($_SESSION['users_rights_delete'] == 1) {
?>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages['text_are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages['delete_option_warning']?></p>
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
            DeleteOption('first');
          },
          "<?=$languages['btn_cancel'];?>": function() {
            $(".delete_option_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $(".delete_option_link").click(function() {
        $(".delete_option_link").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm").dialog("open");
      });
    });
    </script>
<?php
    }
  }
  else {
?>
      <tr>
        <td width="60%" class="text_left"><?=$languages['text_no_options_yet'];?></td>
        <td></td>
      </tr>
<?php
  }
}

function list_categories_options_for_reorder() {
  
  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  
  $query_options = "SELECT `option_description`.`opt_backend_name`,`options`.`option_id`,`options`.`option_sort_order`
                      FROM `option_description` 
                INNER JOIN `options` USING(`option_id`)
                     WHERE `option_description`.`language_id` = '$current_language_id' 
                  ORDER BY `options`.`option_sort_order` ASC";
  //echo $query_options."<br>";
  $result_options = mysqli_query($db_link, $query_options);
  if(!$result_options) echo mysqli_error($db_link);
  $count_options = mysqli_num_rows($result_options);
  if($count_options > 0) {
?>
      <ul class="sortable sortable_list">
<?php
    while ($row = mysqli_fetch_assoc($result_options)) {

      $option_id = $row['option_id'];
      $opt_backend_name = stripslashes($row['opt_backend_name']);
?>
      <li class="mjs-nestedSortable-leaf" data-id="<?=$option_id;?>">
        <input type="hidden" name="categories_options[]" value="<?=$option_id;?>">
        <div class="sortable_label"><?=$opt_backend_name;?></div>
      </li>
<?php
    }
    mysqli_free_result($result_options);
?>
      </ul>
<?php
  }
}

function list_categories_options_values() {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $current_option_id;
  global $languages_array;
  global $isset_submit_product_option;
  global $option_values_array;
  global $ovd_values_array;
  global $product_option_errors;
?>
  <table class="border">
    <thead>
      <tr>
        <th style="width:55%" class="text_left"><?=$languages['header_option_value'];?></th>
        <th style="width:15%">ID</th>
        <th style="width:15%"><?=$languages['header_reorder'];?></th>
        <th style="width:15%"><?=$languages['header_actions'];?></th>
      </tr>
    </thead>
<?php
if(!$isset_submit_product_option) {
  $query_option_values = "SELECT `option_value_id`,`ov_sort_order` 
                            FROM `option_value`
                           WHERE `option_id` = '$current_option_id' AND `ov_is_custom` = '0'
                        ORDER BY `ov_sort_order` ASC";
  //echo $query_option_values;
  $result_option_values = mysqli_query($db_link, $query_option_values);
  if(!$result_option_values) echo mysqli_error($db_link);
  $option_values_count = mysqli_num_rows($result_option_values);
  if($option_values_count > 0) {
    while($row_option_values = mysqli_fetch_assoc($result_option_values)) {
      $option_values_array[] = $row_option_values;
    }
  }
}
else {
  //echo"<pre>";print_r($option_values_array);print_r($ovd_values_array);
  $option_values_count = count($option_values_array);
}

if($option_values_count > 0) {

  foreach($option_values_array as $option_key => $option_value) {

    $option_value_id = $option_value['option_value_id'];
    $ov_sort_order = $option_value['ov_sort_order'];
?>
    <tbody id="option_value_row_<?=$option_key;?>">
      <tr>   
        <td class="text_left">     
<?php
    $key = 0;
    
    foreach($languages_array as $row_languages) {

      $language_id = $row_languages['language_id'];
      $language_code = $row_languages['language_code'];
      $language_menu_name = $row_languages['language_menu_name'];

      if(!$isset_submit_product_option) {
        $query_ovd_values = "SELECT `ovd_value` FROM `option_value_description` WHERE `option_value_id` = '$option_value_id' AND `language_id` = '$language_id'";
        //echo $query_ovd_values;
        $result_ovd_values = mysqli_query($db_link, $query_ovd_values);
        if(!$result_ovd_values) echo mysqli_error($db_link);
        $ovd_count = mysqli_num_rows($result_ovd_values);
        if($ovd_count > 0) {
          while($row = mysqli_fetch_assoc($result_ovd_values)) {
            $ovd_values_array[$option_value_id][$language_id] = $row['ovd_value'];
          }
        }
      }
      
      $ovd_value = "";
      $input_new_entry = "";
      if(isset($ovd_values_array['new_entry'][$option_key])) {
        $ovd_value = $ovd_values_array['new_entry'][$option_key][$language_id];
        $input_new_entry = '<input type="hidden" name="option_value['.$option_key.'][new_entry]" value="1" />';
      }
      if($key == 0) {
      ?>
        <input type="hidden" name="option_value[<?=$option_key;?>][option_value_id]" value="<?=$option_value_id;?>" />
      <?php
      }
      if(isset($ovd_values_array[$option_value_id][$language_id])) {
        $ovd_value = $ovd_values_array[$option_value_id][$language_id];
      }
      else {
         /*
          * no record for this language, because the language was added after the first time the option value was created
          */
     ?>
       <input type="hidden" name="new_entry[ovd_value][<?=$option_key;?>][<?=$language_id;?>]" value="1" />
     <?php 
      }
      ?>
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <?=$input_new_entry;?>
        <input type="text" name="option_value[<?=$option_key;?>][ovd_value][<?=$language_id;?>]" value="<?=$ovd_value;?>" style="width:92%" />
        &nbsp;&nbsp;<img src="/<?=$_SESSION['admin_dir_name'];?>/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
        <?php
          if(isset($product_option_errors[$option_key]['ovd_value'][$language_id])) {
            echo "<div class='error'>".$product_option_errors[$option_key]['ovd_value'][$language_id]."</div>";
          }
        ?>
      </div>
      <p class="clearfix"></p>
<?php
      $key++;
    } // foreach($languages_array)
?>
      </td>
      <td><?=$option_value_id;?></td>   
      <!--<td><?="$current_option_id.$option_value_id";?></td>-->
      <td>
        <div class="float_left" style="width:49%;">
          <input type="text" name="option_value[<?=$option_key;?>][ov_sort_order]" value="<?=$ov_sort_order;?>" style="width: 20px;">&nbsp;
        </div>
        <div class="float_left" style="width:49%;">
        <?php
          // if($option_values_count > 1) we gonna give the appropriate moveing options
          // else we gonna leave this empty
          if($option_values_count > 1) {
            if($option_key == 0) {
          ?>
            <a href="javascript:;" class="edit_link" onclick="MoveOptionValueForwardBackward('<?=$current_option_id;?>','<?=$option_value_id;?>','<?=$ov_sort_order;?>','<?=$option_key;?>','backward')">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
            </a>
          <?php } elseif($option_key == $option_values_count-1) { ?>
            <a href="javascript:;" class="edit_link" onclick="MoveOptionValueForwardBackward('<?=$current_option_id;?>','<?=$option_value_id;?>','<?=$ov_sort_order;?>','<?=$option_key;?>','forward')">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
            </a>
          <?php } else { ?>
            <a href="javascript:;" class="edit_link" onclick="MoveOptionValueForwardBackward('<?=$current_option_id;?>','<?=$option_value_id;?>','<?=$ov_sort_order;?>','<?=$option_key;?>','backward')">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-d.gif" class="systemicon" alt="<?=$languages['alt_move_backward'];?>" title="<?=$languages['title_move_backward'];?>" width="16" height="16" />
            </a>
            <a href="javascript:;" class="edit_link" onclick="MoveOptionValueForwardBackward('<?=$current_option_id;?>','<?=$option_value_id;?>','<?=$ov_sort_order;?>','<?=$option_key;?>','forward')">
              <img src="/<?=$_SESSION['admin_dir_name'];?>/images/arrow-u.gif" class="systemicon" alt="<?=$languages['alt_move_forward'];?>" title="<?=$languages['title_move_forward'];?>" width="16" height="16" />
            </a>
        <?php 
            }
          } // if($option_values_count > 1)
        ?>
        </div>
      </td>
      <td>
        <a href="javascript:;" class="delete_option_value_link delete_link" data-id="<?=$option_value_id;?>" data-row="<?=$option_key;?>">
          <img src="/<?=$_SESSION['admin_dir_name'];?>/images/delete.gif" class="systemicon" alt="<?=$languages['alt_delete'];?>" title="<?=$languages['title_delete'];?>" width="16" height="16" />
        </a>
      </td>
    </tr>
  </tbody>
<?php
  } // foreach($option_values_array)
} // if(!empty($option_values_array))
?>

  <tfoot>
    <tr>
      <td class="text_left">
        <a class="button green" onClick="AddOptionValue()"><i class="icon icon_plus_sign"></i><?=$languages['btn_add_new_option_row'];?></a>
      </td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </tfoot>
</table>
<input type="hidden" id="option_values_count" value="<?=$option_values_count;?>" />
<?php
}

function list_date_months_in_select($select_name_id,$current_month = false) {
  global $languages;
  global $current_lang;
?>
    <select name="<?=$select_name_id;?>" id="<?=$select_name_id;?>" style="width: auto;">
<?php
    if(!$current_month) {
      $date = new DateTime();
      $current_month = $date->format('m');
    }
    for($month = 01; $month <= 12; $month += 01) {
      $padded_month = sprintf('%02d', $month);
      $option_name = "option_date_month_$padded_month";
      $selected = ($current_month == $month) ? ' selected="selected"' : "";
      echo "<option value='$padded_month'$selected>".$languages[$option_name]."</option>";
    }
?>
    </select>
<?php
}

function list_date_days_in_select($select_name_id,$current_day = false) {
?>
    <select name="<?=$select_name_id;?>" id="<?=$select_name_id;?>" style="width: auto;">
<?php
    $date = new DateTime();
    $last_day = $date->format('t');
    if(!$current_day) $current_day = $date->format('d');
    for($days = 01; $days <= $last_day; $days += 01) {
      $padded_day = sprintf('%02d', $days);
      $selected = ($current_day == $days) ? ' selected="selected"' : "";
      echo "<option value='$padded_day'$selected>$padded_day</option>";
    }
?>
    </select>
<?php
}

function list_date_years_in_select($select_name_id,$current_year = false) {
?>
    <select name="<?=$select_name_id;?>" id="<?=$select_name_id;?>" style="width: auto;">
<?php
    if(!$current_year) {
      $date = new DateTime();
      $current_year = $date->format('Y');
    }
    $start_year = $current_year-10;
    $end_year = $current_year+15;
    for($year = $start_year; $year <= $end_year; $year++) {
      $selected = ($current_year == $year) ? ' selected="selected"' : "";
      echo "<option value='$year'$selected>$year</option>";
    }
?>
    </select>
<?php
}

function list_date_hours_in_select($select_name_id,$current_hour = false) {
?>
    <select name="<?=$select_name_id;?>" id="<?=$select_name_id;?>" style="width: auto;">
<?php
    if(!$current_hour) {
      $date = new DateTime();
      $current_hour = $date->format('H');
    }
    $last_hour = "24";
    for($hours = 00; $hours <= $last_hour; $hours += 01) {
      $padded_hour = sprintf('%02d', $hours);
      $selected = ($current_hour == $hours) ? ' selected="selected"' : "";
      echo "<option value='$padded_hour'$selected>$padded_hour</option>";
    }
?>
    </select>
<?php
}

function list_date_minutes_in_select($select_name_id,$current_minute = false) {
?>
    <select name="<?=$select_name_id;?>" id="<?=$select_name_id;?>" style="width: auto;">
<?php
    if(!$current_minute) {
      $date = new DateTime();
      $current_minute = $date->format('i');
    }
    $last_minute = "59";
    for($minutes = 00; $minutes <= $last_minute; $minutes += 01) {
      $padded_minute = sprintf('%02d', $minutes);
      $selected = ($current_minute == $minutes) ? ' selected="selected"' : "";
      echo "<option value='$padded_minute'$selected>$padded_minute</option>";
    }
?>
    </select>
<?php
}

function list_date_seconds_in_select($select_name_id,$current_second = false) {
?>
    <select name="<?=$select_name_id;?>" id="<?=$select_name_id;?>" style="width: auto;">
<?php
    if(!$current_second) {
      $date = new DateTime();
      $current_second = $date->format('s');
    }
    $last_second = "59";
    for($seconds = 00; $seconds <= $last_second; $seconds += 01) {
      $padded_second = sprintf('%02d', $seconds);
      $selected = ($current_second == $seconds) ? ' selected="selected"' : "";
      echo "<option value='$padded_second'$selected>$padded_second</option>";
    }
?>
    </select>
<?php
}

function print_html_admin_footer() {
  
  global $db_link;
  global $languages;
  global $current_lang;
?>
<!--footer-->
  <footer>
    <div class="inside_container">
      <div id="rights">
        <b><?=$languages['company_name'];?>  (<?=@date("Y");?>)</b>
      </div>
    </div>
  </footer>
<!--footer-->
  <script type="text/javascript">
    $(document).ready(function() {
      $("main").addClass("col-lg-11 col-md-10 col-sm-10 col-xs-10");
      $(".row_over *").click(function() {
        $(".row_over").removeClass("row_over_edit");
        $(this).closest(".row_over").addClass("row_over_edit");
      });
      $("tr.even,tr.odd").mouseenter(function() {
        var me = $(this);
        me.addClass("hover");
      });
      $("tr.even,tr.odd").mouseleave(function() {
        var me = $(this);
        me.removeClass("hover");
      });
      if($("body").hasClass("opened_nav")) {
        if($(".menu_ul_1_level li.active").length) {
          $(".menu_ul_1_level li.active").parent(".menu_ul_1_level").show();
        }
        if($(".menu_ul_2_level li.active").length) {
          $(".menu_ul_2_level li.active").parent(".menu_ul_2_level").show();
          $(".menu_ul_2_level li.active").parent(".menu_ul_2_level").parent(".menu_li_2_level").addClass("active");
          $(".menu_ul_2_level li.active").parent(".menu_ul_2_level").parent(".menu_li_2_level").parent(".menu_ul_1_level").show();
        }
      }
      $("nav a.menu_a_1_level").bind('click', function() {
        var link = $(this);
        var link_parent = link.parent(".menu_li_1_level");
        if(link.hasClass("active")) {
          link.removeClass("active");
          link_parent.find("ul.menu_ul_1_level").slideUp();
        }
        else {
          $("nav a.menu_a_1_level").removeClass("active");
          $("nav li.menu_li_1_level").removeClass("active");
          link.addClass("active");
          $("nav").find("ul.menu_ul_1_level").slideUp();
          link_parent.find("ul.menu_ul_1_level").slideDown();
        }
      });
      $("nav a.menu_a_2_level").bind('click', function() {
        var link = $(this);
        var link_parent = link.parent(".menu_li_2_level");
        if(link.hasClass("active")) {
          link.removeClass("active");
          link_parent.find("ul.menu_ul_2_level").slideUp();
        }
        else {
          $("nav a.menu_a_2_level").removeClass("active");
          $("nav li.menu_li_2_level").removeClass("active");
          link.addClass("active");
          link_parent.addClass("active");
          $("nav").find("ul.menu_ul_2_level").slideUp();
          link_parent.find("ul.menu_ul_2_level").slideDown();
        }
      });
      $("#ajax_notification .close_warning").bind('click', function() {
        $("#ajax_notification").slideUp(900);
      });
<?php if($_SESSION['users_rights_add'] == 0) { ?>
        if($(".add_new_link").length) $(".add_new_link").remove();   
<?php } if($_SESSION['users_rights_edit'] == 0) { ?>
        if($(".edit_link").length) {
          $(".edit_link").each(function () {
            var link_html = $(this).html();
            if($(link_html).is("img")) {
              $(this).parent().html(link_html).attr("onclick","alert('<?=$languages['text_no_edit_rights'];?>')");
            }
            else {
              $(".edit_link").remove(); 
            }
          }); 
        }  
<?php } if($_SESSION['users_rights_delete'] == 0) { ?>
        if($(".delete_link").length) $(".delete_link").remove();   
<?php } ?>
      $('#menu .open_close_nav').bind('click', function() {
        var body = $("body");
        if(body.hasClass("opened_nav")) {
          body.removeClass("opened_nav").addClass("closed_nav");
          createCookie('nav','closed_nav',2);
          if($(".has_children a").length) {
            $(".has_children a").each(function () {
              $(this).find(".arrow_down").removeClass("fa-chevron-down arrow_down").addClass("fa-chevron-right arrow_right");
              $(this).find(".arrow_up").removeClass("fa-chevron-up arrow_up").addClass("fa-chevron-left arrow_left");
            }); 
          }
        }
        else {
          body.removeClass("closed_nav").addClass("opened_nav");
          createCookie('nav','opened_nav',2);
          if($(".has_children a").length) {
            $(".has_children a").each(function () {
              $(this).find(".arrow_right").removeClass("fa-chevron-right arrow_right").addClass("fa-chevron-down arrow_down");
              $(this).find(".arrow_left").removeClass("fa-chevron-left arrow_left").addClass("fa-chevron-up arrow_up");
            }); 
          }
        }
      });
    });
  </script>
<?php
  if(isset($db_link)) {
    DB_CloseI($db_link);
  }
}