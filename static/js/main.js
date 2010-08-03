/*
 * Hitchwiki Maps: main.js
 * Requires:
 * - jquery.js
 * - OpenLayers.js
 */


/*
 * Settings
 */
var debug = true; // enable / disable the log
var show_log = false; // show log on start (toggle from footer)
var mapEventlisteners = false; // These will be turned on when page stops loading and map is ready


/*
 * When page loads
 */
$(document).ready(function() {

	// Initialize stuff when page has finished loading
	// --------------------------------------------------

	// Debug log-box
	if(debug==true) {
	
		$("#log").draggable();
		$("#log").resizable();
		
		// Create a toggle button for log
		$("#developers").append(' &bull; <a href="#" id="toggle_log">Toggle log</a>');
		$("#toggle_log").click(function(e){
			e.preventDefault();
			$("#log").toggle();
		});
		
		// Some positioning...
		$("#log").attr("style","position:absolute; top: 100px; right: 10px;");
		
		// Show or hide log at the start?
		if(show_log==true) {
			$("#log").show();
		} else { 
			$("#log").hide();
		}
		
	}

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
  		search($("#search_form #q").val());
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
								value: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName
								//value: item.name + ", " + item.countryName
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
    if($("#Login").hasClass("logged_out")) {
		maps_debug("Initialize the login form");
		
    	$("#loginPanel").hide();
    	$("#loginOpener").click(function(e){
    		e.preventDefault();
    		if($(this).hasClass("open")) {
    			maps_debug("Close login slider.");
		    	$(this).removeClass("open");
    			$("#loginPanel").slideUp("fast");
    			$(this).blur();
    		} else {
    			maps_debug("Open login slider.");
		    	$(this).addClass("open");
    			$("#loginPanel").slideDown("fast");
    			$("input#email").focus();
    		}
    	});
		$("#login_form").submit(function(){ 
			maps_debug("Login submitted.");
		
		
  			$(this).hide();
  			$("#loginPanel .loading").show();
		
			// Grab login info from the form
			var p_email = $("#Login #email").val();
			var p_password = $("#Login #password").val();
			var p_remember = $("#Login #remember_me").val();
		
			if(p_email == "" || p_password == "") {
				info_dialog("Please type in your email and password", "Login failed",true);
			} else {
		
				// Send it as a post-requeset
   				maps_debug("Requesting to login: "+p_email);
				$.post('lib/login.php', { email: p_email, password: p_password, remember: p_remember },   
				function(data) {
   					maps_debug("Got login responce, login: "+data.login);
   					
   					// Empty these just in case
   					p_email = false;
   					p_password = false;
				
					if(data.login == true) {
   						$("#reloadPage input").click();
    	 			}
					else if(data.login == false) {
   						$("#loginPanel .loading").hide();
   						$("#login_form").show();
    	 				info_dialog('<p><strong>Incorrect email or password.</strong></p><p><a href="./?page=lost_password">Lost your password?</a></p>', 'Login failed',true);
    	 			}
    	 			else {
   						$("#loginPanel .loading").hide();
   						$("#login_form").show();
    	 				info_dialog("Mystical error with login, please try again.", "Login failed",true);
    	 			}
    	 			
    	 			
			}, "json"
			); // post end
   			
   			} // if empty-else end
   			
    	    return false; 
    	});
    }// logged_out?
    
    
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
	/*
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
	*/
	
	// custom icons:
	/*
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
	//$("#maplist").hide();
	
	
	// Tools Panel - Opening/closing
	$("#tools").click(function(e){
		e.preventDefault();
		$("#toolsPanel").toggle();
	});
	
	// Tools Panel - Add a close button to tools panel
	$("#toolsPanel h4").append('<a href="#" class="close ui-button ui-corner-all ui-state-default ui-icon ui-icon-closethick align_right">Close</a>');
	$("#toolsPanel h4 .close").click(function(e){
		e.preventDefault();
		$("#toolsPanel").toggle();
	});
	
	// Tools Panel - make it draggable
	$("#toolsPanel").draggable({ handle: 'h4' });
	$("#toolsPanel").attr("style","text-align:left; top: 100px; right: 30px;");

	// Tools Panel - init zoom tool slider
	$("#toolsPanel #zoom_slider").slider({
	    range: "max",
	    min: 0,
	    max: 18,
	    value: markersZoomLimit,
	    slide: function(event, ui) {
	    
	    		$("#toolsPanel #zoom_slider_amount").text(ui.value);
	    		
	    		markersZoomLimit = ui.value;
	    		
	    		if(ui.value <= 6) {
	    			$("#toolsPanel #zoomlevel *").hide();
	    			$("#toolsPanel #zoomlevel .z_continent").show();
	    		}
	    		else if(ui.value <= 10) {
	    			$("#toolsPanel #zoomlevel *").hide();
	    			$("#toolsPanel #zoomlevel .z_country").show();
	    		}
	    		else if(ui.value <= 14) {
	    			$("#toolsPanel #zoomlevel *").hide();
	    			$("#toolsPanel #zoomlevel .z_city").show();
	    		}
	    		else {
	    			$("#toolsPanel #zoomlevel *").hide();
	    			$("#toolsPanel #zoomlevel .z_streets").show();
	    		}
	    		
	    		$.cookie(cookie_prefix+'markersZoomLimit', ui.value, { path: '/', expires: 666 });

	    		refreshMapMarkers();
	    }
	});
	
	$("#toolsPanel #zoom_slider_amount").text($("#toolsPanel #zoom_slider").slider("value"));
	
	// Tools Panel and loading animation - hide at the beginning
	$("#toolsPanel, #loading-bar").hide();
	
	var sidebar_height = $("#Sidebar").height();
	$("#map").attr("style","min-height: "+sidebar_height+"px");

});

/*
 * Initialize map
 */

var map, places, map_center;
var handle_click = false;

function init_map() {
	maps_debug("Initialize the map");

	// Custom images from our own server
	OpenLayers.ImgPath = "static/gfx/openlayers/";
	
	// Create map with controls	
	map = new OpenLayers.Map('map', {
		projection: new OpenLayers.Projection("EPSG:4326"),
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		eventListeners: {
			"move": mapEventMoveStarted,
		    "moveend": mapEventMove,
		    "zoomend": mapEventZoom,
		    "changelayer": mapLayerChanged,
		    "changebaselayer": mapBaseLayerChanged
		},
	    controls: [
	        new OpenLayers.Control.Navigation(),
	        new OpenLayers.Control.PanZoomBar(),
	        //new OpenLayers.Control.LayerSwitcher({'ascending':false}),
	        new OpenLayers.Control.ScaleLine(),
	        //new OpenLayers.Control.Permalink('permalink'),
	        //new OpenLayers.Control.Permalink(),
	        //new OpenLayers.Control.MousePosition(),
	        new OpenLayers.Control.OverviewMap(),
	        new OpenLayers.Control.KeyboardDefaults()
	        
	        
	    ],
	    numZoomLevels: 19
	    
	});
	
 	
	// Measure controls
	// http://openlayers.org/dev/examples/measure.html
	
	// style the sketch fancy
	var sketchSymbolizers = {
	    "Point": {
	        pointRadius: 4,
	        graphicName: "square",
	        fillColor: "white",
	        fillOpacity: 1,
	        strokeWidth: 1,
	        strokeOpacity: 1,
	        strokeColor: "#db7700"
	    },
	    "Line": {
	        strokeWidth: 3,
	        strokeOpacity: 1,
	        strokeColor: "#333",
	        strokeDashstyle: "dash"
	    },
	    "Polygon": {
	        strokeWidth: 2,
	        strokeOpacity: 1,
	        strokeColor: "#333",
	        fillColor: "white",
	        fillOpacity: 0.3
	    }
	};
	var style = new OpenLayers.Style();
	style.addRules([
	    new OpenLayers.Rule({symbolizer: sketchSymbolizers})
	]);
	var styleMap = new OpenLayers.StyleMap({"default": style});
	
	measureControls = {
	    line: new OpenLayers.Control.Measure(
	        OpenLayers.Handler.Path, {
	            persist: true,
	            handlerOptions: {
	                layerOptions: {styleMap: styleMap}
	            }
	        }
	    ),
	    polygon: new OpenLayers.Control.Measure(
	        OpenLayers.Handler.Polygon, {
	            persist: true,
	            handlerOptions: {
	                layerOptions: {styleMap: styleMap}
	            }
	        }
	    )
	};
	var control;
	for(var key in measureControls) {
	    control = measureControls[key];
        control.geodesic = true;//take this away if you want to make geodesic optional
	    control.events.on({
	        "measure": handleMeasurements,
	        "measurepartial": handleMeasurements
	    });
	    map.addControl(control);
	}
	
	document.getElementById('noneToggle').checked = true;

	// Map layers	
	var layer_osm = 		new OpenLayers.Layer.OSM("Open Street Map");
	
/*
	// Virtual Earth
	var layer_ve_road = 	new OpenLayers.Layer.VirtualEarth("Virtual Earth Streets", {type: VEMapStyle.Road, visibility: false});
	var layer_ve_air =		new OpenLayers.Layer.VirtualEarth("Virtual Earth Aerial", {type: VEMapStyle.Aerial, visibility: false});
	
	// Yahoo
	var layer_yahoo = 		new OpenLayers.Layer.Yahoo("Yahoo Maps", {visibility: false});
	
	// Google
	var layer_google = 		new OpenLayers.Layer.Google("Google Maps", {visibility: false});
	var layer_google_sat = 	new OpenLayers.Layer.Google("Google Maps Satellite", {type: G_SATELLITE_MAP, visibility: false});
*/


 	// Different colors for markers depending on their rating
	var colors = [	
					"#ffffff", // rate 0 (white)
					"#00ad00", // rate 1 (green)
					"#96ad00", // rate 2
					"#ffff00", // rate 3
					"#ff8d00", // rate 4
					"#ff0000"  // rate 5 (red)
				];
	
	// Get rating from marker
	var markerContext = {
	    getColor: function(feature) {
	        return colors[feature.attributes["rating"]];
	    }/*,
	    radius: function(feature) { 
	    	return Math.min(feature.attributes.count, 7) + 3;
	    }*/
	};
	
	// Initialize a layer for the places
	// You can fill it with refreshMapMarkers() using listener events
	places = new OpenLayers.Layer.Vector(
		"Places", {/*
				strategies: [
				    new OpenLayers.Strategy.Fixed(),
				    new OpenLayers.Strategy.Cluster()
				],*/
				styleMap: new OpenLayers.StyleMap({
                	"default": new OpenLayers.Style({
                	
    				    graphicZIndex: 1,
					    pointRadius: 5,//"${radius}",
    				    strokeWidth: 2,
						cursor: "pointer",
                	    fillColor: "#000000",//#ffcc66
					    strokeColor: "${getColor}" // using context.getColor(feature)
					    
					}, {context: markerContext}),
                	"select": new OpenLayers.Style({
                	
                	    graphicZIndex: 2,
                	    fillColor: "#66ccff",
                	    strokeColor: "#3399ff"
                	    
                	}),
                	"hover": new OpenLayers.Style({
                	
                	    graphicZIndex: 2,
                	    fillColor: "#3399ff"
                	    
                	})
                }), //stylemap end

			isBaseLayer: false,
			rendererOptions: {yOrdering: true}
        }
	);//places end
	
  
	// Event listeners for places layer
	places.events.on({
		'featureselected': function(event) {
			feature = event.feature;
			maps_debug("Selected marker "+feature.attributes.id);
			showPlacePanel(feature.attributes.id);
                
		},
		'featureunselected': function(feature) {
			maps_debug("Unselected marker.");
			hidePlacePanel();
		}
	});


	// Layer for the places count
	places_count = new OpenLayers.Layer.Vector(
		"Places count",
		{
			styleMap: new OpenLayers.StyleMap({

				fillOpacity: 1, 
				fillColor: "#fde669", 
				strokeColor: "#c38b06", 
    			strokeWidth: 1,
				pointRadius: "${radius}",
				cursor: "pointer",
								
				label: "${places}",
				fontColor: "#824300",
				fontSize: "10px",
				fontFamily: "'Trebuchet MS',Arial,sans-serif",
				fontWeight: "bold",
				labelAlign: "cm"
			}),
			isBaseLayer: false,
			rendererOptions: {yOrdering: true}
		}
	);


	// Add produced layers to the map
	map.addLayers([
					layer_osm,
					places,
					places_count
				  ]);
	/*
					layer_google, 
					layer_google_sat,
					layer_ve_road, 
					layer_ve_air, 
					layer_yahoo, 
	*/
	
	// Hovering markers
	var hover_marker = new OpenLayers.Control.SelectFeature(places, {
	    hover: true,
	    highlightOnly: true,
	    renderIntent: "hover"
	});
	map.addControl(hover_marker);
	hover_marker.activate();

	// Selecting markers
	var select_marker = new OpenLayers.Control.SelectFeature(places, 
							{
								hover: false,
								highlightOnly: true,
								clickout: true,
								multiple: false,
								box: false
							});
    map.addControl(select_marker);
    select_marker.activate();



	// Get labels to the map
	markerCountLabels();
	
	// Selecting labels
	var select_countrydot = new OpenLayers.Control.SelectFeature(places_count, 
							{
								onSelect: onCountrydotSelect, 
								onUnselect: onCountrydotUnselect,
								hover: true,
								clickout: true,
								multiple: false,
								box: false
							});
    map.addControl(select_countrydot);
    select_countrydot.activate();


	// add and expand the overview map control
	var overview = new OpenLayers.Control.OverviewMap();
	map.addControl(overview);
	overview.maximizeControl();

	
	// Set map
    map.setCenter(new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()));
    
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

	// Let eventlisteners be free! :-)
	// Meaning, they can be called from now on that page has stopped loading
	mapEventlisteners = true;

	// Hide panel where we show info about place
	$("#PlacePanel").hide();

}



/*
 * Open small countryinfo-popup when selecting count labels
 */
/*
var selectControl, selectedFeature;
function onCountrydotPopupClose(evt) {
    selectControl.unselect(selectedFeature);
}
*/
function onCountrydotSelect(feature) {
	var point = feature.geometry.getBounds().getCenterLonLat();

    if(point.lat > map_center.lat) {
    	var offset = 5;
    } else {
    	var offset = -5;
    }
    
    popup = new OpenLayers.Popup.FramedCloud("Country", 
                             point,
                             null,
                             '<div style="color: #111;"><h4 style="margin:0; padding: 0 0 3px 21px; background: url(static/gfx/flags/png/'+feature.attributes.iso.toLowerCase()+'.png) no-repeat 0 3px;">' + feature.attributes.name +'</h4><small class="grey">' + feature.attributes.places +' places. <!--<a href="#" onclick="zoomMapIn(' + feature.geometry.getBounds().getCenterLonLat().lat +',' + feature.geometry.getBounds().getCenterLonLat().lon +',7); return false;">Zoom in</a>--></small></div>',
                             {
								'size': new OpenLayers.Size(15,15), 
								'offset': new OpenLayers.Pixel(5,offset)
							}, 
							false);//, onCountrydotPopupClose);
    feature.popup = popup;
    map.addPopup(popup);
}
function onCountrydotUnselect(feature) {
    map.removePopup(feature.popup);
    feature.popup.destroy();
    feature.popup = null;
} 

            
/*
 * Measure controls
 * http://openlayers.org/dev/examples/measure.html
 */
function handleMeasurements(event) {
    var geometry = event.geometry;
    var units = event.units;
    var order = event.order;
    var measure = event.measure;
    var element = document.getElementById('toolOutput');
    var out = "";
    if(order == 1) {
        out += measure.toFixed(3) + " " + units;
    } else {
        out += measure.toFixed(3) + " " + units + "<sup>2</" + "sup>";
    }
    element.innerHTML = out;
}

function toggleControl(element) {
    for(key in measureControls) {
        var control = measureControls[key];
        if(element.value == key && element.checked) {
            control.activate();
        } else {
            control.deactivate();
        }
    }
}
/* Hidden since geodesic is true by default, but if you want to make it optional, use this (and check HTML from index.php)
function toggleGeodesic(element) {
    for(key in measureControls) {
        var control = measureControls[key];
        control.geodesic = element.checked;
    }
}
*/

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
  cookiedata = $.cookie(geolocation_cookiename);
  if ('' != cookiedata) {
    locationinfo = $.evalJSON(cookiedata);
    if ((locationinfo != null) && (locationinfo.IP == ip)) {
      displaylocation(locationinfo);
      $.cookie(geolocation_cookiename, cookiedata, geolocation_cookieoptions);
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
      $.cookie(geolocation_cookiename, cookiedata, geolocation_cookieoptions);
    }
  );
}



