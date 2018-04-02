//console.log(base_url);
//console.log(admin_dir_name);
/**
 * http://www.openjs.com/scripts/events/keyboard_shortcuts/
 * Version : 2.01.B
 * By Binny V A
 * License : BSD
 */
shortcut = {
  'all_shortcuts': {}, //All the shortcuts are stored in this array
  'add': function (shortcut_combination, callback, opt) {
    //Provide a set of default options
    var default_options = {
      'type': 'keydown',
      'propagate': false,
      'disable_in_input': false,
      'target': document,
      'keycode': false
    }
    if (!opt)
      opt = default_options;
    else {
      for (var dfo in default_options) {
        if (typeof opt[dfo] == 'undefined')
          opt[dfo] = default_options[dfo];
      }
    }

    var ele = opt.target;
    if (typeof opt.target == 'string')
      ele = document.getElementById(opt.target);
    var ths = this;
    shortcut_combination = shortcut_combination.toLowerCase();

    //The function to be called at keypress
    var func = function (e) {
      e = e || window.event;

      if (opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
        var element;
        if (e.target)
          element = e.target;
        else if (e.srcElement)
          element = e.srcElement;
        if (element.nodeType == 3)
          element = element.parentNode;

        if (element.tagName == 'INPUT' || element.tagName == 'TEXTAREA')
          return;
      }

      //Find Which key is pressed
      if (e.keyCode)
        code = e.keyCode;
      else if (e.which)
        code = e.which;
      var character = String.fromCharCode(code).toLowerCase();

      if (code == 188)
        character = ","; //If the user presses , when the type is onkeydown
      if (code == 190)
        character = "."; //If the user presses , when the type is onkeydown

      var keys = shortcut_combination.split("+");
      //Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
      var kp = 0;

      //Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
      var shift_nums = {
        "`": "~",
        "1": "!",
        "2": "@",
        "3": "#",
        "4": "$",
        "5": "%",
        "6": "^",
        "7": "&",
        "8": "*",
        "9": "(",
        "0": ")",
        "-": "_",
        "=": "+",
        ";": ":",
        "'": "\"",
        ",": "<",
        ".": ">",
        "/": "?",
        "\\": "|"
      }
      //Special Keys - and their codes
      var special_keys = {
        'esc': 27,
        'escape': 27,
        'tab': 9,
        'space': 32,
        'return': 13,
        'enter': 13,
        'backspace': 8,

        'scrolllock': 145,
        'scroll_lock': 145,
        'scroll': 145,
        'capslock': 20,
        'caps_lock': 20,
        'caps': 20,
        'numlock': 144,
        'num_lock': 144,
        'num': 144,

        'pause': 19,
        'break': 19,

        'insert': 45,
        'home': 36,
        'delete': 46,
        'end': 35,

        'pageup': 33,
        'page_up': 33,
        'pu': 33,

        'pagedown': 34,
        'page_down': 34,
        'pd': 34,

        'left': 37,
        'up': 38,
        'right': 39,
        'down': 40,

        'f1': 112,
        'f2': 113,
        'f3': 114,
        'f4': 115,
        'f5': 116,
        'f6': 117,
        'f7': 118,
        'f8': 119,
        'f9': 120,
        'f10': 121,
        'f11': 122,
        'f12': 123
      }

      var modifiers = {
        shift: {wanted: false, pressed: false},
        ctrl: {wanted: false, pressed: false},
        alt: {wanted: false, pressed: false},
        meta: {wanted: false, pressed: false}	//Meta is Mac specific
      };

      if (e.ctrlKey)
        modifiers.ctrl.pressed = true;
      if (e.shiftKey)
        modifiers.shift.pressed = true;
      if (e.altKey)
        modifiers.alt.pressed = true;
      if (e.metaKey)
        modifiers.meta.pressed = true;

      for (var i = 0; k = keys[i], i < keys.length; i++) {
        //Modifiers
        if (k == 'ctrl' || k == 'control') {
          kp++;
          modifiers.ctrl.wanted = true;

        } else if (k == 'shift') {
          kp++;
          modifiers.shift.wanted = true;

        } else if (k == 'alt') {
          kp++;
          modifiers.alt.wanted = true;
        } else if (k == 'meta') {
          kp++;
          modifiers.meta.wanted = true;
        } else if (k.length > 1) { //If it is a special key
          if (special_keys[k] == code)
            kp++;

        } else if (opt['keycode']) {
          if (opt['keycode'] == code)
            kp++;

        } else { //The special keys did not match
          if (character == k)
            kp++;
          else {
            if (shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
              character = shift_nums[character];
              if (character == k)
                kp++;
            }
          }
        }
      }

      if (kp == keys.length &&
              modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
              modifiers.shift.pressed == modifiers.shift.wanted &&
              modifiers.alt.pressed == modifiers.alt.wanted &&
              modifiers.meta.pressed == modifiers.meta.wanted) {
        callback(e);

        if (!opt['propagate']) { //Stop the event
          //e.cancelBubble is supported by IE - this will kill the bubbling process.
          e.cancelBubble = true;
          e.returnValue = false;

          //e.stopPropagation works in Firefox.
          if (e.stopPropagation) {
            e.stopPropagation();
            e.preventDefault();
          }
          return false;
        }
      }
    }
    this.all_shortcuts[shortcut_combination] = {
      'callback': func,
      'target': ele,
      'event': opt['type']
    };
    //Attach the function with the event
    if (ele.addEventListener)
      ele.addEventListener(opt['type'], func, false);
    else if (ele.attachEvent)
      ele.attachEvent('on' + opt['type'], func);
    else
      ele['on' + opt['type']] = func;
  },

  //Remove the shortcut - just specify the shortcut and I will remove the binding
  'remove': function (shortcut_combination) {
    shortcut_combination = shortcut_combination.toLowerCase();
    var binding = this.all_shortcuts[shortcut_combination];
    delete(this.all_shortcuts[shortcut_combination])
    if (!binding)
      return;
    var type = binding['event'];
    var ele = binding['target'];
    var callback = binding['callback'];

    if (ele.detachEvent)
      ele.detachEvent('on' + type, callback);
    else if (ele.removeEventListener)
      ele.removeEventListener(type, callback, false);
    else
      ele['on' + type] = false;
  }
}

shortcut.add("esc", function () {
  $(".row_over").removeClass("row_over_edit");
});
shortcut.add("F1", function () {
  window.location = "";
});
shortcut.add("F3", function () {
  var note_box_position = $("#notes_box").css("right");
  if (note_box_position == "0px") {
    // close the box
    var notes_box_width = $("#notes_box").css("width");
    var whole_notes_box_width = parseInt(notes_box_width) + 30;
    $("#notes_box").animate({right: '-' + whole_notes_box_width + 'px'}, 500);
  } else {
    // open the box
    $("#notes_box").animate({right: '0px'}, 500);
  }
});

function readableColor(bckgr_color, input_class) {
  
  var c = bckgr_color.toString().substring(1);  // strip #
  var rgb = parseInt(c, 16);   // convert rrggbb to decimal
  var r = (rgb >> 16) & 0xff;  // extract red
  var g = (rgb >>  8) & 0xff;  // extract green
  var b = (rgb >>  0) & 0xff;  // extract blue

  var luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // per ITU-R BT.709

  if(luma < 130) {
    //dark
    $("."+input_class).css("color","#FFFFFF");
  }
  else {
    //light
    $("."+input_class).css("color","#000000");
  }
}

function isEmpty(str) {
  return (!str || 0 === str.length);
}

function JsPaginating(btn) {
  var pag_id = $(btn).attr("data");
  if (pag_id == "" || pag_id === undefined)
    return;
  var page_count = $("#categories_list .page_count").val();
  var prev_page = "";
  var next_page = "";
  if (pag_id == "1") {
    $(".js_pagination .btn_prev_page").addClass("disabled");
    $(".js_pagination .btn_prev_page a").attr("data", "");
    $(".js_pagination .btn_next_page").removeClass("disabled");
    $(".js_pagination .btn_next_page a").attr("data", "2");
  } else if (pag_id == page_count) {
    prev_page = parseInt(pag_id) - 1;
    $(".js_pagination .btn_prev_page").removeClass("disabled");
    $(".js_pagination .btn_prev_page a").attr("data", prev_page);
    $(".js_pagination .btn_next_page").addClass("disabled");
    $(".js_pagination .btn_next_page a").attr("data", "");
  } else {
    prev_page = parseInt(pag_id) - 1;
    next_page = parseInt(pag_id) + 1;
    $(".js_pagination .btn_prev_page").removeClass("disabled");
    $(".js_pagination .btn_prev_page a").attr("data", prev_page);
    $(".js_pagination .btn_next_page").removeClass("disabled");
    $(".js_pagination .btn_next_page a").attr("data", next_page);
  }
  if ($(btn).parent().hasClass("active")) {
    // do nothing
  } else {
    $(".js_pagination li").removeClass("active");
    $(".js_pagination #pag_" + pag_id).addClass("active");
    $("#categories_list table").hide();
    $("#categories_list table.row_" + pag_id).show();
  }
  event.preventDefault();
}

