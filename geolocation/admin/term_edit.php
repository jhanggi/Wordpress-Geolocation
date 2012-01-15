<?php 
$types = array('category', 'post_tag');
foreach ($types as $type) {
	add_action($type . '_add_form_fields', 'category_metabox_add');
	add_action($type. '_edit_form_fields', 'category_metabox_add');
	add_action('edited_' . $type, 'category_save_fields');
	add_action('created_' . $type, 'category_save_fields');
}

function category_metabox_add() {
	admin_head('tag');
	include 'inner_box.php';
}

function category_save_fields($term_id) {
	
	$latitude = clean_coordinate($_POST['geolocation-latitude']);
	$longitude = clean_coordinate($_POST['geolocation-longitude']);
	$address = reverse_geocode($latitude, $longitude);
	$public = $_POST['geolocation-public'];
	$on = $_POST['geolocation-on'];
	
	if((clean_coordinate($latitude) != '') && (clean_coordinate($longitude)) != '') {
		update_term_meta($term_id, 'geo_latitude', $latitude);
		update_term_meta($term_id, 'geo_longitude', $longitude);
			
		if(esc_html($address) != '')
		update_term_meta($term_id, 'geo_address', $address);
		
		if($on) {
			update_post_meta($term_id, 'geo_enabled', 1);
		
			if($public) {
				update_post_meta($post_id, 'geo_public', 1);
			} else {
				update_post_meta($post_id, 'geo_public', 0);
			}
		} else {
			update_post_meta($post_id, 'geo_enabled', 0);
			update_post_meta($post_id, 'geo_public', 1);
		}
	}
}
?>