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

	init();

});


/*
 * Initialize
 */
function init() {
    var map = new OpenLayers.Map('map');
    
    map.addLayer(new OpenLayers.Layer.OSM());
    map.addLayer(new OpenLayers.Layer.VirtualEarth());
    map.addLayer(new OpenLayers.Layer.Yahoo());
    // map.addLayer(new OpenLayers.Layer.Google("Google"));

    var panelControls = [
        new OpenLayers.Control.Navigation(),
        new OpenLayers.Control.LayerSwitcher()
    ];

    var toolbar = new OpenLayers.Control.Panel({
        displayClass: 'olControlEditingToolbar',
        defaultControl: panelControls[0]
    });
    toolbar.addControls(panelControls);
    map.addControl(toolbar);
    map.zoomTo(3);
    map.setCenter(new OpenLayers.LonLat(49, 8.3));

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
}
