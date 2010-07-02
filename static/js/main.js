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

	// Initialize stuff when page has finished loading
	// --------------------------------------------------

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
			open_card( $(this).attr("id"), $(this).text() );
		});
	});


	// Search form
	$("#search_form").submit(function(){ 
  		//search($("#search_form #q").value());
        return false; 
    });

	// Autosuggest in search
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
		$("#language_selection #submit").click();
		//$(this).parent("form").submit(); //<- Ain't working for some reason?
	});
    
    
    // Login panel
    $("#loginPanel").hide();
    $("#loginOpener").click(function(e){
    	e.preventDefault();
    	if($(this).hasClass("open")) {
	    	$(this).removeClass("open");
    		$("#loginPanel").slideUp("fast");
    		$(this).blur();
    	} else {
	    	$(this).addClass("open");
    		$("#loginPanel").slideDown("fast");
    		$("input#username").focus();
    	}
    });
	$("#login_form").submit(function(){ 
  		$(this).hide();
  		$("#loginPanel .loading").show();
  		$.post("lib/login.php", { username: $("#Login #username"), password: $("#Login #password") },
   		function(data){
   			$("#loginPanel .loading").hide();
   			$("#login_form").show();
     		alert("Login: " + data);
   		});
        return false; 
    });
  
    // Initialize page content area
	$("#pages").html('<div class="page"><a href="#" class="close ui-button ui-corner-all ui-state-default ui-icon ui-icon-closethick">Close</a><div class="content"> </div></div>');
	$("#pages .page .close").click(function(e){
		e.preventDefault();
		close_page();
	});
	$("#pages .page .content").hide();
	$("#pages .page").hide();


	// Navigation functions
	$("#add_place").click(function(){
		init_add_place();
	});
	
	
	// Map selector
	$("#map_selector").show();
	$(function() {
		$("#selected_map")
			.button({
        	    icons: {
        	        primary: 'ui-icon-image',
        	        secondary: 'ui-icon-triangle-1-s'
        	    },
        	    text: true
        	})
				.click( function() {
					$("#maplist").slideToggle('fast');
				});
	});
	
	
	$("#maplist li input").button()
		.click( function() {
			var mapname = $(this).next("label").text();
			$("#selected_map .ui-button-text").text( 'Map: '+mapname );
		    $("#maplist").slideToggle('fast');
		    // todo: Change layer from the map
		});
	/*
	// custom icons:
	$("#maplist #map_osm").button({
			text: true,
			icons: {primary: 'icon-osm'}
	});
	$("#maplist #map_goostr", "#maplist #map_goosat", "#maplist #map_goosatl").button({
			text: true,
			icons: {primary: 'icon-google'}
	});
	$("#maplist #map_yahoo").button({
			text: true,
			icons: {primary: 'icon-yahoo'}
	});
	$("#maplist #map_bing").button({
			text: true,
			icons: {primary: 'icon-bing'}
	});
	*/
	$("#maplist").hide();
	
});



/*
 * Initialize map
 */
 
        
        
