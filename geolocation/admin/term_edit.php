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
	$nonce = wp_create_nonce('geolocation_term_meta');
	echo '<input type="hidden" id="geolocation_nonce" name="geolocation_nonce" value="' .
		$nonce . '" />';
	include 'inner_box.php';
}

function category_save_fields($term_id) {
	return geolocation_update_meta('term', $term_id, 'geolocation_term_meta');
}
?>