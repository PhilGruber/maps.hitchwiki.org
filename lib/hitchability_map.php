<?php
/*
 * Load config to set language and stuff
 */
require_once "../config.php";

?><html>
  <head>
    <script type='text/javascript' src='http://www.google.com/jsapi'></script>

<?php if(isset($_GET["map"]) && $_GET["map"] == "1"): ?>

<?php
	// Gather data
	$data = list_countries();
?>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['intensitymap']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', '', 'Country');
        data.addColumn('number', 'Hitchability', 'a');
        data.addColumn('number', 'Places', 'b');
        data.addRows(<?php echo count($data); ?>);
      <?php

	  // Spread it out
      foreach($data as $i => $country) {
      		echo "\t data.setValue(".$i.", 0, '".$country[0]."');\n";
      		echo "\t data.setValue(".$i.", 1, ".rand(1,5).");\n";
      		echo "\t data.setValue(".$i.", 2, ".$country[1].");\n\n";
      }
      ?>

        var chart = new google.visualization.IntensityMap(document.getElementById('map_canvas'));

		var color_arr = new Array('#eb7f00','#eb7f00');
        chart.draw(data, {width: 440, height: 220, colors: color_arr});
      }
    </script>

 <pre><?php print_r($countrylist2); ?></pre>

<?php elseif(isset($_GET["map"]) && $_GET["map"] == "2"): ?>

<?php
	// Gather data
	$data = list_countries();
	$codes = countrycodes();
?>
  <script type='text/javascript'>
   google.load('visualization', '1', {'packages': ['geomap']});
   google.setOnLoadCallback(drawMap);

    function drawMap() {
      var data = new google.visualization.DataTable();
      data.addRows(<?php echo count($data); ?>);
      data.addColumn('string', 'Country');
      data.addColumn('number', 'Hitchability');
      <?php

	  // Spread it out
      foreach($data as $i => $country) {

      	echo "\t data.setValue(".$i.", 0, '".$codes[$country[0]]."');\n";
      	echo "\t data.setValue(".$i.", 1, ".rand(1,5).");\n\n";

      }
      ?>

      var options = {};
      options['dataMode'] = 'regions';
      options['width'] = '800';
      options['height'] = '400';

      var container = document.getElementById('map_canvas');
      var geomap = new google.visualization.GeoMap(container);
      geomap.draw(data, options);
  };
  </script>

<?php elseif(isset($_GET["map"]) && $_GET["map"] == "3"): ?>

<?php
	// Gather data
	$data = list_countries();
	$codes = countrycodes();
?>

  <script type='text/javascript'>
   google.load('visualization', '1', {'packages': ['geomap']});
   google.setOnLoadCallback(drawMap);

    function drawMap() {
      var data = new google.visualization.DataTable();
      data.addRows(<?php echo count($data); ?>);
      data.addColumn('string', 'Country');
      data.addColumn('number', 'Places');
      <?php

	  // Spread it out
      foreach($data as $i => $country) {

      	echo "\t data.setValue(".$i.", 0, '".$codes[$country[0]]."');\n";
      	echo "\t data.setValue(".$i.", 1, ".$country[1].");\n\n";

      }
      ?>
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