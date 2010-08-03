<?php
/*
 * Hitchwiki Maps: config.php
 *
 */


/* 
 * SETTINGS you might want to adjust:
 */
// Tools for devs:
$settings["debug"] = 					false;
$settings["maintenance_page"] = 		false; // Set true to close down visible page
$settings["maintenance_api"] = 			false; // Set true to close down API
$settings["non_maintenance_ip"] = 		array(); // Add IP addresses to whom show a normal page while in maintenance mode.

// Common settings:
$settings["google_maps_api_key"] = 		"";
$settings["yahoo_maps_appid"] = 		"";
$settings["email"] = 					"help@liftershalte.info";
$settings["cookie_prefix"] = 			"hitchwiki_maps_";
$settings["default_language"] = 		"en_UK"; // Fall back and default language
$settings["valid_languages"] = 			array( // Remember to add language cells to your database too!
											"en_UK" => "In English", 
											"de_DE" => "Auf Deutsch", 
											"es_ES" => "En Español", 
											"ru_RU" => "По-Pусский",
											"fi_FI" => "Suomeksi", 
											"lt_LT" => "Lietuvių"
										);
$settings["languages_in_english"] = 	array(
											"en_UK" => "English", 
											"de_DE" => "German", 
											"es_ES" => "Spanish", 
											"ru_RU" => "Russian",
											"fi_FI" => "Finnish", 
											"lt_LT" => "Lithuanian"
										); 


// Usually you don't need to edit this, but you can set it manually, too. No ending "/".
$settings["base_url"] = 				"http://devmaps.hitchwiki.org"; //"http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);

/*
 * MySQL settings
 */
$mysql_conf = array(
    "user"		=> 		'', 
    "password"	=> 		'',
    "host"		=> 		'',
    "database"	=> 		''
);


/**** DO NOT EDIT FROM HERE ****/

/*
 * Select language
 * Sets $settings["language"]
 */
require_once "lib/language.php";


/*
 * Load common functions
 */
require_once "lib/functions.php";


/*
 * Load Maps API
 */
require_once("lib/api.php");


/*
 * Load Markdown
 */
require_once "lib/markdown.php";



?>