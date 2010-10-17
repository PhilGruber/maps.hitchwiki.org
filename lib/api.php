<?php
/**
 * @package    maps_api
 * @author     Mikael Korpela <mikael@ihminen.org>
 * @copyright  Copyright (c) 2010 {@link http://www.ihminen.org Mikael Korpela}
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported
 *
 * This is a simple API class to get Hitchwiki markers from {@link http://maps.hitchwiki.org/ Hitchwiki Maps} database.
 * 
 * Created: 2010-07-21
 *
 */

class maps_api
{

	public $format = 'json'; // json (default) | kml | array | string


	/*
	 * Construct
	 */
	public function __construct($format="json") {
	
		$this->set_format($format);
	
		start_sql(); // from lib/functions.php
		return true;
	}


	/**
	 * Set format
	 * format: json (default) | kml | array | string
	 */
	public function set_format($format='json') {
		if(strtolower($format) == 'json' 
			OR strtolower($format) == 'kml' 
			OR strtolower($format) == 'array' 
			OR strtolower($format) == 'string') {
				$this->format = strtolower($format);
			}
	}


	/*
	 * Function to stop API
	 */
	function API_error($msg="true", $error_format=false) {
		$error["error"] = strip_tags($msg);

		// You can use custom return format in errors if you want to
		if($error_format==false) $error_format = $this->format;

   		if($error_format=="string") return print_r($error,true);
   		elseif($error_format=="json") return json_encode($error);
   		elseif($error_format=="kml") return $this->array2kml($error);
   		else return $error;
   
		exit;
	}
	

	/*
	 * Output an API result:
	 * input: array
	 * output: array formated in wanted format (see $this->format)
	 */
	function output( $result = array() ) {

		if(empty($result)) return $this->API_error("empty");
   		elseif($this->format=="json") return json_encode($result);
   		elseif($this->format=="kml") return $this->array2kml($result);
   		elseif($this->format=="string") return print_r($result,true);
   		else return $result;
	}
	
	
	/*
	 * Get a place by ID
	 */
	function getMarker($id, $more=false) {
    	
    	// Get place
    	// Validate more: false | true
    	$place = ($more == true) ? get_place($id,true): get_place($id,false);

   		// Return
    	if($place===false) return $this->API_error("Illegal ID.");
    	else return $this->output($place);

	}


	/*
	 * Get places by boundingbox coordinates
	 * Square corners, eg. 60.0066276,60.3266276,24.783508,25.103508 (Helsinki, Finland)
	 */
	function getMarkersByBound($lt, $lb, $rt, $rb, $description=false) {
    	global $settings;


		// Get description with markers?
		if(!isset($settings["valid_languages"][$description])) $description = false;

    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`";
    	
    	if($description!=false) $query .= ",`".$description."`";
    	
    	$query .= " FROM `t_points` WHERE 
					`type` = 1 AND 
					`lat` > ".mysql_real_escape_string($lt)." AND 
					`lat` < ".mysql_real_escape_string($lb)." AND 
					`lon` > ".mysql_real_escape_string($rt)." AND 
					`lon` < ".mysql_real_escape_string($rb);

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    
   		    if($description!=false) {
   		    	$result[$i]["description"] = $r[$description];
   		    	//$result[$i]["description_language"] = $description;
   		    }
   		    
   		    $i++;
   		}
   	
   	
   		// Return
   		return $this->output($result);
    	
	}


	/*
	 * Get places by city/town
	 */
	function getMarkersByLocality($city) {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`,`city` FROM `t_points` WHERE 
					`type` = 1 AND 
					`city` = '".mysql_real_escape_string($city)."'";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}
   		
   		
   		// Return
   		return $this->output($result);
    }


	/*
	 * Get places by country
	 */
	function getMarkersByCountry($country) {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`,`country` FROM `t_points` WHERE 
					`type` = 1 AND 
					`country` = '".mysql_real_escape_string($country)."'";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}
   		
   		
   		// Return
   		return $this->output($result);
    }


	/*
	 * Get places by continent
	 */
	function getMarkersByContinent($continent) {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`,`continent` FROM `t_points` WHERE 
					`type` = 1 AND 
					`continent` = '".mysql_real_escape_string($continent)."'";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}
   		
   		
   		// Return
   		return $this->output($result);
    }



