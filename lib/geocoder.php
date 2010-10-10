<?php
/* Hitchwiki Maps
 * Geocoder
 * Reverse Geocoder
 * 
 * Example:
 * geocoder.php?q=Finland or geocoder.php?q=Tampere,+Finland&service=nominatim
 */

require_once("../config.php");


header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
#header("Content-type: application/json");


/* 
 * Geocode
 */

if(isset($_GET["q"]) && !empty($_GET["q"])) {

	// Get query
	$q = strip_tags($_GET["q"]);

	$q = urlencode(utf8_encode($q));
	

	// Let's roll...
	switch (strtolower($_GET["service"])) {
	
    	case "nominatim":
    	    echo nominatim($q);
    	    break;
    	    
    	case "nominatim_reverse":
    	    echo nominatim_reverse($q);
    	    break;
    	    
    	case "google":
    	    echo google($q);
    	    break;
    	    
    	case "tiny_geocoder":
    	    echo tiny_geocoder($q);
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
	echo '{error:"true"}';
	exit;
}



/* 
 * Preview
 */
function preview($data) {
	global $_GET;
	
	if(isset($_GET["debug"])) {
		$return = '<pre>';
		$return .= print_r(json_decode($data),true);
		$return .= '</pre>';
		return $return;
	}
	else return $data;
}



/* 
 * Tiny Geocoder
 * http://tinygeocoder.com/
 * - Geocode
 */
function tiny_geocoder($q) {
	$raw = readURL('http://tinygeocoder.com/create-api.php?q='.urlencode($q));
	$latlon = explode(",",$raw);

	$data = '{"lat":"'.$latlon[0].'","lon":"'.$latlon[1].'","service": "tiny geocoder"}';
	
	return preview($data);
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
function nominatim($q) {
	global $settings;
	
	$xml = readURL('http://nominatim.openstreetmap.org/search?q='.$q.'&format=xml&email='.urlencode($settings["email"]));
	$data = new SimpleXMLElement($xml);

	$return = '{';
	
	if(isset($data->place[0])) {
		foreach($data->place[0]->attributes() as $a => $b) {
			$return .= '"'.$a.'":"'.$b.'",';
		}
		#$return = substr($return, 0, -1); // take last , away
		
	}
	else {
		$return .= '"error":true';
	}
	
  		$return .= ',"service": "nominatim"';
  		
	$return .= '}';
  
	return preview($return);
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

	$q = explode("%2C",$q); // divide from ","

	$xml = readURL('http://nominatim.openstreetmap.org/reverse?format=xml&lat='.urlencode($q[0]).'&lon='.urlencode($q[1]).'&zoom=18&email='.urlencode($settings["email"]));
	$raw = new SimpleXMLElement($xml);

	if(empty($raw) OR !empty($raw->error)) $data["error"] = true;
	else {

		if(!empty($raw->result)) $data["address"] = (string)$raw->result;
		
		if(!empty($raw->addressparts->road)) $data["road"] = (string)$raw->addressparts->road;
		
		if(!empty($raw->addressparts->postcode)) $data["postcode"] = (string)$raw->addressparts->postcode;
		
		if(!empty($raw->addressparts->city)) $data["locality"] = (string)$raw->addressparts->city;
		elseif(!empty($raw->addressparts->town)) $data["locality"] = (string)$raw->addressparts->town;
		
		if(!empty($raw->addressparts->country_code)) $data["country_code"] = strtoupper($raw->addressparts->country_code);
		
		if(!empty($raw->addressparts->country_code)) $data["country_name"] = ISO_to_country($raw->addressparts->country_code);
		
		$data["lat"] = strip_tags($q[0]);
		$data["lon"] = strip_tags($q[1]);

	}
	
	return json_encode($data);
 
}




/* 
 * Google
 * - Geocode
 * - Reverse geocode
 */
function google($q) {

	$return = readURL('http://maps.google.com/maps/geo?q='.urlencode($q));
	$raw = json_decode($return);
	
	$latlon = explode(",",$raw->name);

	#return print_r($raw,true);

$data = '{
  "lat": "'.$latlon[0].'",
  "lon": "'.$latlon[1].'",
  "address": "'.$raw->Placemark[0]->address.'",
  "locality": "'.$raw->Placemark[2]->AddressDetails->Country->AdministrativeArea->AddressLine[0].'",
  "country_name": "'.$raw->Placemark[0]->AddressDetails->Country->CountryName.'",
  "country_code": "'.$raw->Placemark[0]->AddressDetails->Country->CountryNameCode.'",
  "service": "google"
}';
	return preview($data);
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

	$xml = readURL('http://ws.geonames.org/search?q='.urlencode($q).'&maxRows=1&style=SHORT');
	$raw = new SimpleXMLElement($xml);
	
	$latlon = explode(",",$raw->name);

	// Define zoom level by object type
	// http://www.geonames.org/export/codes.html
	if($raw->geoname->fcl == "A") $zoom = '6';
	elseif($raw->geoname->fcl == "P") $zoom = '11';
	else $zoom = '14';
		

$data = '{
  "lat": "'.$raw->geoname->lat.'",
  "lon": "'.$raw->geoname->lng.'",
  "locality": "'.$raw->geoname->toponymName.'",
  "country_name": "'.ISO_to_country($raw->geoname->countryCode).'",
  "country_code": "'.$raw->geoname->countryCode.'",
  "zoom": "'.$zoom.'",
  "service": "geonames"
}';
	return preview($data);
}



