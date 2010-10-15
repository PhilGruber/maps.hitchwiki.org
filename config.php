<?php
/*
 * Hitchwiki Maps: config.php
 *
 */

/* 
 * SETTINGS you might want to adjust:
 */
// Tools for devs:
$settings["debug"] = 					true;
$settings["maintenance_page"] = 		false; // Set true to close down visible page
$settings["maintenance_api"] = 			false; // Set true to close down API
$settings["non_maintenance_ip"] = 		array(); // Add IP addresses to whom show a normal page while in maintenance mode.

// API-keys:
$settings["google_maps_api_key"] = 		""; // API key to enable
$settings["yahoo_maps_appid"] = 		""; // APP ID to enable
$settings["ms_virtualearth"] = 			false; // false|true to enable

$settings["google_analytics_id"] =		""; // ID to enable

// fb:admins or fb:app_id - A comma-separated list of either the Facebook IDs of page administrators or a Facebook Platform application ID. At a minimum, include only your own Facebook ID.
$settings["fb"]["admins"] = 			"";
$settings["fb"]["page_id"] = 			"";

$settings["fb"]["app"]["id"] = 			"";
$settings["fb"]["app"]["api"] = 		"";
$settings["fb"]["app"]["secret"] = 		"";

$settings["email"] = 					"help@liftershalte.info";
$settings["cookie_prefix"] = 			"hitchwiki_maps_";

// Languages
// See ./admin/ to set up new languages
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
$settings["base_url"] = "http://yoshi.mega2000.de/~simison/maps.hitchwiki.org";
#TODO, automate this. "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);

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
 * Select language (sets $settings["language"])
 * Load common functions
 * Load Maps API
 * Load Markdown
 */
require_once "lib/language.php";
require_once "lib/functions.php";
require_once("lib/api.php");
require_once "lib/markdown.php";

?>