/*
 * Call when moving ends
 */
function mapEventMove() {
	if(mapEventlisteners==true) {
		//maps_debug("map movement ended");
		
		// Refresh markers in viewport
		refreshMapMarkers();
		
	}
}


/*
 * Call when moving starts
 */
function mapEventMoveStarted() {

	var currentZoom = map.getZoom();

	// Hide markers layer if zoom level isn't deep enough, and show marker count-labels instead
	if(currentZoom < markersZoomLimit) {
		places.display(false);
	}
	else {
		places.display(true);
	}
	
}


/*
 * Call when zooming ends
 */
function mapEventZoom() {
	if(mapEventlisteners==true) {
		maps_debug("Map Zoom: "+map.getZoom());
		
		// This gets called anyway, because when zooming, then also moving
		//refreshMapMarkers();
	}
}


/*
 * Call when changing map layer
 */
function mapLayerChanged() {
	if(mapEventlisteners==true) {
		maps_debug("map layer changed");
		//alert("event listener: mapLayerChanged");
	
	}
}


/*
 * Call when changing map base layer
 */
function mapBaseLayerChanged() {
	if(mapEventlisteners==true) {
		maps_debug("map baselayer changed");
		//alert("event listener: mapBaseLayerChanged");


	}
}


/*
 * Add marker count labels to the map
 */

