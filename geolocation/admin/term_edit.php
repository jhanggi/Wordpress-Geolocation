<?php 
function geolocation_add_term_admin_functions() {
	if (function_exists('update_term_meta')) {
		$types = array('category', 'post_tag');
		foreach ($types as $type) {
			add_action($type . '_add_form_fields', 'geolocation_taxonomy_metabox_add');
			add_action($type. '_edit_form_fields', 'geolocation_taxonomy_metabox_add');
			add_action('edited_' . $type, 'geolocation_taxonomy_save_fields');
			add_action('created_' . $type, 'geolocation_taxonomy_save_fields');
		}
	}
}
add_action('admin_init', 'geolocation_add_term_admin_functions');

function geolocation_taxonomy_metabox_add() {
	admin_head('tag');
	$nonce = wp_create_nonce('geolocation_term_meta');
	echo '<input type="hidden" id="geolocation_nonce" name="geolocation_nonce" value="' .
		$nonce . '" />';
	include 'inner_box.php';
}

function geolocation_taxonomy_save_fields($term_id) {
	return geolocation_update_meta('term', $term_id, 'geolocation_term_meta');
}
?>