<?php
/* Hitchwiki Maps - views.php
 * JS loads content from views-folder with this.
 *
 */

/*
 * Load config to set language
 */
require_once "../config.php";


/*
 * Returns an info-array about logged in user (or false if not logged in)
 */
$user = current_user();


if($_GET["type"] == "card") $type = "cards";
elseif($_GET["type"] == "page") $type = "pages";
else  die(_("Fatal error!"));

$file = "../views/".$type."/".$_GET["page"].".php";


/*
 * Show page
 */
if( isset($_GET["page"]) && !empty($_GET["page"]) && !ereg('[^0-9A-Za-z_-]', $_GET["page"]) && file_exists($file) ):

	include($file);

/*
 * Not found error
 */
else:

	echo '<h2>'._('Error 404 - page not found').'</h2>';
	echo '<br /><br />:-(';

	// For debugging:
	if($settings["debug"]==true) echo '<p>Page: '.htmlspecialchars($_GET["page"]).' | Type: '.$type.' | Lang: '.$settings["language"].'</p>';

endif;

?>