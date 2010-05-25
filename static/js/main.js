/*
 * Hitchwiki Maps: main.js
 * Requires:
 * - jquery.js
 * - OpenLayers.js
 */



/*
 * When page loads
 */
$(document).ready(function() {

	// getUserLocation:
	fetchlocation();
	
	// Map
	init_map();

	// Search form
	$("#search_form").submit(function(){ 
  		//search($("#search_form #q").value());
        return false; 
    });
    
    

});



/*
 * Initialize map
 */
function init_map() {
	
	// Create map with controls	
	var map = new OpenLayers.Map('map', {
	    controls: [
	        new OpenLayers.Control.Navigation(),
	        new OpenLayers.Control.PanZoomBar(),
	        new OpenLayers.Control.LayerSwitcher({'ascending':false}),
	        new OpenLayers.Control.Permalink(),
	        new OpenLayers.Control.ScaleLine(),
	        new OpenLayers.Control.Permalink('permalink'),
	        new OpenLayers.Control.MousePosition(),
	        new OpenLayers.Control.OverviewMap(),
	        new OpenLayers.Control.KeyboardDefaults()
	        
	        
	    ],
	    numZoomLevels: 6
	    
	});
	
        
        
	// Map layers	
	var layer_osm = 		new OpenLayers.Layer.OSM("Open Street Map");
	/*
	var layer_ve_road = 	new OpenLayers.Layer.VirtualEarth("Virtual Earth Streets", {type: VEMapStyle.Road});
	var layer_ve_air =		new OpenLayers.Layer.VirtualEarth("Virtual Earth Aerial", {type: VEMapStyle.Aerial});
	var layer_yahoo = 		new OpenLayers.Layer.Yahoo("Yahoo Maps");
	var layer_google = 		new OpenLayers.Layer.Google("Google Maps");
	var layer_google_sat = 	new OpenLayers.Layer.Google("Google Maps Satellite", {type: G_SATELLITE_MAP});
	
	layer_ve_road.setVisibility(false);
	layer_ve_air.setVisibility(false);
	layer_yahoo.setVisibility(false);
	layer_google.setVisibility(false);
	layer_google_sat.setVisibility(false);
	
	map.addLayers([
					layer_osm, 
					layer_ve_road, 
					layer_ve_air, 
					layer_yahoo, 
					layer_google, 
					layer_google_sat
				  ]);
	*/
	map.addLayers([layer_osm]);
	
	
	// add and expand the overview map control
	var overview = new OpenLayers.Control.OverviewMap();
	map.addControl(overview);
	overview.maximizeControl();
	
	// Set map
    map.zoomTo(3);
    map.setCenter(new OpenLayers.LonLat(49, 8.3));
    
    
	//if (!map.getCenter()) map.zoomToMaxExtent();

    /*
    var size = new OpenLayers.Size(16,16);
    var offset = new OpenLayers.Pixel(0,0) //-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon('http://maps.hitchwiki.org/img/hitch.png', size, offset);
    var markers = new OpenLayers.Layer.Markers("Points");
    var res = rpc.getMarkers(49, 8.3, 3);
    for (i in res) {
        markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(res[i][0], res[i][1]), icon.clone()));
    }
    var tmp = new OpenLayers.LonLat(49,8.3);
    alert(tmp.toShortString());
    
    markers.addMarker(new OpenLayers.Marker(tmp,icon.clone()));
    markers.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(49.1,8.3),icon.clone()));

    map.addLayer(markers);
    
    */
}




/* Get User Location by current IP
 * http://there4development.com/2010/03/geolocation-services-with-jquery-and-ipinfodb/
 * Requires:
 * - jQuery
 * - jQuery JSON
 * - jQuery Cookie
 * - Snoopy PHP
 *
 * JSON example:
{
  "Ip" : "76.121.45.200",
  "Status" : "OK",
  "CountryCode" : "US",
  "CountryName" : "United States",
  "RegionCode" : "53",
  "RegionName" : "Washington",
  "City" : "Bellingham",
  "ZipPostalCode" : "98226",
  "Latitude" : "48.7982",
  "Longitude" : "-122.41"
}
 */
displaylocation = function(location) {
  if (location.Status == 'OK') {
  
  	// Tool is hidden as a default
  	var show_nearby = false;
  
  	// City
  	if(location.City != '') { 
  		$('#nearby .city a').text(location.City);
  		$('#nearby .city a').click(function(){ search(location.City + ', ' + location.CountryName); });
  		$('#nearby .city').show('fast');
  		show_nearby = true;
  	}
  
  	// State / Region
  	if(location.State != '--') {
  		$('#nearby .state a').text(location.State);
  		$('#nearby .state a').click(function(){ search(location.State + ', ' + location.CountryName); });
  		$('#nearby .state').show('fast');
  		show_nearby = true;
  	}
  	else if(location.RegionName != '') {
  		$('#nearby .state a').text(location.RegionName);
  		$('#nearby .state a').click(function(){ search(location.RegionName + ', ' + location.CountryName); });
  		$('#nearby .state').show('fast');
  		show_nearby = true;
  	}
  	
  	// Country
  	if(location.CountryName != '') { 
  		$('#nearby .country a').text(location.CountryName);
  		$('#nearby .country a').click(function(){ search(location.CountryName); });
  		$('#nearby .country').show('fast');
  		show_nearby = true;
  	}
  	
  	// Show tool if content is filled
    if(show_nearby == true) { $('#nearby').show('fast'); }
    
  }
}

fetchlocation = function() {
  // look in the cookie for the location data
  cookiedata = $.cookie(cookiename);
  if ('' != cookiedata) {
    locationinfo = $.evalJSON(cookiedata);
    if ((locationinfo != null) && (locationinfo.IP == ip)) {
      displaylocation(locationinfo);
      $.cookie(cookiename, cookiedata, cookieoptions);
      return;
    }
  }
  // it's not in the cookie, so fetch from the server
  $.getJSON(
    geolocation, {
      'timezone' : 'false', // set this to false to save the service 2 queries
      'ip'       : ip
    },
    function(data) {
      data.IP = ip;
      displaylocation(data);
      cookiedata = $.toJSON(data);
      $.cookie(cookiename, cookiedata, cookieoptions);
    }
  );
}




/* 
 * Search
 */
function search(q) {
	alert("Search: "+q);

	// Geocode
	$.ajax({
		url: "lib/geocoder.php?q=" + q,
		async: false,
		success: function(geocode){
			alert("Lat, Lon: "+geocode);
			/*
			map.zoomTo(10);
    		map.setCenter(new OpenLayers.LonLat(geocode));
    		*/
      	}
	});
	
    return false;
}