function fixedEncodeURIComponent(str) {
  return encodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
}

// Handling Cookies

function createCookie(name, value, hours) {
  var expires = "";
  if (hours) {
    var date = new Date();
    date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
  }
  document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ')
      c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) == 0)
      return c.substring(nameEQ.length, c.length);
  }
  return null;
}

function eraseCookie(name) {
  createCookie(name, "", -3);
}

// Disable Right Click Script

function IE(e) {
  if (navigator.appName == "Microsoft Internet Explorer" && (event.button == "2" || event.button == "3")) {
    return false;
  }
}
function NS(e) {
  if (document.layers || (document.getElementById && !document.all)) {
    if (e.which == "2" || e.which == "3") {
      return false;
    }
  }
}
//document.onmousedown=IE;document.onmouseup=NS;document.oncontextmenu=new Function("return false");


//Custom alert box
var ALERT_TITLE = "Oops!";
var ALERT_BUTTON_TEXT = "Ok";

if (document.getElementById) {
  window.alert = function (txt) {
    createCustomAlert(txt);
  }
}

function createCustomAlert(txt) {
  d = document;

  if (d.getElementById("modal_container"))
    return;

  mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
  mObj.id = "modal_container";
  //mObj.style.height = $(window).height() + "px";

  alertObj = mObj.appendChild(d.createElement("div"));
  alertObj.id = "alert_box";
  if (d.all && !window.opera)
    alertObj.style.top = document.documentElement.scrollTop + "px";
  alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth) / 2 + "px";
  alertObj.style.visiblity = "visible";

  h1 = alertObj.appendChild(d.createElement("h1"));
  h1.appendChild(d.createTextNode(ALERT_TITLE));

  msg = alertObj.appendChild(d.createElement("p"));
  //msg.appendChild(d.createTextNode(txt));
  msg.innerHTML = txt;

  btn = alertObj.appendChild(d.createElement("a"));
  btn.id = "closeBtn";
  btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
  btn.href = "#";
  btn.focus();
  btn.onclick = function () {
    removeCustomAlert();
    return false;
  }

  alertObj.style.display = "block";

}

function removeCustomAlert() {
  document.getElementsByTagName("body")[0].removeChild(document.getElementById("modal_container"));
}

function ShowAjaxMessageSuccess(massage) {
  $("#ajax_notification .ajaxmessage").html(massage);
  $("#ajax_notification").slideDown(500);
  $("#ajax_notification").delay(3500).slideUp(900);
}

function Checkbox(checkbox) {
  state = checkbox.checked;
  //alert(state);
  if ($(checkbox).parent().hasClass("checkbox_checked")) {
    $(checkbox).parent().removeClass("checkbox_checked");
    $(checkbox).attr("checked", false);
  } else {
    $(checkbox).parent().addClass("checkbox_checked");
    $(checkbox).attr("checked", true);
  }
}

function SelectAllCheckboxes(clicked_checkbox, class_name) {
  var checkboxes = document.getElementsByClassName(class_name);
  var state = clicked_checkbox.checked;
  for (i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].type === "checkbox") {
      checkboxes[i].checked = state;
    }
  }
}

function CalculateSelectedSubcategories() {
  $.each($(".tree li.expandable"), function () {
    var checked_cat = 0;
    var current_list = $(this);
    var current_list_level = current_list.attr("data-level");
    var checkboxes = current_list.find("input");
    $.each($(checkboxes), function () {
      var level = $(this).attr("data-level");
      if (level != 1) {
        if ($(this).is(":checked"))
          checked_cat++;
      }
    });
    if (checked_cat > 0) {
      current_list.find("a.dropdown_link_" + current_list_level + " .category_count_box").show();
      current_list.find("a.dropdown_link_" + current_list_level + " .category_count_digits").html(checked_cat);
      if (checked_cat > 1) {
        current_list.find("a.dropdown_link_" + current_list_level + " .category_count_text").html("подкатегории избрани");
      } else {
        current_list.find("a.dropdown_link_" + current_list_level + " .category_count_text").html("подкатегория избрана");
      }
    } else {
      current_list.find("a.dropdown_link_" + current_list_level + " .category_count_box").hide();
      current_list.find("a.dropdown_link_" + current_list_level + " .category_count_digits").html("");
      current_list.find("a.dropdown_link_" + current_list_level + " .category_count_text").html("");
    }
  });
}

function ToggleCollapse(cid) {
  document.getElementById(cid).style.display = (document.getElementById(cid).style.display != "block") ? "block" : "none";
}

function NoRightsToAdd() {
  alert($("#text_no_add_rights").val());
  $(".row_over").removeClass("row_over_edit");
}

function NoRightsToEdit() {
  alert($("#text_no_edit_rights").val());
  $(".row_over").removeClass("row_over_edit");
}

function NoRightsToDelete() {
  alert($("#text_no_delete_rights").val());
  $(".row_over").removeClass("row_over_edit");
}

function CheckEditRights() {

  var user_access_edit = $(".menu_ul_2_level li.active .menu_a_3_level").attr("user-access-edit");
  if (user_access_edit == undefined) {
    user_access_edit = $(".menu_ul_1_level li.active .menu_a_2_level").attr("user-access-edit");
  }
  if (user_access_edit == undefined) {
    user_access_edit = $("#menu li.active .menu_a_1_level").attr("user-access-edit");
  }
  //console.log(user_access_edit);
  if (user_access_edit == 0) {
    NoRightsToEdit();
    return false;
  }

  return user_access_edit;

}

function CheckDeleteRights() {

  var user_access_delete = $(".menu_ul_2_level li.active .menu_a_3_level").attr("user-access-delete");
  if (user_access_delete == undefined) {
    user_access_delete = $(".menu_ul_1_level li.active .menu_a_2_level").attr("user-access-delete");
  }
  if (user_access_delete == undefined) {
    user_access_delete = $("#menu li.active .menu_a_1_level").attr("user-access-delete");
  }
  if (user_access_delete == 0) {
    NoRightsToDelete();
    return false;
  }

  return true;

}

function PreloadImages() {
  var aImages = new Array('images/image-1.png', 'images/image-2.png');
  for (var i = 0; i < aImages.length; i++) {
    var img = new Image();
    img.src = aImages[i];
  }
}

function CountCharacters(element, count) {
  var len = element.value.length;
  if (len >= count) {
    //disable entering more characters
    //element.value = element.value.substring(0, count);
    $(element).parent().find(".warning").show();
    $(element).parent().find(".info .info_b").text(count - len);
  } else {
    $(element).parent().find(".warning").hide();
    $(element).parent().find(".info .info_b").text(count - len);
  }
}
;

function ShowAjaxLoader() {
  $("#ajax_loader_backgr, #ajax_loader").show();
  setTimeout(function () {
    $("#ajax_loader_backgr, #ajax_loader").hide();
  }, 5000);
}

function HideAjaxLoader() {
  setTimeout(function () {
    $("#ajax_loader_backgr, #ajax_loader").hide();
  }, 250);
}

