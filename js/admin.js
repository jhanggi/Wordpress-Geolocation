var $j = jQuery.noConflict();

WPGeolocation.loadAdmin = function(options) {
	var hasLocation = false;
	var center = new google.maps.LatLng(0.0, 0.0);
	var postLatitude = options.latitude; 
	var postLongitude = options.longitude;
	var public = options.public; 
	var on = options.enabled;

	if (public == '0')
		$j("#geolocation-public").attr('checked', false);
	else
		$j("#geolocation-public").attr('checked', true);

	if (on == '0')
		disableGeo();
	else
		enableGeo();

	if ((postLatitude != '') && (postLongitude != '')) {
		center = new google.maps.LatLng(postLatitude, postLongitude);
		hasLocation = true;
		$j("#geolocation-latitude").val(center.lat());
		$j("#geolocation-longitude").val(center.lng());
		reverseGeocode(center);
	}

	// var image = '<?php echo esc_js(esc_url(plugins_url('img/wp_pin.png',
	// __FILE__ ))); ?>';
	// var shadow = new google.maps.MarkerImage('<?php echo
	// esc_js(esc_url(plugins_url('img/wp_pin_shadow.png', __FILE__ ))); ?>',
	// new google.maps.Size(39, 23),
	// new google.maps.Point(0, 0),
	// new google.maps.Point(12, 25));

	var map = new google.maps.Map(document.getElementById('geolocation-map'),
			WPGeolocation.defaultOptions);
	var marker = new google.maps.Marker({
		position : center,
		map : map,
		title : 'Post Location'
	// icon: image,
	// shadow: shadow
	});

	if ((!hasLocation) && (google.loader.ClientLocation)) {
		center = new google.maps.LatLng(google.loader.ClientLocation.latitude,
				google.loader.ClientLocation.longitude);
		reverseGeocode(center);
	} else if (!hasLocation) {
		map.setZoom(1);
	}

	google.maps.event.addListener(map, 'click', function(event) {
		placeMarker(event.latLng);
	});

	var currentAddress;
	var customAddress = false;
	$j("#geolocation-address").click(function() {
		currentAddress = $j(this).val();
		if (currentAddress != '')
			$j("#geolocation-address").val('');
	});

	$j("#geolocation-load").click(function() {
		if ($j("#geolocation-address").val() != '') {
			customAddress = true;
			currentAddress = $j("#geolocation-address").val();
			geocode(currentAddress);
		}
	});

	$j("#geolocation-address").keyup(function(e) {
		if (e.keyCode == 13)
			$j("#geolocation-load").click();
	});

	$j("#geolocation-enabled").click(function() {
		enableGeo();
	});

	$j("#geolocation-disabled").click(function() {
		disableGeo();
	});

	function placeMarker(location) {
		marker.setPosition(location);
		map.setCenter(location);
		if ((location.lat() != '') && (location.lng() != '')) {
			$j("#geolocation-latitude").val(location.lat());
			$j("#geolocation-longitude").val(location.lng());
		}

		if (!customAddress)
			reverseGeocode(location);
	}

	function geocode(address) {
		var geocoder = new google.maps.Geocoder();
		if (geocoder) {
			geocoder.geocode({
				"address" : address
			}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					placeMarker(results[0].geometry.location);
					if (!hasLocation) {
						map.setZoom(16);
						hasLocation = true;
					}
				}
			});
		}
		//$j("#geodata").html(latitude + ', ' + longitude);
	}

	function reverseGeocode(location) {
		var geocoder = new google.maps.Geocoder();
		if (geocoder) {
			geocoder.geocode({
				"latLng" : location
			}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[1]) {
						var address = results[1].formatted_address;
						if (address == "")
							address = results[7].formatted_address;
						else {
							$j("#geolocation-address").val(address);
							placeMarker(location);
						}
					}
				}
			});
		}
	}

	function enableGeo() {
		$j("#geolocation-address").removeAttr('disabled');
		$j("#geolocation-load").removeAttr('disabled');
		$j("#geolocation-map").css('filter', '');
		$j("#geolocation-map").css('opacity', '');
		$j("#geolocation-map").css('-moz-opacity', '');
		$j("#geolocation-public").removeAttr('disabled');
		$j("#geolocation-map").removeAttr('readonly');
		$j("#geolocation-disabled").removeAttr('checked');
		$j("#geolocation-enabled").attr('checked', 'checked');

		if (public == '1')
			$j("#geolocation-public").attr('checked', 'checked');
	}

	function disableGeo() {
		$j("#geolocation-address").attr('disabled', 'disabled');
		$j("#geolocation-load").attr('disabled', 'disabled');
		$j("#geolocation-map").css('filter', 'alpha(opacity=50)');
		$j("#geolocation-map").css('opacity', '0.5');
		$j("#geolocation-map").css('-moz-opacity', '0.5');
		$j("#geolocation-map").attr('readonly', 'readonly');
		$j("#geolocation-public").attr('disabled', 'disabled');

		$j("#geolocation-enabled").removeAttr('checked');
		$j("#geolocation-disabled").attr('checked', 'checked');

		if (public == '1')
			$j("#geolocation-public").attr('checked', 'checked');
	}
}