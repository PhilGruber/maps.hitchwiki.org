<?php
/* Hitchwiki Maps
 * Geocoder
 * Reverse Geocoder
 * 
 * Examples:
 * geocoder.php?q=Finland
 * geocoder.php?q=Tampere,+Finland&service=nominatim
 * geocoder.php?q=64.363,25.332&service=nominatim_reverse&debug
 *
 * Default service is always geocoder, not reverse geocoder.
 * 
 * File has setup for multiple geocoding services, including our own database -based.
 * These are not all in use around the service, but maintained in here "just in case".
 * 
 * TODO:
 *
 * - Geonames reverse encoder
 * 
 * - If one service fails, jump to the next one: error handling.
 *   Change geocoders to respond false in failure, and output function 
 *   turns fail into an error-json
 *
 * - Class
 *
 */

require_once("../config.php");


header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
#header("Content-type: application/json");


/*
 * Precoded zoom levels
 */ 
$zoomlevels["country"] = '6';
$zoomlevels["city"] = '11';
$zoomlevels["street"] = '14';
	

/* 
 * Geocode by get
 */

if(isset($_GET["q"]) && !empty($_GET["q"])) {

	// Get query
	$q = strip_tags($_GET["q"]);

	$q = urlencode(utf8_encode($q));
	

	// Let's roll...
	switch (strtolower($_GET["service"])) {
	
    	case "nominatim":
    	    echo nominatim_geocode($q);
    	    break;
    	    
    	case "nominatim_reverse":
    	    echo nominatim_reverse($q);
    	    break;
    	    
    	case "google":
    	    echo google($q);
    	    break;
    	    
    	case "tiny_geocoder":
    	    echo tiny_geocode($q);
    	    break;
    	
    	case "hitchwiki":
    		echo hitchwiki_geocode($q);
    		break;
    	
    	case "geonames":
    		echo geonames_geocode($q);
    		break;
    	
    	default:
    	    echo geonames_geocode($q);
    	    break;
	}
}
else {
	echo geocoder_output( array("error" => true) );
	exit;
}



/* 
 * Output
 */
function geocoder_output($data) {
	global $_GET;
	
	// Turn on debug view (human readable output) with get "debug"
	if(isset($_GET["debug"])) return '<pre>'.print_r($data,true).'</pre>';
	
	// By defalt output json
	else return json_encode($data);
}



/* 
 * Tiny Geocoder
 * http://tinygeocoder.com/
 * - Geocode
 */
function tiny_geocode($q) {
	
	$raw = readURL('http://tinygeocoder.com/create-api.php?q='.$q);
	
	if(empty($raw)) $output = array("error"=>true);
	else {
		$latlon = explode(",",$raw);

		$output["lat"] = $latlon[0];
		$output["lon"] = $latlon[1];
		$output["service"] = "tiny geocoder";
	}
	
	return geocoder_output($output);
}

