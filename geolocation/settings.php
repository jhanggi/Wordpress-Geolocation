<?php
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
