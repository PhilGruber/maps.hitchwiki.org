<?php

/* Hitchwiki - maps
 * Global Maps functions
 * 
 */
 
 
/* 
 * cURL
 * Requires http://curl.haxx.se/
 */
function readURL($url) {

	if (function_exists('curl_init')) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}
	else return false;
}


/*
 * Start MySQL connection
 */
function start_sql() {
	global $mysql_conf,$link;

	if(isset($mysql_conf) && !empty($mysql_conf) && !isset($link)) {
		if (!$link = mysql_connect($mysql_conf['host'], $mysql_conf['user'], $mysql_conf['password'])) {
		    echo ' Could not connect to mysql. ';
		    exit;
		}
		
		if (!mysql_select_db($mysql_conf['database'], $link)) {
		    echo ' Could not select database. ';
		    exit;
		}
	
		return $link;
	}
	else return false;
} 



/*
 * Get a place array by ID
 * id: INT (required)
 * wide: true | false (default) - gets more info than just basics
 */
function get_place($id=false, $more=false) {
	global $settings;
	start_sql();

	if(preg_match ("/^([0-9]+)$/", $id) && !empty($id)) {

		$place["id"] = $id;
		 
		$query = "SELECT 
					`id`,
					`user`, 
					`type`,
					`lat`,
					`lon`,
					`rating`";
		 
		// Get more wider set of info
		if($more==true) {
		 
			$query .= ",
						`rating_count`,
			    		`country`,
			    		`continent`,
			    		`city`,
			    		`datetime`";
									
			// Add all available languages to the query
			foreach($settings["valid_languages"] as $code => $name) {
				$query .= ",`".$code."`";
			}
			
		}//if more end
		
		$query .= " FROM `t_points` 
		    		WHERE `type` = 2 AND `id` = ".mysql_real_escape_string($id)."
		    		LIMIT 1";

		$res = mysql_query($query);

		// Loop data in to an array
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
		    
		 	$place["lat"] = $r["lat"];
		 	$place["lon"] = $r["lon"];
		 	
		 	if($more==true) {
		 		$place["location"]["city"] = $r["city"];
		 		$place["location"]["country"]["iso"] = $r["country"];
		 		$place["location"]["country"]["name"] = ISO_to_country($r["country"]);
		 		$place["location"]["continent"]["code"] = $r["continent"];
		 		$place["location"]["continent"]["name"] = continent_name($r["continent"]);
		 		
		 		if(!empty($r["user"])) {
			 		$place["user"]["id"] = $r["user"];
			 		$place["user"]["name"] = username($r["user"]);
				}
				
		 		$place["link"] = $settings["base_url"]."/?place=".$id;
		 		$place["datetime"] = $r["datetime"];
		 		
		 		// Loop trough descriptions in different languages
		 		foreach($settings["valid_languages"] as $code => $name) {
		 			$place["description"][$code] = stripslashes($r[$code]);
		 		}
		 		
		 	} // end more
		 	
		 	// Nice to have ratings at this point, but continue $more after this...
		 	$place["rating"] = $r["rating"];

			if($more==true) {
								
				// Get stats about ratings if we know there are more than one
				if($r["rating_count"] > 1) {
					
					$place["rating_stats"] = rating_stats($id);
					
				} // end if more than 1
				else {
					$place["rating_stats"]["rating_count"] = $r["rating_count"];
				}
				
				
				// Comments
		 		$place["comments"] = get_comments($id);
		 		$place["comments_count"] = count($place["comments"]);
			} // end more
		
		} // while end
		
		
   
   		// output
   		return $place;
   
	}
	else {
		// ID wasn't valid
		return false;
	}

}


/*
 * Get rating statistics for a place
 * id: fk_point in t_ratings (required)
 */