/* 
 * Nominatim
 * http://nominatim.openstreetmap.org
 * Data Copyright OpenStreetMap Contributors, Some Rights Reserved. CC-BY-SA 2.0
 *
 * - Geocode
 <?xml version="1.0" encoding="UTF-8" ?>
<searchresults timestamp='Wed, 02 Jun 10 23:15:15 +0100' attribution='Data Copyright OpenStreetMap Contributors, Some Rights Reserved. CC-BY-SA 2.0.' querystring='tampere,finland' polygon='false' exclude_place_ids='89119,149474,65829569' more_url='http://nominatim.openstreetmap.org/search?format=xml&amp;exclude_place_ids=89119,149474,65829569&amp;accept-language=&amp;q=tampere%2Cfinland'>
<place place_id='89119' osm_type='node' osm_id='30969480' boundingbox="61.3284301757812,61.6241645812988,23.5721302032471,23.9525089263916" lat='61.4979604' lon='23.7593137' display_name='Tampere, Suomi, Europe' class='place' type='city' icon='http://nominatim.openstreetmap.org/images/mapicons/poi_place_city.p.20.png'/><place place_id='149474' osm_type='node' osm_id='34943548' boundingbox="61.4983177185059,61.4985198974609,23.7736911773682,23.7738914489746" lat='61.4984186' lon='23.7737914' display_name='Tampere, 25, Rautatienkatu, Tammela, 33100, Suomi' class='railway' type='station' icon='http://nominatim.openstreetmap.org/images/mapicons/transport_train_station2.p.20.png'/><place place_id='65829569' osm_type='node' osm_id='34943548' boundingbox="61.4983177185059,61.4985198974609,23.7736911773682,23.7738914489746" lat='61.4984186' lon='23.7737914' display_name='Tampere, 25, Rautatienkatu, Tammela, 33100, Suomi' class='place' type='house'/></searchresults>
    [place] => Array
        (
            [0] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [place_id] => 89119
                            [osm_type] => node
                            [osm_id] => 30969480
                            [boundingbox] => 61.3284301757812,61.6241645812988,23.5721302032471,23.9525089263916
                            [lat] => 61.4979604
                            [lon] => 23.7593137
                            [display_name] => Tampere, Suomi, Europe
                            [class] => place
                            [type] => city
                            [icon] => http://nominatim.openstreetmap.org/images/mapicons/poi_place_city.p.20.png
                        )

                )

 */
function nominatim_geocode($q) {
	global $settings;
	
	$xml = readURL('http://nominatim.openstreetmap.org/search?q='.$q.'&format=xml&email='.urlencode($settings["email"]));
	$data = new SimpleXMLElement($xml);
;
	// Loop XML trough and output if we had a result	
	if(isset($data->place[0])) {
		foreach($data->place[0]->attributes() as $a => $b) {
			$output[$a] = $b;
		}		
	}
	else {
		$output["error"] = true;
	}
	
	$output["service"] = "nominatim geocoder";

	return geocoder_output($output);
}



/* 
 * Nominatim
 * http://nominatim.openstreetmap.org
 * Data Copyright OpenStreetMap Contributors, Some Rights Reserved. CC-BY-SA 2.0
 *
 * - Reverse Geocode
 * q: lat,lon
 */
function nominatim_reverse($q) {
	global $settings;

	$q = explode("%2C",$q); // divide from "," - $q is already urlencoded, that's why it's in this format

	$xml = readURL('http://nominatim.openstreetmap.org/reverse?format=xml&lat='.urlencode($q[0]).'&lon='.urlencode($q[1]).'&zoom=18&email='.urlencode($settings["email"]));
	$raw = new SimpleXMLElement($xml);

	if(empty($raw) OR !empty($raw->error)) $output["error"] = true;
	else {

		if(!empty($raw->result)) $output["address"] = (string)$raw->result;
		
		if(!empty($raw->addressparts->road)) $output["road"] = (string)$raw->addressparts->road;
		
		if(!empty($raw->addressparts->postcode)) $output["postcode"] = (string)$raw->addressparts->postcode;
		
		if(!empty($raw->addressparts->city)) $output["locality"] = (string)$raw->addressparts->city;
		elseif(!empty($raw->addressparts->town)) $output["locality"] = (string)$raw->addressparts->town;
		
		if(!empty($raw->addressparts->country_code)) $output["country_code"] = strtoupper($raw->addressparts->country_code);
		
		if(!empty($raw->addressparts->country_code)) $output["country_name"] = ISO_to_country($raw->addressparts->country_code);
		
		$output["lat"] = strip_tags($q[0]);
		$output["lon"] = strip_tags($q[1]);

	}
	$output["service"] = "nominatim reverse geocoder";
	
	return geocoder_output($output);
 
}




/* 
 * Google
 * - Geocode
 * - Reverse geocode
 *
 * Notice, use this only when outputting results into a google map layer: 
 *
 * "The geocoding service may only be used in conjunction with displaying results on a Google map; 
 * geocoding results without displaying them on a map is prohibited."
 *
 * - Terms of service: http://code.google.com/apis/maps/documentation/geocoding/index.html
 *
 *
 */
