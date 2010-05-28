<html>
  <head>
    <script type='text/javascript' src='http://www.google.com/jsapi'></script>

<?php if(isset($_GET["map"]) && $_GET["map"] == "1"): ?>

    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['intensitymap']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', '', 'Country');
        data.addColumn('number', 'Hitchability', 'a');
        data.addColumn('number', 'Places', 'b');
        data.addRows(6);
        data.setValue(0, 0, 'CN');
        data.setValue(0, 1, 4);
        data.setValue(0, 2, 6);
        data.setValue(1, 0, 'IN');
        data.setValue(1, 1, 3);
        data.setValue(1, 2, 23);
        data.setValue(2, 0, 'US');
        data.setValue(2, 1, 1);
        data.setValue(2, 2, 56);
        data.setValue(3, 0, 'ID');
        data.setValue(3, 1, 4);
        data.setValue(3, 2, 30);
        data.setValue(4, 0, 'RU');
        data.setValue(4, 1, 5);
        data.setValue(4, 2, 210);
        data.setValue(5, 0, 'DE');
        data.setValue(5, 1, 5);
        data.setValue(5, 2, 421);
		
        var chart = new google.visualization.IntensityMap(document.getElementById('map_canvas'));
        
		var color_arr = new Array('#eb7f00','#eb7f00');
        chart.draw(data, {width: 440, height: 220, colors: color_arr});
      }
    </script>
 
<?php elseif(isset($_GET["map"]) && $_GET["map"] == "2"): ?>

  <script type='text/javascript'>
   google.load('visualization', '1', {'packages': ['geomap']});
   google.setOnLoadCallback(drawMap);

    function drawMap() {
      var data = new google.visualization.DataTable();
      data.addRows(7);
      data.addColumn('string', 'Country');
      data.addColumn('number', 'Hitchability');
      data.setValue(0, 0, 'Germany');
      data.setValue(0, 1, 5);
      data.setValue(1, 0, 'United States');
      data.setValue(1, 1, 1);
      data.setValue(2, 0, 'Brazil');
      data.setValue(2, 1, 3);
      data.setValue(3, 0, 'Canada');
      data.setValue(3, 1, 3);
      data.setValue(4, 0, 'France');
      data.setValue(4, 1, 4);
      data.setValue(5, 0, 'Russia');
      data.setValue(5, 1, 5);
      data.setValue(6, 0, 'Finland');
      data.setValue(6, 1, 5);

      var options = {};
      options['dataMode'] = 'regions';
      options['width'] = '800';
      options['height'] = '400';

      var container = document.getElementById('map_canvas');
      var geomap = new google.visualization.GeoMap(container);
      geomap.draw(data, options);
  };
  </script>

<?php endif; ?>

  </head>

  <body>
    <div id="map_canvas"></div>
  </body>
</html>