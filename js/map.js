var WPGeolocation = {}
WPGeolocation.defaultOptions = {
  zoom: 6,
  center: new google.maps.LatLng(0.0, 0.0),
  mapTypeId: google.maps.MapTypeId.ROADMAP,
  width: "100%",
  height: "440px",
  location: {latitude: 0, longitude: 0, title: "Post Location"},
};
WPGeolocation.mapNumber = 0;
WPGeolocation.drawMap = function(options) {
	
	options = jQuery.extend({}, WPGeolocation.defaultOptions, options);
	
	document.write("<div id=\"geolocation-" + WPGeolocation.mapNumber + "\" class=\"geolocation-map\" style=\"width: " + options.width + "; height: " + options.height + ";\"></div>");
	var map = new google.maps.Map(document.getElementById("geolocation-" + WPGeolocation.mapNumber), options);
	WPGeolocation.mapNumber++;
	map.setZoom(options.zoom);
	var locations = jQuery.isArray(options.location) ? options.location : [options.location];
	for (var i = 0; i < locations.length; i++) {
		var location = new google.maps.LatLng(locations[i].latitude, locations[i].longitude);
		var marker = new google.maps.Marker({
			position: location, 
			map: map, 
			title: locations[i].title
		});
		marker.setPosition(location);
	}
	map.setCenter(location);
	
}