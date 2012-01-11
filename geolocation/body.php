<?php 
function add_geo_div() {
	
	//echo '<div id="map" class="geolocation-map" style="width:'.$width.'px;height:'.$height.'px;"></div>';
	
}

function add_geo_support() {
	global $geolocation_options, $posts;
	
	// To do: add support for multiple Map API providers
	switch(PROVIDER) {
		case 'google':
			//echo add_google_maps($posts);
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
		$width = esc_attr(get_option('geolocation_map_width'));
		$height = esc_attr(get_option('geolocation_map_height'));
		$html = '<a class="geolocation-link" href="#" id="geolocation'.$post->ID.'" name="'.$latitude.','.$longitude.'" onclick="return false;">Posted from '.esc_html($address).'.</a>';
		$html = '<script>WPGeolocation.drawMap({
			width: "' . $width . '",
			height: "' . $height . '",
			latitude: ' . $latitude . ',
			longitude: ' . $longitude . ',
			zoom : ' . 	(int) get_option('geolocation_default_zoom');
// 		if(get_option('geolocation_wp_pin')) {
// 			$html .= ',
// 							icon: image,
// 							shadow: shadow';
// 		}
		
		$html .= '});</script>';
		//$html .= '<div id="map" class="geolocation-map" style="width:'.$width.'px;height:'.$height.'px;"></div>';
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