function rating_stats($id) {

	if(empty($id) OR !is_numeric($id)) return false;

	$rating_query = "SELECT `fk_user`,`fk_point`,`rating`,
		    COUNT(DISTINCT rating) AS different_ratings,
		    COUNT(*) AS ratings_count,	
		    AVG(rating) AS avg_rating
		    FROM t_ratings 
		    WHERE `fk_point` = ".mysql_real_escape_string($id)." 
		    GROUP BY rating WITH ROLLUP";
	
	$place = array();
	$rating_res = mysql_query($rating_query);
	while($rating_r = mysql_fetch_array($rating_res, MYSQL_ASSOC)) {
	
	    // It's inforow collected from all ratings
	    if(empty($rating_r["rating"])) {
	    	$place["exact_rating"] = $rating_r["avg_rating"];
	    	$place["rating_count"] = $rating_r["ratings_count"];
	    	$place["different_ratings"] = $rating_r["different_ratings"];
	    }
	    // Single rating number 1-5
	    else {
	    	$place["ratings"][$rating_r["rating"]]["rating"] = $rating_r["rating"];
	    	$place["ratings"][$rating_r["rating"]]["rating_count"] = $rating_r["ratings_count"];
	    }
	}
	
	return $place;
}



/* 
 * List comments to the place by ID or just all there are in DB
 * Returns an array
 *
 * ID: place ID (Not comment ID)
 * limit: how many rows will be returned, eg: "3" or "1,3"
 */
function get_comments($id=false, $limit=false) {
	global $user;


	// Start building a query
	$query = "SELECT `id`,`fk_place`,`fk_user`,`nick`,`comment`,`datetime`,`hidden` FROM `t_comments` WHERE `hidden` IS NULL ";
	
	// For a place (default: all)
	if($id !== false && is_numeric($id)) $query .= "AND `fk_place` = ".mysql_real_escape_string($id);
	
	// Query with limit
	if($limit !== false && !empty($limit)) $query .= " LIMIT ".mysql_real_escape_string($limit);
	
	$query .= " ORDER BY `datetime` ASC";
	
	// Build an array
   	$res = mysql_query($query);
   	
   	$i=0;
	while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   	    $result[$i]["id"] = $r["id"];
   	    
   	    if($id === false) $result[$i]["place_id"] = $r["fk_place"];
   	    
   	    $result[$i]["comment"] = stripslashes($r["comment"]);
   	    $result[$i]["datetime"] = $r["datetime"];
   	    
   	    // User stuff
   	    if(!empty($r["fk_user"])) $result[$i]["user"]["id"] = $r["fk_user"];
   	    
   	    if(!empty($r["fk_user"])) $result[$i]["user"]["name"] = username($r["fk_user"]);
   	    
   	    if(!empty($r["nick"])) $result[$i]["user"]["nick"] = $r["nick"];
   	    
   	    $i++;
   	}
   	
   	return $result;
}



/* 
 * List available countries with markers
 * type: option | tr | li | array (default)
 * order: markers (default) | name | false
 * limit: int | false (default)
 * count: true (default) | false 
 * world: true | false (default) (list's all the countries, even without any markers)
 * coordinates: true | false (default)
 */
