<?php
/*
 * Hitchwiki Maps: js-translation.json.php
 * Return JSON array of translated strings to use in JavaScript
 * End use requires jQuery Gettext: http://plugins.jquery.com/project/gettext
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";

/*
 * Strings to be translated
 */
$strings = array(
	_("Toggle log")
);

$translated_strings = array();

// Translate all array values using gettext
foreach($strings as $string) {

	$translated_strings[$string] = _($string);

}

echo json_encode($translated_strings);

?>