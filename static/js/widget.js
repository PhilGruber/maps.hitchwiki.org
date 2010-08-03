
var markersZoomLimit = 6;

var markers_free = false;
var map, places, map_center;

$(document).ready(function() {

	// Remove JS-required alert	
	$("#map").text('');
	
	
	
	// Custom images from our own server
	OpenLayers.ImgPath = "static/gfx/openlayers/";
	
	// Create map with controls	
	map = new OpenLayers.Map('map', {
		projection: new OpenLayers.Projection("EPSG:4326"),
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		eventListeners: {
		    "moveend": refreshMapMarkers
		},
	    controls: [
	        new OpenLayers.Control.Navigation(),
	        new OpenLayers.Control.ScaleLine()
	    ],
	    numZoomLevels: 6
	    
	});
 
 	// Different colors for markers depending on their rating
	var colors = [	
					"#ffffff", // rate 0
					"#00ad00", // rate 1
					"#96ad00", // rate 2
					"#ffff00", // rate 3
					"#ff8d00", // rate 4
					"#ff0000"  // rate 5
				];
	
	// Get rating from marker
	var markerContext = {
	    getColor: function(feature) {
	        return colors[feature.attributes["rating"]];
	    }
	};

	// Initialize a layer for the places
	// You can fill it with refreshMapMarkers() using listener events
	places = new OpenLayers.Layer.Vector(
		"Places", {
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
                	    
                	})
                }), //stylemap end
			isBaseLayer: false,
			rendererOptions: {yOrdering: true}
        }
	);//places end

	var layer_osm = new OpenLayers.Layer.OSM("Open Street Map");

	// Add produced layers to the map
	map.addLayers([layer_osm,places]);

	// Selecting markers
	var select_marker = new OpenLayers.Control.SelectFeature(places, 
							{
								onSelect: onFeatureSelect, 
								onUnselect: onFeatureUnselect,
								hover: true,
								clickout: true,
								multiple: false,
								box: false
							});
    map.addControl(select_marker);
    select_marker.activate();
    
    
	// Set map
    map.setCenter(new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()));
    map.zoomTo(zoom);

	// Let markers be freeeee
	markers_free = true;
	refreshMapMarkers();
});



function onFeatureSelect(feature) {
	
	if(feature.attributes.description != "") {
		var description = feature.attributes.description;// + '<br /><br />';
	}
	else {
		var description = "<i>No description...</i>";
	}
	
	var point = feature.geometry.getBounds().getCenterLonLat();

    if(point.lat > map_center.lat) {
    	var offset = 5;
    } else {
    	var offset = -5;
    }
    
    popup = new OpenLayers.Popup.FramedCloud("Info", 
							point,
							null,
							'<div style="font-size:.8em">' + description +'</div>',//<small><a target="_top" href="./?place=' + feature.attributes.id +'">'+read_more_txt+'</a></small></div>',
							{
								'size': new OpenLayers.Size(15,15), 
								'offset': new OpenLayers.Pixel(5,offset)
							}, 
							false);

    feature.popup = popup;
    map.addPopup(popup);
}
function onFeatureUnselect(feature) {
    map.removePopup(feature.popup);
    feature.popup.destroy();
    feature.popup = null;
} 

var markers = new Array();
function refreshMapMarkers() {

	var currentZoom = map.getZoom();
	
	// Hide markers layer if zoom level isn't deep enough, and show marker count-labels instead
	if(currentZoom < markersZoomLimit) {
		places.setVisibility(false);
	}
	else {
		places.setVisibility(true);
	}
	
	if(currentZoom >= markersZoomLimit && markers_free==true) {
	
	map_center = map.getCenter();
	
	maps_debug("refreshMapMarkers called.");

		// Get corner coordinates from the map
		var extent = map.getExtent();
		var corner1 = new OpenLayers.LonLat(extent.left, extent.top).transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
		var corner2 = new OpenLayers.LonLat(extent.right, extent.bottom).transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
		
		
		var apiCall = 'api/?description=en_UK&bounds='+corner2.lat+','+corner1.lat+','+corner1.lon+','+corner2.lon;	
		maps_debug("Calling API: "+apiCall);
		
		// Get markers from the API for this area
		$.getJSON(apiCall, function(data) {
		    // Go trough all markers
		    
		    maps_debug("Starting markers each-loop...");
		    
		    // Loop markers we got trough 
		    var markerStock = [];
		    $.each(data, function(key, value) {
		    
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
		    					rating: value.rating,
		    					description: value.description
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
		    
		    // Loading done, hide loading bar
		    hide_loading_bar();
		    
		// getjson * end
		});

	}
	else if(currentZoom < markersZoomLimit && markers_free==true){
		hide_loading_bar();
	}

}



function maps_debug(str) {

		$("#log").append("<li>"+str+"</li>");
		$("#log").attr({ scrollTop: $("#log").attr("scrollHeight") });

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
		$("#loading-bar").hide();
	}
}