function list_countries($type="array", $order="markers", $limit=false, $count=true, $world=false, $coordinates=false) {
	start_sql();
	
	// Get all country iso-codes
	$codes = countrycodes();
	
	// Get also coordinates
	if($coordinates==true) $country_coordinates = country_coordinates();
	
	if($world==true) $empty_countries = $codes;
	
	
	// Build up a query
	$query = "SELECT `country`, count(*) AS cnt
	                    FROM `t_points`
	                    WHERE type=2
	                    GROUP BY `country`";
	
	
	if($order=="markers") $query .= " ORDER BY cnt DESC";
	elseif($order=="name") $query .= " ORDER BY country ASC";
	
	
	// Gathering stuff...
	$res = mysql_query($query);

	// Create an array out of this stuff
	$i=0;
	while($r = mysql_fetch_row($res)) {
	
		// Remove "used" country from empty countries -list
		if($world==true) unset($empty_countries[$r[0]]);
	
		// Gather an array
		$country_array[$r[0]]["iso"] = $r[0];
		$country_array[$r[0]]["name"] = ISO_to_country($r[0], $codes);
		$country_array[$r[0]]["places"] = $r[1];
		
		// Add also coordinates if requested
		if($coordinates==true && $country_coordinates[$r[0]]["lat"] != "" && $country_coordinates[$r[0]]["lon"] != "") {
			$country_array[$r[0]]["lat"] = $country_coordinates[$r[0]]["lat"];
			$country_array[$r[0]]["lon"] = $country_coordinates[$r[0]]["lon"];
		} elseif($coordinates==true) {
			$country_array[$r[0]]["lat"] = "";
			$country_array[$r[0]]["lon"] = "";
		}
		
		// limit results if asked to
		if($limit!=false && $i==$limit) break;
		$i++;
	}
	
	
	// Add empty countries to the main list if requested
	if($world==true) {
		foreach($empty_countries as $iso => $countryname) {
			$country_array2[$iso]["iso"] = $iso;
			$country_array2[$iso]["name"] = $countryname;
			$country_array2[$iso]["places"] = 0;
			
			// Add also coordinates if requested
			if($coordinates==true && $country_coordinates[$iso]["lat"] != "" && $country_coordinates[$iso]["lon"] != "") {
				$country_array2[$iso]["lat"] = $country_coordinates[$iso]["lat"];
				$country_array2[$iso]["lon"] = $country_coordinates[$iso]["lon"];
			} elseif($coordinates==true) {
				$country_array[$iso]["lat"] = "";
				$country_array[$iso]["lon"] = "";
			}
		}
	
		$country_array = array_merge($country_array, $country_array2);
		sort($country_array);
	}
	
	
	// Print it out
	foreach($country_array as $country) {
	
		// print a selection option
		if($type=="option") {
			echo '<option value="'.$country["iso"].'" class="'.strtolower($country["iso"]).'">'.$country["name"];
			if($count==true) echo ' <small class="grey">('.$country["places"].')</small>';
			echo '</option>';
		}
		
		// print a list item
		elseif($type=="li") {
			echo '<li><img class="flag" alt="'.strtolower($country["iso"]).'" src="static/gfx/flags/png/'.strtolower($country["iso"]).'.png" /> <a href="#" onclick="alert();">'.$country["name"]."</a>";
			if($count==true) echo ' <small class="grey">('.$country["places"].')</small>';
			echo '</li>';
		}
		
		// print a table row
		elseif($type=="tr") {
			echo '<tr><td><img class="flag" alt="'.strtolower($country["iso"]).'" src="static/gfx/flags/png/'.strtolower($country["iso"]).'.png" /> '.$country["name"].'</td>';
			if($count==true) echo '<td>'.$country["places"].'</td>';
			echo '</tr>';
		}
		
	}
	
	// Return gathered array if requested type = array 
	if($type=="array") return $country_array;

}



/* 
 * List available cities with markers
 * type: option | tr | li | array (default)
 * order: markers (default) | name (TODO!)
 * limit: int | false (default)
 * count: true (default) | false 
 * country: ISO-countrycode | false (default)
 * user_id: INT | false (default)
 */
