<?php 
require_once('simple_widget.php');

class WP_Geolocation_Widget extends WP_Simple_Widget {
	function WP_Geolocation_Widget() {
		$widget_options = array(
			'id' => 'wp_geolocation',
			'title' => 'Geolocation Widget',
			'description' => 'Display a map of your geotagged posts'
		);
		$my_options = array(
			'title' => array(),
			'zoom' => array('type' => 'select', 
							'choices' => array('Globe' => 1, 'Country' => 3, 'State' => 6, 'City' => 9),
							'number' => true),
			'height' => array(),
			'default display' => array('type' => 'select',
										'choices' => array('All Posts' => 'all', 'Tags' => 'tags', 'Categories' => 'categories'))
		);
		parent::WP_Simple_Widget($widget_options, $my_options);
	}
	
	function render_title($instance) {
		global $tag, $cat;
		if ($tag) {
			return get_term_by('slug', $tag, 'post_tag')->name;
		} elseif ($cat) {
			$category = get_term($cat, 'category');
			return $category->name;
		} else {
			return $instance['title'];
		}
	}
	
	function render($instance) {
		$zoom = $instance['zoom'] ? $instance['zoom'] : 1;
		
		global $tag, $cat;

		$options = array('height' => $instance['height'], 'width' => '100%', 'zoom' => $zoom);
		if ($tag) {
			geolocation_map_term('tag', $tag, $options);
		} elseif ($cat) {
			geolocation_map_term('category', $cat, $options);
		} else {
			$term_enabled = function_exists('get_term_meta');
			$disp = $instance['default display'];
			if ($term_enabled && $disp == 'tags') {
				geolocation_map_all_terms('post_tag', $options);
			} elseif ($term_enabled && $disp == 'categories') {
				geolocation_map_all_terms('category', $options);
			} else {
				geolocation_map_all_posts($options);
			}
		}
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget("WP_Geolocation_Widget");' ) );

?>