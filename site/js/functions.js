var base_url = $(".base_url").val();
/**
 * http://www.openjs.com/scripts/events/keyboard_shortcuts/
 * Version : 2.01.B
 * By Binny V A
 * License : BSD
 */
shortcut = {
	'all_shortcuts':{},//All the shortcuts are stored in this array
	'add': function(shortcut_combination,callback,opt) {
		//Provide a set of default options
		var default_options = {
			'type':'keydown',
			'propagate':false,
			'disable_in_input':false,
			'target':document,
			'keycode':false
		}
		if(!opt) opt = default_options;
		else {
			for(var dfo in default_options) {
				if(typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
			}
		}

		var ele = opt.target;
		if(typeof opt.target == 'string') ele = document.getElementById(opt.target);
		var ths = this;
		shortcut_combination = shortcut_combination.toLowerCase();

		//The function to be called at keypress
		var func = function(e) {
			e = e || window.event;
			
			if(opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
				var element;
				if(e.target) element=e.target;
				else if(e.srcElement) element=e.srcElement;
				if(element.nodeType==3) element=element.parentNode;

				if(element.tagName == 'INPUT' || element.tagName == 'TEXTAREA') return;
			}
	
			//Find Which key is pressed
                        var code = "";
			if (e.keyCode) code = e.keyCode;
			else if (e.which) code = e.which;
			var character = String.fromCharCode(code).toLowerCase();
			
			if(code == 188) character=","; //If the user presses , when the type is onkeydown
			if(code == 190) character="."; //If the user presses , when the type is onkeydown

			var keys = shortcut_combination.split("+");
			//Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
			var kp = 0;
			
			//Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
			var shift_nums = {
				"`":"~",
				"1":"!",
				"2":"@",
				"3":"#",
				"4":"$",
				"5":"%",
				"6":"^",
				"7":"&",
				"8":"*",
				"9":"(",
				"0":")",
				"-":"_",
				"=":"+",
				";":":",
				"'":"\"",
				",":"<",
				".":">",
				"/":"?",
				"\\":"|"
			}
			//Special Keys - and their codes
			var special_keys = {
				'esc':27,
				'escape':27,
				'tab':9,
				'space':32,
				'return':13,
				'enter':13,
				'backspace':8,
	
				'scrolllock':145,
				'scroll_lock':145,
				'scroll':145,
				'capslock':20,
				'caps_lock':20,
				'caps':20,
				'numlock':144,
				'num_lock':144,
				'num':144,
				
				'pause':19,
				'break':19,
				
				'insert':45,
				'home':36,
				'delete':46,
				'end':35,
				
				'pageup':33,
				'page_up':33,
				'pu':33,
	
				'pagedown':34,
				'page_down':34,
				'pd':34,
	
				'left':37,
				'up':38,
				'right':39,
				'down':40,
	
				'f1':112,
				'f2':113,
				'f3':114,
				'f4':115,
				'f5':116,
				'f6':117,
				'f7':118,
				'f8':119,
				'f9':120,
				'f10':121,
				'f11':122,
				'f12':123
			}
	
			var modifiers = { 
				shift: { wanted:false, pressed:false},
				ctrl : { wanted:false, pressed:false},
				alt  : { wanted:false, pressed:false},
				meta : { wanted:false, pressed:false}	//Meta is Mac specific
			};
                        
			if(e.ctrlKey)	modifiers.ctrl.pressed = true;
			if(e.shiftKey)	modifiers.shift.pressed = true;
			if(e.altKey)	modifiers.alt.pressed = true;
			if(e.metaKey)   modifiers.meta.pressed = true;
                        
			for(var i=0; k=keys[i],i<keys.length; i++) {
				//Modifiers
				if(k == 'ctrl' || k == 'control') {
					kp++;
					modifiers.ctrl.wanted = true;

				} else if(k == 'shift') {
					kp++;
					modifiers.shift.wanted = true;

				} else if(k == 'alt') {
					kp++;
					modifiers.alt.wanted = true;
				} else if(k == 'meta') {
					kp++;
					modifiers.meta.wanted = true;
				} else if(k.length > 1) { //If it is a special key
					if(special_keys[k] == code) kp++;
					
				} else if(opt['keycode']) {
					if(opt['keycode'] == code) kp++;

				} else { //The special keys did not match
					if(character == k) kp++;
					else {
						if(shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
							character = shift_nums[character]; 
							if(character == k) kp++;
						}
					}
				}
			}
			
			if(kp == keys.length && 
						modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
						modifiers.shift.pressed == modifiers.shift.wanted &&
						modifiers.alt.pressed == modifiers.alt.wanted &&
						modifiers.meta.pressed == modifiers.meta.wanted) {
				callback(e);
	
				if(!opt['propagate']) { //Stop the event
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
			'callback':func, 
			'target':ele, 
			'event': opt['type']
		};
		//Attach the function with the event
		if(ele.addEventListener) ele.addEventListener(opt['type'], func, false);
		else if(ele.attachEvent) ele.attachEvent('on'+opt['type'], func);
		else ele['on'+opt['type']] = func;
	},

	//Remove the shortcut - just specify the shortcut and I will remove the binding
	'remove':function(shortcut_combination) {
		shortcut_combination = shortcut_combination.toLowerCase();
		var binding = this.all_shortcuts[shortcut_combination];
		delete(this.all_shortcuts[shortcut_combination])
		if(!binding) return;
		var type = binding['event'];
		var ele = binding['target'];
		var callback = binding['callback'];

		if(ele.detachEvent) ele.detachEvent('on'+type, callback);
		else if(ele.removeEventListener) ele.removeEventListener(type, callback, false);
		else ele['on'+type] = false;
	}
}

shortcut.add("esc",function() {
  //$("#loginform").hide();
  $("#modal_window_backgr").hide();
  $(".warning_field").remove();
});

function getAbsolutePath() {
  var loc = window.location;
  var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
  return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

// Handling Cookies

function ConfirmCookiesPolicy() {
  createCookie('cookie_policy','1',168);
  $("#cookies_policy").remove();
}

function createCookie(name,value,hours) {
  var expires = "";
  if (hours) {
    var date = new Date();
    date.setTime(date.getTime()+(hours*60*60*1000));
    expires = "; expires="+date.toGMTString();
  }
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function eraseCookie(name) {
  createCookie(name,"",-3);
}

function isEmpty(str) {
  return (!str || 0 === str.length);
}

function new_freecap() {
  // loads new freeCap image
  if(document.getElementById) {
    // extract image name from image source (i.e. cut off ?randomness)
    thesrc = document.getElementById("freecap").src;
    thesrc = thesrc.substring(0,thesrc.lastIndexOf(".")+4);
    // add ?(random) to prevent browser/isp caching
    document.getElementById("freecap").src = thesrc+"?"+Math.round(Math.random()*100000);
  } else {
    alert("Sorry, cannot autoreload freeCap image\nSubmit the form and a new freeCap will be loaded");
  }
}

// Slider

var slider_rotation_time = 6000 // slider time in ms
var slider_enable_rotation = 0;
var slider_timer;
var slider_current = 1;

function SliderRotator(anable) {
  slider_enable_rotation = anable;
  if(slider_enable_rotation == 0) {
    $("#slider_line_change").stop();
  }
  else {
    $("#slider_line_change").animate({width:"100%"},slider_rotation_time, function() {
      // Animation complete.
      $("#slider_line_change").css({width:"0px"});
    });
  }
  clearTimeout(slider_timer);
  slider_timer = setTimeout("SliderAutoRotate()", slider_rotation_time);
}

function SliderAutoRotate() {
  if (slider_enable_rotation == 1) {
    (slider_current < 4) ? slider_current++ : slider_current = 1;
    SliderMakeRotation(slider_current);
  }
  slider_timer = setTimeout("SliderAutoRotate()", slider_rotation_time);
  $("#slider_line_change").animate({width:"100%"},slider_rotation_time, function() {
    // Animation complete.
    $("#slider_line_change").css({width:"0px"});
  });
}

function SliderMakeRotation(slider_current_id) {
  var slider_width = $(".slider_box").css("width");
  var sliders_count = $(".slider_box").length;
  for (i = 1; i < 5; i++) {
    if(i == slider_current_id) {
      var prev_gallery_block = parseInt(slider_current_id)-1;
      var gallery_line_position = "-"+(parseInt(slider_width)*prev_gallery_block);
      $(".slider_box .slider_img").animate({opacity:"0",left:"120%"});
      $(".slider_box h2").animate({top:"-100px"});
      $(".slider_box p").animate({opacity:"0"});
      $(".slider_box a").animate({top:"400px"});
      $(".slider_box").removeClass("slider_box_current");
      $(".slider_thumb").removeClass("current_thumb");
      if(prev_gallery_block == sliders_count) {
        $("#slider_thumb_1").addClass("current_thumb");
        $("#slider_stripe").stop().animate({left:0},500, function() {
          // Animation complete.
          $("#slider_box_1 .slider_img").animate({opacity:"1",left:"60%"},300);
          $("#slider_box_1 h2").animate({top:"60px"},700);
          $("#slider_box_1 p").animate({opacity:"1"},700);
          $("#slider_box_1 a").animate({top:"270px"},700);
        });
        $("#current_slider").val("1");
        slider_current = 1;
      }
      else {
        $("#slider_thumb_"+slider_current_id).addClass("current_thumb");
        $("#slider_stripe").stop().animate({left:gallery_line_position},500, function() {
          // Animation complete.
          $("#slider_box_"+slider_current_id+" .slider_img").animate({opacity:"1",left:"60%"},300);
          $("#slider_box_"+slider_current_id+" h2").animate({top:"60px"},700);
          $("#slider_box_"+slider_current_id+" p").animate({opacity:"1"},700);
          $("#slider_box_"+slider_current_id+" a").animate({top:"270px"},700);
        });
        $("#current_slider").val(slider_current_id);
        slider_current = slider_current_id;
      }
    }
  }
}

function Checkbox(checkbox) {
  //state = checkbox.checked;
  //alert(state);
  if ($(checkbox).parent().hasClass("checkbox_checked")) {
    $(checkbox).parent().removeClass("checkbox_checked");
    $(checkbox).attr("checked", false);
  } else {
    $(checkbox).parent().addClass("checkbox_checked");
    $(checkbox).attr("checked", true);
  }
}

function SelectRadio(radio) {
  state = radio.checked;
  //console.log(state);
  $(".radio.checkbox input").attr("checked", false);
  if(state) { $(radio).attr("checked", true); }
  else { $(radio).attr("checked", false); }
}

function InitLanguages() {
  $(".choose_language").html($("#languages li.active a").html() + " <i class='fa fa-angle-down'></i>");
  $("#languages li.active").hide();
  $("#choose_language .choose_language").bind('click', function () {
    if ($("#languages").css("display") == "block") {
      $("#languages").slideUp();
      $("#choose_language").removeClass("active");
    }
    else {
      $("#languages").slideDown();
      $("#choose_language").addClass("active");
    }
  });
}

function insertParam(e, t) {
  e = escape(e);
  t = escape(t);
  var n = document.location.search.substr(1).split("&");
  if (n == "") {
      document.location.search = "?" + e + "=" + t
  } else {
      var r = n.length;
      var i;
      while (r--) {
          i = n[r].split("=");
          if (i[0] == e) {
              i[1] = t;
              n[r] = i.join("=");
              break
          }
      }
      if (r < 0) {
          n[n.length] = [e, t].join("=")
      }
      document.location.search = n.join("&")
  }
}

function updateURLParameter(url, param, paramVal){
  var newAdditionalURL = "";
  var tempArray = url.split("?");
  var baseURL = tempArray[0];
  var additionalURL = tempArray[1];
  var temp = "";
  if (additionalURL) {
      tempArray = additionalURL.split("&");
      for (i=0; i<tempArray.length; i++){
          if(tempArray[i].split('=')[0] != param){
              newAdditionalURL += temp + tempArray[i];
              temp = "&";
          }
      }
  }

  var rows_txt = temp + "" + param + "=" + paramVal;
  return baseURL + "?" + newAdditionalURL + rows_txt;
}

function OpenModalWindow(data) {
  $("#modal_window").html(data);
  $("#modal_window").append('<a href="javascript:;" class="close_btn"></a>');
  CalculateModalWindowSize();
  $("#modal_window_backgr").show();
  $("#modal_window").show();
  $("#modal_window .close_btn").click(function() {
    $("#modal_window_backgr").hide();
    $("#modal_window").hide().html("");
  });
}

function CalculateModalWindowSize() {
  var html_width = $(window).width();
  var html_height = $(window).height();
  var modal_window_width = $("#modal_window").width();
  var modal_window_height = $("#modal_window").height();
  //alert(modal_window_width);alert(modal_window_height);
  var modal_window_left = parseInt(html_width-modal_window_width-10)/2.1;
  var modal_window_top = parseInt(html_height-modal_window_height-10)/2.1;
  //alert(modal_window_top);alert(modal_window_left);
  $("#modal_window").css({top: modal_window_top+"px",left: modal_window_left+"px"})
}

function ShowAjaxLoader() {
  $("#ajax_loader_backgr, #ajax_loader").show();
  setTimeout(function () { $("#ajax_loader_backgr, #ajax_loader").hide(); }, 5000);
}

function HideAjaxLoader() {
  setTimeout(function () { $("#ajax_loader_backgr, #ajax_loader").hide(); }, 250);
}

function CheckIfUserEmailIsValid(e, t) {
  $.ajax({
      url: "/"+sitefolder+"/ajax/check-if-user-email-is-valid.php",
      type: "POST",
      data: {
          customer_email: e,
          current_lang: t
      }
  }).done(function(e) {
      if (e == "") {
          $("#customer_email_is_valid").html("");
          $(".email").removeClass("form-error");
          $(".email span").html("");
          $("#customer_email_status").val("ok")
      } else {
          $("#customer_email_is_valid").html(e);
          $(".email").addClass("form-error");
          $("#customer_email_status").val("error")
      }
  }).fail(function(e) {
      console.log(e)
  })
}

function CheckIfUserEmailIsValidForUpdate(e, t) {
  var n = $("#customer_id").val();
  $.ajax({
      url: "/"+sitefolder+"/ajax/check-if-user-email-is-valid.php",
      type: "POST",
      data: {
          customer_id: n,
          customer_email: e,
          current_lang: t
      }
  }).done(function(e) {
      if (e == "") {
          $("#customer_email_is_valid").html("");
          $("#customer_email_status").val("ok")
      } else {
          $("#customer_email_is_valid").html(e);
          $("#customer_email_status").val("error")
      }
  }).fail(function(e) {
      console.log(e)
  })
}

function ValidateUserPassword(e, t) {
  if ($("#customer_email_status").val() == "error") return;
  $.ajax({
      url: "/"+sitefolder+"/ajax/check-if-user-password-is-valid.php",
      type: "POST",
      data: {
          customer_password: e,
          current_lang: t
      }
  }).done(function(e) {
      if (e == "") {
          $("#customer_password_is_valid").html("")
      } else {
          $("#customer_password_is_valid").html(e);
          //$("#customer_password").focus()
      }
  }).fail(function(e) {
      console.log(e)
  })
}

function GetOptions() {
  var e = [];
  var t = decodeURIComponent($(".options_section input:not(.input_color)").serialize());
  if (t != "") {
      e.push(t)
      //console.log(t);
  }
  return e.join("&")
}

function GetAttributes() {
  var e = [];
  var t = decodeURIComponent($(".attributes_section input:not(.input_color)").serialize());
  if (t != "") {
      e.push(t)
  }
  return e.join("&")
}

function GetColorOptions() {
  var e = [];
  var t = decodeURIComponent($(".color_box input").serialize());
  if (t != "") {
      e.push(t)
      //console.log(f);
  }
  return e.join("&")
}

$(document).on('click', '#layer_cart .cross, #layer_cart .continue, .layer_cart_overlay', function (e) {
  e.preventDefault();
  $('.layer_cart_overlay').hide();
  $('#layer_cart').fadeOut('fast');
});

function ShowOneMoreCertificateInput(max_certificates) {
  var current_input_id = $("#more_certificates_id").val();
  if ($("input.user_certificate").length == max_certificates) {
    alert("You can't upload more then " + max_certificates + " certificates");
    return;
  }
  $("#more_certificates_container").append('<p  id="user_certificate_' + current_input_id + '"><a class="remove_c" onclick="RemoveCertificateRow(' + current_input_id + ')"><i class="fa fa-lg fa-trash" aria-hidden="true" title="' + $("#alt_delete").val() + '"></i></a><input type="file" name="user_certificates[]" class="user_certificate" style="width: auto;" /></p>');
  $("#user_certificate_" + current_input_id).show();
  var next_input_id = (parseInt(current_input_id) + 1);
  $("#more_certificates_id").val(next_input_id);
}

function RemoveCertificateRow(current_input_id) {
  $("#user_certificate_" + current_input_id).remove();
}
  
function DisplayCountryAddressForm(country_id) {
  if($(".customer_address_country_id").length) {
    $(".customer_address_country_id").val(country_id);
  }
  else {
    $(".invoice_country_id").val(country_id);
  }
  if (country_id == 33) {
      //alert("bg");return;
      $("#not_bg_form").hide();
      $("#bg_form").show()
  } else {
      $("#bg_form").hide();
      $("#not_bg_form").show()
  }
}

function CheckboxAttribute(checkbox) {
  //state = checkbox.checked;
  //alert(state);
  $(".required_attribute_checkbox").removeClass("checkbox_checked");
  $(".input_color").attr("checked", false);
  if ($(checkbox).parent().hasClass("checkbox_checked")) {
    $(checkbox).parent().removeClass("checkbox_checked");
    $(checkbox).attr("checked", false);
  } else {
    $(checkbox).parent().addClass("checkbox_checked");
    $(checkbox).attr("checked", true);
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

function AddProductRating(product_id) {
  ShowAjaxLoader();
  var rating_stars_value = $("#rating_stars_value").val();
  $.ajax({
    url: "/"+sitefolder+"/ajax/add-product-rating.php",
    type: "POST",
    data: {
        product_id: product_id,
        rating_stars_value:rating_stars_value
    }
  }).done(function(rating) {
    
    $("#rating_stars").html(rating);
    
    HideAjaxLoader()
  }).fail(function(e) {
    console.log(e)
  })
}