function list_cities($type="array", $order="markers", $limit=false, $count=true, $country=false, $user_id=false) {
	start_sql();
	
	// Get ISO-countrycode list with countrynames
	$codes = countrycodes();
	
	// Start building a query
	$query = "SELECT country, city, count(*) AS cnt FROM `t_points` WHERE type=2";
	
	// Only from some specific country
	if($country != false && strlen($country) == 2) {
		$query .= " AND country = '".mysql_real_escape_string($country)."'";
	} 
	else {
		$country = false;
	}
	
	// Only from some specific user
	if($user_id != false && is_numeric($user_id)) {
		$query .= " AND user = '".mysql_real_escape_string($user_id)."'";
	} 
	else {
		$user_id = false;
	}
	
	
	// Continue with query...
	$query .= " GROUP BY country, city ORDER BY cnt DESC";
	

    $res = mysql_query($query);

	$i=0;
	while($r = mysql_fetch_row($res)) {
		/* 
		 * $r[#]:
		 * 0 = countrycode
		 * 1 = city
		 * 2 = markercount
		 */

		$countryname = ISO_to_country($r[0], $codes);
	
	
		if($type=="option") {
			echo '<option value="'.$r[1].'" class="'.strtolower($r[0]).'">'.$r[1];
			
			if($country != false) echo ', '.$countryname;
			
			if($count==true) echo ' ('.$r[2].')';
			
			echo '</option>';
		}
		elseif($type=="li") {
		
			if($country == false) echo '<li><img class="flag" alt="'.strtolower($r[0]).'" src="static/gfx/flags/png/'.strtolower($r[0]).'.png" /> '.$r[1].', '.$countryname;
			else echo '<li>'.$r[1];
			
			if($count==true) echo ' <small class="grey">('.$r[2].')</small>';
			
			echo '</li>';
		}
		elseif($type=="tr") {
			echo '<tr><td>'.$r[1].'</td>';
			
			if($country == false) echo '<td><img class="flag" alt="'.strtolower($r[0]).'" src="static/gfx/flags/png/'.strtolower($r[0]).'.png" /> '.$countryname.'</td>';
			
			if($count == true) echo '<td>'.$r[2].'</td>';
			
			echo '</tr>';
		}
		else {
			$array[$i]["city"] = $r[1];
			if($country == false) {
				$array[$i]["country_iso"] = $r[0];
				$array[$i]["country_name"] = $countryname;
			}
			$array[$i]["places"] = $r[2];
		}
		
		if($limit!=false && $i==$limit) break;
		$i++;
	}

	// Return gathered array if any
	if(isset($array)) return $array;
}



/* 
 * List all continents
 * type: option | tr | li | array (default)
 * count: true | false (default)
 */
function list_continents($type="array", $count=false) {

	// Continents (translated)
	$continents["AS"]["name"] = _("Asia");
	$continents["AS"]["code"] = "AS";
	$continents["AS"]["places"] = "0";

	$continents["AF"]["name"] = _("Africa");
	$continents["AF"]["code"] = "AF";
	$continents["AF"]["places"] = "0";

	$continents["NA"]["name"] = _("North America");
	$continents["NA"]["code"] = "NA";
	$continents["NA"]["places"] = "0";

	$continents["SA"]["name"] = _("South America");
	$continents["SA"]["code"] = "SA";
	$continents["SA"]["places"] = "0";

	$continents["AN"]["name"] = _("Antarctica");
	$continents["AN"]["code"] = "AN";
	$continents["AN"]["places"] = "0";

	$continents["EU"]["name"] = _("Europe");
	$continents["EU"]["code"] = "EU";
	$continents["EU"]["places"] = "0";

	$continents["OC"]["name"] = _("Australia and Oceania");
	$continents["OC"]["code"] = "OC";
	$continents["OC"]["places"] = "0";

	// Get marker count if requested
	if($count==true) {
		
		start_sql();
		
		$query = "SELECT `continent`, count(*) AS cnt
	                    FROM `t_points`
	                    WHERE type=2
	                    GROUP BY `continent`  ORDER BY cnt DESC";
	
    	$res = mysql_query($query);
		
		while($r = mysql_fetch_row($res)) {
			$continents[$r[0]]["places"] = $r[1];
		}
	}
	


	// Spread it out
	if($type == "option") {
		foreach($continents as $continent) {
			echo '<option value="'.$continent["code"].'">'.$continent["name"];
			
			if($count==true) echo " (".$continent["places"].")";
			
			echo "</option>\n";
		}
	}
	elseif($type == "li") {
		foreach($continents as $continent) {
			echo "<li>".$continent["name"];
			
			if($count==true) echo " (".$continent["places"].")";
			
			echo "</li>\n";
		}
	}
	elseif($type == "tr") {
		foreach($continents as $continent) {
			echo "<tr><td>".$continent["name"]."</td>";
			
			if($count==true) echo "<td>".$continent["places"]."</td>";
			
			echo "</tr>\n";
		}
	}
	else {		
		return $continents;
	}
}


