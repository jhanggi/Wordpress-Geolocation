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