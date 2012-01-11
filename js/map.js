var WPGeolocation = {}
WPGeolocation.defaultOptions = {
  zoom: 6,
  center: new google.maps.LatLng(0.0, 0.0),
  mapTypeId: google.maps.MapTypeId.ROADMAP,
  width: "100%",
  height: "440px"
};
WPGeolocation.mapNumber = 0;
WPGeolocation.drawMap = function(options) {
	
	options = jQuery.extend({}, WPGeolocation.defaultOptions, options);
	
	document.write("<div id=\"geolocation-" + WPGeolocation.mapNumber + "\" class=\"geolocation-map\" style=\"width: " + options.width + "; height: " + options.height + ";\"></div>");
	var map = new google.maps.Map(document.getElementById("geolocation-" + WPGeolocation.mapNumber), options);
	WPGeolocation.mapNumber++;
	var marker = new google.maps.Marker({
		position: options.center, 
		map: map, 
		title:"Post Location"
	});
	map.setZoom(options.zoom);
	var location = new google.maps.LatLng(options.latitude, options.longitude);
	marker.setPosition(location);
	map.setCenter(location);
	
}