function EditUserRights(user_id) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_users_rights_' + user_id + '"]').serializeArray();
  form_data.push(
    {name: 'user_id', value: user_id}
  );
  //alert(user_id);
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/edit-user.php",
    type: "POST",
    data: form_data
  }).done(function (data) {
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $(".users_details").removeClass("access_rights_edit");
    if (data == "") {
      $("#user" + user_id + " td").effect("highlight", {}, 3000);
    } else {
      $("#users_list").append(data);
      //alert(data);
    }
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteUser() {
  var user_id = $(".delete_user_link.active").attr("data-id");
  var super_user = $(".delete_user_link.active").attr("data-su");
  if(super_user == "1") {
    alert($("#cannnot_delete_admin").val());
    return
  }
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  //alert(user_id);
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/delete/delete-user.php",
    type: "POST",
    data: {
      user_id: user_id
    }
  }).done(function () {
    //alert(data);
    $("#modal_confirm").dialog("close");
    $("#users_list #user" + user_id).remove();
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditRestrictedUser(user_id) {
  ShowAjaxLoader();
  var user_div = "#user" + user_id;
  var user_username = $(user_div + " .user_username").val();
  var user_password = $(user_div + " .user_password").val();
  //alert(task_group_name);
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/edit-restricted-user.php",
    type: "POST",
    data: {
      user_id: user_id,
      user_username: user_username,
      user_password: user_password
    }
  }).done(function (data) {
    $(".row_over").removeClass("row_over_edit");

    if (data == "") {
      $(user_div + " td").effect("highlight", {}, 3000);
    } else {
      alert(data);
    }

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditUserForReset(user_id) {
  ShowAjaxLoader();
  $.post("/" + admin_dir_name + "/administration/ajax/edit/edit-user-for-reset.php", {

    user_id: user_id,
    user_name: $("#user_username" + user_id).val(),
    user_password: $("#user_password" + user_id).val()
  }).done(function (data) {
    $(".row_over").removeClass("row_over_edit");
    $(".users_detail").removeClass("access_rights_edit");
    if (data == "") {
      $("#user" + user_id + " td").effect("highlight", {}, 3000);
    } else {
      alert(data);
    }
    HideAjaxLoader();
  }).fail(function (data) {
    console.log("Error: " + data);
    alert("Error: " + data);
  });

}

function EditUsersTypeDefaultRights(user_type_id) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_users_type_default_rights"]').serializeArray();
  form_data.push(
          {name: 'user_type_id', value: user_type_id}
  );
  //alert(user_id);
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/edit-users-type-default-rights.php",
    type: "POST",
    data: form_data,
  }).done(function () {

    GetUsersTypesDefaultRights();

    var massage = $("#ajaxmessage_changes_was_saved_successfully").val();
    ShowAjaxMessageSuccess(massage);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetUsersTypesDefaultRights() {
  ShowAjaxLoader();
  var user_type_id = $(".selected_user_type a").attr("data-id");
  var user_type = $(".selected_user_type a").html();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/get/get-users-types-default-rights.php",
    type: "POST",
    data: {
      user_type_id: user_type_id,
      user_type: user_type
    }
  }).done(function (data) {

    $(".users_type_default_rights").attr("onclick", "EditUsersTypeDefaultRights('" + user_type_id + "')");
    $(".users_type_default_rights").show();
    $("#users_type_default_rights tbody").html(data);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveUserTypeForwardBackward(user_type_id, user_type_sort_order, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/move-users-types-forward-backward.php",
    type: "POST",
    data: {
      user_type_id: user_type_id,
      user_type_sort_order: user_type_sort_order,
      action: action
    }
  }).done(function (users_types) {

    $("#users_types_list").html(users_types);
    $("#tr_" + user_type_id + " td").effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteUserType() {
  var user_type_id = $(".delete_user_type.active").attr("data-id");
  if (user_type_id == "1") {
    alert($("#text_cannnot_delete_admin").val());
    return;
  }
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  //alert(user_type_id);
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/delete/delete-user-type.php",
    type: "POST",
    data: {
      user_type_id: user_type_id
    }
  }).done(function () {
    //alert(data);
    $("#modal_confirm").dialog("close");
    $("#users_types_list #user_type_" + user_type_id).remove();
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetUsersForType() {
  ShowAjaxLoader();
  var user_type_id = $(".selected_user_type a").attr("data-id");
  var user_type = $(".selected_user_type a").html();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/get/get-users-for-type.php",
    type: "POST",
    data: {
      user_type_id: user_type_id,
      user_type: user_type
    }
  }).done(function (data) {

    $(".contents_options").show();
    $("#right_column").show();
    $("#users_list").html(data);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetUserLog(user_id) {
  var url = "/" + admin_dir_name + "/administration/ajax/get/get-user-log.php?user_id=" + user_id;
  window.open(url, 'mywindow', 'status=no,location=yes,resizable=yes,scrollbars=yes,width=800,height=800,left=100,top=0,screenX=0,screenY=0');
}

function ResetIP(user_id) {
  ShowAjaxLoader();
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/edit-user-ip.php",
    type: "POST",
    data: {
      user_id: user_id
    }
  }).done(function () {
    $(".row_over").removeClass("row_over_edit");
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function ToggleExpandMenu(menu_id, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/toggle-expand-menu.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      action: action
    }
  }).done(function (menus) {

    $("#menus_list").html(menus);
    $("#tr_" + menu_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetMenuActiveInactive(link, menu_id, set_menu) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/set-menu-active-inactive.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      set_menu: set_menu
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_menu == "0") {
      $(link).attr("onClick", "SetMenuActiveInactive(this,'" + menu_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetMenuActiveInactive(this,'" + menu_id + "','0')");
      $(link).html(img_active);
    }

    $("#menu_" + menu_id+" td").effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveMenuForwardBackward(menu_id, menu_parent_id, menu_sort_order, menu_hierarchy_level, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/move-menu-forward-backward.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      menu_parent_id: menu_parent_id,
      menu_sort_order: menu_sort_order,
      menu_hierarchy_level: menu_hierarchy_level,
      action: action
    }
  }).done(function (menus) {

    $("#menus_list").html(menus);
    $("#menu_" + menu_id+" td").effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteMenuLink() {
  if (CheckDeleteRights() === false) return;
  ShowAjaxLoader();
  var menu_id = $(".delete_menu_link.active").attr("data-id");
  var menu_parent_id = $(".delete_menu_link.active").attr("data-parent");
//  var pathname = window.location.pathname; // Returns path only
//  var url = window.location.href;     // Returns full URL
//  console.log(pathname);
//  console.log(url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/delete/delete-menu-link.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      menu_parent_id: menu_parent_id
    }
  }).done(function (data) {

    $("#modal_confirm").dialog("close");
    if (isEmpty(data)) {
      $("#menu_" + menu_id).remove();
      //window.location = pathname;
    } else {
      alert(data);
    }

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetMenuLinkChildren(level) {
  ShowAjaxLoader();
  var prev_level = parseInt(level) - 1;
  var menu_id = $(".selected_menu_link_level_" + prev_level + " a").attr("data");
  var menu_name = $(".selected_menu_link_level_" + prev_level + " a").html();
  //alert(prev_level);return false;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/get/get-menu-link-children.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      menu_name: menu_name,
      level: level
    }
  }).done(function (data) {
    if (data == "") {
      // if there are no children then do nothing
    } else {
      $("#menu_links_level_" + level).html(data);
    }
    GetMenuLinkNote(menu_id);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetMenuLinkNote(menu_id) {
  ShowAjaxLoader();
  var language_id = $(".selected_language a").attr("data");
  if (menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_2 a").attr("data");
  }
  if (menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_1 a").attr("data");
  }
  if (menu_id == undefined) {
    menu_id = $(".selected_menu_link_level_0 a").attr("data");
  }
  //alert(part_id);return false;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/get/get-menu-link-note.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      language_id: language_id
    }
  }).done(function (data) {
    $("#add_new_menu_link_note").html("");
    $("#menu_link_note").html(data);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function AddMenuLinkNote() {
  ShowAjaxLoader();
  var language_id = $(".selected_language a").attr("data");
  var menu_id = $(".selected_menu_link_level_2 a").attr("data");
  var menu_link_note = CKEDITOR.instances["ckeditor"].getData();
  if (menu_id === undefined) {
    menu_id = $(".selected_menu_link_level_1 a").attr("data");
  }
  if (menu_id === undefined) {
    menu_id = $(".selected_menu_link_level_0 a").attr("data");
  }
  //alert(menu_link_note);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/add/add-menu-link-note.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      language_id: language_id,
      menu_link_note: menu_link_note
    }
  }).done(function (data) {
    //alert(data);
    GetMenuLinkNote(menu_id);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditMenuLinkNote(menu_id, language_id) {
  ShowAjaxLoader();
  var menu_link_note_tr = "#menu_link_note_" + menu_id + language_id;
  var menu_link_note = CKEDITOR.instances["ckeditor"].getData();
  //alert(news_feed_is_web);return;
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/edit/edit-menu-link-note.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      language_id: language_id,
      menu_link_note: menu_link_note
    }
  }).done(function (data) {
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $(menu_link_note_tr + " td").effect("highlight", {}, 3000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteMenuLinkNote() {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var menu_id = $(".delete_menu_link_note.active").attr("data-menu-id")
  var language_id = $(".delete_menu_link_note.active").attr("data-lang-id")
  $.ajax({
    url: "/" + admin_dir_name + "/administration/ajax/delete/delete-menu-link-note.php",
    type: "POST",
    data: {
      menu_id: menu_id,
      language_id: language_id
    }
  }).done(function () {
    //alert(data);
    $("div#menu_link_note_" + menu_id + language_id).remove();
    $("#modal_confirm").dialog("close");
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetCustomersForGroup() {
  ShowAjaxLoader();
  var customer_group_id = $(".selected_customer_group a").attr("data-id");
  var customer_group_code = $(".selected_customer_group a").attr("data-code");
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/get/get-customers-for-group.php",
    type: "POST",
    data: {
      customer_group_id: customer_group_id,
      customer_group_code: customer_group_code
    }
  }).done(function(customers_list) {

    $(".contents_options").show();
    $("#right_column").show();
    $("#customers_list").html(customers_list);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditCustomer(customer_id) {
  ShowAjaxLoader();
  var customer_group_id = $("#customer_" + customer_id + " .select_customer_group_id").val();
  var customer_is_in_mailist = ($("#customer_" + customer_id + " .customer_is_in_mailist").is(":checked") ? 1 : 0);
  var customer_is_blocked = ($("#customer_" + customer_id + " .customer_is_blocked").is(":checked") ? 1 : 0);
  var customer_is_active = ($("#customer_" + customer_id + " .customer_is_active").is(":checked") ? 1 : 0);
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/edit/edit-customer.php",
    type: "POST",
    data: {
      customer_id: customer_id,
      customer_group_id: customer_group_id,
      customer_is_in_mailist: customer_is_in_mailist,
      customer_is_blocked: customer_is_blocked,
      customer_is_active: customer_is_active
    }
  }).done(function () {
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $("#customer_" + customer_id + " td").effect("highlight", {}, 3000);
//    var massage = $("#ajaxmessage_update_product_tab_success").val();
//    $("#ajax_notification .ajaxmessage").html(massage);
//    $("#ajax_notification").slideDown(500);
//    $("#ajax_notification").delay(3500).slideUp(900);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function ToggleCustomerDetails(customer_id) {
  if ($("#customer_details_" + customer_id).hasClass("active")) {
    $("#customer_details_" + customer_id).removeClass("active");
    $("#customer_details_" + customer_id).slideUp();
    $("#customer_" + customer_id + " .toggle_user_details").html("&plus;");
    $("#customer_" + customer_id + " tr").addClass("hover");
  } else {
    $("#customer_details_" + customer_id).addClass("active");
    $("#customer_details_" + customer_id).slideDown();
    $("#customer_" + customer_id + " .toggle_user_details").html("&minus;");
    $("#customer_" + customer_id + " tr").removeClass("hover");
  }
}

function DeleteCustomer() {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var customer_id = $(".delete_customer_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/delete/delete-customer.php",
    type: "POST",
    data: {
      customer_id: customer_id
    }
  }).done(function () {
    //alert(data);
    $("#modal_confirm").dialog("close");
    $("div#customer_" + customer_id).remove();
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetCustomerLog(customer_id) {
  var url = "/" + admin_dir_name + "/customers/ajax/get/get-customer-log.php?customer_id=" + customer_id;
  window.open(url, 'mywindow', 'status=no,location=yes,resizable=yes,scrollbars=yes,width=800,height=800,left=100,top=0,screenX=0,screenY=0');
}

function AddCustomerGroup() {
  ShowAjaxLoader();
  var customer_group_name = $("#add_customer_group_name").val();
  var customer_group_sort_order = $("#add_customer_group_sort_order").val();
  //alert(menu_link_note);return;
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/add/add-customer-group.php",
    type: "POST",
    data: {
      customer_group_name: customer_group_name,
      customer_group_sort_order: customer_group_sort_order
    }
  }).done(function () {
    //alert($("#current_page").val());

    window.location = base_url + $("#current_page").val();

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditCustomerGroup(customer_group_id) {
  ShowAjaxLoader();
  var customer_group_div = "#customer_group_" + customer_group_id;
  var customer_group_name = $(customer_group_div + " .customer_group_name").val();
  var customer_group_sort_order = $(customer_group_div + " .customer_group_sort_order").val();
  //alert(menu_translation_text);return;
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/edit/edit-customer-group.php",
    type: "POST",
    data: {
      customer_group_id: customer_group_id,
      customer_group_name: customer_group_name,
      customer_group_sort_order: customer_group_sort_order
    }
  }).done(function () {
    $(".row_over").removeClass("row_over_edit");
    $(customer_group_div + " td").effect("highlight", {}, 3000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteCustomerGroup() {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var customer_group_id = $(".delete_customer_group.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/delete/delete-customer-group.php",
    type: "POST",
    data: {
      customer_group_id: customer_group_id
    }
  }).done(function () {
    //alert(data);
    $("#modal_confirm").dialog("close");
    $("div#customer_group_" + customer_group_id).remove();
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function ToggleCustomerGroupTranslation(button, customer_group_id) {
  if ($(".customer_group_translation_row_" + customer_group_id).hasClass("active")) {
    $(".customer_group_translation_row_" + customer_group_id).removeClass("active");
    $(".customer_group_translation_row_" + customer_group_id).hide();
    $(button).html("+");
  } else {
    $(".customer_group_translation_row_" + customer_group_id).addClass("active");
    $(".customer_group_translation_row_" + customer_group_id).show();
    $(button).html("-");
  }
}

function AddCustomerGroupTranslation(customer_group_id, language_id) {
  ShowAjaxLoader();
  var customer_group_translation_tr = "#customer_group_translation_" + customer_group_id + language_id;
  var customer_group_translation_text = $(customer_group_translation_tr + " .customer_group_translation_text").val();
  if (customer_group_translation_text == "") {
    alert("Please enter a resort name!");
    return;
  }
  //alert(customer_group_translation_text);
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/add/add-customer-group-translation.php",
    type: "POST",
    data: {
      customer_group_id: customer_group_id,
      language_id: language_id,
      customer_group_translation_text: customer_group_translation_text
    }
  }).done(function () {
    //alert(data);
    $(".row_over").removeClass("row_over_edit");
    $(customer_group_translation_tr + " td").effect("highlight", {}, 3000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditCustomerGroupTranslation(customer_group_id, language_id) {
  ShowAjaxLoader();
  var customer_group_translation_tr = "#customer_group_translation_" + customer_group_id + language_id;
  var customer_group_translation_text = $(customer_group_translation_tr + " .customer_group_translation_text").val();
  //alert(customer_group_translation_text);return;
  $.ajax({
    url: "/" + admin_dir_name + "/customers/ajax/edit/edit-customer-group-translation.php",
    type: "POST",
    data: {
      customer_group_id: customer_group_id,
      language_id: language_id,
      customer_group_translation_text: customer_group_translation_text
    }
  }).done(function () {
    $(".row_over").removeClass("row_over_edit");
    $(customer_group_translation_tr + " td").effect("highlight", {}, 3000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetContentTypeActiveInactive(link, content_type_id, set_content_type) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/set-content-type-active-inactive.php",
    type: "POST",
    data: {
      content_type_id: content_type_id,
      set_content_type: set_content_type
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if(set_content_type == "0") {
      $(link).attr("onClick", "SetContentTypeActiveInactive(this,'" + content_type_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetContentTypeActiveInactive(this,'" + content_type_id + "','0')");
      $(link).html(img_active);
    }

    $("#ct_" + content_type_id+" td").effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveContentTypeForwardBackward(content_type_id, content_type_sort_order, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/move-content-type-forward-backward.php",
    type: "POST",
    data: {
      content_type_id: content_type_id,
      content_type_sort_order: content_type_sort_order,
      action: action
    }
  }).done(function (contents) {

    $("#content_types_list").html(contents);
    $("#ct_" + content_type_id+" td").effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteContentType() {
  ShowAjaxLoader();
  var content_type_id = $(".delete_content_type_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/delete/delete-content-type.php",
    type: "POST",
    data: {
      content_type_id: content_type_id
    }
  }).done(function (content_types) {

    $("#modal_confirm").dialog("close");
    $("#content_types_list").html(content_types);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function ToggleExpandContent(content_id, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/toggle-expand-content.php",
    type: "POST",
    data: {
      content_id: content_id,
      action: action
    }
  }).done(function (contents) {

    $("#contents_list").html(contents);
    $("#tr_" + content_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetContentActiveInactive(link, content_id, set_content) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/set-content-active-inactive.php",
    type: "POST",
    data: {
      content_id: content_id,
      set_content: set_content
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_content == "0") {
      $(link).attr("onClick", "SetContentActiveInactive(this,'" + content_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetContentActiveInactive(this,'" + content_id + "','0')");
      $(link).html(img_active);
    }

    $("#tr_" + content_id).effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetContentAsHomePage(content_id, content_root_id) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/set-content-as-home-page.php",
    type: "POST",
    data: {
      content_id: content_id,
      content_root_id: content_root_id
    }
  }).done(function (contents) {

    $("#contents_list").html(contents);
    $("#tr_" + content_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveContentForwardBackward(content_id, content_parent_id, content_menu_order, content_hierarchy_level, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/move-content-forward-backward.php",
    type: "POST",
    data: {
      content_id: content_id,
      content_parent_id: content_parent_id,
      content_menu_order: content_menu_order,
      content_hierarchy_level: content_hierarchy_level,
      action: action
    }
  }).done(function (contents) {

    $("#contents_list").html(contents);
    $("#tr_" + content_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteContent() {
  ShowAjaxLoader();
  var content_id = $(".delete_content_link.active").attr("data-id");
  var content_parent_id = $(".delete_content_link.active").attr("data-parent");
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/delete/delete-content.php",
    type: "POST",
    data: {
      content_id: content_id,
      content_parent_id: content_parent_id
    }
  }).done(function (contents) {

    $("#modal_confirm").dialog("close");
    $("#contents_list").html(contents);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditContentMainTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_content_main_tab"]').serializeArray();
  form_data.push(
      {name: 'content_id', value: $("#content_id").val()}
  );
  $.each($(clicked_tab + " .language_tab"), function () {
    var language_id = $(this).attr("data-id");
    //console.log(language_id);
    form_data.push(
      {name: 'content_summary[' + language_id + ']', value: CKEDITOR.instances["content_summary[" + language_id + "]"].getData()},
      {name: 'content_text[' + language_id + ']', value: CKEDITOR.instances["content_text[" + language_id + "]"].getData()}
    );
  });
  //console.log(pl_summaries);
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/edit-content-main-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditContentOptionsTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_content_options_tab"]').serializeArray();
  form_data.push(
    {name: 'content_id', value: $("#content_id").val()}
  );
  //console.log(pl_summaries);
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/edit-content-options-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditContentMetaTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_content_meta_information_tab"]').serializeArray();
  form_data.push(
    {name: 'content_id', value: $("#content_id").val()}
  );
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/edit-content-meta-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditContentIncludeBlocksTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_content_inlude_blocks_tab"]').serializeArray();
  form_data.push(
    {name: 'content_id', value: $("#content_id").val()}
  );
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/edit/edit-content-include-blocks-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteContent() {
  ShowAjaxLoader();
  var content_id = $(".delete_content_link.active").attr("data-id");
  var content_parent_id = $(".delete_content_link.active").attr("data-parent");
  $.ajax({
    url: "/" + admin_dir_name + "/content/ajax/delete/delete-content.php",
    type: "POST",
    data: {
      content_id: content_id,
      content_parent_id: content_parent_id
    }
  }).done(function (contents) {

    $("#modal_confirm").dialog("close");
    $("#contents_list").html(contents);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetLanguageActiveInactive(link, language_id, set_language) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/language/ajax/edit/set-language-active-inactive.php",
    type: "POST",
    data: {
      language_id: language_id,
      set_language: set_language
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_language == "0") {
      $(link).attr("onClick", "SetLanguageActiveInactive(this,'" + language_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetLanguageActiveInactive(this,'" + language_id + "','0')");
      $(link).html(img_active);
    }

    $("#tr_" + language_id).effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveLanguageForwardBackward(language_id, language_menu_order, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/language/ajax/edit/move-language-forward-backward.php",
    type: "POST",
    data: {
      language_id: language_id,
      language_menu_order: language_menu_order,
      action: action
    }
  }).done(function (languages) {

    $("#languages_list").html(languages);
    $("#tr_" + language_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetLanguageDefault(language_id, default_for) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/language/ajax/edit/set-language-default.php",
    type: "POST",
    data: {
      language_id: language_id,
      default_for: default_for
    }
  }).done(function (languages) {

    $("#languages_list").html(languages);
    $("#tr_" + language_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteLanguage(step) {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var language_id = $(".delete_language_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/language/ajax/delete/delete-language.php",
    type: "POST",
    data: {
      language_id: language_id,
      step: step
    }
  }).done(function (languages) {

    if (step == "first") {
      $("#modal_confirm").dialog("close");
    } else {
      $("#modal_confirm_delete_language").dialog("close");
    }
    $("#languages_list").html(languages);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function ShowOneMoreImagesInput(max_images) {
  var current_input_id = $("#more_content_images_id").val();
  if ($("input.content_image_file").length == max_images) {
    alert("You can't upload more then " + max_images + " gallery pictures");
    return;
  }
  $("#more_gal_imgs_container").append('<p  id="content_image_' + current_input_id + '"><input type="file" name="content_image[]" class="content_image_file" style="width: auto;" />&nbsp;<a onclick="RemoveContentImageRow(' + current_input_id + ')"><img src="/' + admin_dir_name + '/images/delete.gif" class="systemicon" alt="' + $("#alt_delete").val() + '" title="' + $("#alt_delete").val() + '" width="16" height="16" /></a></p>');
  $("#content_image_" + current_input_id).show();
  var next_input_id = (parseInt(current_input_id) + 1);
  $("#more_content_images_id").val(next_input_id);
}

function SetNewsActiveInactive(link, news_id, set_news) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/edit/set-news-active-inactive.php",
    type: "POST",
    data: {
      news_id: news_id,
      set_news: set_news
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_news == "0") {
      $(link).attr("onClick", "SetNewsActiveInactive(this,'" + news_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetNewsActiveInactive(this,'" + news_id + "','0')");
      $(link).html(img_active);
    }

    $("#tr_" + news_id).effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditNewsMainTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edin_news_main_tab"]').serializeArray();
  form_data.push({name: 'news_id', value: $("#news_id").val()});
  $.each($(clicked_tab + " .language_tab"), function () {
    //alert(CKEDITOR.instances["news_text["+$(this).attr("data-id")+"]"].getData());
    var language_id = $(this).attr("data-id");
    form_data.push(
      {name: 'news_summary[' + language_id + ']', value: CKEDITOR.instances["news_summary[" + language_id + "]"].getData()},
      {name: 'news_text[' + language_id + ']', value: CKEDITOR.instances["news_text[" + language_id + "]"].getData()}
    );
  });
  //alert(news_form);return;
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/edit/edit-news-main-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab + " .ajax_result").html(result);
    $("#edit_content").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditNewsCategoriesTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_news_categories_tab"]').serializeArray();
  form_data.push({name: 'news_id', value: $("#news_id").val()});
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/edit/edit-news-categories-tab.php",
    type: "POST",
    data: form_data
  }).done(function (categories) {

    $(clicked_tab + " #ajax_result").html(categories);
    $(clicked_tab + " #edit_news_categories_tab").html(categories);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_news_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditNewsMetaTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_news_meta_information_tab"]').serializeArray();
  form_data.push({name: 'news_id', value: $("#news_id").val()});
  //console.log(pd_summaries);
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/edit/edit-news-meta-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditNewsGalleryTab() {
  ShowAjaxLoader();
  var news_id = $("#news_id").val();
  var news_image_ids = [];
  var i = 0;
  $.each($(".ui-state-default"), function () {
    news_image_ids[i] = $(this).attr("data-id");
    i++;
  });
  //alert(clicked_tab);return;
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/edit/edit-news-images-tab.php",
    type: "POST",
    data: {
      news_id: news_id,
      news_image_ids: news_image_ids
    }
  }).done(function () {

    $("#edit_news").effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_news_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteNewsImage(image_id, image, type) {
  ShowAjaxLoader();
  if (type == '1') {
    // default image
    //var image = $("#default_image").val();
  }
  if (type == '2') {
    // gallery image
    //var image = $("#gallery_image_"+image_id).val();
  }
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/delete/delete-news-image.php",
    type: "POST",
    data: {
      image_id: image_id,
      image: image,
      type: type
    }
  }).done(function () {

    $("#modal_confirm_delete_img").dialog("close");
    $("#gallery_image_" + image_id).remove();

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function ToggleExpandNewsCategory(news_category_id, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/edit/toggle-expand-news-category.php",
    type: "POST",
    data: {
      news_category_id: news_category_id,
      action: action
    }
  }).done(function (news_categories) {

    $("#news_categories_list").html(news_categories);
    $("#tr_" + news_category_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveNewsCategoryForwardBackward(news_category_id, news_cat_parent_id, news_cat_sort_order, news_cat_hierarchy_level, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/edit/move-news-category-forward-backward.php",
    type: "POST",
    data: {
      news_category_id: news_category_id,
      news_cat_parent_id: news_cat_parent_id,
      news_cat_sort_order: news_cat_sort_order,
      news_cat_hierarchy_level: news_cat_hierarchy_level,
      action: action
    }
  }).done(function (news_categories) {

    $("#news_categories_list").html(news_categories);
    $("#tr_" + news_category_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteNewsCategory() {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var news_category_id = $(".delete_news_category_link.active").attr("data-id");
  var news_cat_parent_id = $(".delete_news_category_link.active").attr("data-parent-id");
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/delete/delete-news-category.php",
    type: "POST",
    data: {
      news_category_id: news_category_id,
      news_cat_parent_id: news_cat_parent_id
    }
  }).done(function (news_categories) {

    $("#modal_confirm").dialog("close");
    $("#news_categories_list").html(news_categories);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteNews(page) {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var news_id = $(".delete_news_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/delete/delete-news.php",
    type: "POST",
    data: {
      news_id: news_id,
      page: page
    }
  }).done(function (news) {

    $("#modal_confirm").dialog("close");
    if (page == "news_list") {
      $("#news_list").html(news);
    } else {
      window.history.back();
    }

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetNewsDefaultImage(news_id) {
  ShowAjaxLoader();
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/get/get-news-default-image.php",
    type: "POST",
    data: {
      news_id: news_id
    }
  }).done(function (current_image) {

    $("#current_image").html(current_image);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetNewsGalleryImages(news_id) {
  ShowAjaxLoader();
  $.ajax({
    url: "/" + admin_dir_name + "/news/ajax/get/get-news-gallery-images.php",
    type: "POST",
    data: {
      news_id: news_id
    }
  }).done(function (gallery_images) {

    $("#news_gallery_tab").html(gallery_images);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetMapCoords() {
  var map_address = $("#map_address").val();
  //console.log(map_address);
  if(!$.trim(map_address)) {
    alert($("#warning_address_cant_be_empty").val()); return;
  }
  else {
    var address = map_address.toLowerCase().replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
    var geocoder = new google.maps.Geocoder();
    if (address.length != 0) {
      geocoder.geocode({ address: address, region: 'BG' }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK && status != google.maps.GeocoderStatus.ZERO_RESULTS) {

            var geometry = results[0].geometry; //console.log(results[0]);
            var latitude = geometry.location.lat();
            var longitude = geometry.location.lng();

            if((!$.trim(latitude)) || (!$.trim(longitude))) {
              alert($("#warning_google_cant_find_coords_for_this_address").val());
              return false;
            }
            else {
              $("#contact_map_lat").val(latitude);
              $("#contact_map_lng").val(longitude);
            }

        } else {
            alert("Google Maps could not find a location matching entry with the provided address, city and state");
            return false;
        }
      });
    } else {
        alert("Address coulnd't be empy!"); return;
        return false;
    }
  }
}

function SetContactDefault(contact_id) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
  url:"/" + admin_dir_name + "/contacts/ajax/edit/set-contact-as-default.php",
  type:"POST",
  data:{
    contact_id:contact_id
    }
  }).done(function(contacts){
    
    $("#modal_confirm_set_contact_default").dialog("close");
    $("#contacts_list").html(contacts);
    $("#contact_"+contact_id+" td").effect("highlight", {}, 1000);
    
    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  });
}

function SetContactActiveInactive(link,contact_id, set_contact) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
  url:"/" + admin_dir_name + "/contacts/ajax/edit/set-contact-active-inactive.php",
  type:"POST",
  data:{
    contact_id:contact_id,
    set_contact:set_contact
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();
    
    if(set_contact == "0") {
      $(link).attr("onClick","SetContactActiveInactive(this,'"+contact_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetContactActiveInactive(this,'"+contact_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+contact_id+" td").effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  });
}

function AddContactPhoneRow() {
  var current_phones_count = $("#current_phones_count").val();
  $("#more_contact_phones_container").append('<div id="contact_phone_' + current_phones_count + '" class="col-lg-6 col-md-8 col-sm-12 col-xs-12">\n\
    <input type="text" name="contact_phones[' + current_phones_count + '][phone]" class="col-lg-7 col-md-7 col-sm-12 col-xs-12" >&nbsp;\n\
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">&nbsp;\n\
    &nbsp;<input type="checkbox" name="contact_phones[' + current_phones_count + '][is_home]"> Стационарен&nbsp;\n\
    </div>\n\
    <a onclick="RemoveContactPhoneRow(' + current_phones_count + ')" title="' + $("#title_delete").val() + '">\n\
      <img src="/' + admin_dir_name + '/images/delete.gif" class="systemicon" alt="' + $("#title_delete").val() + '" width="16" height="16" />\n\
    </a></div><p class="clearfix"></p>');
  var current_phones_count = (parseInt(current_phones_count) + 1);
  $("#current_phones_count").val(current_phones_count);
}

function RemoveContactPhoneRow(current_phones_count) {
  $("#contact_phone_" + current_phones_count).remove();
}

function MoveContactForwardBackward(contact_id, contact_sort_order, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
  url:"/" + admin_dir_name + "/contacts/ajax/edit/move-contact-forward-backward.php",
  type:"POST",
  data:{
    contact_id:contact_id,
    contact_sort_order:contact_sort_order,
    action:action
    }
  }).done(function(contacts){
    
    $("#contacts_list").html(contacts);
    $("#contact_"+contact_id+" td").effect("highlight", {}, 1000);
    
    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  });
}

function DeleteContact() {
  if(CheckDeleteRights() === false) return;
  ShowAjaxLoader();
  var contact_id = $(".delete_contact_link.active").attr("data-id");
  $.ajax({
  url:"/" + admin_dir_name + "/contacts/ajax/delete/delete-contact.php",
  type:"POST",
  data:{
    contact_id:contact_id
    }
  }).done(function(){
  
    $("#modal_confirm").dialog("close");
    $("#contacts_list #contact_"+contact_id).remove();

    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  });
}

function SetContactSocialActiveInactive(link,contact_social_id, set_contact_social) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
  url:"/" + admin_dir_name + "/contacts/ajax/edit/set-contact-social-active-inactive.php",
  type:"POST",
  data:{
    contact_social_id:contact_social_id,
    set_contact_social:set_contact_social
    }
  }).done(function(){
    
    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();
    
    if(set_contact_social == "0") {
      $(link).attr("onClick","SetContactSocialActiveInactive(this,'"+contact_social_id+"','1')");
      $(link).html(img_inactive);
    }
    else {
      $(link).attr("onClick","SetContactSocialActiveInactive(this,'"+contact_social_id+"','0')");
      $(link).html(img_active);
    }
    
    $("#tr_"+contact_social_id+" td").effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  });
}

function MoveContactSocialForwardBackward(contact_social_id, contact_social_sort_order, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
  url:"/" + admin_dir_name + "/contacts/ajax/edit/move-contact-social-forward-backward.php",
  type:"POST",
  data:{
    contact_social_id:contact_social_id,
    contact_social_sort_order:contact_social_sort_order,
    action:action
    }
  }).done(function(contacts){
    
    $("#contact_socials_list").html(contacts);
    $("#contact_social_"+contact_social_id+" td").effect("highlight", {}, 1000);
    
    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  });
}

function DeleteContactSocial() {
  if(CheckDeleteRights() === false) return;
  ShowAjaxLoader();
  var contact_social_id = $(".delete_contact_social_link.active").attr("data-id");
  $.ajax({
  url:"/" + admin_dir_name + "/contacts/ajax/delete/delete-contact-social.php",
  type:"POST",
  data:{
    contact_social_id:contact_social_id
    }
  }).done(function(){
  
    $("#modal_confirm").dialog("close");
    $("#contact_socials_list #contact_social_"+contact_social_id).remove();

    HideAjaxLoader();
  }).fail(function(error){
    console.log(error);
  });
}

function ToggleExpandCategory(category_hierarchy_ids, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/toggle-expand-category.php",
    type: "POST",
    data: {
      category_hierarchy_ids: category_hierarchy_ids,
      action: action
    }
  }).done(function (categories) {

    $("#categories_list").html(categories);
    $("#cat_" + category_hierarchy_ids).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetCategoryActiveInactive(link, category_hierarchy_ids, set_category) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/set-category-active-inactive.php",
    type: "POST",
    data: {
      category_hierarchy_ids: category_hierarchy_ids,
      set_category: set_category
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_category == "0") {
      $(link).attr("onClick", "SetCategoryActiveInactive(this,'" + category_hierarchy_ids + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetCategoryActiveInactive(this,'" + category_hierarchy_ids + "','0')");
      $(link).html(img_active);
    }

    $("#cat_" + category_hierarchy_ids).effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveCategoryForwardBackward(category_id,category_parent_id,category_root_id,category_sort_order,category_hierarchy_level,action) {
  ShowAjaxLoader();
  //alert(category_id);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/move-category-forward-backward.php",
    type: "POST",
    data: {
      category_id: category_id,
      category_parent_id: category_parent_id,
      category_root_id: category_root_id,
      category_sort_order: category_sort_order,
      category_hierarchy_level: category_hierarchy_level,
      action: action
    }
  }).done(function (categories) {

    $("#categories_list").html(categories);
    $("#cat_"+category_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditCategoryMainTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_category_main_tab"]').serializeArray();
  form_data.push({name: 'category_id', value: $("#category_id").val()},{name: 'category_parent_id_level', value: $("#category_parent_id_level").val()});
  $.each($(clicked_tab + " .language_tab"), function () {
    var language_id = $(this).attr("data-id");
    form_data.push(
      {name: 'cd_descriptions[' + language_id + ']', value: CKEDITOR.instances["cd_descriptions[" + language_id + "]"].getData()}
    );
  });
  //console.log(pl_summaries);
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/edit-category-main-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditCategoryCategoriesTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_category_categories_tab"]').serializeArray();
  form_data.push({name: 'category_id', value: $("#category_id").val()});
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/edit-category-categories-tab.php",
    type: "POST",
    data: form_data
  }).done(function (categories) {

    //$(clicked_tab + " .ajax_result").html(categories);
    $(clicked_tab + " #edit_category_categories_tab").html(categories);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditCategoryOptionsTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_category_options_tab"]').serializeArray();
  form_data.push({name: 'category_hierarchy_ids', value: $("#category_hierarchy_ids").val()});
  //console.log(pl_summaries);
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/edit-category-options-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function EditCategoryMetaTab(clicked_tab) {
  ShowAjaxLoader();
  var form_data = $('form[name="edit_category_meta_information_tab"]').serializeArray();
  form_data.push({name: 'category_id', value: $("#category_id").val()}, {name: 'category_parent_id_level', value: $("#category_parent_id_level").val()});
  //console.log(pd_summaries);
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/edit-category-meta-tab.php",
    type: "POST",
    data: form_data
  }).done(function (result) {

    $(clicked_tab).prepend(result);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetSubcategories(category_id) {
  //if(CheckEditRights() === false) return;
  ShowAjaxLoader();
  var category_ids_list = $("#link_" + category_id).attr("data-ids");
  var category_name = $("#link_" + category_id).html();
  var category_hierarchy_level = $("#link_" + category_id).attr("data-h-level");
  $(".contents_options").hide();
  $("#right_column").hide();
  $.each($("#subcategories .list_container"), function () {
    var level = $(this).attr("level");
    if (level > category_hierarchy_level)
      $(this).remove();
  });
  $("#left_column .level_" + category_hierarchy_level + " td").removeClass("active");
  $("#link_" + category_id).parent().addClass("active");
  //alert(attribute_group_id);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/get/get-subcategories-by-category.php",
    type: "POST",
    data: {
      category_ids_list: category_ids_list,
      category_id: category_id,
      category_name: category_name,
      category_hierarchy_level: category_hierarchy_level
    }
  }).done(function (categories) {

    if (category_hierarchy_level > 1) {
      $("#left_column #subcategories").append(categories);
    } else {
      $("#left_column #subcategories").html(categories);
    }
//    if(current_category_id != 0) {
//      GetCategoriesByCategory(current_category_id);
//    }
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteCategory() {
  ShowAjaxLoader();
  var category_id = $(".delete_category_link.active").attr("data-id");
  var parent_id = $(".delete_category_link.active").attr("data-parent");
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/delete/delete-category.php",
    type: "POST",
    data: {
      category_id: category_id,
      parent_id: parent_id
    }
  }).done(function (categories) {

    $("#modal_confirm").dialog("close");
    $("#categories_list").html(categories);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveOptionForwardBackward(option_id, option_sort_order, action) {
  ShowAjaxLoader();
  //alert(option_id);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/move-option-forward-backward.php",
    type: "POST",
    data: {
      option_id: option_id,
      option_sort_order: option_sort_order,
      action: action
    }
  }).done(function (options) {

    $("#categories_options_list").html(options);
    $("#tr_" + option_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteOption(step) {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var option_id = $(".delete_option_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/delete/delete-option.php",
    type: "POST",
    data: {
      option_id: option_id,
      step: step
    }
  }).done(function (options) {

    //jQuery('html, body').animate({scrollTop: 0}, 1000);
    if (step == "first") {
      $("#modal_confirm").dialog("close");
    } else {
      $("#modal_confirm_delete_option").dialog("close");
    }
    $("#categories_options_list").html(options);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveOptionValueForwardBackward(option_id, option_value_id, ov_sort_order, option_key, action) {
  ShowAjaxLoader();
  //alert(option_value_id);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/move-option-value-forward-backward.php",
    type: "POST",
    data: {
      option_id: option_id,
      option_value_id: option_value_id,
      ov_sort_order: ov_sort_order,
      action: action
    }
  }).done(function (option_values) {

    $("#option_values").html(option_values);
    $("#option_value_row_" + option_key).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteOptionValue(step) {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var option_value_id = $(".delete_option_value_link.active").attr("data-id");
  var option_value_row = $(".delete_option_value_link.active").attr("data-row");
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/delete/delete-option-value.php",
    type: "POST",
    data: {
      option_value_id: option_value_id,
      step: step
    }
  }).done(function (option_values) {

    //$("#delete_result").hide();
    if (step == "first") {
      $("#modal_confirm_delete_option_value").dialog("close");
      if (option_values == "") {
        $("#option_value_row_" + option_value_row).remove();
      } else {
        $("#delete_result").show();
        $("#delete_result").html(option_values);
      }
    } else {
      $("#modal_confirm_delete_option_value").dialog("close");
      $("#option_value_row_" + option_value_row).remove();
    }

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function ShowOneMoreImagesInput(max_images) {
  var current_input_id = $("#more_product_images_id").val();
  if ($("input.product_image_file").length == max_images) {
    alert("You can't upload more then " + max_images + " gallery pictures");
    return;
  }
  $("#more_gal_imgs_container").append('<p  id="product_image_' + current_input_id + '"><input type="file" name="product_image[]" class="product_image_file" style="width: auto;" />&nbsp;<a onclick="RemoveProductImageRow(' + current_input_id + ')"><img src="/' + admin_dir_name + '/images/delete.gif" class="systemicon" alt="' + $("#alt_delete").val() + '" title="' + $("#alt_delete").val() + '" width="16" height="16" /></a></p>');
  $("#product_image_" + current_input_id).show();
  var next_input_id = (parseInt(current_input_id) + 1);
  $("#more_product_images_id").val(next_input_id);
}

function RemoveProductImageRow(current_input_id) {
  $("#product_image_" + current_input_id).remove();
}

function AddProductOptionValue(option_key) {
  var option_div = "#option_" + option_key;
  var product_option_value_row = $(option_div + " .product_option_values_count").val();
  //console.log(languages_count);

  var html = '<tbody class="product_option_value_row_' + product_option_value_row + ' product_option_value_row" row-key="' + product_option_value_row + '">';
  html += '  <tr>';
  html += '    <td class="text_left">';
  if ($(option_div + " .product_options_select").length) {
    html += '    <input type="hidden" name="product_options[' + option_key + '][product_option_value][' + product_option_value_row + '][new_entry]" value="1" />';
    html += '    <select name="product_options[' + option_key + '][product_option_value][' + product_option_value_row + '][option_value_id]">';
    html += $(option_div + " .product_options_select").html();
    html += '    </select>';
  } else {
    html += $(option_div + " .text_no_options_yet").val();
  }
  html += '</td>';
  html += '    <td class="text_left"><input type="hidden" name="product_options[' + option_key + '][product_option_value][' + product_option_value_row + '][new_entry]" value="1" />';
  $.each($(".language_ids"), function () {
    var language_id = $(this).val();
    var language_code = $(this).attr("data-code");
    var language_name = $(this).attr("data-name");
    html += '<input type="text" style="width:90%" name="product_options[' + option_key + '][product_option_value][' + product_option_value_row + '][ovd_values][' + language_id + ']" value="" />';
    html += '&nbsp;&nbsp;<img src="/' + admin_dir_name + '/images/flags/' + language_code + '.png" title="' + language_name + '"><p class="clearfix"></p>';
  });
  html += '    </td>';
  html += '    <td><a onclick="RemoveProductOptionValue(\'' + option_div + '\',\'' + product_option_value_row + '\')" class="button red">' + $("#text_btn_delete").val() + '</a></td>';
  html += '  </tr>';
  html += '</tbody>';

  $(option_div + " table tfoot").before(html);
//  $(option_div + " table tfoot").addClass("hidden");

  product_option_value_row++;
  $(option_div + " .product_option_values_count").val(product_option_value_row);

}

function RemoveProductOptionValue(option_div, product_option_value_row) {
  $(option_div + ' .product_option_value_row_' + product_option_value_row).remove();
//  $(option_div + " table tfoot").removeClass("hidden");
  $(option_div+" .product_option_values_count").val(product_option_value_row);
}

function EditProductOptionsTab(clicked_tab) {
  ShowAjaxLoader();
  var product_id = $("#product_id").val();
  var form_data = $('form[name="edit_product_options_tab"]').serializeArray();
  form_data.push({name: 'product_id', value: product_id});
  //alert(clicked_tab);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/edit/edit-product-options-tab.php",
    type: "POST",
    data: form_data
  }).done(function (options) {

    $(clicked_tab).prepend(options);
    $(clicked_tab).effect("highlight", {}, 1000);
    var massage = $("#ajaxmessage_update_tab_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    GetProductOptions(product_id);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetProductOptions(product_id) {
  ShowAjaxLoader();
  var language_id = $("#language_id").val();
  var category_id = $("#category_id").val();
  var active_option_tab = $("#product_options_tabs a.active").attr("href");
  //alert(attribute_group_id);return;
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/get/get-product-options.php",
    type: "POST",
    data: {
      language_id: language_id,
      category_id: category_id,
      product_id: product_id
    }
  }).done(function (product_options) {

    $("#product_options_tab form").html(product_options);

    $("#product_options_tabs a").removeClass("active");
    $(".product_option_tab").hide();
    $("#product_options_tabs a[href='" + active_option_tab + "']").addClass("active");
    $(active_option_tab).show();

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteProductOptionValue(product_option_id, option_value_id, ov_is_custom, product_option_div) {
  ShowAjaxLoader();
  $.ajax({
    url: "/" + admin_dir_name + "/welding/ajax/delete/delete-product-option-value.php",
    type: "POST",
    data: {
      product_option_id: product_option_id,
      option_value_id: option_value_id,
      ov_is_custom: ov_is_custom
    }
  }).done(function (result) {

    $("#ajax_result").html(result);
    $("#modal_confirm_delete_option_value").dialog("close");
    $(product_option_div).remove();

//    GetProductOptions(product_id);
    
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetSliderImage(slider_id) {
  ShowAjaxLoader();
  $.ajax({
    url: "/" + admin_dir_name + "/sliders/ajax/get/get-slider-image.php",
    type: "POST",
    data: {
      slider_id: slider_id
    }
  }).done(function (current_image) {

    $("#current_image").html(current_image);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetSliderActiveInactive(link, slider_id, set_slider) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/sliders/ajax/edit/set-slider-active-inactive.php",
    type: "POST",
    data: {
      slider_id: slider_id,
      set_slider: set_slider
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_slider == "0") {
      $(link).attr("onClick", "SetSliderActiveInactive(this,'" + slider_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetSliderActiveInactive(this,'" + slider_id + "','0')");
      $(link).html(img_active);
    }

    $("#tr_" + slider_id).effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveSliderForwardBackward(slider_id, slider_sort_order, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/sliders/ajax/edit/move-slider-forward-backward.php",
    type: "POST",
    data: {
      slider_id: slider_id,
      slider_sort_order: slider_sort_order,
      action: action
    }
  }).done(function (sliders) {

    $("#sliders_list").html(sliders);
    $("#tr_" + slider_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteSliderImage(dropzone_id) {
  ShowAjaxLoader();
  var dropzone_div = "#" + dropzone_id;
  var slider_id = $(dropzone_div + " .slider_id").val();
  var slider_type = $(dropzone_div + " .slider_type").val();
  $.ajax({
    url: "/" + admin_dir_name + "/sliders/ajax/delete/delete-slider-image.php",
    type: "POST",
    data: {
      slider_id: slider_id,
      slider_type: slider_type
    }
  }).done(function () {

    if (slider_type == "background") {
      $("#current_background_image").html("");
    } else {
      $("#current_forground_image").html("");
    }

    CalculateDropzoneBoxHeight();

    HideAjaxLoader();
  }).fail(function (e) {
    console.log(e)
  })
}

function DeleteSlider(page) {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var slider_id = $(".delete_slider_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/sliders/ajax/delete/delete-slider.php",
    type: "POST",
    data: {
      slider_id: slider_id
    }
  }).done(function () {

    $("#modal_confirm").dialog("close");

    if (page == "details") {
      window.location = "/" + admin_dir_name + "/sliders/sliders.php";
    } else {
      $("#sliders_list #tr_" + slider_id).remove();
    }

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function GetBannerImage(banner_id) {
  ShowAjaxLoader();
  $.ajax({
    url: "/" + admin_dir_name + "/banners/ajax/get/get-banner-image.php",
    type: "POST",
    data: {
      banner_id: banner_id
    }
  }).done(function (current_image) {

    $("#current_image").html(current_image);
    var massage = $("#ajaxmessage_update_banner_success").val();
    $("#ajax_notification .ajaxmessage").html(massage);
    $("#ajax_notification").slideDown(500);
    $("#ajax_notification").delay(3500).slideUp(900);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetBannerActiveInactive(link, banner_id, set_banner) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/banners/ajax/edit/set-banner-active-inactive.php",
    type: "POST",
    data: {
      banner_id: banner_id,
      set_banner: set_banner
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_banner == "0") {
      $(link).attr("onClick", "SetBannerActiveInactive(this,'" + banner_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetBannerActiveInactive(this,'" + banner_id + "','0')");
      $(link).html(img_active);
    }

    $("#tr_" + banner_id).effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveBannerForwardBackward(banner_id, banner_sort_order, action) {
    return;
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/banners/ajax/edit/move-banner-forward-backward.php",
    type: "POST",
    data: {
      banner_id: banner_id,
      banner_sort_order: banner_sort_order,
      action: action
    }
  }).done(function (banners) {

    $("#banners_list").html(banners);
    $("#tr_" + banner_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteBanner(page) {
  ShowAjaxLoader();
  var banner_id = $(".delete_banner_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/banners/ajax/delete/delete-banner.php",
    type: "POST",
    data: {
      banner_id: banner_id
    }
  }).done(function () {

    $("#modal_confirm").dialog("close");

    if(page == "details") {
      window.location = "/" + admin_dir_name + "/banners/banners.php";
    } else {
      $("#banners_list #banner_" + banner_id).remove();
    }

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function SetTestimonialActiveInactive(link, testimonial_id, set_testimonial) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/testimonials/ajax/edit/set-testimonial-active-inactive.php",
    type: "POST",
    data: {
      testimonial_id: testimonial_id,
      set_testimonial: set_testimonial
    }
  }).done(function () {

    var img_active = $(".images_act_inact .act").html();
    var img_inactive = $(".images_act_inact .inact").html();

    if (set_testimonial == "0") {
      $(link).attr("onClick", "SetTestimonialActiveInactive(this,'" + testimonial_id + "','1')");
      $(link).html(img_inactive);
    } else {
      $(link).attr("onClick", "SetTestimonialActiveInactive(this,'" + testimonial_id + "','0')");
      $(link).html(img_active);
    }

    $("#tr_" + testimonial_id).effect("highlight", {}, 1000);
    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function MoveTestimonialForwardBackward(testimonial_id, testimonial_sort_order, action) {
  ShowAjaxLoader();
  //alert(friendly_url);return;
  $.ajax({
    url: "/" + admin_dir_name + "/testimonials/ajax/edit/move-testimonial-forward-backward.php",
    type: "POST",
    data: {
      testimonial_id: testimonial_id,
      testimonial_sort_order: testimonial_sort_order,
      action: action
    }
  }).done(function (testimonials) {

    $("#testimonials_list").html(testimonials);
    $("#tr_" + testimonial_id).effect("highlight", {}, 1000);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}

function DeleteTestimonial() {
  if (CheckDeleteRights() === false)
    return;
  ShowAjaxLoader();
  var testimonial_id = $(".delete_testimonial_link.active").attr("data-id");
  $.ajax({
    url: "/" + admin_dir_name + "/testimonials/ajax/delete/delete-testimonial.php",
    type: "POST",
    data: {
      testimonial_id: testimonial_id
    }
  }).done(function (testimonials) {

    $("#modal_confirm").dialog("close");
    $("#testimonials_list").html(testimonials);

    HideAjaxLoader();
  }).fail(function (error) {
    console.log(error);
  });
}