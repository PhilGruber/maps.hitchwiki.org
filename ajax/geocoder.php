<?php
/* 
 * @package    maps_geocode
 * @author     Mikael Korpela <mikael@ihminen.org>
 * @copyright  Copyright (c) 2010 {@link http://www.ihminen.org Mikael Korpela}
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported
 *
 *
 * Geocoder and Reverse Geocoder for {@link http://maps.hitchwiki.org/ Hitchwiki Maps}
 * 
 * Examples:
 * - geocoder.php?q=Finland
 * - geocoder.php?q=Tampere,+Finland&service=nominatim
 * - geocoder.php?q=Tampere,+Finland&service=nominatim&debug
 * - geocoder.php?q=64.363,25.332&service=nominatim&mode=reverse
 *
 * The default servicemode used is always geocoder, not reverse geocoder.
 * 
 * File has setup for multiple geocoding services, including our own database -based.
 * These are not all in use around the service, but maintained in here "just in case".
 * 
 * Class requires ./config.php to be loaded
 *
 * Created: 2010-10-16
 */

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
#header("Content-type: application/json");

require_once("../config.php");


/*
 * Initialize
 */ 
$geocoder = new maps_geocode();

// Geocode only on query
if(isset($_GET["q"]) && !empty($_GET["q"])) {

	// Change from geocode(default) to reverse geocode
	if(isset($_GET["mode"]) && $_GET["mode"] == "reverse") $geocoder->set_mode("reverse_geocode");

	// Set service, default is first one from the services list defined in class
	if(isset($_GET["service"]) && !empty($_GET["service"])) $geocoder->set_service($_GET["service"]);
	
	// Querry
	echo $geocoder->geocode( urlencode(utf8_encode(strip_tags($_GET["q"]))) );
	
}
// If no querry, echo error
else echo $geocoder->geocoder_output(false);



/*
 * GEOCODER CLASS
 */ 
class maps_geocode
{

	/*
	 * Default settings
	 */ 
	public $history = 	false; // true|false - keep search history?
	public $mode = 		"geocode"; // default mode (geocode | reverse_geocode)
	public $zoomlevels = array(
							"country" 	=> '6',
							"city" 		=> '11',
							"street" 	=> '14'
						);

	// Add services in use to here. First one will be default
	public $services = array(
	    "geocode" => array(
	    	"geonames_geocode",
	    	"nominatim_geocode",
	    	"tiny_geocode",
	    	"hitchwiki_geocode"
	    	//"google_geocode"
	    ),
	    "reverse_geocode" => array(
	    	"nominatim_reverse",
	    	"geonames_reverse"
	    	//"google_reverse"
	    )
	);
	
	public $service;
	

	/*
	 * Construct
	 */
	public function __construct($mode=false, $service=false) {
		
		if($mode!==false) $this->set_mode($mode);
		
		$this->set_service($service);
		
		return true;
	}


	/**
	 * Set service
	 */
	public function set_service($service=false) {
	
		// Services left?
		if(!empty($this->services[$this->mode])) {
		
			// If given service is false (we might want to set up default with this function)
			// Sets it to default
			if($service===false) $this->service = current($this->services[$this->mode]);
			
			// Check if requested service is valid (in our array)
			else {		
				$service = strtolower($service);
				$service_key = array_search($service, $this->services[$this->mode]);
				$this->service = ( isset( $this->services[$this->mode][$service_key] ) ) ? $service: current(reset($this->services[$this->mode]));
			}
		}
		else $this->service = false;
	}
	
	
	/**
	 * Unset service
	 */
	public function unset_service($service) {
		
		// Find service from service list
		$remove_key = array_search(strtolower($service), $this->services[$this->mode]);
		// If found, remove it
		if($remove_key!==false) {
			// If found, remove it
			unset($this->services[$this->mode][$remove_key]);
			return true;
		}
		else return false;
	}
	