/* 
 * Places in total
 * country: ISO shortcode for the country | false (default; will just get them all)
 */
function total_places($country=false) {

	// Start building a query
	$query = "SELECT COUNT(id) FROM t_points WHERE type=2";

	// Query just from one country
	if($country != false && strlen($_GET["country"]) == 2) $query .= " AND country = '".$country."'";

	// Gather data
	start_sql();
	$result = mysql_query($query);
	if (!$result) {
	   die("query failed.");
	}

	// Plop!
	while ($row = mysql_fetch_array($result)) {
	    return $row[0];
	}
}


/* 
 * Get countrycode list in two forms:
 * code => countryname (default)
 * or
 * countryname => code
 *
 * first: code | name
 * lang: get countrynames from different languagecol: en_UK | fi_FI | de_DE | es_ES | ru_RU | lt_LT | ...etc
 * lowercase: true | false (default)  - returns names in lowercase
 */
function countrycodes($first="code", $lang="", $lowercase=false) {
	global $settings;

	// Check if language is valid (if not, use default)
	// Settings comes from config.php
	if(!isset($settings["valid_languages"][$lang])) $lang = $settings["language"];


	// Built up a query
	$query = "SELECT iso, ".mysql_real_escape_string($lang);
	
	// Get default language name also, if query language wasn't already it (we use it as a fall-back name)
	// Most likely it's en_UK
	if($lang != $settings["default_language"]) $query .= ", ".mysql_real_escape_string($settings["default_language"]);
	
	$query .= " FROM country";


	// Gather data
	start_sql();
	$result = mysql_query($query);
	if (!$result) {
	   die("Error: SQL query failed with countrycodes()");
	}
	
	while ($row = mysql_fetch_array($result)) {
		
		// Countryname (fall-back to the default)
		if(!empty($row[$lang])) $name = $row[$lang];
		else $name = $row[$settings["default_language"]];
		
		// Make name lowercase if asked to
		if($lowercase==true) $name = strtolower($name);
	
		// Gather list in form "iso => name" or "name => iso"
	    if($first=="name") $list[$name] = $row["iso"];
	    else $list[$row["iso"]] = $name;
	}
	
	return $list;
}



/* 
 * List countrycoordinates in array:
 * Array
 * (
 *     [iso] 	=>	 "de"
 *     [lat] 	=>	 51
 *     [lon] 	=>	 9
 * )
 
 *
 */
function country_coordinates() {

	// Gather data
	start_sql();
	$result = mysql_query("SELECT iso, lat, lon FROM country");
	if (!$result) {
	   die("Error: SQL query failed with country_coordinates()");
	}
	while ($row = mysql_fetch_array($result)) {
		
	    $list[$row["iso"]]["iso"] = $row["iso"];
	    $list[$row["iso"]]["lat"] = $row["lat"];
	    $list[$row["iso"]]["lon"] = $row["lon"];
	}
	
	return $list;
}


/* 
 * Shorten country names to ISO 3166-codes
 * Finland -> FI, Germany -> DE, etc
 */
function country_to_ISO($country,$db=false, $lang="") {

	if(!empty($country)) {
	
		if(is_array($db) && !empty($db)) {
		
			return $db[strtolower($country)];
		
		} else {
		
			// Gather data
			start_sql();
			$result = mysql_query("SELECT iso,en_UK FROM country WHERE en_UK = '".mysql_real_escape_string($country)."' LIMIT 1");
			if (!$result) {
	   			die("query failed.");
			}
			
			while ($row = mysql_fetch_array($result)) {
			    if(!empty($row)) return $row[1];
			    else return $country;
			}
		}
	} else return false;
}


/* 
 * Give names behind ISO-country codes
 * FI -> Finland, DE -> Germany, etc
 */
