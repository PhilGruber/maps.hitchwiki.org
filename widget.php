<?php

/*
 * Initialize Maps
 */
if(@is_file('config.php')) require_once "config.php";
else $settings["maintenance_page"] = true;


/*
 * Put up a maintenance -sign
 * Set it up from config.php or test it from ./?maintenance
 */
if(isset($_GET["maintenance"])) $settings["maintenance_page"] = true;
if($settings["maintenance_page"]===true && !in_array($_SERVER['REMOTE_ADDR'], $settings["non_maintenance_ip"])) {
	include("maintenance_page.php");
	exit;
}


/*
 * Map settings
 */
 
// Show a country
if(isset($_GET["country"]) && !empty($_GET["country"]) && strlen($_GET["country"]) == 2) {
	$zoom = '9';
	$lat = '51';
	$lon = '9';
}
// Show free spot
else { 
	// Zoom, lat, lon, layers
	$zoom = (isset($_GET["zoom"]) && ctype_digit($_GET["zoom"])) ? $_GET["zoom"] : '9';
	
	// Centered to Germany (51,9). Projection center would be '49','8.3'
	$lat = (isset($_GET["lat"]) && is_numeric($_GET["lat"])) ? $_GET["lat"] : '51';
	$lon = (isset($_GET["lon"]) && is_numeric($_GET["lon"])) ? $_GET["lon"] : '9';
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo shortlang(); ?>">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
		<title>Hitchwiki - <?php echo _("Maps"); ?></title>

        <link rel="stylesheet" type="text/css" href="static/css/widget.css<?php if($settings["debug"]==true) echo '?cache='.date("jnYHis"); ?>" media="all" />

        <script src="static/js/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="http://openlayers.org/api/OpenLayers.js" type="text/javascript" type="text/javascript"></script>
        <script type="text/javascript">
		//<![CDATA[

			/*
			 * Default map settings
			 */
			var lat = <?php echo $lat; ?>;
			var lon = <?php echo $lon; ?>;
			var zoom = <?php echo $zoom; ?>;

			var read_more_txt = '<?php echo _("Read more..."); ?>';

		//]]>
        </script>
		<script src="static/js/widget.js<?php if($settings["debug"]==true) echo '?cache='.date("jnYHis"); ?>" type="text/javascript"></script>
		<link rel="shortcut icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />
		<link rel="bookmark icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />

		<meta name="description" content="<?php printf(_("This is just a preview map. Go to %s for actual service."), $settings["base_url"]."/"); ?>" />
	</head>
	<body>

	    <small id="loading-bar"><?php echo _("Loading..."); ?></small>

		<div id="map">
			<br /><br />
			<?php echo _("Loading..."); ?>
		</div>

		<ul id="log" style="display:none;"></ul>

	</body>
</html>