function markerCountLabels() {
	maps_debug("Gathering marker count labels from the API...");
	
	// Get labels from the database
	// Gets only countries with 1+ places
	
	var apiCall = 'api/?countries&coordinates';	
	maps_debug("Calling API: "+apiCall);
		
	$.getJSON(apiCall, function(data) {
		
		maps_debug("Got labels by JSON. Starting to loop 'em.");
	
		// Got labels, build up markers
	    var labelStock = [];

	    $.each(data, function(key, value) {
			
			//maps_debug("Label: "+value.name+" | "+value.lon+", "+value.lat);

			if(value.lon != "" || value.lat != "" || value.lon != undefined || value.lat != undefined) {
			
				// This value is used to make bigger background dot for bigger values, so all the numbers can fit in
	    		if(value.places >= 1000) var pointRadius = 14;
	    		else if(value.places >= 100) var pointRadius = 12;
	    		else if(value.places >= 10) var pointRadius = 9;
	    		else var pointRadius = 7;
				
	    		var coords = new OpenLayers.LonLat(value.lon, value.lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
				labelStock.push(
					new OpenLayers.Feature.Vector(
						new OpenLayers.Geometry.Point(coords.lon, coords.lat),
						{
							name: value.name,
							places: value.places,
							iso: value.iso,
							radius: pointRadius
						}
					)
				);
				
			} else {
				maps_debug("Lat/Lon missing for the label: "+value.name+" ("+value.lat+", "+value.lon+")");
			}
	    }); // each end

		// Add array of markers to the map
		if(labelStock.length > 0) {
		    maps_debug("Adding "+labelStock.length+" labels to the map.");
		    places_count.addFeatures(labelStock);
		} else {
		    maps_debug("Error: didn't get any labels to add.");
		}
	}); // get json end
}


/*
 * Our own little markermanager
 * Shows only what's needed, not all the world at the same time. ;-)
 */
var markers = new Array();
function refreshMapMarkers() {
	
	var currentZoom = map.getZoom();
	map_center = map.getCenter();

	// Hide markers layer if zoom level isn't deep enough, and show marker count-labels instead
	if(currentZoom < markersZoomLimit) {
		places.setVisibility(false);
		places_count.setVisibility(true);
	}
	else {
		places.setVisibility(true);
		places_count.setVisibility(false);
	}
	
	
	// Start loading markers only after certain zoom level
	if(currentZoom >= markersZoomLimit) {

		// Get corner coordinates from the map
		var extent = map.getExtent();
		var corner1 = new OpenLayers.LonLat(extent.left, extent.top).transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
		var corner2 = new OpenLayers.LonLat(extent.right, extent.bottom).transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));

		var apiCall = 'api/?bounds='+corner2.lat+','+corner1.lat+','+corner1.lon+','+corner2.lon;	
		maps_debug("Calling API: "+apiCall);
	
		// Get markers from the API for this area
		$.getJSON(apiCall, function(data) {
			// Go trough all markers
			
			maps_debug("Starting markers each-loop...");
			
			// Loop markers we got trough 
	        var markerStock = [];
			$.each(data, function(key, value) {
			/* Value includes:
			    value.id; 
			    value.lat;
			    value.lon;
			    value.rating;
			*/
			
				// Check if marker isn't already on the map
				// and add it to the map
				if(markers[value.id] != true) {
					markers[value.id] = true;
					
					//maps_debug("Adding marker #"+value.id +"<br />("+value.lon+", "+value.lat+")...");
					
	                var coords = new OpenLayers.LonLat(value.lon, value.lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	                
	                markerStock.push(
	                    new OpenLayers.Feature.Vector(
	                        new OpenLayers.Geometry.Point(coords.lon, coords.lat),
							{
								id: value.id,
								rating: value.rating
							}
	                    )
	                );
	                
	                
	                //maps_debug("...done.");
	
				} 
				else {
					//maps_debug("marker #"+value.id +" already on the map.");
				}
				
			// each * end
			});
			
			if(markerStock.length > 0) {
				maps_debug("Loop ended. Adding "+markerStock.length+" new markers to the map.");
		        places.addFeatures(markerStock);
			} else {
				maps_debug("Loop ended. No new markers found from this area.");
			}	
		// getjson * end
		});
	
	
		
	// end zoom limit
	}
}