	/*
	 * Get a country
	 */
	function getCountry($iso=false, $lang=false) {
		global $settings;
	
		// Validate ISO country code
		$codes = countrycodes();
		if($iso===false OR strlen($iso) != 2 OR !isset($codes[strtoupper($iso)])) return $this->API_error("Wrong countrycode.");

    	$result = country_info($iso, $lang);

   		// Return
   		return $this->output($result);
    }


	/*
	 * Get countries
	 * all: true | false (default)
	 * coordinates: true | false (default)
	 */
	function getCountries($all=false, $coordinates=false) {

    	// List all countries, or just ones with places?
    	// Validate as "true" or "false"
    	if(is_bool($all) === false) $all = false;
   
    	// List with coordinates
    	// Validate as "true" or "false"
    	if(is_bool($coordinates) === false) $coordinates = false;

    	// Get a list of countries
    	$result = list_countries("array", "name", false, true, $all, $coordinates);

   		// Return
   		return $this->output($result);
    }


	/*
	 * Get continents
	 */
	function getContinents() {
    	
    	// Get list of continents
    	$result = list_continents("array", true);
    	
   		// Return
   		return $this->output($result);
    }


    
	/*
	 * Get all markers
	 */
	function getAll() {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating` FROM `t_points` WHERE 
					`type` = 1";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}

   		// Return
   		return $this->output($result);
    }

	
	
	/* 
	 * Remove comment
	 */
	function removeComment($id=false) {
	
		// ID
		if($id===false OR empty($id) OR !is_numeric($id)) return $this->API_error("Invalid ID.");
		
		// Check if user has rights to remove comment
	 	$user = current_user();
		// Admins have rights to remove anything, others we need to check from the database
		if($user["admin"] !== true) {
		
			$rescheck = mysql_query("SELECT `id`,`fk_user` FROM `t_comments` WHERE `fk_user` = ".$user["id"]." AND `id` = ".mysql_real_escape_string($id)." LIMIT 1");
   			if(!$rescheck) return $this->API_error("Query failed!");
			
			// If we didn't find any rows matching comment-id AND user-id, user doesn't have permissions to remove this
			if(mysql_num_rows($rescheck) <= 0) return $this->API_error("permission_denied");
		}
		
		// Remove it
   		$res = mysql_query("DELETE FROM `t_comments` WHERE `id` = ".mysql_real_escape_string($id)." LIMIT 1");

   		if(!$res) return $this->API_error("Query failed!");
   		
   		if(mysql_affected_rows() >= 1) return $this->output( array("success"=>true) );
   		else return $this->API_error("Comment ID not found.");
	
	}
	
	
	/* 
	 * Remove waitingtime
	 */
	function removeWaitingtime($id=false) {
	
		// ID
		if($id===false OR empty($id) OR !is_numeric($id)) return $this->API_error("Invalid ID.");
		
		// Check if user has rights to remove comment
	 	$user = current_user();
		// Admins have rights to remove anything, others we need to check from the database
		if($user["admin"] !== true) {
		
			$rescheck = mysql_query("SELECT `id`,`fk_user` FROM `t_waitingtimes` WHERE `fk_user` = ".$user["id"]." AND `id` = ".mysql_real_escape_string($id)." LIMIT 1");
   			if(!$rescheck) return $this->API_error("Query failed!");
			
			// If we didn't find any rows matching waitingtime-id AND user-id, user doesn't have permissions to remove this
			if(mysql_num_rows($rescheck) <= 0) return $this->API_error("permission_denied");
		}
				
		// Remove it
   		$res = mysql_query("DELETE FROM `t_waitingtimes` WHERE `id` = ".mysql_real_escape_string($id)." LIMIT 1");

   		if(!$res) return $this->API_error("Query failed!");
   		
   		if(mysql_affected_rows() >= 1) return $this->output( array("success"=>true) );
   		else return $this->API_error("Comment ID not found.");
	
	}
	
	
	/*
	 * Add comment
	 * Comment must be an array including:
	 * - place_id (required)
	 * - comment (required)
	 * - user_id (optional)
	 * - user_nick (optional)
	 */
	function addComment($comment=array()) {
	
		// Place ID
		if(!isset($comment["place_id"]) OR empty($comment["place_id"]) OR !is_numeric($comment["place_id"])) return $this->API_error("Invalid place ID.");
	
		// Comment
		if(!isset($comment["comment"]) OR empty($comment["comment"])) return $this->API_error("Comment missing.");
		else {
			$comment["comment"] = htmlspecialchars($comment["comment"]);
		}
	
		// User ID
		if(isset($comment["user_id"])) {
			
	 		$user = current_user();
		
			if(!is_numeric($comment["user_id"]) OR empty($comment["user_id"])) return $this->API_error("Invalid user ID.");
			elseif($comment["user_id"] != $user["id"]) return $this->API_error("Posting commend failde. You need to be logged in. (".$user["id"].")");
			else $user_id = $comment["user_id"];
	
		} else {
			$user_id = "NULL";
		}
		
		
		// User nick
		if(isset($comment["user_nick"]) && !empty($comment["user_nick"]) && available_nick($comment["user_nick"])) $nick = "'".mysql_real_escape_string($comment["user_nick"])."'";
		else {
			$nick = "NULL";
			
			// If nick was empty but user_id no, produce nick out from it to send back to the user 
			if($user_id != "NULL") $comment["user_nick"] = username($user_id);
			else $comment["user_nick"] = _("Anonymous");
		}
	
		// Build a query
		$query = "	INSERT INTO `t_comments` (`id`, `fk_place`, `fk_user`, `nick`, `comment`, `datetime`) 
						VALUES (NULL, 
								'".mysql_real_escape_string($comment["place_id"])."', 
								".mysql_real_escape_string($user_id).", 
								".$nick.", 
								'".mysql_real_escape_string($comment["comment"])."', 
								NOW())";
	
	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		
   		$comment["date"] = date("j.n.Y");
   		$comment["time"] = date("H:i:s");
   		$comment["date_r"] = date("r");
   		$comment["comment"] = Markdown(stripslashes($comment["comment"]));
   		
   		$comment["id"] = mysql_insert_id();
   		$comment["success"] = true;
   		
   		// Return
   		return $this->output($comment);
	
	}

	
	/*
	 * Add place
	 * Place must be an array including:
	 * - lat (required)
	 * - lon (required)
	 * - user_id (optional)
	 * - descreption[lang_code] (optional) (as many as you wish) lang_code must be valid language in use.
	 * - hitchability: 1-5 (optional)
	 */
	function addPlace($place=array()) {
		global $settings;
	
		// Latitude
		if(!isset($place["lat"]) OR empty($place["lat"]) OR !is_numeric($place["lat"])) return $this->API_error("Invalid latitude.");
	
	
		// Longitude
		if(!isset($place["lon"]) OR empty($place["lon"]) OR !is_numeric($place["lon"])) return $this->API_error("Invalid longitude.");
		
		
		// Validate ISO country code
		if(isset($place["country"]) OR isset($place["manual_country"])) {
			
			if(isset($place["country"])) $country_iso = $place["country"];
			elseif(isset($place["manual_country"])) $country_iso = $place["manual_country"];
			
			$codes = countrycodes();
			if($country_iso===false OR strlen($country_iso) != 2 OR !isset($codes[strtoupper($country_iso)])) return $this->API_error("Invalid countrycode.");
		}
		else return $this->API_error("Select country.");
		
		
		// Continent
		$continent = country_iso_to_continent($country_iso);
		if($continent===false) return $this->API_error("Problem with the countrycode.");
		
		
		// Description
		$descreption_fields = "";
		$descreptions = "";
		foreach($settings["valid_languages"] as $code => $name) {
		
			if(isset($place["description_".$code]) & !empty($place["description_".$code])) {

				// Build a bit of mysql query
				$descreption_fields .= '`'.mysql_real_escape_string($code).'`, '; 
				$descreptions .= "'".mysql_real_escape_string(htmlspecialchars($place["description_".$code]))."', "; 
			
			}
		
		}
			
	
		// User ID
		if(isset($place["user_id"])) {
			
	 		$user = current_user();
		
			if(!is_numeric($place["user_id"]) OR empty($place["user_id"])) return $this->API_error("Invalid user ID.");
			elseif($place["user_id"] != $user["id"]) return $this->API_error("Adding place failed. You need to be logged in. (".$user["id"].")");
			else $user_id = $place["user_id"];
	
		} else {
			$user_id = "NULL";
		}
		
		
		// Type
		if($place["type"] == 1 OR $place["type"] == 2) {
			$type = $place["type"];
		}
		else $type = "NULL";
		
		
		// City/town
		if(isset($place["locality"]) && !empty($place["locality"])) {
			$locality = "'".mysql_real_escape_string($place["locality"])."'";
		} else $locality = 'NULL';
		
		
		// Rating + rating count
		if(isset($place["rating"])) {
		
			if(!is_numeric($place["rating"]) OR $place["rating"] < 0 OR $place["rating"] > 5) return $this->API_error("Invalid rating.");
			elseif($place["rating"] == "0") {
				$rating = 0;
				$rating_count = 0;
			}
			else {
				$rating = mysql_real_escape_string($place["rating"]);
				$rating_count = 1;
			}
			
		} else {
			$rating = 0;
			$rating_count = 0;
		}
		
		// Waitingtime + waitingtime count
		if(isset($place["waitingtime"])) {
		
			if(!is_numeric($place["waitingtime"]) OR $place["waitingtime"] < 0) return $this->API_error("Invalid waiting time.");
			else {
				$waitingtime = mysql_real_escape_string($place["waitingtime"]);
				$waitingtime_count = 1;
			}
			
		} else {
			$waitingtime = "NULL";
			$waitingtime_count = 0;
		}
		
		
		
		
		
		
		// Build a query
		$query = "	INSERT INTO `t_points` (	`id`, 
												`user`, 
												`type`, 
												`lat`, 
												`lon`, 
												".$descreption_fields."
												`rating`, 
												`rating_count`, 
												`waitingtime`, 
												`waitingtime_count`, 
												`country`, 
												`continent`, 
												`locality`, 
												`datetime`) 
						VALUES (NULL, 
								".$user_id.",
								".$type.",
								'".mysql_real_escape_string($place["lat"])."',
								'".mysql_real_escape_string($place["lon"])."',
								".$descreptions."
								".$rating.",
								".$rating_count.",  
								".$waitingtime.",
								".$waitingtime_count.",  
								'".mysql_real_escape_string($country_iso)."', 
								'".mysql_real_escape_string($continent)."', 
								".$locality.", 
								NOW())";
	
	    // Add place to the DB
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!".$query);
   		
   		$result["id"] = mysql_insert_id();
   		$result["success"] = true;
   		
   		
   		// Add possible rating to the database
   		if($rating_count != 0) {
   			if($user_id=="NULL") $user_id = false;
   		
   			$this->rate($rating, $result["id"], $user_id);
   		}
   		
   		
   		// Add possible waitingtime to the database
   		if($rating_count != 0) {
   			if($user_id=="NULL") $user_id = false;
   		
   			$this->addWaitingtime($waitingtime, $result["id"], $user_id);
   		}
   		
   		
   		// Return
   		return $this->output($result);
	
	}
	
	
	
	/*
	 * Get all comments for a place
	 * 
	 */
	function getComments($id=false, $limit=false) {
	
		
		$result = get_comments($id, $limit);
		
   		if($result===false) return $this->API_error("Query failed!");
		else return $this->output($result);
	
	}



	/*
	 * Add a waitingtime for a place
	 * TODO: flood blocking by IP?
	 */
	 function addWaitingtime($waitingtime, $point_id, $user_id=false) {
	 	
	 	// Validating values
		if(empty($point_id) OR !is_numeric($point_id)) return $this->API_error("Giving a waitingtime Failed. Wrong place ID.");
		
		if($waitingtime < 0 OR !is_numeric($waitingtime)) return $this->API_error("Giving a waitingtime Failed. Time must be at least one minute.");
	
		if($user_id!==false) {
	 		$user = current_user();
	 	
			if(empty($user_id) OR !is_numeric($user_id)) return $this->API_error("Giving a waitingtime Failed. Wrong user ID.");
			elseif($user_id != $user["id"]) return $this->API_error("Giving a waitingtime Failed. You need to be logged in. ".$user["id"]);
			
			$user = mysql_real_escape_string($user_id);
		}
		else $user = "NULL";



		// Build a query
		$query = "INSERT INTO `t_waitingtimes` (`id`,`fk_user`,`fk_point`,`waitingtime`,`datetime`,`ip`) 
		    			VALUES (NULL, 
		    					".$user.", 
		    					".mysql_real_escape_string($point_id).", 
		    					".mysql_real_escape_string($waitingtime).", 
		    					NOW(),
		    					'".$_SERVER['REMOTE_ADDR']."')";

   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed! (1)");
   		
   		
   		$result["success"] = true;
   		$result["point_id"] = $point_id;
   		$result["waitingtimes"] = waitingtimes($point_id);
   		
   		
   		// Update "quick access info" to the t_points
   		$res2 = mysql_query("UPDATE `t_points` SET `waitingtime` = '".mysql_real_escape_string(round($result["waitingtimes"]["avg"]))."',`waitingtime_count` = '".mysql_real_escape_string($result["waitingtimes"]["count"])."' WHERE `id` = ".mysql_real_escape_string($point_id).";");
   		if(!$res2) return $this->API_error("Query failed! (2)");
   			
   		return $this->output($result);
   		
	 }



	/*
	 * Get a waitingtimes for a place
	 */
	 function waitingtime($point_id) {
	 
		if(empty($point_id) OR !is_numeric($point_id)) return $this->API_error("Getting a waitingtime Failed. Wrong place ID.");
	 	
	 	return $this->output( waitingtimes($point_id) );
	 	
	 }




	/*
	 * Delete profile
	 */
	 function deleteProfile($user_id) {
	 	
	 	// Only logged in user can delete himself, except admins can delete anybody
	 	$user = current_user();
	 		
		if(empty($user_id) OR !is_numeric($user_id)) return $this->API_error("Wrong user ID.");
		elseif($user_id != $user["id"] && $user["admin"] !== true) return $this->API_error("Deleting a profile failed. You need to be logged in. ".$user["id"]);

	 	$res = mysql_query("DELETE FROM `t_users` WHERE `t_users`.`id` = ".mysql_real_escape_string($user_id)." LIMIT 1;");
   		if(!$res) return $this->API_error("Query failed!");

	 	return $this->output( array("success"=>true) );
	 	
	 }



	/*
	 * Rate a place
	 * TODO: flood blocking by IP?
	 */
	 function rate($rating, $point_id, $user_id=false) {
	 	
	 	
	 	// Validating values
		if(empty($point_id) OR !is_numeric($point_id)) return $this->API_error("Rating Failed. Wrong place ID.");
		
		if(empty($rating) OR !is_numeric($rating)) return $this->API_error("Rating Failed. Rate must be between 1-5.");
	
		if($user_id!==false) {
	 		$user = current_user();
	 	
			if(empty($user_id) OR !is_numeric($user_id)) return $this->API_error("Rating Failed. Wrong user ID.");
			elseif($user_id != $user["id"]) return $this->API_error("Rating Failed. You need to be logged in. ".$user["id"]);
			
			$user = mysql_real_escape_string($user_id);
		}
		else $user = "NULL";

		// Check if user has already rated this spot (then we'll just old record)
		// any old records there? -check
		if($user != "NULL") {
		
			$res4 = mysql_query("SELECT `id`,`fk_user`,`fk_point` FROM `t_ratings` WHERE `fk_user` = ".$user." AND `fk_point` = ".mysql_real_escape_string($point_id)." LIMIT 1");
   			if(!$res4) return $this->API_error("Query failed! (4)");
			
			// If we have a result
			if(mysql_num_rows($res4) > 0) {
				// Get an ID of row we need to just update
				while($r = mysql_fetch_array($res4, MYSQL_ASSOC)) {
					$update_old = $r["id"];
				}
			}

		}
		
		// Since we had a result on "any old records there?"-check, perform just an update
		if(isset($update_old)) {
		
   			$res3 = mysql_query("UPDATE `t_ratings` SET `rating` = '".mysql_real_escape_string($rating)."',`datetime` = NOW() WHERE `id` = ".mysql_real_escape_string($update_old).";");
   			if(!$res3) return $this->API_error("Query failed! (3)");
   			
   			$result["success"] = true;
   			$result["updated"] = true;
		
		} 
		// No result on "any old records there?"-check, so just add new record
		else {

			// Build a query
			$query = "INSERT INTO `t_ratings` (`id`,`fk_user`,`fk_point`,`rating`,`datetime`,`ip`) 
							VALUES (NULL, 
									".$user.", 
									".mysql_real_escape_string($point_id).", 
									".mysql_real_escape_string($rating).", 
									NOW(),
									'".$_SERVER['REMOTE_ADDR']."')";

   			$res = mysql_query($query);
   			if(!$res) return $this->API_error("Query failed! (1)");
   			
   			$result["success"] = true;
   			
   		}
   		
   		$result["point_id"] = $point_id;
   		$result["rating_stats"] = rating_stats($point_id);
   		
   		// Update "quick access info" to the t_points
   		$res2 = mysql_query("UPDATE `t_points` SET `rating` = '".mysql_real_escape_string(round($result["rating_stats"]["exact_rating"]))."',`rating_count` = '".mysql_real_escape_string($result["rating_stats"]["rating_count"])."' WHERE `id` = ".mysql_real_escape_string($point_id).";");
   		if(!$res2) return $this->API_error("Query failed! (2)");
   			
   		return $this->output($result);
	 }


	/*
	 * Output data in KML format
	 * TODO!
	 */
	function array2kml($array) {
		$array["format"] = "kml";
		return print_r($array,true);
	}


	/*
	 * Output a string in zipped format
	 *
	 * TODO! http://php.net/manual/en/book.zip.php
	 *
	 * gmz: true | false (default) - use for "kml"-files: uses "gmz" file-extension instead of "zip"
	 */
	function zipPackage($string, $gmz=false) {
		/*
		$zip = new ZipArchive();
		$filename = "./test112.zip";
		
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
		    exit("cannot open <$filename>\n");
		}
		
		$zip->addFromString("testfilephp.txt" . time(), "#1 This is a test string added as testfilephp.txt.\n");
		$zip->addFromString("testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
		$zip->addFile($thisdir . "/too.php","/testfromfile.php");
		echo "numfiles: " . $zip->numFiles . "\n";
		echo "status:" . $zip->status . "\n";
		$zip->close();
		*/
		return false;
	}


	/*
	 * Get a list of available languages
	 */
	function getLanguages() {
		global $settings;

    	foreach($settings["valid_languages"] as $code => $lang) {
    		$result[$code]["code"] = $code;
    		$result[$code]["name"] = _($settings["languages_in_english"][$code]);
    		$result[$code]["name_original"] = $lang;
    	}
    	
   		// Return
   		return $this->output($result);
    }



	/*
	 * Add public transport page to the catalog
	 */
	 function AddPublicTransport($pageinfo) {
	 
	 	// User id
	 	$user = current_user();
	 	
		if(empty($pageinfo["user_id"]) OR !is_numeric($pageinfo["user_id"])) return $this->API_error("Rating Failed. Wrong user ID.");
		elseif($pageinfo["user_id"] != $user["id"]) return $this->API_error("Rating Failed. You need to be logged in. ".htmlspecialchars($pageinfo["user_id"])."/".$user["id"]);
	
		// City
		if(!empty($pageinfo["city"])) $city = "'".mysql_real_escape_string($pageinfo["city"])."'";
		else $city = "NULL";
		
		// Country
		$codes = countrycodes();
		
		if(!empty($pageinfo["country"]) && isset($codes[$pageinfo["country"]])) $country = "'".mysql_real_escape_string($pageinfo["country"])."'";
		else return $this->API_error("Missing country.");
		
		// URL
		if(!empty($pageinfo["url"])) $url = "'".mysql_real_escape_string($pageinfo["url"])."'";
		else return $this->API_error("Missing an URL.");
		
		// Title
		if(!empty($pageinfo["title"])) $title = "'".mysql_real_escape_string($pageinfo["title"])."'";
		else $title = "NULL";
		
		// Type
		if(!empty($pageinfo["type"])) $type = "'".mysql_real_escape_string($pageinfo["type"])."'";
		else $type = "NULL";
		
	
		// Build a query
		$query = "INSERT INTO `t_ptransport` (`id`,`city`,`country`,`URL`,`title`,`type`,`datetime`,`user_id`) 
		    			VALUES (NULL, 
		    					".$city.", 
		    					".$country.", 
		    					".$url.", 
		    					".$title.", 
		    					".$type.", 
		    					NOW(),
		    					".$pageinfo["user_id"].")";

   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   			
   			
	 	$result["success"] = true;
	 
   		// Return
   		return $this->output($result);
	 }



} // the class ends
?>