	/**
	 * Set mode
	 */
	public function set_mode($mode) {
		if($mode == "geocode" OR $mode == "reverse_geocode") {
			if($this->mode != $mode) {
				
				$this->mode = $mode;
				
				// We need to select service again, force to default:
				$this->set_service();
			}
		}
	}
	
	
	/*
	 * Log search history
	 */
	public function log($q="", $r=false) {
		// Keep history log?
		if($this->history == true) {
		
	    	start_sql();
	    	
			// Clean q
			$q = (!empty($q)) ? "'".mysql_real_escape_string(strtolower($q))."'": "NULL";
		
			// Flag 1 if had a result
			$r = ($r) ? "1": "NULL";
		
			// To the DB
	    	mysql_query("INSERT INTO `t_search_history` (`timestamp`,`q`,`result`) VALUES (CURRENT_TIMESTAMP,".$q.",".$r.")");
		}
	}
	
	
	/* 
	 * Output
	 */
	public function geocoder_output($data) {
		global $_GET;
		
		if($data === false OR empty($data) OR $data == "error") $data = array("error"=>true);
		
		// Turn on debug view (human readable output) with get "debug"
		if(isset($_GET["debug"])) return '<pre>'.print_r($data,true).'</pre>';
		
		// By defalt output json
		else return json_encode($data);
	}
	
	
	/*
	 * Geocode loop
	 */
	public function geocode($q) {
	
		// If reverse geocoding, validate querry
		$latlon = explode(",", $q);
		if(!validate_lat($latlon[0]) && !validate_lon($latlon[1])) return $this->geocoder_output(false);
	
	
		// Let's roll...
		switch($this->service) {
    	    
			// Geocoders
			case "geonames_geocode":
				$geocode = $this->geonames_geocode($q);
				break;
    	    
    	    case "geonames_reverse":
    	    	$geocode = $this->geonames_reverse($q);
    	    	break;
    	    
			case "nominatim_geocode":
				$geocode = $this->nominatim_geocode($q);
				break;
    	    
			case "tiny_geocode":
				$geocode = $this->tiny_geocode($q);
				break;
    	    
			case "hitchwiki_geocode":
				$geocode = $this->hitchwiki_geocode($q);
				break;
    	    
			case "google_geocode":
				$geocode = $this->google($q);
				break;
    	    
			// Reverse geocoders
			case "google_reverse":
				$geocode = $this->google($q);
				break;
    	    
			case "nominatim_reverse":
				$geocode = $this->nominatim_reverse($q);
				break;
    	    
			// Nothing selected, continue
			default:
				$geocode = false;
	    	    
	    	    
    	} // switch end
		
		
		// Return successfull results
		if($geocode !== false) {
			$this->log($q, true);
			return $geocode;
		}
		
		// If we didn't get any results, remove service from our list and try again with next one
		elseif($geocode === false && !empty($this->services[$this->mode])) {
		
			// Log event
			$this->log($q, false);
		
			// Remove used
			$this->unset_service($this->service);
			
			// Choose new
			$this->set_service();
			
			// Geocode again
			return $this->geocode($q);
		}
		
		// If we ran out of services, just return error
		else {
			$this->log($q, false);
			return $this->geocoder_output(false);
		}
	}
	
	
	
	

