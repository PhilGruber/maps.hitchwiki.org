

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
    
    map.setCenter(new OpenLayers.LonLat(49, 8.3));
    map.zoomTo(3);

    var test = rpc.getMarkers(49, 8.3, 3);
    alert(test);
}