var map, vectors, controls;
var handle_click = false;

            
function init_map() {

	// Custom images from our own server
	OpenLayers.ImgPath = "static/gfx/openlayers/";
	
	// Create map with controls	
	map = new OpenLayers.Map('map', {
		/*
		projection: new OpenLayers.Projection("EPSG:900913"),
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		*/
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
    map.setCenter(new OpenLayers.LonLat(lat, lon));
    map.zoomTo(zoom);
  
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
    else { $('#nearby').hide(); }
    
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
 * Initialize add new place
 */
function init_add_place() {
	
	// Start listening single clicks
	handle_click = true;
	
	// Add target to the center of the map
	
	// Create base layer for new place
	var target_style = new OpenLayers.StyleMap(OpenLayers.Util.applyDefaults(
        {
        	fillColor: "#f57900", 
        	fillOpacity: 1, 
        	stroke: false
        }, OpenLayers.Feature.Vector.style["default"]));
        
    var target = new OpenLayers.Layer.Vector("target", {styleMap: target_style});

	// Create a dot for new place
	var new_place = new OpenLayers.Feature.Vector(
			new OpenLayers.Geometry.Point(map.getCenter().lon, map.getCenter().lat),
			{some:'data'} /*,
			{externalGraphic: 'static/gfx/map_icons/target.png', graphicHeight: 23, graphicWidth: 23}
			*/
			);
	target.addFeatures(new_place);

	map.addLayer(target);

	// Add dot dragging
	var drag = new OpenLayers.Control.DragFeature(target, {
		onComplete: function(feature) { 
			var lon = feature.geometry.x; 
            var lat = feature.geometry.y;
			
			new_place.move(new OpenLayers.LonLat(lon, lat));
	    	update_add_place(lon, lat);
		}
	});
	map.addControl(drag);
	drag.activate();


	// Click Handler
	OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
	       defaultHandlerOptions: {
	           'single': true,
	           'double': false,
	           'pixelTolerance': 0,
	           'stopSingle': false,
	           'stopDouble': false
	       },
	
	       initialize: function(options) {
	           this.handlerOptions = OpenLayers.Util.extend(
	               {}, this.defaultHandlerOptions
	           );
	           OpenLayers.Control.prototype.initialize.apply(
	               this, arguments
	           ); 
	           this.handler = new OpenLayers.Handler.Click(
	               this, {
	                   'click': this.trigger
	               }, this.handlerOptions
	           );
	       }, 
	
	       trigger: function(e) {
	       		if(handle_click != false) {
		           var lonlat = map.getLonLatFromViewPortPx(e.xy);
	    	       new_place.move(lonlat);
	    	       update_add_place(lonlat.lon, lonlat.lat);
				}
	       }
	 });

	// Add click-handler to the map
	var click = new OpenLayers.Control.Click();
	map.addControl(click);
	click.activate();
	
	// Perform when dialog is opened
	$("#card_add_place").bind( "dialogopen", function(event, ui) {
			// Add coordinates and address to the card
			update_add_place(map.getCenter().lon, map.getCenter().lat);
	});
	
	// Perform when dialog is closed
	$("#card_add_place").bind( "dialogclose", function(event, ui) {

		// Disable single click-listener from map
		click.deactivate();
		map.removeControl(click);
		
		// Remove layer
		target.destroy();
	});
}

function update_add_place(q_lon, q_lat) {

	// Convert coordinates to the "Google projection"
	var g_lonLat = new OpenLayers.LonLat(q_lon, q_lat).transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));

	// Reverse Geocode latlon -> address
	// TODO: Change service from google to something else (we just don't have other reverce geocoders implemented yet...)
	$.getJSON('lib/geocoder.php?service=google&q=' + g_lonLat.lat + ',' + g_lonLat.lon, function(data) {				
			$("#address_row #address").text(data.address);
			if($("#address_row").is(":hidden")) { $("#address_row").show(); }
	});
	
}



/* 
 * Search
 */
function search(q) {
	
	// Close open stuff
	close_cards();
	close_page();

	alert("Search: "+q);

	// Geocode
	$.getJSON('lib/geocoder.php?q=' + q, function(data) {
		
			// TODO: Get bounds, not only center
			
			alert("Lat: "+data.lat.toString()+", Lon: "+data.lon);
			
			// Transform coordinates from "Google Projection" to openlayers
			var o_lonLat = new OpenLayers.LonLat(data.lon, data.lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
			
			map.moveTo(new OpenLayers.LonLat(o_lonLat.lon, o_lonLat.lat), 11);
	});
	
    return false;
}


/* 
 * Open page
 */
function open_page(name) {

	// Close cards if open
	if($("#cards .card").is(':visible')) { close_cards(); }
	
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
function open_card(name, title) { //, x_coord, y_coord, width
	
	/*
	if(x_coord = undefined) { var x_coord = '300'; } 
	if(y_coord = undefined) { var y_coord = '300'; }
	if(width = undefined) { var width = '200'; }
	*/

	// Close pages if open
	if($("#pages .page").is(':visible')) { close_page(); }

	// Allow only one card per type
	if($("#card_"+name).size()) {
		$("#card_"+name).dialog("close");
	}
	
	//$(".card").dialog("destroy");

	$.ajax({
	    url: "lib/views.php?type=card&lang="+locale+"&page=" + name,
	    async: false,
	    success: function(content){
	    	
	    	$("#cards").html('<div class="card" id="card_'+name+'" title="'+title+'">'+content+'</div>');
	    	$("#cards .card").dialog({
						position: [280,100],
						height: 400,
						width: 390,
	    				maxHeight: 600,
	    				maxWidth: 650,
	    				show: 'slide',
	    				/*
	    				buttons: {
							Cancel: function() {
								$(this).dialog('close');
							}
						}
						*/
	    			});
	    	
	    	// If pages not opened yet
	    	/*
	    	if($("#cards .card").is(':hidden')) {
	    		$("#cards .card .content").html(content).show();
	    		$("#cards .card").slideDown('fast');
	    	} else {
	    		$("#cards .card .content").html(content);
	    	}
	    	//$("#cards").attr('css','top:'+x_coord+'px; left:'+y_coord+'+px; width:'+width+'px;')
	    	$("#cards .card").draggable({ cursor: 'move', handle: '.dragArea', containment: 'parent' });
	    	$("#cards .card").resizable({
				containment: 'parent'
			});
			*/
      	}
	});
	
}


/* 
 * Close all cards
 * TODO: Not quite working yet... :-)
 * Maybe some foreach loop inside #cards ?
 */
function close_cards() {
	$(".card").dialog("close");
}
