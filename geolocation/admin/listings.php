<?php 

add_action( 'init', 'geolocation_add_custom_columns');
function geolocation_add_custom_columns() {
	geolocation_enqueue_styles();
	add_filter('manage_posts_columns', 'geolocation_admin_columns_header');
	add_filter('manage_posts_custom_column', 'geolocation_admin_posts_columns', 10, 3);
	
	if (function_exists('get_term_meta')) {
		add_filter( 'manage_edit-category_columns', 'geolocation_admin_columns_header' );
		add_filter( 'manage_edit-post_tag_columns', 'geolocation_admin_columns_header');
		add_filter('manage_category_custom_column', 'geolocation_admin_categories_columns', 10, 3);
		add_filter('manage_post_tag_custom_column', 'geolocation_admin_categories_columns', 10, 3);
		function geolocation_admin_categories_columns($out, $column_name, $term_id) {
			if ($column_name == 'geolocation') {
				if(get_term_meta($term_id, 'geo_enabled')) {
					echo "<span class=\"geolocation-icon\"></span>";
				}
			}
			
		}
	}
	
	function geolocation_admin_columns_header($columns) {
		$new = array('geolocation' => "");
		return array_merge($new, $columns);
	}
	function geolocation_admin_posts_columns($column_name) {
		global $post;
		if ($column_name == 'geolocation') {
			$geo = get_post_meta($post->ID, 'geo_enabled', true);
			if($geo) {
				echo "<span class=\"geolocation-icon\"></span>";
			}
		}
	}
}

?>