/*
 * Initialize add new place
 */
function init_add_place() {
	
	maps_debug("Initialize adding a new place");
	
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
        
    var target = new OpenLayers.Layer.Vector("New place", {styleMap: target_style});

	// Create a dot for new place
	var new_place = new OpenLayers.Feature.Vector( new OpenLayers.Geometry.Point(map.getCenter().lon, map.getCenter().lat) );
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
	maps_debug("Updating add place -card");

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
 * Show marker panel
 */
function showPlacePanel(id,zoomin) {
	maps_debug("Show marker panel for place: "+id);

	$.ajax({
		url: "lib/place.php?id="+id+"&lang="+locale,
		async: false,
		success: function(content){
			maps_debug("Loaded marker data OK.");
			$("#PlacePanel").html(content);
			
			if(zoomin == true) {
				lat = $("#PlacePanel #coordinates .lat").text(); 
				lon = $("#PlacePanel #coordinates .lon").text(); 
				
				zoomMapIn(lat, lon, 16);
			}
      	}
	});
	
	// Show panel
	$("#PlacePanel").show();

	// Shring map a bit and make some space for the panel
	$("#map").attr("style","right:250px;");
	
}


/* 
 * Hide marker panel
 */
function hidePlacePanel() {
	maps_debug("Hiding Place Panel.");
	
	// Map to full width again
	$("#map").attr("style","right:0;");
	
	$("#PlacePanel").hide();
	$("#PlacePanel").html("");
}


/* 
 * Search
 */
function search(q) {
	maps_debug("Search: "+q);
	
	// Close open stuff
	close_cards();
	close_page();

	show_loading_bar("Searching...");

	// Geocode
	//$.getJSON('lib/geocoder.php?q=' + q, function(data) {
	$.ajax({
		// Define AJAX properties.
		method: "get",
		url: 'lib/geocoder.php?q=' + q,
		dataType: "json",
		timeout: (2 * 1000),
	 
		// Got a place
		success: function(data){
		
			//maps_debug("Search found: lat: "+data.lat+", lon: "+data.lon);
			maps_debug("Search found and moving to: "+data.boundingbox);
			
			// Hide "searching"
			hide_loading_bar();
	
			// build a bounding box coordinates and zoom in
			var boundingbox = data.boundingbox.split(',');
			
			bounds = new OpenLayers.Bounds();
			bounds.extend( new OpenLayers.LonLat(boundingbox[2],boundingbox[0]).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()) );
			bounds.extend( new OpenLayers.LonLat(boundingbox[3],boundingbox[1]).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()) );
			
			map.zoomToExtent( bounds );
			
		},
	 
	 
		// Did not got a place
		error: function( objAJAXRequest, strError ){
			maps_debug("Search didn't find anything. Error type: "+strError);
			
			// Hide "searching"
			hide_loading_bar();
			
			info_dialog('<p>Your search did not match any places.</p><p>Try searching by english city names or/and add a country name with cities.', 'Not found', false);
		}
	});
	
	
			
	//});
	
    return false;
}


