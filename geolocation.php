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

function admin_init() {
	add_action('admin_head-post-new.php', 'admin_head');
	add_action('admin_head-post.php', 'admin_head');
	add_action('admin_menu', 'geolocation_add_custom_box');
	add_action('save_post', 'geolocation_save_postdata');
}


function add_geo_div() {
	$width = esc_attr(get_option('geolocation_map_width'));
	$height = esc_attr(get_option('geolocation_map_height'));
	echo '<div id="map" class="geolocation-map" style="width:'.$width.'px;height:'.$height.'px;"></div>';
}

function add_geo_support() {
	global $geolocation_options, $posts;
	
	// To do: add support for multiple Map API providers
	switch(PROVIDER) {
		case 'google':
			echo add_google_maps($posts);
			break;
		case 'yahoo':
			echo add_yahoo_maps($posts);
			break;
		case 'bing':
			echo add_bing_maps($posts);
			break;
	}
	echo '<link type="text/css" rel="stylesheet" href="'.esc_url(plugins_url('style.css', __FILE__)).'" />';
}

function add_google_maps($posts) {
	default_settings();
	$zoom = (int) get_option('geolocation_default_zoom');
	global $post_count;
	$post_count = count($posts);
	
	echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(function(){
			var center = new google.maps.LatLng(0.0, 0.0);
			var myOptions = {
		      zoom: '.$zoom.',
		      center: center,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    };
		    var map = new google.maps.Map(document.getElementById("map"), myOptions);
		    var image = "'.esc_js(esc_url(plugins_url('img/wp_pin.png', __FILE__ ))).'";
		    var shadow = new google.maps.MarkerImage("'.plugins_url('img/wp_pin_shadow.png', __FILE__ ).'",
		    	new google.maps.Size(39, 23),
				new google.maps.Point(0, 0),
				new google.maps.Point(12, 25));
		    var marker = new google.maps.Marker({
					position: center, 
					map: map, 
					title:"Post Location"';
				if(get_option('geolocation_wp_pin')) {
					echo ',
					icon: image,
					shadow: shadow';
				}
				echo '});
			
			var allowDisappear = true;
			var cancelDisappear = false;
		    
			$j(".geolocation-link").mouseover(function(){
				$j("#map").stop(true, true);
				var lat = $j(this).attr("name").split(",")[0];
				var lng = $j(this).attr("name").split(",")[1];
				var latlng = new google.maps.LatLng(lat, lng);
				placeMarker(latlng);
				
				var offset = $j(this).offset();
				$j("#map").fadeTo(250, 1);
				$j("#map").css("z-index", "99");
				$j("#map").css("visibility", "visible");
				$j("#map").css("top", offset.top + 20);
				$j("#map").css("left", offset.left);
				
				allowDisappear = false;
				$j("#map").css("visibility", "visible");
			});
			
			$j(".geolocation-link").mouseover(function(){
			});
			
			$j(".geolocation-link").mouseout(function(){
				allowDisappear = true;
				cancelDisappear = false;
				setTimeout(function() {
					if((allowDisappear) && (!cancelDisappear))
					{
						$j("#map").fadeTo(500, 0, function() {
							$j("#map").css("z-index", "-1");
							allowDisappear = true;
							cancelDisappear = false;
						});
					}
			    },800);
			});
			
			$j("#map").mouseover(function(){
				allowDisappear = false;
				cancelDisappear = true;
				$j("#map").css("visibility", "visible");
			});
			
			$j("#map").mouseout(function(){
				allowDisappear = true;
				cancelDisappear = false;
				$j(".geolocation-link").mouseout();
			});
			
			function placeMarker(location) {
				map.setZoom('.$zoom.');
				marker.setPosition(location);
				map.setCenter(location);
			}
			
			google.maps.event.addListener(map, "click", function() {
				window.location = "http://maps.google.com/maps?q=" + map.center.lat() + ",+" + map.center.lng();
			});
		});
	</script>';
}

function geo_has_shortcode($content) {
	$pos = strpos($content, SHORTCODE);
	if($pos === false)
		return false;
	else
		return true;
}

function display_location($content)  {
	default_settings();
	global $post, $shortcode_tags, $post_count;

	// Backup current registered shortcodes and clear them all out
	$orig_shortcode_tags = $shortcode_tags;
	$shortcode_tags = array();
	$post_id = $post->ID;
	$latitude = clean_coordinate(get_post_meta($post->ID, 'geo_latitude', true));
	$longitude = clean_coordinate(get_post_meta($post->ID, 'geo_longitude', true));
	$address = get_post_meta($post->ID, 'geo_address', true);
	$public = (bool)get_post_meta($post->ID, 'geo_public', true);
	
	$on = true;
	if(get_post_meta($post->ID, 'geo_enabled', true) != '')
		$on = (bool)get_post_meta($post->ID, 'geo_enabled', true);
	
	if(empty($address))
		$address = reverse_geocode($latitude, $longitude);
	
	if((!empty($latitude)) && (!empty($longitude) && ($public == true) && ($on == true))) {
		$html = '<a class="geolocation-link" href="#" id="geolocation'.$post->ID.'" name="'.$latitude.','.$longitude.'" onclick="return false;">Posted from '.esc_html($address).'.</a>';
		switch(esc_attr(get_option('geolocation_map_position')))
		{
			case 'before':
				$content = str_replace(SHORTCODE, '', $content);
				$content = $html.'<br/><br/>'.$content;
				break;
			case 'after':
				$content = str_replace(SHORTCODE, '', $content);
				$content = $content.'<br/><br/>'.$html;
				break;
			case 'shortcode':
				$content = str_replace(SHORTCODE, $html, $content);
				break;
		}
	}
	else {
		$content = str_replace(SHORTCODE, '', $content);
	}

	// Put the original shortcodes back
	$shortcode_tags = $orig_shortcode_tags;
	
    return $content;
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

function geolocation_settings_page() {
	default_settings();
	$zoomImage = get_option('geolocation_default_zoom');
	if(get_option('geolocation_wp_pin'))
		$zoomImage = 'wp_'.$zoomImage.'.png';
	else
		$zoomImage = $zoomImage.'.png';
	?>
	<style type="text/css">
		#zoom_level_sample { background: url('<?php echo esc_url(plugins_url('img/zoom/'.$zoomImage, __FILE__)); ?>'); width:390px; height:190px; border: solid 1px #999; }
		#preload { display: none; }
		.dimensions strong { width: 50px; float: left; }
		.dimensions input { width: 50px; margin-right: 5px; }
		.zoom label { width: 50px; margin: 0 5px 0 2px; }
		.position label { margin: 0 5px 0 2px; }
	</style>
	<script type="text/javascript">
		var file;
		var zoomlevel = <?php echo (int) esc_attr(get_option('geolocation_default_zoom')); ?>;
		var path = '<?php echo esc_js(plugins_url('img/zoom/', __FILE__)); ?>';
		function swap_zoom_sample(id) {
			zoomlevel = document.getElementById(id).value;
			pin_click();
		}
		
		function pin_click() {
			var div = document.getElementById('zoom_level_sample');
			file = path + zoomlevel + '.png';
			if(document.getElementById('geolocation_wp_pin').checked)
				file = path + 'wp_' + zoomlevel + '.png';
			div.style.background = 'url(' + file + ')';
		}
	</script>
	<div class="wrap"><h2>Geolocation Plugin Settings</h2></div>
	
	<form method="post" action="options.php">
    <?php settings_fields( 'geolocation-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
	        <tr valign="top">
	        <th scope="row">Dimensions</th>
	        <td class="dimensions">
	        	<strong>Width:</strong><input type="text" name="geolocation_map_width" value="<?php echo esc_attr(get_option('geolocation_map_width')); ?>" />px<br/>
	        	<strong>Height:</strong><input type="text" name="geolocation_map_height" value="<?php echo esc_attr(get_option('geolocation_map_height')); ?>" />px
	        </td>
        </tr>
        <tr valign="top">
        	<th scope="row">Position</th>
        	<td class="position">        	
				<input type="radio" id="geolocation_map_position_before" name="geolocation_map_position" value="before"<?php is_value('geolocation_map_position', 'before'); ?>><label for="geolocation_map_position_before">Before the post.</label><br/>
				
				<input type="radio" id="geolocation_map_position_after" name="geolocation_map_position" value="after"<?php is_value('geolocation_map_position', 'after'); ?>><label for="geolocation_map_position_after">After the post.</label><br/>
				<input type="radio" id="geolocation_map_position_shortcode" name="geolocation_map_position" value="shortcode"<?php is_value('geolocation_map_position', 'shortcode'); ?>><label for="geolocation_map_position_shortcode">Wherever I put the <strong>[geolocation]</strong> shortcode.</label>
	        </td>
        </tr>
        <tr valign="top">
	        <th scope="row">Default Zoom Level</th>
	        <td class="zoom">        	
				<input type="radio" id="geolocation_default_zoom_globe" name="geolocation_default_zoom" value="1"<?php is_value('geolocation_default_zoom', '1'); ?> onclick="javascipt:swap_zoom_sample(this.id);"><label for="geolocation_default_zoom_globe">Globe</label>
				
				<input type="radio" id="geolocation_default_zoom_country" name="geolocation_default_zoom" value="3"<?php is_value('geolocation_default_zoom', '3'); ?> onclick="javascipt:swap_zoom_sample(this.id);"><label for="geolocation_default_zoom_country">Country</label>
				<input type="radio" id="geolocation_default_zoom_state" name="geolocation_default_zoom" value="6"<?php is_value('geolocation_default_zoom', '6'); ?> onclick="javascipt:swap_zoom_sample(this.id);"><label for="geolocation_default_zoom_state">State</label>
				<input type="radio" id="geolocation_default_zoom_city" name="geolocation_default_zoom" value="9"<?php is_value('geolocation_default_zoom', '9'); ?> onclick="javascipt:swap_zoom_sample(this.id);"><label for="geolocation_default_zoom_city">City</label>
				<input type="radio" id="geolocation_default_zoom_street" name="geolocation_default_zoom" value="16"<?php is_value('geolocation_default_zoom', '16'); ?> onclick="javascipt:swap_zoom_sample(this.id);"><label for="geolocation_default_zoom_street">Street</label>
				<input type="radio" id="geolocation_default_zoom_block" name="geolocation_default_zoom" value="18"<?php is_value('geolocation_default_zoom', '18'); ?> onclick="javascipt:swap_zoom_sample(this.id);"><label for="geolocation_default_zoom_block">Block</label>
				<br/>
				<div id="zoom_level_sample"></div>
	        </td>
        </tr>
        <tr valign="top">
        	<th scope="row"></th>
        	<td class="position">        	
				<input type="checkbox" id="geolocation_wp_pin" name="geolocation_wp_pin" value="1" <?php is_checked('geolocation_wp_pin'); ?> onclick="javascript:pin_click();"><label for="geolocation_wp_pin">Show your support for WordPress by using the WordPress map pin.</label>
	        </td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="geolocation_map_width,geolocation_map_height,geolocation_default_zoom,geolocation_map_position,geolocation_wp_pin" />
</form>
	<div id="preload">
		<img src="<?php echo esc_url(plugins_url('img/zoom/1.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/3.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/6.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/9.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/16.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/18.png', __FILE__)); ?>"/>
		
		<img src="<?php echo esc_url(plugins_url('img/zoom/wp_1.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/wp_3.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/wp_6.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/wp_9.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/wp_16.png', __FILE__)); ?>"/>
		<img src="<?php echo esc_url(plugins_url('img/zoom/wp_18.png', __FILE__)); ?>"/>
	</div>
	<?php
}

?>