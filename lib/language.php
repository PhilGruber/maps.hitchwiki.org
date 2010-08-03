<?php
/* Hitchwiki Maps - language.php
 * Language
 * http://fi.php.net/manual/en/function.gettext.php
 * Requires:
 * - Config file loaded before
 * - Gettext
 */
 

// Set langauge from URL
if(isset($_GET["lang"]) && array_key_exists($_GET["lang"], $settings["valid_languages"])) {
	$settings["language"] = $_GET["lang"];
}
// Set language from cookie
elseif(isset($_COOKIE[$settings["cookie_prefix"]."lang"]) && array_key_exists($_COOKIE[$settings["cookie_prefix"]."lang"], $settings["valid_languages"])) {
	$settings["language"] = $_COOKIE[$settings["cookie_prefix"]."lang"];
}
// Set language from users settings
elseif(isset($user["language"]) && in_array($user["language"], $settings["valid_languages"])) {
	$settings["language"] = $user["language"];
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
if(isset($_COOKIE[$settings["cookie_prefix"]."lang"]) && $_COOKIE[$settings["cookie_prefix"]."lang"] != $settings["language"]) {
	setcookie($settings["cookie_prefix"]."lang", $settings["language"]);
}
elseif(!isset($_COOKIE[$settings["cookie_prefix"]."lang"]) || !array_key_exists($_COOKIE[$settings["cookie_prefix"]."lang"], $settings["valid_languages"])) {
	setcookie($settings["cookie_prefix"]."lang", $settings["language"]);
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
 * TODO: dynamic from valid_languages -array
 */
function get_http_locale() {
	$replace_these = array("en", "de", "fi", "es", "ru", "lt");
	$replace_with = array("en_UK", "de_DE", "fi_FI", "es_ES", "ru_RU", "lt_LT");

	return str_replace($replace_these, $replace_with, substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
}


?>