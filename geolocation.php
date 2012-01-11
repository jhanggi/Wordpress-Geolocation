<?php
/*
Plugin Name: Geolocation
Plugin URI: http://wordpress.org/extend/plugins/geolocation/
Description: Displays post geotag information on an embedded map.
Version: 0.1.1
Author: Chris Boyd
Author URI: http://geo.chrisboyd.net
License: GPL2
*/

/*  Copyright 2010 Chris Boyd (email : chris@chrisboyd.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('wp_head', 'add_geo_support');
add_action('wp_footer', 'add_geo_div');
add_action('admin_menu', 'add_settings');
add_filter('the_content', 'display_location', 5);
admin_init();
register_activation_hook(__FILE__, 'activate');
wp_enqueue_script("jquery");

define('PROVIDER', 'google');
define('SHORTCODE', '[geolocation]');

function activate() {
	register_settings();
	add_option('geolocation_map_width', '350');
	add_option('geolocation_map_height', '150');
	add_option('geolocation_default_zoom', '16');
	add_option('geolocation_map_position', 'after');
	add_option('geolocation_wp_pin', '1');
}

function geolocation_add_custom_box() {
		if(function_exists('add_meta_box')) {
			add_meta_box('geolocation_sectionid', __( 'Geolocation', 'myplugin_textdomain' ), 'geolocation_inner_custom_box', 'post', 'advanced' );
		} 
		else {
			add_action('dbx_post_advanced', 'geolocation_old_custom_box' );
		}
}

require_once(dirname(__FILE__).'/geolocation/admin.php');
require_once(dirname(__FILE__).'/geolocation/body.php');
require_once(dirname(__FILE__).'/geolocation/settings.php');
function admin_init() {
	add_action('admin_head-post-new.php', 'admin_head');
	add_action('admin_head-post.php', 'admin_head');
	add_action('admin_menu', 'geolocation_add_custom_box');
	add_action('save_post', 'geolocation_save_postdata');
}


function reverse_geocode($latitude, $longitude) {
	$url = "http://maps.google.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&sensor=false";
	$result = wp_remote_get($url);
	$json = json_decode($result['body']);
	foreach ($json->results as $result)
	{
		foreach($result->address_components as $addressPart) {
			if((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$city = $addressPart->long_name;
	    	else if((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$state = $addressPart->long_name;
	    	else if((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types)))
	    		$country = $addressPart->long_name;
		}
	}
	
	if(($city != '') && ($state != '') && ($country != ''))
		$address = $city.', '.$state.', '.$country;
	else if(($city != '') && ($state != ''))
		$address = $city.', '.$state;
	else if(($state != '') && ($country != ''))
		$address = $state.', '.$country;
	else if($country != '')
		$address = $country;
		
	return $address;
}

function clean_coordinate($coordinate) {
	$pattern = '/^(\-)?(\d{1,3})\.(\d{1,15})/';
	preg_match($pattern, $coordinate, $matches);
	return $matches[0];
}

function add_settings() {
	if ( is_admin() ){ // admin actions
		add_options_page('Geolocation Plugin Settings', 'Geolocation', 'administrator', 'geolocation.php', 'geolocation_settings_page', __FILE__);
  		add_action( 'admin_init', 'register_settings' );
	} else {
	  // non-admin enqueues, actions, and filters
	}
}

function register_settings() {
  register_setting( 'geolocation-settings-group', 'geolocation_map_width', 'intval' );
  register_setting( 'geolocation-settings-group', 'geolocation_map_height', 'intval' );
  register_setting( 'geolocation-settings-group', 'geolocation_default_zoom', 'intval' );
  register_setting( 'geolocation-settings-group', 'geolocation_map_position' );
  register_setting( 'geolocation-settings-group', 'geolocation_wp_pin');
}

function is_checked($field) {
	if (get_option($field))
 		echo ' checked="checked" ';
}

function is_value($field, $value) {
	if (get_option($field) == $value) 
 		echo ' checked="checked" ';
}

function default_settings() {
	if(get_option('geolocation_map_width') == '0')
		update_option('geolocation_map_width', '450');
		
	if(get_option('geolocation_map_height') == '0')
		update_option('geolocation_map_height', '200');
		
	if(get_option('geolocation_default_zoom') == '0')
		update_option('geolocation_default_zoom', '16');
		
	if(get_option('geolocation_map_position') == '0')
		update_option('geolocation_map_position', 'after');
}

?>