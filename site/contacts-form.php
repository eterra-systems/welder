<?php
  $sitekey = "6Le3IEYUAAAAAPssLvABf4DmEfxX5RLwb04bIRHw";
?>
  <div class="section sm pb-20">
    <div class="container">
      
      <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
          <div class="section-title">

            <h2><?=$content_name;?></h2>
            <p><?=$content_summary;?></p>

          </div>
        </div>
      </div>

      <div class="row">
        <div id="contact-content" class="col-sm-7 col-md-6 col-md-offset-1 mb-30">
          
          <p class="alert alert-success hidden"><?= $languages['text_inquiry_was_sended_successfully']; ?><span id="appointment_date"></span></p>
          <form id="emailform" class="contact-form-wrapper" action="<?=SITEFOLDERSL;?>/inquiry.php" method="post" data-toggle="validator">
            <input type="hidden" name="current_lang" value="<?=$current_lang;?>" >
            
            <div class="row">
              
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="fullname"><?=$languages['header_fullname'];?> <span class="font10 text-danger">*</span></label>
                  <input name="fullname" id="fullname" type="text" required="required" class="form-control required_field">
                  <div class="alert alert-danger error hidden"><?= $languages['error_required_field']; ?></div>
                </div>
              </div>

              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <label for="phone"><?=$languages['header_phone'];?> <span class="font10 text-danger">*</span></label>
                <input name="phone" id="phone" type="text" required="required" class="form-control required_field">
                <div class="alert alert-danger error hidden"><?= $languages['error_required_field']; ?></div>
              </div>

              <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="email"><?=$languages['header_email'];?> <span class="font10 text-danger">*</span></label>
                  <input name="email" id="email" type="email" required="required" class="form-control required_field email">
                  <div class="alert alert-danger error invalid_email hidden"><?= $languages['error_email_is_not_valid']; ?></div>
                </div>
              </div>

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="subject"><?=$languages['header_subject'];?></label>
                  <input type="text" name="subject" id="subject" class="form-control">
                </div>
              </div>

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <label for="textarea"><?=$languages['header_inquiry'];?> <span class="font10 text-danger">*</span></label>
                  <textarea name="message" id="textarea" required="required" rows="4" class="form-control required_field form-textarea"></textarea>
                  <div class="alert alert-danger error hidden"><?= $languages['error_required_field']; ?></div>
                </div>
              </div>

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="g-recaptcha" data-sitekey="<?=$sitekey;?>"></div>
                <div class="alert alert-danger error hidden recaptcha_error"><?=$languages['error_create_customer_recaptcha'];?></div>
              </div>
              <div class="clearfix"></div>
              
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <button type="submit" name="submit_inquiery" id="submit" class="btn btn-primary mt-5"><?=$languages['btn_submit_inquiry'];?></button>
              </div>
              
            </div>
            
          </form>
        </div>
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
        <script type="text/javascript">
          var onloadCallback = function() {
            grecaptcha.render('g-recaptcha', {'sitekey' : '<?=$sitekey;?>'});
          };
        </script>
        
        <div class="col-sm-5 col-md-4">
<?php
  $contacts_array = get_contacts();
  if(!empty($contacts_array)) {
    foreach($contacts_array as $contact_row) {

      $contact_id = $contact_row['contact_id'];
      $contact_email = $contact_row['contact_email'];
      $contact_city = $contact_row['contact_city'];
      $contact_address = stripslashes($contact_row['contact_address']);
      $contact_postcode = $contact_row['contact_postcode'];
      $contact_info = stripslashes($contact_row['contact_info']);
      $contact_address .= (!empty($contact_info)) ? " ($contact_info)" : "";
      $contact_address .= ", $contact_postcode $contact_city";
      $contact_is_default = $contact_row['contact_is_default'];
?>      
          <ul class="address-list">
            <li>
              <h5>Address</h5>
              <address> <?=$contact_address;?> </address>
            </li>
            <li>
              <h5>Email</h5><a href="mailto:<?=$contact_email;?>"><?=$contact_email;?></a>
            </li>
            <li>
              <h5>Office phone</h5> <a href="tel:<?=str_replace(array("/","-"," "), array("","",""), $_SESSION['contact_home_phone']);?>"><?=$_SESSION['contact_home_phone'];?></a>
            </li>
            <li>
              <h5>Mobile phone</h5> <a href="tel:<?=str_replace(array("/","-"," "), array("","",""), $_SESSION['contact_mobile_phones'][0]);?>"><?=$_SESSION['contact_mobile_phones'][0];?></a>
            </li>
          </ul>
<?php
    }
  }
?>
        </div>

      </div>
        
    </div>
  </div>
