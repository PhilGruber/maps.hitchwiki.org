<?php
/*
 * Hitchwiki Maps: country.php
 * Show information about countries
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";


/* 
 * Gather data
 */
if(isset($_GET["country"]) && strlen($_GET["country"]) == 2) {

	// Check if language is valid (if not, use default)
	// Settings comes from config.php
	if(isset($_GET["lang"]) && !empty($settings["valid_languages"][$_GET["lang"]])) $lang = $_GET["lang"];
	else $lang = $settings["language"];
		
	$country["iso"] = 			htmlspecialchars(strtoupper($_GET["country"]));
	$country["name"] = 			ISO_to_country($country["iso"], $lang);
	$country["places"] = 		total_places($country["iso"]);
	$country["hitchability"] = 	"#";
	
	$xmlstr = readURL("http://ws.geonames.org/countryInfo?lang=".shortlang($lang)."&country=".$country["iso"]);
	$xml = new SimpleXMLElement($xmlstr);

	$country["capital"] = (string)$xml->country->capital;
	$country["areaInSqKm"] = (string)$xml->country->areaInSqKm;
	$country["population"] = (string)$xml->country->population;
	$country["currencyCode"] = (string)$xml->country->currencyCode;
	$country["continent"] = (string)$xml->country->continent;
	
	$country["cities"] = 		list_cities("array", "markers", false, true, $country["iso"]);
	$country["cities_count"] = 	count($country["cities"]);

}
else {
	echo 'Choose country.';
	exit;
}

/* 
 * Choose format
 */
if(isset($_GET["format"]) && strtolower($_GET["format"]) == "json") $format = "json";
elseif(isset($_GET["format"]) && strtolower($_GET["format"]) == "html") $format = "html";
else $format = "array";

/* 
 * Print it out in:
 * json (default) | html | array (debugging)
 */
 
// Array
if($format == 'array') {

	echo '<pre>';
	print_r($country);
	echo '</pre>';

}
elseif($format == 'json') {

	echo json_encode($country);

} 
// HTML
elseif($format == 'html') {
?>

<div class="align_left" style="margin: 0 40px 20px 0;">

	<h3><?php echo _("About"); ?></h3>
	
	<table class="infotable" cellspacing="0" cellpadding="0">
	    <tbody>
	    	<?php /*
	    	<tr>
	    		<td><b><?php echo _("Hitchability"); ?></b></td>
	    		<td><?php echo $country["hitchability"]; ?></td>
	    	</tr>
	    	*/ ?>
	    	<tr>
	    		<td><b><?php echo _("Places in Maps"); ?></b></td>
	    		<td><?php echo $country["places"]; ?></td>
	    	</tr>
	    	<tr>
	    		<td><b><?php echo _("Cities in Maps"); ?></b></td>
	    		<td><?php echo $country["cities_count"]; ?></td>
	    	</tr>
	    	<tr>
	    		<td><b><?php echo _("Capital"); ?></b></td>
	    		<td><?php echo $country["capital"]; ?></td>
	    	</tr>
	    	<tr>
	    		<td><b><?php echo _("Continent"); ?></b></td>
	    		<td><?php echo continent_name($country["continent"]); ?></td>
	    	</tr>
	    	<tr>
	    		<td><b><?php echo _("Area"); ?></b></td>
	    		<td><?php echo $country["areaInSqKm"]; ?> <sup>2</sup>km</td>
	    	</tr>
	    	<tr>
	    		<td><b><?php echo _("Population"); ?></b></td>
	    		<td><?php echo $country["population"]; ?></td>
	    	</tr>
	    	<tr>
	    		<td><b><?php echo _("Currency"); ?></b></td>
	    		<td><?php echo $country["currencyCode"]; ?></td>
	    	</tr>
	    	<tr>
	    		<td colspan="2"><small>
	    			<a target="_blank" href="http://hitchwiki.org/en/index.php?title=Special%3ASearch&search=<?php echo urlencode($country["name"]); ?>&go=Go">Hitchwiki</a>, 
	    			<a target="_blank" href="http://en.wikipedia.org/wiki/Special:Search?search=<?php echo urlencode($country["name"]); ?>">Wikipedia</a>, 
	    			<a target="_blank" href="http://wikitravel.org/en/Special:Search?search=<?php echo urlencode($country["name"]); ?>&go=Go">Wikitravel</a>, 
	    			<a target="_blank" href="http://www.couchsurfing.org/statistics.html?country_name=<?php echo urlencode($country["name"]); ?>">CouchSurfing</a>
	    		</small></td>
	    	</tr>
	    	
	    </tbody>
	</table>
	
</div>

<?php if(!empty($country["cities_count"])): ?>
<div class="align_left" style="margin: 0 40px 20px 0;">

	<h3><?php 
	
	if($country["cities_count"] < 10) $top = $country["cities_count"];
	else $top = "10";
	
	printf( _( 'Top %s cities' ), $top ); ?></h3>
	<table class="infotable" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo _("City"); ?></th>
				<th><?php echo _("Places"); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php list_cities("tr", "markers", 10, true, $country["iso"]); ?>
		</tbody>
	</table>
	
</div>
<?php endif; ?>

<div class="align_left" style="margin: 0 0 20px 0;">
	
	<h3><img class="flag" alt="<?php echo $country["iso"]; ?>" src="static/gfx/flags/png/<?php echo strtolower($country["iso"]); ?>.png" /> <?php echo $country["name"]; ?></h3>

	<!-- http://code.google.com/apis/visualization/documentation/gallery/geomap.html -->
	<iframe src="ajax/map_statistics.php?map=<?php
	
	/*
	 * To keep loadingtimes shorter we use two versions from this map. 
	 * Another (4) loads all cities to the map, and another (5) shows only the country
	 *
	 * Current limit for this is now 30 cities
	 */
	
	if(empty($country["cities_count"])) echo '5';
	elseif($country["cities_count"] <= 30) echo '4';
	else {
		echo '5';
		$mapLimit = true;
	}
	?>&country=<?php echo $country["iso"]; ?>" name="countrymap" id="countrymap" width="560" height="350" border="0" style="border:0;"></iframe>
	
	<?php if($mapLimit): ?><small id="show_map_with_cities"><br /><a onclick="$('#show_map_with_cities').html('<br /><i>Map started to load. This might take some time.</i>').delay(10000).fadeOut(1000);" href="ajax/map_statistics.php?map=4&country=<?php echo $country["iso"]; ?>" target="countrymap"><?php echo _("Show cities on the map"); ?> (<?php echo _("Experimental, might be slow."); ?>)</a></small><?php endif; ?>

</div>
<div class="clear"></div>

<h3><?php echo _("Public transport"); ?></h3>
<?php pt_list($country["iso"]); ?>

<?php
}

?>