function ISO_to_country($iso, $db=false, $lang="") {

	if(!empty($iso)) {

		global $settings;
		
		// Check if language is valid (if not, use default)
		// Settings comes from config.php
		if(empty($settings["valid_languages"][$lang])) $lang = $settings["default_language"];


		if(is_array($db) && !empty($db)) {
		
			return $db[$iso];
		
		} else {
		
			// Gather data
			start_sql();
			$result = mysql_query("SELECT iso,".mysql_real_escape_string($lang)." FROM country WHERE iso = '".mysql_real_escape_string(strtoupper($iso))."' LIMIT 1");
			if (!$result) {
	   			die("query failed.");
			}
			
			while ($row = mysql_fetch_array($result)) {
			    if(!empty($row)) return $row[1];
			    else return $iso;
			    
			}
		}
	} else return false;
}


/* 
 * Shorten longer language codes
 * ISO_639-1 ('en_UK' => 'en')
 */
function shortlang($lang="") {

	// Use default in not specified
	if(empty($lang)) {
		global $settings;
		$lang = $settings["language"];
	}
	
	// Return shortie
	return substr($lang, 0, 2); 
}



/* 
 * Content name
 * NA => North America
 */
function continent_name($code="") {

	$continent = list_continents();
	
	if(!empty($code)) return $continent[$code]["name"];
	else return $code;
}

/* 
 * Return a user name by ID
 */
function username($id) {

	if(!empty($id) && is_numeric($id)) {
		start_sql();
		
		// Get users name from database
		$res = mysql_query("SELECT `id`,`name` FROM `users` WHERE `id` = ".mysql_real_escape_string($id)." LIMIT 1");
		if(!$res) return _("Unknown");
		
		// If we have a result, go and get the name
		if(mysql_num_rows($res) > 0) {
		    while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
		    	return htmlspecialchars($r["name"]);
		    }
		}
		else return _("Unknown");
	}
	else return _("Unknown");
}


/* 
 * Return a textual hitchability
 */
function hitchability2textual($rating=false) {

	if($rating == 1) return _("Very good");
	elseif($rating == 2) return _("Good");
	elseif($rating == 3) return _("Average");
	elseif($rating == 4) return _("Bad");
	elseif($rating == 5) return _("Senseless");
	else return _("Unknown");
}

/*
 * Returns a graph img-url of ratings
 * Uses Google Chart API
 * http://code.google.com/apis/chart/docs/gallery/bar_charts.html
 */
function rating_chart($rating_stats=false, $width="50") {

	// Get ALL ratings
	if($place===false) {
	
	}
	// Test so that we have a rating stats in an array form
	elseif(!is_array($rating_stats)) return false;
	
	// Validate width
	if(empty($width) || !is_numeric($width)) $width = "50";

	// Default is 0%
	$raintg_percentages[1]["rating"] = "0";
	$raintg_percentages[1]["count"] = "0";
	$raintg_percentages[2]["rating"] = "0";
	$raintg_percentages[2]["count"] = "0";
	$raintg_percentages[3]["rating"] = "0";
	$raintg_percentages[3]["count"] = "0";
	$raintg_percentages[4]["rating"] = "0";
	$raintg_percentages[4]["count"] = "0";
	$raintg_percentages[5]["rating"] = "0";
	$raintg_percentages[5]["count"] = "0";

	// Count percentages
	foreach($rating_stats["ratings"] as $rating) {
		// Count the percentage of this rating
		$raintg_percentages[$rating["rating"]]["rating"] = 100*($rating["rating_count"]/$rating_stats["rating_count"]);
		$raintg_percentages[$rating["rating"]]["count"] = $rating["rating_count"];
	}

	$url = 'http://chart.apis.google.com/chart';
	
	$url .= '?cht=bhs';
	$url .= '&chf=bg,s,faf9f3';
	$url .= '&chs='.$width.'x55';
	$url .= '&chd=t:'.$raintg_percentages[1]["rating"].','.$raintg_percentages[2]["rating"].','.$raintg_percentages[3]["rating"].','.$raintg_percentages[4]["rating"].','.$raintg_percentages[5]["rating"];
	$url .= '&chxt=y,r';
	$url .= '&chxl=';
		$url .= '1:';
		$url .= '|'.hitchability2textual(5);
		$url .= '|'.hitchability2textual(4);
		$url .= '|'.hitchability2textual(3);
		$url .= '|'.hitchability2textual(2);
		$url .= '|'.hitchability2textual(1);
		$url .= '|';
		
		$url .= '0:';
		$url .= '|'.$raintg_percentages[5]["count"];
		$url .= '|'.$raintg_percentages[4]["count"];
		$url .= '|'.$raintg_percentages[3]["count"];
		$url .= '|'.$raintg_percentages[2]["count"];
		$url .= '|'.$raintg_percentages[1]["count"];
		$url .= '|';
	#$url .= '&chxs=0,ad8c55,8|1,ad8c55,7';
	$url .= '&chxs=1,ad8c55,10,-1,t,ad8c55|0,ad8c55,10';
	
	$url .= '&chco=00ad00|96ad00|ffff00|ff8d00|ff0000';
	$url .= '&chbh=6,3';


	return $url;
}

