<?php 

add_action('admin_head-post-new.php', 'admin_head');
add_action('admin_head-post.php', 'admin_head');
add_action('admin_menu', 'geolocation_add_custom_box');
add_action('save_post', 'geolocation_save_postdata');


function geolocation_inner_custom_box() {
	echo '<input type="hidden" id="geolocation_nonce" name="geolocation_nonce" value="' . 
	wp_create_nonce(plugin_basename(__FILE__) ) . '" />';
	include 'admin/inner_box.php';
}

/* Prints the edit form for pre-WordPress 2.5 post/page */
function geolocation_old_custom_box() {
	echo '<div class="dbx-b-ox-wrapper">' . "\n";
	echo '<fieldset id="geolocation_fieldsetid" class="dbx-box">' . "\n";
	echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' .
	__( 'Geolocation', 'geolocation_textdomain' ) . "</h3></div>";
	 
	echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

	geolocation_inner_custom_box();

	echo "</div></div></fieldset></div>\n";
}

function geolocation_save_postdata($post_id) {
	// Check authorization, permissions, autosave, etc
	if (!wp_verify_nonce($_POST['geolocation_nonce'], plugin_basename(__FILE__)))
	return $post_id;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
	return $post_id;

	if('page' == $_POST['post_type'] ) {
		if(!current_user_can('edit_page', $post_id))
		return $post_id;
	} else {
		if(!current_user_can('edit_post', $post_id))
		return $post_id;
	}

	$latitude = clean_coordinate($_POST['geolocation-latitude']);
	$longitude = clean_coordinate($_POST['geolocation-longitude']);
	$address = reverse_geocode($latitude, $longitude);
	$public = $_POST['geolocation-public'];
	$on = $_POST['geolocation-on'];

	if((clean_coordinate($latitude) != '') && (clean_coordinate($longitude)) != '') {
		update_post_meta($post_id, 'geo_latitude', $latitude);
		update_post_meta($post_id, 'geo_longitude', $longitude);
		 
		if(esc_html($address) != '')
		update_post_meta($post_id, 'geo_address', $address);

		if($on) {
			update_post_meta($post_id, 'geo_enabled', 1);

			if($public)
			update_post_meta($post_id, 'geo_public', 1);
			else
			update_post_meta($post_id, 'geo_public', 0);
		}
		else {
			update_post_meta($post_id, 'geo_enabled', 0);
			update_post_meta($post_id, 'geo_public', 1);
		}
	}

	return $post_id;
}

function admin_head() {
	global $post;
	$post_id = $post->ID;
	$post_type = $post->post_type;
	$zoom = (int) get_option('geolocation_default_zoom');
	geolocation_enqueue_scripts(true);
	wp_enqueue_script('google_jsapi', "http://www.google.com/jsapi");
	?>	
		<script type="text/javascript">
		jQuery(function() {
		WPGeolocation.loadAdmin({
			latitude: '<?php echo esc_js(get_post_meta($post_id, 'geo_latitude', true)); ?>',
			longitude: '<?php echo esc_js(get_post_meta($post_id, 'geo_longitude', true)); ?>',
			public: '<?php echo get_post_meta($post_id, 'geo_public', true); ?>',
			enabled: '<?php echo get_post_meta($post_id, 'geo_enabled', true); ?>'
		});
		});
		</script>
	<?php
}

?>