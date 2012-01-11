<?php 
function geolocation_inner_custom_box() {
	echo '<input type="hidden" id="geolocation_nonce" name="geolocation_nonce" value="' . 
	wp_create_nonce(plugin_basename(__FILE__) ) . '" />';
	echo '
		<label class="screen-reader-text" for="geolocation-address">Geolocation</label>
		<div class="taghint">Enter your address</div>
		<input type="text" id="geolocation-address" name="geolocation-address" class="newtag form-input-tip" size="25" autocomplete="off" value="" />
		<input id="geolocation-load" type="button" class="button geolocationadd" value="Load" tabindex="3" />
		<input type="hidden" id="geolocation-latitude" name="geolocation-latitude" />
		<input type="hidden" id="geolocation-longitude" name="geolocation-longitude" />
		<div id="geolocation-map" style="border:solid 1px #c6c6c6;width:265px;height:200px;margin-top:5px;"></div>
		<div style="margin:5px 0 0 0;">
			<input id="geolocation-public" name="geolocation-public" type="checkbox" value="1" />
			<label for="geolocation-public">Public</label>
			<div style="float:right">
				<input id="geolocation-enabled" name="geolocation-on" type="radio" value="1" />
				<label for="geolocation-enabled">On</label>
				<input id="geolocation-disabled" name="geolocation-on" type="radio" value="0" />
				<label for="geolocation-disabled">Off</label>
			</div>
		</div>
	';
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
	?>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript">
		 	var $j = jQuery.noConflict();
			$j(function() {
				$j(document).ready(function() {
				    var hasLocation = false;
					var center = new google.maps.LatLng(0.0,0.0);
					var postLatitude =  '<?php echo esc_js(get_post_meta($post_id, 'geo_latitude', true)); ?>';
					var postLongitude =  '<?php echo esc_js(get_post_meta($post_id, 'geo_longitude', true)); ?>';
					var public = '<?php echo get_post_meta($post_id, 'geo_public', true); ?>';
					var on = '<?php echo get_post_meta($post_id, 'geo_enabled', true); ?>';
					
					if(public == '0')
						$j("#geolocation-public").attr('checked', false);
					else
						$j("#geolocation-public").attr('checked', true);
					
					if(on == '0')
						disableGeo();
					else
						enableGeo();
					
					if((postLatitude != '') && (postLongitude != '')) {
						center = new google.maps.LatLng(postLatitude, postLongitude);
						hasLocation = true;
						$j("#geolocation-latitude").val(center.lat());
						$j("#geolocation-longitude").val(center.lng());
						reverseGeocode(center);
					}
						
				 	var myOptions = {
				      'zoom': <?php echo $zoom; ?>,
				      'center': center,
				      'mapTypeId': google.maps.MapTypeId.ROADMAP
				    };
				    var image = '<?php echo esc_js(esc_url(plugins_url('img/wp_pin.png', __FILE__ ))); ?>';
				    var shadow = new google.maps.MarkerImage('<?php echo esc_js(esc_url(plugins_url('img/wp_pin_shadow.png', __FILE__ ))); ?>',
						new google.maps.Size(39, 23),
						new google.maps.Point(0, 0),
						new google.maps.Point(12, 25));
						
				    var map = new google.maps.Map(document.getElementById('geolocation-map'), myOptions);	
					var marker = new google.maps.Marker({
						position: center, 
						map: map, 
						title:'Post Location'<?php if(get_option('geolocation_wp_pin')) { ?>,
						icon: image,
						shadow: shadow
					<?php } ?>
					});
					
					if((!hasLocation) && (google.loader.ClientLocation)) {
				      center = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
				      reverseGeocode(center);
				    }
				    else if(!hasLocation) {
				    	map.setZoom(1);
				    }
					
					google.maps.event.addListener(map, 'click', function(event) {
						placeMarker(event.latLng);
					});
					
					var currentAddress;
					var customAddress = false;
					$j("#geolocation-address").click(function(){
						currentAddress = $j(this).val();
						if(currentAddress != '')
							$j("#geolocation-address").val('');
					});
					
					$j("#geolocation-load").click(function(){
						if($j("#geolocation-address").val() != '') {
							customAddress = true;
							currentAddress = $j("#geolocation-address").val();
							geocode(currentAddress);
						}
					});
					
					$j("#geolocation-address").keyup(function(e) {
						if(e.keyCode == 13)
							$j("#geolocation-load").click();
					});
					
					$j("#geolocation-enabled").click(function(){
						enableGeo();
					});
					
					$j("#geolocation-disabled").click(function(){
						disableGeo();
					});
									
					function placeMarker(location) {
						marker.setPosition(location);
						map.setCenter(location);
						if((location.lat() != '') && (location.lng() != '')) {
							$j("#geolocation-latitude").val(location.lat());
							$j("#geolocation-longitude").val(location.lng());
						}
						
						if(!customAddress)
							reverseGeocode(location);
					}
					
					function geocode(address) {
						var geocoder = new google.maps.Geocoder();
					    if (geocoder) {
							geocoder.geocode({"address": address}, function(results, status) {
								if (status == google.maps.GeocoderStatus.OK) {
									placeMarker(results[0].geometry.location);
									if(!hasLocation) {
								    	map.setZoom(16);
								    	hasLocation = true;
									}
								}
							});
						}
						$j("#geodata").html(latitude + ', ' + longitude);
					}
					
					function reverseGeocode(location) {
						var geocoder = new google.maps.Geocoder();
					    if (geocoder) {
							geocoder.geocode({"latLng": location}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
							  if(results[1]) {
							  	var address = results[1].formatted_address;
							  	if(address == "")
							  		address = results[7].formatted_address;
							  	else {
									$j("#geolocation-address").val(address);
									placeMarker(location);
							  	}
							  }
							}
							});
						}
					}
					
					function enableGeo() {
						$j("#geolocation-address").removeAttr('disabled');
						$j("#geolocation-load").removeAttr('disabled');
						$j("#geolocation-map").css('filter', '');
						$j("#geolocation-map").css('opacity', '');
						$j("#geolocation-map").css('-moz-opacity', '');
						$j("#geolocation-public").removeAttr('disabled');
						$j("#geolocation-map").removeAttr('readonly');
						$j("#geolocation-disabled").removeAttr('checked');
						$j("#geolocation-enabled").attr('checked', 'checked');
						
						if(public == '1')
							$j("#geolocation-public").attr('checked', 'checked');
					}
					
					function disableGeo() {
						$j("#geolocation-address").attr('disabled', 'disabled');
						$j("#geolocation-load").attr('disabled', 'disabled');
						$j("#geolocation-map").css('filter', 'alpha(opacity=50)');
						$j("#geolocation-map").css('opacity', '0.5');
						$j("#geolocation-map").css('-moz-opacity', '0.5');
						$j("#geolocation-map").attr('readonly', 'readonly');
						$j("#geolocation-public").attr('disabled', 'disabled');
						
						$j("#geolocation-enabled").removeAttr('checked');
						$j("#geolocation-disabled").attr('checked', 'checked');
						
						if(public == '1')
							$j("#geolocation-public").attr('checked', 'checked');
					}
				});
			});
		</script>
	<?php
}

?>