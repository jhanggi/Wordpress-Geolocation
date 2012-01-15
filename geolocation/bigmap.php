<?php 

function geolocation_map_all($options) {
	$args = array('numberposts' => 99999, 'meta_key' => 'geo_enabled', 'meta_value' => true, 'order' => 'ASC');
	geolocation_map_for_posts(get_posts($args), $options);
}
function geolocation_map_for_posts($posts, $options) {
	$locations = array();
	foreach ($posts as $post) {
		$loc = array();
		$loc['latitude'] = clean_coordinate(get_post_meta($post->ID, 'geo_latitude', true));
		$loc['longitude'] = clean_coordinate(get_post_meta($post->ID, 'geo_longitude', true));
		$loc['title'] = get_post_meta($post->ID, 'geo_address', true);
		$loc['url'] = get_permalink($post->ID);
		array_push($locations, $loc);
	}
	$default_options = array(
		'width' => '400px',
		'height' => '400px',
		'location' => $locations
	);
	$options = array_merge($default_options, $options);
	echo '<script>WPGeolocation.drawMap('. json_encode($options) . ');</script>';
}


?>