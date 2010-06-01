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

	// Remove JS-required alert	
	$("#map").text('');

	// Load Map
	init_map();

	// Navigation
	$(".pagelink").each(function(index) {
		$(this).click(function(e){
			e.preventDefault();
			open_page( $(this).attr("id") );
		});
	});
	$(".cardlink").each(function(index) {
		$(this).click(function(e){
			e.preventDefault();
			open_card( $(this).attr("id") );
		});
	});

	// Search form
	$("#search_form").submit(function(){ 
  		//search($("#search_form #q").value());
        return false; 
    });

	$(function() {
		$("#search_form #q").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: "http://ws.geonames.org/searchJSON",
					dataType: "jsonp",
					data: {
						featureClass: "P",
						style: "full",
						maxRows: 10,
						name_startsWith: request.term
					},
					success: function(data) {
						response($.map(data.geonames, function(item) {
							return {
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
								value: item.name + ", " + item.countryName
							}
						}))
					}
				})
			},
			minLength: 2,
			select: function(event, ui) {
				search(ui.item.label);
			},
			open: function() {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function() {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		});
	});
	
	// Language selection
	$("#language_selection #submit").hide();
	$("#language_selection select").change(function() {
		alert($(this).attr("value"));
		$(this).parent("form").submit();
	});
    
    // Initialize page content area
	$("#pages").html('<div class="page"><a href="#" class="close">x</a><div class="content"> </div></div>');
	$("#pages .page .close").click(function(e){
		e.preventDefault();
		close_page();
	});
	$("#pages .page .content").hide();
	$("#pages .page").hide();

    
    // Initialize card content area
	$("#cards").html('<div class="card"><a href="#" class="close">x</a><div class="content"> </div></div>');
	$("#cards .card .close").click(function(e){
		e.preventDefault();
		close_card();
	});
	$("#cards .card .content").hide();
	$("#cards .card").hide();

});



/*
 * Initialize map
 */
function init_map() {
	
	// Custom images from our own server
	OpenLayers.ImgPath = "static/gfx/openlayers/";
	
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
	
	// Close open stuff
	close_card();
	close_page();

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


/* 
 * Open page
 */
function open_page(name) {

	// Close cards if open
	if($("#cards .card").is(':visible')) { close_card(); }
	
	$.ajax({
		url: "lib/views.php?type=page&lang="+locale+"&page=" + name,
		async: false,
		success: function(content){
			// If pages not opened yet
			if($("#pages .page").is(':hidden')) {
				$("#pages .page .content").html(content).show();
				$("#pages .page").slideDown('fast');
			} else {
				$("#pages .page .content").html(content);
			}
      	}
	});
}


/* 
 * Close page
 */
function close_page() {
	if($("#pages .page").is(':visible')) {
			$("#pages .page .content").hide('fast').text('');
			$("#pages .page").slideUp('fast');
	}
}


/* 
 * Open card
 */
function open_card(name) { //, x_coord, y_coord, width
	
	/*
	if(x_coord = undefined) { var x_coord = '300'; } 
	if(y_coord = undefined) { var y_coord = '300'; }
	if(width = undefined) { var width = '200'; }
	*/

	// Close pages if open
	if($("#pages .page").is(':visible')) { close_page(); }
	
	$.ajax({
		url: "lib/views.php?type=card&lang="+locale+"&page=" + name,
		async: false,
		success: function(content){
			// If pages not opened yet
			if($("#cards .card").is(':hidden')) {
				$("#cards .card .content").html(content).show();
				$("#cards .card").slideDown('fast');
			} else {
				$("#cards .card .content").html(content);
			}
			//$("#cards").attr('css','top:'+x_coord+'px; left:'+y_coord+'+px; width:'+width+'px;')
			$("#cards .card").draggable();
      	}
	});
}


/* 
 * Close card
 */
function close_card() {
	if($("#cards .card").is(':visible')) {
			$("#cards .card .content").hide('fast').text('');
			$("#cards .card").slideUp('fast');
	}
}