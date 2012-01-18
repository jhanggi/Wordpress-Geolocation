<?php 

function geolocation_map_all_posts($options = array()) {
	$args = array('numberposts' => 99999, 'meta_key' => 'geo_enabled', 'meta_value' => true, 'order' => 'ASC');
	geolocation_map_for_posts(get_posts($args), $options);
}

//if (function_exists('get_term_meta')) {
function geolocation_map_all_tags($options = array()) {
	geolocation_map_all_terms('post_tag', $options);
}

function geolocation_map_all_categories($options = array()) {
	geolocation_map_all_terms('category', $options);
}
//}
function geolocation_map_term($term_type, $term_id, $options) {
	$args = array('numberposts' => 99999, 'meta_key' => 'geo_enabled', 'meta_value' => true, 'order' => 'ASC',
		$term_type => $term_id
	);
	geolocation_map_for_posts(get_posts($args), $options);
}

function geolocation_map_all_terms($term_type, $options = array()) {
	$terms = get_terms($term_type);
	geolocation_map_for_terms($terms, $options);
}

function geolocation_map_for_terms($terms, $options = array()) {
	$locations = array();
	foreach ($terms as $term) {
		$loc = array();
		if (get_term_meta($term->term_id, 'geo_enabled') && get_term_meta($term->term_id, 'geo_address', true)) {
			$loc['latitude'] = clean_coordinate(get_term_meta($term->term_id, 'geo_latitude', true));
			$loc['longitude'] = clean_coordinate(get_term_meta($term->term_id, 'geo_longitude', true));
			$loc['title'] = get_term_meta($term->term_id, 'geo_address', true);
			$loc['url'] = get_term_link($term);
			error_log("putting url:" . $loc['url']);
			array_push($locations, $loc);
		}
	}
	$default_options = array(
			'width' => '400px',
			'height' => '400px',
			'location' => $locations,
			'zoom' => 6
	);
	$options = array_merge($default_options, $options);
	echo '<script>WPGeolocation.drawMap('. json_encode($options) . ');</script>';
}
function geolocation_map_for_posts($posts, $options) {
	$locations = array();
	foreach ($posts as $post) {
		$loc = array();
		if (get_post_meta($post->ID, 'geo_enabled') && get_post_meta($post->ID, 'geo_address', true)) {
			$loc['latitude'] = clean_coordinate(get_post_meta($post->ID, 'geo_latitude', true));
			$loc['longitude'] = clean_coordinate(get_post_meta($post->ID, 'geo_longitude', true));
			$loc['title'] = get_post_meta($post->ID, 'geo_address', true);
			$loc['url'] = get_permalink($post->ID);
			array_push($locations, $loc);
		}
	}
	$default_options = array(
		'width' => '400px',
		'height' => '400px',
		'location' => $locations,
		'zoom' => 6
	);
	$options = array_merge($default_options, $options);
	echo '<script>WPGeolocation.drawMap('. json_encode($options) . ');</script>';
}


?>