<?php
  if(isset($_SESSION['contact_map_lat']) && $_SESSION['contact_map_lng']) {
?>
  <div class="contact-map">
    <div id="map" data-lat="<?=$_SESSION['contact_map_lat'];?>" data-lon="<?=$_SESSION['contact_map_lng'];?>" style="width: 100%; height: 500px;"></div>

    <div class="infobox-wrapper shorter-infobox contact-infobox">
      <div id="infobox">
        <div class="infobox-address">
          <h6><?=$contact_address;?></h6>
        </div>

      </div>
    </div>
  </div>

  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script type="text/javascript" src="<?=SITEFOLDERSL;?>/js/infobox.js"></script>
  <script type="text/javascript">
    function initialize() {

  // Create an array of styles.
      var styles = [{"featureType": "all", "elementType": "labels", "stylers": [{"lightness": 63}, {"hue": "#ff0000"}]}, {"featureType": "administrative", "elementType": "all", "stylers": [{"hue": "#000bff"}, {"visibility": "on"}]}, {"featureType": "administrative", "elementType": "geometry", "stylers": [{"visibility": "on"}]}, {"featureType": "administrative", "elementType": "labels", "stylers": [{"color": "#4a4a4a"}, {"visibility": "on"}]}, {"featureType": "administrative", "elementType": "labels.text", "stylers": [{"weight": "0.01"}, {"color": "#727272"}, {"visibility": "on"}]}, {"featureType": "administrative.country", "elementType": "labels", "stylers": [{"color": "#ff0000"}]}, {"featureType": "administrative.country", "elementType": "labels.text", "stylers": [{"color": "#ff0000"}]}, {"featureType": "administrative.province", "elementType": "geometry.fill", "stylers": [{"visibility": "on"}]}, {"featureType": "administrative.province", "elementType": "labels.text", "stylers": [{"color": "#545454"}]}, {"featureType": "administrative.locality", "elementType": "labels.text", "stylers": [{"visibility": "on"}, {"color": "#737373"}]}, {"featureType": "administrative.neighborhood", "elementType": "labels.text", "stylers": [{"color": "#7c7c7c"}, {"weight": "0.01"}]}, {"featureType": "administrative.land_parcel", "elementType": "labels.text", "stylers": [{"color": "#404040"}]}, {"featureType": "landscape", "elementType": "all", "stylers": [{"lightness": 16}, {"hue": "#ff001a"}, {"saturation": -61}]}, {"featureType": "poi", "elementType": "labels.text", "stylers": [{"color": "#828282"}, {"weight": "0.01"}]}, {"featureType": "poi.government", "elementType": "labels.text", "stylers": [{"color": "#4c4c4c"}]}, {"featureType": "poi.park", "elementType": "all", "stylers": [{"hue": "#00ff91"}]}, {"featureType": "poi.park", "elementType": "labels.text", "stylers": [{"color": "#7b7b7b"}]}, {"featureType": "road", "elementType": "all", "stylers": [{"visibility": "on"}]}, {"featureType": "road", "elementType": "labels", "stylers": [{"visibility": "off"}]}, {"featureType": "road", "elementType": "labels.text", "stylers": [{"color": "#999999"}, {"visibility": "on"}, {"weight": "0.01"}]}, {"featureType": "road.highway", "elementType": "all", "stylers": [{"hue": "#ff0011"}, {"lightness": 53}]}, {"featureType": "road.highway", "elementType": "labels.text", "stylers": [{"color": "#626262"}]}, {"featureType": "transit", "elementType": "labels.text", "stylers": [{"color": "#676767"}, {"weight": "0.01"}]}, {"featureType": "water", "elementType": "all", "stylers": [{"hue": "#0055ff"}]}];

      var loc, map, marker, infobox;

      var styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});

      loc = new google.maps.LatLng($("#map").attr("data-lat"), $("#map").attr("data-lon"));

      map = new google.maps.Map(document.getElementById("map"), {
        zoom: 14,
        center: loc,
        scrollwheel: false,
        //draggable:true,
        navigationControl: false,
        scaleControl: false,
        mapTypeControl: false,
        streetViewControl: false,
        mapTypeControlOptions: {
          mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
        },
        mapTypeId: google.maps.MapTypeId.ROADMAP,
      });

  //Associate the styled map with the MapTypeId and set it to display.
      map.mapTypes.set('map_style', styledMap);
      map.setMapTypeId('map_style');

      marker = new google.maps.Marker({
        map: map,
        position: loc,
        //disableDefaultUI:true,

        icon: "<?=SITEFOLDERSL;?>/images/map-marker/00.png",
        //pixelOffset: new google.maps.Size(-140, -100),
        visible: true

                //animation: google.maps.Animation.DROP
      });

      infobox = new InfoBox({
        content: document.getElementById("infobox"),
        disableAutoPan: true,
        //maxWidth: 150,
        pixelOffset: new google.maps.Size(0, -50),
        zIndex: null,
        alignBottom: true,
        isHidden: false,
        //closeBoxMargin: "12px 4px 2px 2px",
        closeBoxURL: "<?=SITEFOLDERSL;?>/images/infobox-close.png",
        closeBoxClass: "infoBox-close",
        infoBoxClearance: new google.maps.Size(1, 1)
      });

      openInfoBox(marker);

      google.maps.event.addListener(marker, 'click', function () {
        openInfoBox(this);
      });

      function openInfoBox(thisMarker) {
        map.panTo(loc);
        map.panBy(0, -80);
        infobox.open(map, thisMarker);
      }

      var center;
      function calculateCenter() {
        center = map.getCenter();
      }
      google.maps.event.addDomListener(map, 'idle', function () {
        calculateCenter();
      });
      google.maps.event.addDomListener(window, 'resize', function () {
        map.setCenter(center);
      });

    }
    google.maps.event.addDomListener(window, 'load', initialize);
  </script>
<?php
  }
?>