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
#header("Content-type: application/json");


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
    	    echo nominatim($q);
    	    break;
	}
}
else {
	echo '{error:"true"}';
	exit;
}

/* 
 * cURL
 * Requires http://curl.haxx.se/
 */
function readURL($url) {
	global $_GET;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	curl_close($ch);
	
	return $data;
}


/* 
 * Preview
 */
function preview($data) {
	global $_GET;
	
	if(isset($_GET["preview"])) {
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
	$raw = readURL('http://tinygeocoder.com/create-api.php?q='.$q);
	$latlon = explode(",",$raw);

	$data = '{"lat":"'.$latlon[0].'","lon":"'.$latlon[1].'"}';
	
	return preview($data);
}

/* 
 * Nominatim
 * http://nominatim.openstreetmap.org
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
	$xml = readURL('http://nominatim.openstreetmap.org/search?q='.$q.'&format=xml&email=help@liftershalte.info');
	$data = new SimpleXMLElement($xml);

	$return = '{';
	foreach($data->place[0]->attributes() as $a => $b) {
		$return .= '"'.$a.'":"'.$b.'",';
	}
	$return = substr($return, 0, -1); // take last , away
	$return .= '}';
  
	return preview($return);
}


/* 
 * Google
 * - Geocode
 * - Reverse geocode
 */
function google($q) {

	$return = readURL('http://maps.google.com/maps/geo?q='.$q);
	$raw = json_decode($return);
	
	$latlon = explode(",",$raw->name);
	
	$data = '{';
	$data .= 	'"lat":"'.$latlon[0].'","lon": "'.$latlon[1].'",';
	$data .= 	'"address":"'.$raw->Placemark[0]->address.'",';
	$data .= 	'"country_name":"'.$raw->Placemark[0]->AddressDetails->Country->CountryName.'",';
	$data .= 	'"country_code":"'.$raw->Placemark[0]->AddressDetails->Country->CountryNameCode.'"';
	$data .= '}';

	return $data;
}


?>