function google($q) {

	$json = readURL('http://maps.google.com/maps/geo?q='.$q);
	$raw = json_decode($json);
	
	$latlon = explode(",",(string)$raw->name);

	// Re format json
	$output["lat"] = trim($latlon[0]);
	$output["lon"] = trim($latlon[1]);
	$output["address"] = (string)$raw->Placemark[0]->address;
	$output["locality"] = (string)$raw->Placemark[2]->AddressDetails->Country->AdministrativeArea->AddressLine[0];
	$output["country_name"] = (string)$raw->Placemark[0]->AddressDetails->Country->CountryName;
	$output["country_code"] = (string)$raw->Placemark[0]->AddressDetails->Country->CountryNameCode;
	$output["service"] = "google";

	return geocoder_output($output);
}




/* 
 * Geonames
 * http://www.geonames.org/export/geonames-search.html
 * - Geocode
 *
 * Response example:

SimpleXMLElement Object
(
    [@attributes] => Array
        (
            [style] => SHORT
        )

    [totalResultsCount] => 4996
    [geoname] => SimpleXMLElement Object
        (
            [toponymName] => London
            [name] => London
            [lat] => 51.50853
            [lng] => -0.12574
            [geonameId] => 2643743
            [countryCode] => GB
            [fcl] => P
            [fcode] => PPLC
        )

)
 */
function geonames_geocode($q) {
	global $zoomlevels;

	$xml = readURL('http://ws.geonames.org/search?q='.$q.'&maxRows=1&style=SHORT');
	$raw = new SimpleXMLElement($xml);
	
	$latlon = explode(",",$raw->name);

	// Define zoom level by object type
	// http://www.geonames.org/export/codes.html
	if($raw->geoname->fcl == "A") $zoom = $zoomlevels["country"];
	elseif($raw->geoname->fcl == "P") $zoom = $zoomlevels["city"];
	else $zoom = $zoomlevels["street"];
			
		
	$output["lat"] = (string)$raw->geoname->lat;
	$output["lon"] = (string)$raw->geoname->lng;
	$output["locality"] = (string)$raw->geoname->toponymName;
	$output["country_name"] = ISO_to_country((string)$raw->geoname->countryCode);
	$output["country_code"] = (string)$raw->geoname->countryCode;
	$output["zoom"] = $zoom;
	$output["service"] = "geonames";

	return geocoder_output($output);
}



/* 
 * Hitchwiki / lookup from the local DB
 * - Geocode
 */
function hitchwiki_geocode($q) {
	global $settings,$zoomlevels;
	
	start_sql();
	
	// Remove some funny common seperators
	$seperators = array("|",":","/","-",";","+");
	
	$q = str_replace($seperators, ",", $q);
	

	/* TEST-1 
	 * Test if it's a countrycode
	 * * * * * * */
	
	if(strlen($q) == 2) {
		
		$result = mysql_query("SELECT iso, lat, lon FROM `t_countries` WHERE `iso` = LOWER('".mysql_real_escape_string($q)."') LIMIT 1");
		
		if (!$result) die("Error: SQL query failed.");
		
		if(mysql_num_rows($result) >= 1) {
			while ($row = mysql_fetch_array($result)) {
			    $latlon["iso"] = $row["iso"];
			    $latlon["lat"] = $row["lat"];
			    $latlon["lon"] = $row["lon"];
			}
		}
		
	}
	if($latlon) return geocoder_output(
			array(
				"lat" => $latlon["lat"],
				"lon" => $latlon["lon"],
				"country_name" => ISO_to_country($latlon["iso"]),
				"country_code" => $latlon["iso"],
				"zoom" => $zoomlevels["country"],
				"service" => "HW DB/countrycode-list"
			)
		);
	
	
	
	/* TEST-2 
	 * Test if it's a country in some language
	 * * * * * * */
	
	$country = country_to_ISO($q);

	if($country!==false) {
		$latlon = explode("|", getCountryCoords($country));
		return geocoder_output(
			array(
				"lat" => $latlon[0],
				"lon" => $latlon[1],
				"country_name" => ISO_to_country($country),
				"country_code" => $country,
				"zoom" => $zoomlevels["country"],
				"service" => "HW DB/country-list"
			)
		);
	}
	
	
	
	/* TEST-3 
	 * Try to find by city
	 * * * * * * */
	
	$latlon = explode("|", getCityCoords($q));
	if($latlon[0] != "0" && $latlon[1] != "0") {
		return geocoder_output(
			array(
				"lat"=>$latlon[1],
				"lon"=>$latlon[0],
				"zoom" => $zoomlevels["city"],
				"service" => "HW DB/city-list"
			)
		);
	}
	
	
	/* TEST-4 
	 * * * * * * */
	
	// Uh oh! Final try! With online service!
	geonames_geocode($q);

	
}




