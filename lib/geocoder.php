<?php
/* Hitchwiki Maps
 * Geocoder
 * Reverse Geocoder
 * 
 * Example:
 * geocoder.php?q=Finland or geocoder.php?q=Tampere,+Finland&service=nominatim
 */
 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past


/* 
 * Geocode
 */
if(isset($_GET["q"]) && !empty($_GET["q"])) {

	// Get query
	$q = urlencode(strip_tags($_GET["q"]));

	// Let's roll...
	switch (strtolower($_GET["service"])) {
	
    	case "nominatim":
    	    echo nominatim($q);
    	    break;
    	    
    	case "google":
    	    echo google($q);
    	    break;
    	    
    	case "tiny_geocoder":
    	    echo tiny_geocoder($q);
    	    break;
    	    
    	default:
    	    echo tiny_geocoder($q);
    	    break;
	}
}
else exit;


/* 
 * cURL
 * Requires http://curl.haxx.se/
 */
function readURL($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	curl_close($ch);
	
	return $data;
}


/* 
 * Tiny Geocoder
 * http://tinygeocoder.com/
 * - Geocode
 * - Reverse Geocode
 */
function tiny_geocoder($q, $reverse=false) {
	if($reverse == true) {
		return readURL('http://tinygeocoder.com/create-api.php?g='.$q);
	}
	else {
		return readURL('http://tinygeocoder.com/create-api.php?q='.$q);
	}
}


/* 
 * Nominatim
 * http://wiki.openstreetmap.org/wiki/Nominatim
 * - Geocode
 * - Format: json|xml|html
 */
function nominatim($q) {
	return readURL('http://nominatim.openstreetmap.org/search?q='.$q.'&format=json&email=help@liftershalte.info');
}


/* 
 * Google
 * - Geocode
 * - Reverse geocode
 */
function google($q) {
	return readURL('http://maps.google.com/maps/geo?q='.$q);
}


?>