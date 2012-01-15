<?php 
class WP_Geolocation_Widget extends WP_Widget {
	function __construct() {
		parent::WP_Widget( 'wp_geolocation', 'Geolocation Map', array( 'description' => 'Display a map of geotagged posts' ) );
	}
	function WP_Geolocation_Widget(){
		// widget actual processes
	}

	function form($instance) {
		error_log(print_r($instance, true));
		include "widget/form.php";
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['height'] = strip_tags($new_instance['height']);
		return $instance;
	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title; 
		geolocation_map_all(array('height' => $instance['height'], 'width' => '100%'));		 
		echo $after_widget;
	}

}

add_action( 'widgets_init', create_function( '', 'register_widget("WP_Geolocation_Widget");' ) );

?>