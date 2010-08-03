<?php
/*
 * Hitchwiki Maps: map_statistics.php
 *
 */
 
/*
 * Load config to set language and stuff
 */
require_once "../config.php";


?><html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo substr($settings["language"], 0, 2); /* ISO_639-1 ('en_UK' => 'en') */ ?>">
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <title>Hitchwiki - <?php echo _("Maps"); ?> - Statistics in maps</title>

    <script type="text/javascript" src="http://www.google.com/jsapi"></script>

	<style type="text/css">
	body,html {
		margin: 0;
		padding: 0;
	}
	</style>
<?php


/*
 * World map with places/country and hitchability/country - tabbed
 * http://code.google.com/apis/visualization/documentation/gallery/intensitymap.html
 */
if(isset($_GET["map"]) && $_GET["map"] == "1"): ?>

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
        data.addColumn('number', '<?php echo _("Hitchability"); ?>', 'a');
        data.addColumn('number', '<?php echo _("Places"); ?>', 'b');
        data.addRows(<?php echo count($data); ?>);
      <?php

	  // Spread it out
	  $i=0;
      foreach($data as $country) {
      	echo "\t data.setValue(".$i.", 0, '".$country["iso"]."');\n";
      	echo "\t data.setValue(".$i.", 1, ".rand(1,5).");\n";
      	echo "\t data.setValue(".$i.", 2, ".$country["places"].");\n\n";
		$i++;
      }
      ?>
		
        var chart = new google.visualization.IntensityMap(document.getElementById('map_canvas'));
        
		var color_arr = new Array('#eb7f00','#eb7f00');
        chart.draw(data, {width: 440, height: 220, colors: color_arr});
      }
    </script>

<?php 


/*
 * World map with hitchability/country
 * http://code.google.com/apis/visualization/documentation/gallery/geomap.html
 */
elseif(isset($_GET["map"]) && $_GET["map"] == "2"): ?>

<?php 
	// Gather data
	$data = list_countries();
	$codes = countrycodes("code", "en_UK");
?>
  <script type='text/javascript'>
   google.load('visualization', '1', {'packages': ['geomap']});
   google.setOnLoadCallback(drawMap);

    function drawMap() {
      var data = new google.visualization.DataTable();
      data.addRows(<?php echo count($data); ?>);
      data.addColumn('string', 'Country');
      data.addColumn('number', '<?php echo _("Hitchability"); ?>');
      <?php

	  // Spread it out
	  $i=0;
      foreach($data as $country) {

      	echo "\t data.setValue(".$i.", 0, '".$codes[$country["iso"]]."');\n";
      	echo "\t data.setValue(".$i.", 1, ".rand(1,5).");\n\n";
		$i++;
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

<?php 


/*
 * World map with places/country
 * http://code.google.com/apis/visualization/documentation/gallery/geomap.html
 */
elseif(isset($_GET["map"]) && $_GET["map"] == "3"): ?>

<?php 
	// Gather data
	$data = list_countries();
	$codes = countrycodes("code", "en_UK");
?>

  <script type='text/javascript'>
   google.load('visualization', '1', {'packages': ['geomap']});
   google.setOnLoadCallback(drawMap);

    function drawMap() {
      var data = new google.visualization.DataTable();
      data.addRows(<?php echo count($data); ?>);
      data.addColumn('string', 'Country');
      data.addColumn('number', '<?php echo _("Places"); ?>');
      <?php

	  // Spread it out
	  $i=0;
      foreach($data as $country) {

      	echo "\t data.setValue(".$i.", 0, '".$codes[$country["iso"]]."');\n";
      	echo "\t data.setValue(".$i.", 1, ".$country["places"].");\n\n";

		$i++;
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


<?php 


/*
 * Countrymap with cities
 * http://code.google.com/apis/visualization/documentation/gallery/geomap.html
 */
elseif(isset($_GET["map"]) && $_GET["map"] == "4" && isset($_GET["country"]) && strlen($_GET["country"]) == 2): ?>

<?php
	// Gather data
	$data = list_cities("array", "markers", false, true, htmlspecialchars(strtoupper($_GET["country"])))
?>
  <script type='text/javascript'>
   google.load('visualization', '1', {'packages': ['geomap']});
   google.setOnLoadCallback(drawMap);

    function drawMap() {
      var data = new google.visualization.DataTable();
      data.addRows(<?php echo count($data); ?>);
      data.addColumn('string', 'City');
      data.addColumn('number', '<?php echo _("Places"); ?>');

      <?php
      
      $country = ISO_to_country(strtoupper($_GET["country"]));
      /*
      TODO: we need something like this (lat/lon):
      
dataTable = new google.visualization.DataTable();
dataTable.addRows(1);
dataTable.addColumn('number', 'LATITUDE', 'Latitude');
dataTable.addColumn('number', 'LONGITUDE', 'Longitude');
dataTable.addColumn('number', 'VALUE', 'Value'); // Won't use this column, but still must define it.
dataTable.addColumn('string', 'HOVER', 'HoverText');

dataTable.setValue(0,0,47.00);
dataTable.setValue(0,1,-122.00);
dataTable.setValue(0,3,"Hello World!");

      */
	  // Spread it out
	  $i=0;
      foreach($data as $city) {

      	echo "\t data.setValue(".$i.", 0, '".utf8_encode($city["city"]).", ".$country."');\n";
      	echo "\t data.setValue(".$i.", 1, ".$city["places"].");\n\n";

		$i++;
      }
      
      ?>

      var options = {};
      options['region'] = '<?php echo htmlspecialchars(strtoupper($_GET["country"])); ?>';
      options['colors'] = [0xFF8747, 0xFFB581, 0xc06000]; //orange colors
      options['dataMode'] = 'markers';

      var container = document.getElementById('map_canvas');
      var geomap = new google.visualization.GeoMap(container);
      geomap.draw(data, options);
    };
  
  </script>

<?php 


/*
 * Countrymap
 */
elseif(isset($_GET["map"]) && $_GET["map"] == "5" && isset($_GET["country"])): /* && strlen($_GET["country"]) == 2*/?>

  <script type='text/javascript'>
   google.load('visualization', '1', {'packages': ['geomap']});
   google.setOnLoadCallback(drawMap);

    function drawMap() {
      var data = new google.visualization.DataTable();
      data.addRows(0);
      data.addColumn('string', 'City');

      var options = {};
      options['region'] = '<?php echo htmlspecialchars(strtoupper($_GET["country"])); ?>';
      options['colors'] = [0xFF8747, 0xFFB581, 0xc06000]; //orange colors
      options['dataMode'] = 'markers';
	  options['showLegend'] = false;
	  
      var container = document.getElementById('map_canvas');
      var geomap = new google.visualization.GeoMap(container);
      geomap.draw(data, options);
    };
  
  </script>
<?php endif; ?>

	<meta name="description" content="<?php printf(_("This is just a preview map. Go to %s for actual service."), $settings["base_url"]."/"); ?>" />
</head>
	<body><div id="map_canvas"></div></body>
</html>