	/*
	 * GEOCODERS
	 * * * * * * * * * * * * * * */ 
	
	
	/* 
	 * Tiny Geocoder
	 * http://tinygeocoder.com/
	 * - Geocode
	 */
	public function tiny_geocode($q) {
		
		$raw = readURL('http://tinygeocoder.com/create-api.php?q='.$q);
		
		if(empty($raw)) return false;
		else {
			$latlon = explode(",",$raw);
	
			$output["lat"] = $latlon[0];
			$output["lon"] = $latlon[1];
			$output["service"] = "tiny_geocoder";
		}
		
		return $this->geocoder_output($output);
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
	public function nominatim_geocode($q) {
		global $settings;
		
		$xml = readURL('http://nominatim.openstreetmap.org/search?q='.$q.'&format=xml&email='.urlencode($settings["email"]));
		
		if(!empty($xml)) {
			$data = new SimpleXMLElement($xml);
			
			// Loop XML trough and output if we had a result	
			if(isset($data->place[0])) {
				foreach($data->place[0]->attributes() as $a => $b) {
					$output[$a] = $b;
				}		
			}
			else {
				$output["error"] = true;
			}
			
			$output["service"] = "nominatim";
			
			return $this->geocoder_output($output);
	 	}
	 	// No results
	 	else return false;
	}
	
	
	
	/* 
	 * Nominatim
	 * http://nominatim.openstreetmap.org
	 * Data Copyright OpenStreetMap Contributors, Some Rights Reserved. CC-BY-SA 2.0
	 *
	 * - Reverse Geocode
	 * q: lat,lon
	 */
	public function nominatim_reverse($q) {
		global $settings;
	
		$q = explode("%2C",$q); // divide from "," - $q is already urlencoded, that's why it's in this format
	
		//'.$settings["language"].'
		// Get results in english
		$xml = readURL('http://nominatim.openstreetmap.org/reverse?format=xml&lat='.urlencode($q[0]).'&lon='.urlencode($q[1]).'&zoom=18&addressdetails=1&accept-language=en_UK&email='.urlencode($settings["email"]));

		if(!empty($xml)) {
			$raw = new SimpleXMLElement($xml);
		
			if(empty($raw) OR !empty($raw->error)) return false;
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
			$output["service"] = "nominatim";
			
			return $this->geocoder_output($output);
	 	}
	 	// No results
	 	else return false;
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
	public function google($q) {
	
		$json = readURL('http://maps.google.com/maps/geo?q='.$q);
		
		if(!empty($json)) {
		
			$raw = json_decode($json);
			
			if((string)$raw->Status->code == "200") {
			
				$latlon = explode(",",(string)$raw->name);
				
				// Re format json
				$output["lat"] = trim($latlon[0]);
				$output["lon"] = trim($latlon[1]);
				$output["address"] = (string)$raw->Placemark[0]->address;
				$output["locality"] = (string)$raw->Placemark[2]->AddressDetails->Country->AdministrativeArea->AddressLine[0];
				$output["country_name"] = (string)$raw->Placemark[0]->AddressDetails->Country->CountryName;
				$output["country_code"] = (string)$raw->Placemark[0]->AddressDetails->Country->CountryNameCode;
				$output["service"] = "google";
				
				return $this->geocoder_output($output);
			}
			// On error status code
			else return false;
		}
		// No results
		else return false;
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
	public function geonames_geocode($q) {
	
		$xml = readURL('http://ws.geonames.org/search?q='.$q.'&maxRows=1&style=SHORT');
		#echo "<pre>--".$xml."--</pre>";
		if(empty($xml)) {
		
			return false;
		
		} else {
			$raw = new SimpleXMLElement($xml);
			
			$latlon = explode(",",$raw->name);
			
			// Define zoom level by object type
			// http://www.geonames.org/export/codes.html
			if($raw->geoname->fcl == "A") $zoom = $this->zoomlevels["country"];
			elseif($raw->geoname->fcl == "P") $zoom = $this->zoomlevels["city"];
			else $zoom = $this->zoomlevels["street"];
					
				
			$output["lat"] = (string)$raw->geoname->lat;
			$output["lon"] = (string)$raw->geoname->lng;
			$output["locality"] = (string)$raw->geoname->toponymName;
			$output["country_name"] = ISO_to_country((string)$raw->geoname->countryCode);
			$output["country_code"] = (string)$raw->geoname->countryCode;
			$output["zoom"] = $zoom;
			$output["service"] = "geonames";
			
			return $this->geocoder_output($output);
		}
	}
	
	
	
	/* 
	 * Geonames 
	 * http://www.geonames.org/export/geonames-search.html
	 * - Reverse Geocode
	 *
	 * Response example:
	
	 */
	public function geonames_reverse($q) {
	
		$q = explode("%2C",$q); // divide from "," - $q is already urlencoded, that's why it's in this format
	
		$xml = readURL('http://ws.geonames.org/findNearbyPlaceName?style=SHORT&lat='.urlencode($q[0]).'&lng='.urlencode($q[1]));
		#echo "<pre>--".$xml."--</pre>";
		
		if(empty($xml)) {
		
			return false;
		
		} else {
			$raw = new SimpleXMLElement($xml);
			
			if(!empty($raw->status['value'])) {
				// Errorcodes: http://www.geonames.org/export/webservice-exception.html
				return false;
			} else {
			
				// Define zoom level by object type
				// http://www.geonames.org/export/codes.html
				if($raw->geoname->fcl == "A") $zoom = $this->zoomlevels["country"];
				elseif($raw->geoname->fcl == "P") $zoom = $this->zoomlevels["city"];
				else $zoom = $this->zoomlevels["street"];
						
				$countryname = ISO_to_country((string)$raw->geoname->countryCode);
						
				$output["locality"] = (string)$raw->geoname->toponymName;
				$output["address"] = (string)$raw->geoname->toponymName.", ".$countryname;
				$output["country_code"] = (string)$raw->geoname->countryCode;
				$output["country_name"] = $countryname;
				$output["lat"] = (string)$raw->geoname->lat;
				$output["lon"] = (string)$raw->geoname->lng;
				$output["zoom"] = $zoom;
				$output["service"] = "geonames";
				
				return $this->geocoder_output($output);
				
			}
		}
	}
	
	
	
	/* 
	 * Hitchwiki / lookup from the local DB
	 * - Geocode
	 */
	public function hitchwiki_geocode($q) {
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
			
			if($result && mysql_num_rows($result) >= 1) {
				while ($row = mysql_fetch_array($result)) {
				    $latlon["iso"] = $row["iso"];
				    $latlon["lat"] = $row["lat"];
				    $latlon["lon"] = $row["lon"];
				}
			}
			
		}
		if($latlon) return $this->geocoder_output(
				array(
					"lat" => $latlon["lat"],
					"lon" => $latlon["lon"],
					"country_name" => ISO_to_country($latlon["iso"]),
					"country_code" => $latlon["iso"],
					"zoom" => $this->zoomlevels["country"],
					"service" => "HW DB/countrycode-list"
				)
			);
		
		
		
		/* TEST-2 
		 * Test if it's a country in some language
		 * * * * * * */
		
		$country = country_to_ISO($q);
	
		if($country!==false) {
			$latlon = explode("|", $this->getCountryCoords($country));
			return $this->geocoder_output(
				array(
					"lat" => $latlon[0],
					"lon" => $latlon[1],
					"country_name" => ISO_to_country($country),
					"country_code" => $country,
					"zoom" => $this->zoomlevels["country"],
					"service" => "HW DB/country-list"
				)
			);
		}
		
		
		
		/* TEST-3 
		 * Try to find by city
		 * * * * * * */
		
		$latlon = explode("|", $this->getCityCoords($q));
		if($latlon[0] != "0" && $latlon[1] != "0") {
			return $this->geocoder_output(
				array(
					"lat"=>$latlon[1],
					"lon"=>$latlon[0],
					"zoom" => $this->zoomlevels["city"],
					"service" => "HW DB/city-list"
				)
			);
		}
		
		
		// No results :-(
		return false;
	
	}
	
	
	
	
	/*
	 * Helper functions for the Hitchwiki geocoder
	 * * * * * * * * * * * * * * * * * * * * * * *
	 */
	
	/*
	 * Look for city coordinates from our own database
	 * Original function from Hitchwiki Maps v1 maps-functions.php
	 */
	public function getCityCoords($c) {
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
	public function getCountryZoom($c) {
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
	public function getCountryCoords($c) {
	    start_sql();
	    
	    $query = "SELECT lat, lon FROM `t_countries` WHERE iso='".mysql_escape_string($c)."'";
	    $res = mysql_query($query) or die ($query."-".mysql_error());
	    if ($r = mysql_fetch_row($res)) {
	        return $r[0]."|".$r[1];
	    } else {
	        return false;
	    }
	}


} // CLASS maps_geocode * end


?>