/* 
 * Zoom map in to a point
 */
function zoomMapIn(lat, lon, zoom) {
	maps_debug("Zoom map to a point.");
	map.setCenter(new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()));
	map.zoomTo(zoom);
}


/* 
 * Open page
 */
function open_page(name) {
	maps_debug("Open a page: "+name);

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
	maps_debug("Closing a page");
	
	if($("#pages .page").is(':visible')) {
			$("#pages .page .content").hide('fast').text('');
			$("#pages .page").slideUp('fast');
	}
}


/* 
 * Open card
 */
function open_card(name, title) { //, x_coord, y_coord, width
	maps_debug("Open a card: "+name);
	
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
	maps_debug("Closing all cards...");
	$(".card").dialog("close");
}



// Function to remove comment
// Produces an error popup if current logged in user doesn't have any permission
function removeComment(remove_id) {
    maps_debug("Asked to remove a comment "+remove_id);

	var confirm_remove = confirm("Are you sure you want to remove this comment?")

	if(confirm_remove) {
		// Call API
		$.getJSON('api/?remove_comment='+remove_id, function(data) {
		
			if(data.success == true) {
		
    			maps_debug("Comment "+remove_id+" removed.");
			
				// Fade comment away
    			$("#comment_list #comment-"+remove_id).fadeOut("slow");
    	
    			// Minus one from comment counter
    			var current_comment_count = $("#comments #comment_counter").text();
    			$("#comments #comment_counter").text(parseInt(current_comment_count)-1);
    			
			} else {
				info_dialog("Could not remove comment due error. Please try again!", "Error", true);
    			maps_debug("Could note remove comment. Error: "+data.error);
			}
		
		});
	}
}


