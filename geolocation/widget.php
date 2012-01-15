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
			'height' => array()
		);
		parent::WP_Simple_Widget($widget_options, $my_options);
	}
	
	function getOptions() {
		return array(
			'title',
			'zoom',
			'height'
		);
	}
	
	function render($instance) {
		$zoom = $instance['zoom'] ? $instance['zoom'] : 1;
		geolocation_map_all(array('height' => $instance['height'], 'width' => '100%', 'zoom' => $zoom));
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget("WP_Geolocation_Widget");' ) );

?>