/*
 * Helper functions for the Hitchwiki geocoder
 * * * * * * * * * * * * * * * * * * * * * * *
 */

/*
 * Look for city coordinates from our own database
 * Original function from Hitchwiki Maps v1 maps-functions.php
 */
function getCityCoords($c) {
    $country = '';
    start_sql();

	$c = urldecode($c);

    if (preg_match('!,!', $c)) {
        list($c, $country) = explode(',', $c);
    }
    $c = mysql_real_escape_string(trim($c));
    if (!empty($country)) {
        $country = country_to_ISO(trim($country));
	}
	
    if (empty($country)) {
        $countryquery = '';
    } else {
        $countryquery = "AND country = '$country'";
	}
	
    $query = "SELECT lat, lng FROM `geo_cities` WHERE LOWER(city) = LOWER('$c') $countryquery";
    $res = mysql_query($query) or die ($query."-".mysql_error());

    if ($r = mysql_fetch_row($res))
        return $r[0]."|".$r[1];

    $query = "SELECT lat, lng, city FROM `geo_cities` WHERE (LOWER(city) LIKE LOWER('%$c%')) $countryquery";
    $res = mysql_query($query) or die ($query."-".mysql_error());

    if ($r = mysql_fetch_row($res))
        return $r[0]."|".$r[1];

    $query = "SELECT lat, lng, city FROM `geo_cities` WHERE (city SOUNDS LIKE '$c') $countryquery";
    $res = mysql_query($query) or die ($query."-".mysql_error());

    if ($r = mysql_fetch_row($res))
        return $r[0]."|".$r[1];

    $retval = "0|0";
    $last = '';
    while ($r = mysql_fetch_row($res)) {
        if (similar_text($last, $c) < similar_text($c, $r[2])) {
            $last = $r[2];
            $retval = $r[0]."|".$r[1];
            
        }
    }
    return $retval; 
}


/*
 * Get map zoom level for the country (with countrycode)
 * Original function from Hitchwiki Maps v1 maps-functions.php
 */
function getCountryZoom($c) {
    start_sql();

    $query = "SELECT zoom FROM `t_countries` WHERE iso='".mysql_escape_string($c)."'";
    $res = mysql_query($query);
    if ($r = mysql_fetch_row($res)) {
        return $r[0];
    } else {
        return false;
    }
}


/*
 * Get lat,lon for the country (with countrycode)
 * Original function from Hitchwiki Maps v1 maps-functions.php
 */
function getCountryCoords($c) {
    start_sql();
    
    $query = "SELECT lat, lon FROM `t_countries` WHERE iso='".mysql_escape_string($c)."'";
    $res = mysql_query($query) or die ($query."-".mysql_error());
    if ($r = mysql_fetch_row($res)) {
        return $r[0]."|".$r[1];
    } else {
        return false;
    }
}

?>