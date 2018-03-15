$(document).ready(function () {

  "use strict";

  $('form#emailform').submit(function (event) {

    ShowAjaxLoader();

    var hasError = false;

    $('form#emailform .required_field').each(function () {
      var parent = $(this).parent();
      if (jQuery.trim($(this).val()) == '') {
        //console.log("empty");
        if ($(this).hasClass('email')) {
          if (!parent.find('.invalid_email').hasClass('hidden')) {
            parent.find('.invalid_email').addClass('hidden');
          }
        }
        parent.find('.error').removeClass('hidden');
        hasError = true;
      } else if ($(this).hasClass('email')) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if (!emailReg.test(jQuery.trim($(this).val()))) {
          //console.log("invalid_email");
          parent.find('.error').addClass('hidden');
          parent.find('.invalid_email').removeClass('hidden');
          hasError = true;
        } else {
          parent.find('.error').addClass('hidden');
          parent.find('.invalid_email').addClass('hidden');
        }
      } else {
        parent.find('.error').addClass('hidden');
      }
    });

    if (!hasError) {
      $('form#emailform input.submit').fadeOut('normal', function () {
        $(this).parent().append('');
      });
      var formInput = $(this).serializeArray();
      $.post($(this).attr('action'), formInput, function (data) {
        //console.log(data)
        if (data == "recaptcha_error") {
          $("#s-contact-content .recaptcha_error").removeClass('hidden');
        } else {
          $('#emailform').slideUp();
          $("#s-contact-content p.alert-success").removeClass("hidden");
        }
      });
    }

    HideAjaxLoader();
    event.preventDefault();

  });
  
  // Slideshow
  $(".swiper-container", ".slider-wrapper").show();

  var mySwiper = new Swiper(".swiper-container", {
    pagination: ".pagination",
    loop: true,
    autoplay: 4500,
    grabCursor: true,
    paginationClickable: true
  })

  $(".arrow-left", ".slider-wrapper").on("click", function (e) {
    e.preventDefault()
    mySwiper.swipePrev()
  })

  $(".arrow-right", ".slider-wrapper").on("click", function (e) {
    e.preventDefault()
    mySwiper.swipeNext()
  });

  // Fixed Menu
  var nav = $(".fixed-navigation");
  $(window).scroll(function () {
    if ($(this).scrollTop() > 500) {
      nav.addClass("fixed-navigation-show");
    } else {
      nav.removeClass("fixed-navigation-show");
    }
  });

  // Accordion
  $(".accordion").accordion({autoHeight: false});

  // Toggle	
  $(".toggle > .inner").hide();
  $(".toggle .title").on("click", function () {
    $(this).toggleClass("active");
    if ($(this).hasClass("active")) {
      $(this).closest(".toggle").find(".inner").slideDown(200, "easeOutCirc");
    } else {
      $(this).closest(".toggle").find(".inner").slideUp(200, "easeOutCirc");
    }
  });

  // Tabs
  $(function () {
    $("#tabs").tabs();
  });

  // Search Button Toggle
  $(".search-button", "#primary-navigation").on("click", function () {
    $(".search-form").toggleClass("search-form-show", 0);
  });

  // Search Button Toggle
  $(".close-search", "#primary-navigation").on("click", function () {
    $(".search-form").toggleClass("search-form-show", 0);
  });

  // Mobile Search Button Toggle
  $(".li-mobile-search", "#logo-wrapper").on("click", function () {
    $(".mobile-search-form").toggleClass("mobile-search-form-hide", 0);
    $("#logo").toggleClass("logo-hide", 0);
    $(".li-mobile-nav").toggleClass("li-mobile-nav-hide", 0);
    $(".li-mobile-cart").toggleClass("li-mobile-cart-hide", 0);
    $(".mobile-nav-search .fa-search").toggleClass("li-mobile-cart-hide", 0);
  });

  // Main Navigation
  $("#navigation li").on("mouseenter mouseleave", function (e) {

    var elm = $("ul:first", this);
    var off = elm.offset();
    var l = off.left;
    var w = elm.width();
    var docH = $(".outer-wrapper").height();
    var docW = $(".outer-wrapper").width();

    var isEntirelyVisible = (l + w <= docW);

    if (!isEntirelyVisible) {
      $(this).addClass("edge");
    } else {
      $(this).removeClass("edge");
    }
  });

});

$(function () {

  "use strict";

  // Mobile Menu Expand
  $(".mobile-menu").on("click", ".menu-expand", function (e) {
    e.stopPropagation();
    $(this).parent().children(".sub-menu").toggle();
  });

  // Add Expand To Mobile Menu
  $(".mobile-menu li:has(ul.sub-menu)").append('<div class="menu-expand"><i class="fa fa-plus"></i></div>');

  // Overlay Site Wrapper When Mobile Menu Expanded
  $(".li-mobile-nav").on("click", function () {
    $("body").addClass("menu-expanded");
    $(".site-wrapper-overlay").fadeIn().css("display", "block");
  });

  // Overlay Site Wrapper When Cart Expanded
  $(".li-mobile-cart,.cart-tab").on("click", function () {
    $("body").addClass("cart-expanded");
    $(".site-wrapper-overlay").fadeIn().css("display", "block");
  });

  // Remove Overlay Site Wrapper When Clicked On
  $(".site-wrapper-overlay").on("click", function () {
    $(".site-wrapper-overlay").fadeOut().css("display", "none");
    $("body").removeClass("menu-expanded");
    $("body").removeClass("cart-expanded");
  });

});