/* 
 * Check if nick is available and ok in other ways too
 */
function available_nick($nick=false) {
	
	// Pre non allowed nicks (keep them lowercase)
	$taken_nicks = array(
		"anonymoys",
		"admin",
		"administrator",
		"unknown",
		"nickname",
		"nick",
		"name",
		"hitchwiki",
		"hitchwiki_maps"
	);

	// Check if nick is ok and return
	if(strlen($nick) <= 3 OR empty($nick) OR in_array(strtolower($nick), $taken_nicks)) return false;
	else return true;
}


/* 
 * Return info about logged in user
 * or if user isn't logged in
 * TODO!
 */
function current_user($get_password=false) {
	global $_COOKIE,$settings;
	
	$cookie_email = $settings["cookie_prefix"]."email";
	$cookie_password = $settings["cookie_prefix"]."password";

	if(isset($_COOKIE[$cookie_email]) && isset($_COOKIE[$cookie_password])) {
		$user = check_login($_COOKIE[$cookie_email], $_COOKIE[$cookie_password],$get_password);
		
		// Will either return an array including userinfo or false in case of login fails (wrong email/password)
		// see check_login() for more details
		return $user;
	}
	else return false;
}

/*
 * Check if user is in database
 * email = t_user.email
 * password = md5(t_user.password)
 */
function check_login($email=false, $password=false, $get_password=false) {
	start_sql();

	// Validate as a boolean
    if(is_bool($get_password) === false) $get_password = false;
   
   
	$res = mysql_query("SELECT * FROM `users` WHERE `email` = '".mysql_real_escape_string($email)."' AND `password` = '".mysql_real_escape_string($password)."' LIMIT 1");
   	
   	if(!$res) return false;
			
	// If we have a result, continue gathering user array
	if(mysql_num_rows($res) > 0) {

		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {

			$user["logged_in"] = true;
			$user["id"] = $r["id"];
			$user["name"] = $r["name"];
			$user["email"] = $r["email"];
			$user["location"] = $r["location"];
			$user["country"] = $r["country"];
			$user["language"] = $r["language"];
			
			// Admin? 1:false
			if($r["admin"]=="1") $user["admin"] = true;
			else $user["admin"] = false;
			
			if($get_password===true) $user["password"] = $r["password"];
			
			return $user;
		}
	} 
	else return false;

}


/*
 * Validate en email
 *
 * an RFC822 compliant email address matcher
 * Originally written by Cal Henderson: 
 * http://www.iamcal.com/publish/articles/php/parsing_email/
 */
function is_valid_email_address($email){

    $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';

    $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';

    $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
    	'\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';

    $quoted_pair = '\\x5c[\\x00-\\x7f]';

    $domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";

    $quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";

    $domain_ref = $atom;

    $sub_domain = "($domain_ref|$domain_literal)";

    $word = "($atom|$quoted_string)";

    $domain = "$sub_domain(\\x2e$sub_domain)*";

    $local_part = "$word(\\x2e$word)*";

    $addr_spec = "$local_part\\x40$domain";

    return preg_match("!^$addr_spec$!", $email) ? true : false;
}


?>