/* 
 * Hitchwiki / lookup from the local DB
 * - Geocode
 */
function hitchwiki_geocode($q) {
	global $settings;
	
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
	if($latlon) return preview('{"lat": "'.$latlon["lat"].'", "lon": "'.$latlon["lon"].'","service":"HW DB/countrycode-list"}');
	
	
	
	/* TEST-2 
	 * Test if it's a country in some language
	 * * * * * * */
	
	$country = country_to_ISO($q);

	if($country!==false) {
		$latlon = explode("|", getCountryCoords($country));
		return preview('{"lat": "'.$latlon[0].'", "lon": "'.$latlon[1].'","service":"HW DB/country-list"}');
	}
	
	
	
	/* TEST-3 
	 * Try to find by city
	 * * * * * * */
	
	$latlon = explode("|", getCityCoords($q));
	if($latlon[0] != "0" && $latlon[1] != "0") {
		return preview('{"lat": "'.$latlon[1].'", "lon": "'.$latlon[0].'","service":"HW DB/city-list"}');
	}
	
	
	/* TEST-4 
	 * * * * * * */
	
	// Uh oh! Final try!
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
    $t_cities = "geo_cities";
    $country = '';
    start_sql();

	#$c = str_replace("%2C",",",$c);
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
	
    $query = "SELECT lat, lng FROM $t_cities WHERE LOWER(city) = LOWER('$c') $countryquery";
    $res = mysql_query($query) or die ($query."-".mysql_error());

    if ($r = mysql_fetch_row($res))
        return $r[0]."|".$r[1];

    $query = "SELECT lat, lng, city FROM $t_cities WHERE (LOWER(city) LIKE LOWER('%$c%')) $countryquery";
    $res = mysql_query($query) or die ($query."-".mysql_error());

    if ($r = mysql_fetch_row($res))
        return $r[0]."|".$r[1];

    $query = "SELECT lat, lng, city FROM $t_cities WHERE (city SOUNDS LIKE '$c') $countryquery";
    $res = mysql_query($query) or die ($query."-".mysql_error());

    if ($r = mysql_fetch_row($res))
        return $r[0]."|".$r[1];

    #$retval = "48.873663314036996|2.2950804233551025";
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
 */
function getCountryZoom($c) {

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
 */
function getCountryCoords($c) {
    global $t_countries;
    $query = "SELECT lat, lon FROM `t_countries` WHERE iso='".mysql_escape_string($c)."'";
    $res = mysql_query($query) or die ($query."-".mysql_error());
    if ($r = mysql_fetch_row($res)) {
        return $r[0]."|".$r[1];
    } else {
        return false;
    }
}

?>