<?php
/*
 * Hitchwiki Maps: config.php
 *
 */



/* 
 * SETTINGS you might want to adjust:
 */
$settings["google_maps_api_key"] = 		"";
$settings["yahoo_maps_appid"] = 		"";
$settings["default_language"] = 		"en_UK";
$settings["valid_languages"] = 			array(
											"en_UK" => "In English", 
											"de_DE" => "Auf Deutsch", 
											"fi_FI" => "Suomeksi", 
											"es_ES" => "En Español", 
											"ru_RU" => "По-Pусский"
										);



/**** DO NOT EDIT FROM HERE ****/

/* 
 * Language
 * http://fi.php.net/manual/en/function.gettext.php
 * Requires:
 * - Gettext
 */

// Set langauge from URL
if(isset($_GET["lang"]) && array_key_exists($_GET["lang"], $settings["valid_languages"])) {
	$settings["language"] = $_GET["lang"];
}
// Set language from cookie
elseif(isset($_COOKIE["hitchwiki_maps_lang"]) && array_key_exists($_COOKIE["hitchwiki_maps_lang"], $settings["valid_languages"])) {
	$settings["language"] = $_COOKIE["hitchwiki_maps_lang"];
}
// Set language from preferred locale of the http agent
elseif(in_array(get_http_locale(), $settings["valid_languages"])) {
	$settings["language"] = get_http_locale();
}
// Set language to default
else {
	$settings["language"] = $settings["default_language"];
}

// Save / update language to cookie
if(isset($_COOKIE["hitchwiki_maps_lang"]) && $_COOKIE["hitchwiki_maps_lang"] != $settings["language"]) {
	setcookie("hitchwiki_maps_lang", $settings["language"]);
}
elseif(!isset($_COOKIE["hitchwiki_maps_lang"]) || !array_key_exists($_COOKIE["hitchwiki_maps_lang"], $settings["valid_languages"])) {
	setcookie("hitchwiki_maps_lang", $settings["language"]);
}

// Gettext
putenv('LC_ALL='.$settings["language"]);
setlocale(LC_ALL, $settings["language"]);
bindtextdomain("maps", "./locale"); // Specify location of translation tables
bind_textdomain_codeset("maps", 'UTF-8');
textdomain("maps"); // Choose domain

// Translation is looking for in ./locale/LANGUAGE_CODE/LC_ALL/maps.mo now

/*
 * Fix en -> en_UK
 * Not too nice way though. :-)
 */
function get_http_locale() {
	$replace_these = array("en", "de", "fi", "es", "ru");
	$replace_with = array("en_UK", "de_DE", "fi_FI", "es_ES", "ru_RU");

	return str_replace($replace_these, $replace_with, substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
}

/*
 * Load RPC
 */
require_once "lib/rpc.php";

?>