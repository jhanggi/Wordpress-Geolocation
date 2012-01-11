<?php 
function add_geo_div() {
	$width = esc_attr(get_option('geolocation_map_width'));
	$height = esc_attr(get_option('geolocation_map_height'));
	echo '<div id="map" class="geolocation-map" style="width:'.$width.'px;height:'.$height.'px;"></div>';
}

function add_geo_support() {
	global $geolocation_options, $posts;
	
	// To do: add support for multiple Map API providers
	switch(PROVIDER) {
		case 'google':
			echo add_google_maps($posts);
			break;
		case 'yahoo':
			echo add_yahoo_maps($posts);
			break;
		case 'bing':
			echo add_bing_maps($posts);
			break;
	}
	wp_enqueue_style( 'geolocation_style', esc_url(plugins_url(PLUGIN_LOCATION.'/style.css')));
}


function add_google_maps($posts) {
	default_settings();
	$zoom = (int) get_option('geolocation_default_zoom');
	global $post_count;
	$post_count = count($posts);
	wp_enqueue_script('google_maps', "http://maps.google.com/maps/api/js?sensor=false");

	echo '<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(function(){
			var center = new google.maps.LatLng(0.0, 0.0);
			var myOptions = {
		      zoom: '.$zoom.',
		      center: center,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    };
		    var map = new google.maps.Map(document.getElementById("map"), myOptions);
		    var image = "'.esc_js(esc_url(plugins_url(PLUGIN_LOCATION . '/img/wp_pin.png'))).'";
		    var shadow = new google.maps.MarkerImage("'.plugins_url(PLUGIN_LOCATION .'/img/wp_pin_shadow.png').'",
		    	new google.maps.Size(39, 23),
				new google.maps.Point(0, 0),
				new google.maps.Point(12, 25));
		    var marker = new google.maps.Marker({
					position: center, 
					map: map, 
					title:"Post Location"';
	if(get_option('geolocation_wp_pin')) {
		echo ',
					icon: image,
					shadow: shadow';
	}
	echo '});
			
			var allowDisappear = true;
			var cancelDisappear = false;
		    
			$j(".geolocation-link").mouseover(function(){
				$j("#map").stop(true, true);
				var lat = $j(this).attr("name").split(",")[0];
				var lng = $j(this).attr("name").split(",")[1];
				var latlng = new google.maps.LatLng(lat, lng);
				placeMarker(latlng);
				
				var offset = $j(this).offset();
				$j("#map").fadeTo(250, 1);
				$j("#map").css("z-index", "99");
				$j("#map").css("visibility", "visible");
				$j("#map").css("top", offset.top + 20);
				$j("#map").css("left", offset.left);
				
				allowDisappear = false;
				$j("#map").css("visibility", "visible");
			});
			
			$j(".geolocation-link").mouseover(function(){
			});
			
			$j(".geolocation-link").mouseout(function(){
				allowDisappear = true;
				cancelDisappear = false;
				setTimeout(function() {
					if((allowDisappear) && (!cancelDisappear))
					{
						$j("#map").fadeTo(500, 0, function() {
							$j("#map").css("z-index", "-1");
							allowDisappear = true;
							cancelDisappear = false;
						});
					}
			    },800);
			});
			
			$j("#map").mouseover(function(){
				allowDisappear = false;
				cancelDisappear = true;
				$j("#map").css("visibility", "visible");
			});
			
			$j("#map").mouseout(function(){
				allowDisappear = true;
				cancelDisappear = false;
				$j(".geolocation-link").mouseout();
			});
			
			function placeMarker(location) {
				map.setZoom('.$zoom.');
				marker.setPosition(location);
				map.setCenter(location);
			}
			
			google.maps.event.addListener(map, "click", function() {
				window.location = "http://maps.google.com/maps?q=" + map.center.lat() + ",+" + map.center.lng();
			});
		});
	</script>';
}

function geo_has_shortcode($content) {
	$pos = strpos($content, SHORTCODE);
	if($pos === false)
	return false;
	else
	return true;
}


function display_location($content)  {
	default_settings();
	global $post, $shortcode_tags, $post_count;

	// Backup current registered shortcodes and clear them all out
	$orig_shortcode_tags = $shortcode_tags;
	$shortcode_tags = array();
	$post_id = $post->ID;
	$latitude = clean_coordinate(get_post_meta($post->ID, 'geo_latitude', true));
	$longitude = clean_coordinate(get_post_meta($post->ID, 'geo_longitude', true));
	$address = get_post_meta($post->ID, 'geo_address', true);
	$public = (bool)get_post_meta($post->ID, 'geo_public', true);

	$on = true;
	if(get_post_meta($post->ID, 'geo_enabled', true) != '')
	$on = (bool)get_post_meta($post->ID, 'geo_enabled', true);

	if(empty($address))
	$address = reverse_geocode($latitude, $longitude);

	if((!empty($latitude)) && (!empty($longitude) && ($public == true) && ($on == true))) {
		$html = '<a class="geolocation-link" href="#" id="geolocation'.$post->ID.'" name="'.$latitude.','.$longitude.'" onclick="return false;">Posted from '.esc_html($address).'.</a>';
		switch(esc_attr(get_option('geolocation_map_position')))
		{
			case 'before':
				$content = str_replace(SHORTCODE, '', $content);
				$content = $html.'<br/><br/>'.$content;
				break;
			case 'after':
				$content = str_replace(SHORTCODE, '', $content);
				$content = $content.'<br/><br/>'.$html;
				break;
			case 'shortcode':
				$content = str_replace(SHORTCODE, $html, $content);
				break;
		}
	}
	else {
		$content = str_replace(SHORTCODE, '', $content);
	}

	// Put the original shortcodes back
	$shortcode_tags = $orig_shortcode_tags;

	return $content;
}

?>