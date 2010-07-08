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
		    echo 'Could not connect to mysql';
		    exit;
		}
		
		if (!mysql_select_db($mysql_conf['database'], $link)) {
		    echo 'Could not select database';
		    exit;
		}
	
		return $link;
	}
	else return false;
} 


/* 
 * List available countries with markers
 * type: option | tr | li | array (default)
 * order: markers (default) | name (TODO!)
 * limit: int | false (default)
 * count: true (default) | false 
 */
function list_countries($type="array", $order="markers", $limit=false, $count=true) {
	start_sql();
	
	$codes = countrycodes();
	
	// Gathering stuff...
	$res = mysql_query("SELECT `country`, count(*) AS cnt
	                    FROM `t_points`
	                    WHERE fk_type=2
	                    GROUP BY `country`
	                    ORDER BY cnt DESC;
                    	");

	$i=0;
	while($r = mysql_fetch_row($res)) {
	
		$countryname = ISO_to_country($r[0], $codes);
		
		// print a selection option
		if($type=="option") {
			echo '<option value="'.$r[0].'" class="'.strtolower($r[0]).'">'.$countryname;
			if($count==true) echo ' <small class="grey">('.$r[1].')</small>';
			echo '</option>';
		}
		
		// print a list item
		elseif($type=="li") {
			echo '<li><img class="flag" alt="'.strtolower($r[0]).'" src="static/gfx/flags/png/'.strtolower($r[0]).'.png" /> '.$countryname;
			if($count==true) echo ' <small class="grey">('.$r[1].')</small>';
			echo '</li>';
		}
		
		// print a table row
		elseif($type=="tr") {
			echo '<tr><td><img class="flag" alt="'.strtolower($r[0]).'" src="static/gfx/flags/png/'.strtolower($r[0]).'.png" /> '.$countryname.'</td>';
			if($count==true) echo '<td>'.$r[1].'</td>';
			echo '</tr>';
		}
		
		// return an array
		else {
			$array[] = $r;
		}
		
		// limit results if asked to
		if($limit!=false && $i==$limit) break;
		$i++;
	}
	
	// Return gathered array if any
	if(isset($array)) return $array;

}


/* 
 * List available cities with markers
 * Type: option | tr | li (default)
 * Order: markers (default) | name (TODO!)
 * Limit: int | false (default)
 */
function list_cities($type="li", $order="markers", $limit=false) {
	start_sql();
	
	$codes = countrycodes();
	
    $res = mysql_query("SELECT country, city, count(*) AS cnt
                        FROM `t_points` 
                        WHERE fk_type=2
                        GROUP BY country, city
                        ORDER BY cnt DESC
                        ");
                        //LIMIT $cc

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
			echo '<option value="'.$r[1].'" class="'.strtolower($r[0]).'">'.$r[1].', '.$countryname.' ('.$r[2].')</option>';
		}
		elseif($type=="li") {
			echo '<li><img class="flag" alt="'.strtolower($r[0]).'" src="static/gfx/flags/png/'.strtolower($r[0]).'.png" /> '.$r[1].', '.$countryname.' <small class="grey">('.$r[2].')</small></li>';
		}
		elseif($type=="tr") {
			echo '<tr><td>'.$r[1].'</td><td><img class="flag" alt="'.strtolower($r[0]).'" src="static/gfx/flags/png/'.strtolower($r[0]).'.png" /> '.$countryname.'</td><td>'.$r[2].'</td></tr>';
		}
		else {
			print_r($r);
		}
		
		if($limit!=false && $i==$limit) break;
		$i++;
	}

}


/* 
 * Places in total
 */
function total_places() {

	start_sql();
	$result = mysql_query("SELECT COUNT(id) FROM t_points;");
	if (!$result) {
	   die("query failed: " . mysql_error());
	}
	
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
 * lowercase: true | false (default)  - returns names in lowercase
 * lang: get countrynames from different languagecol: en_UK (current default) | fi_FI | de_DE | es_ES | ru_RU 
 */
function countrycodes($first="code", $lang="", $lowercase=false) {
	global $settings;

	// Check if language is valid (if not, use default)
	// Settings comes from config.php
	if(empty($settings["valid_languages"][$lang])) $lang = $settings["default_language"];

	// Gather data
	start_sql();
	$result = mysql_query("SELECT iso, ".mysql_real_escape_string($lang)." FROM country");
	if (!$result) {
	   die("query failed: " . mysql_error());
	}
	
	while ($row = mysql_fetch_array($result)) {
		
		// Make name lowercase if asked to
		if($lowercase==true) $name = strtolower($row[1]);
		else $name = $row[1];
	
		// Gather list in form "iso => name" or "name => iso"
	    if($first=="name") $list[$name] = $row[0];
	    else $list[$row[0]] = $name;
	}
	
	return $list;
}


/* 
 * Shorten country names to ISO 3166-codes
 * Finland -> FI, Germany -> DE, etc
 */
function country_to_ISO($country,$db=false) {

	if(!empty($country)) {
	
		if(is_array($db) && !empty($db)) {
		
			return $db[strtolower($country)];
		
		} else {
		
			// Gather data
			start_sql();
			$result = mysql_query("SELECT iso,en_UK FROM country WHERE en_UK = '".mysql_real_escape_string($country)."' LIMIT 1");
			if (!$result) {
			   die("query failed: " . mysql_error());
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
function ISO_to_country($iso, $db=false) {

	if(!empty($iso)) {

		if(is_array($db) && !empty($db)) {
		
			return $db[$iso];
		
		} else {
		
			// Gather data
			start_sql();
			$result = mysql_query("SELECT iso,en_UK FROM country WHERE iso = '".mysql_real_escape_string(strtoupper($iso))."' LIMIT 1");
			if (!$result) {
			   die("query failed: " . mysql_error());
			}
			
			while ($row = mysql_fetch_array($result)) {
			    if(!empty($row)) return $row[0];
			    else return $iso;
			}
		}
	} else return false;
}

?>