<?php 

add_action( 'init', 'geolocation_add_custom_columns');
function geolocation_add_custom_columns() {
	if (function_exists('get_term_meta')) {
		add_filter( 'manage_edit-category_columns', 'geolocation_admin_categories_columns_header' );
		add_filter( 'manage_edit-post_tag_columns', 'geolocation_admin_categories_columns_header');
		function geolocation_admin_categories_columns_header($columns) {
			$new = array('geolocation' => "");
			
			return array_merge($new, $columns);
		}
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
}

?>