<?php 

function geolocation_map_all() {
	$args = array('numberposts' => 99999, 'meta_key' => 'geo_enabled', 'meta_value' => true);
	geolocation_map_for_posts(get_posts($args));
}
function geolocation_map_for_posts($posts) {
	$locations = array();
	foreach ($posts as $post) {
		$loc = array();
		$loc['latitude'] = clean_coordinate(get_post_meta($post->ID, 'geo_latitude', true));
		$loc['longitude'] = clean_coordinate(get_post_meta($post->ID, 'geo_longitude', true));
		$loc['title'] = get_post_meta($post->ID, 'geo_address', true);
		array_push($locations, $loc);
	}
	echo '<script>WPGeolocation.drawMap({
		width: "400px",
		height: "400px",
		location: ' . json_encode($locations) . '
	});</script>';
}


?>