/* 
 * Show simple info/error dialog for user
 */
function info_dialog(info, title, alert) {
	
	if(alert==true) { var type = "alert"; }
	else { var type = "info"; }
	
	if(title == undefined) { title = type; }
	
	maps_debug("Calling "+type+"-dialog box; "+title);
	
	
	
	$("#dialog-message").attr("title",title);
	$("#dialog-message").html('<p><span class="ui-icon ui-icon-'+type+'" style="float:left; margin:0 7px 50px 0;"></span>'+info+'</p>');
	
	$("#dialog-message").dialog({
		modal: true,
		resizable: false,
		buttons: {
			Ok: function() {
				$(this).dialog('close');
			}
		}
	});
}


/* 
 * Show simple loading animation
 */
function show_loading_bar(title) {
	maps_debug("Show loading bar: "+title);
	
	if(title != undefined) { $("#loading-bar .title").text(title); }
	else { $("#loading-bar .title").text(""); }
	
	if($("#loading-bar").is(":hidden") == true) {
		$("#loading-bar").show();
	}
}


/* 
 * Show simple loading animation
 */
function hide_loading_bar() {
	maps_debug("Hide loading bar.");
	
	$("#loading-bar .title").text("");
	if($("#loading-bar").is(":visible") == true) {
		$("#loading-bar").fadeOut('slow');
	}
}


/* 
 * Log debug events if debugging is on
 */
function maps_debug(str) {
	if(debug==true) {
		$("#log ul").append("<li>"+str+"</li>");
		$("#log").attr({ scrollTop: $("#log").attr("scrollHeight") });
	}
}
