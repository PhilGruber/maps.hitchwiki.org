<?php
/*
 * Hitchwiki Maps: public_transport.php
 * List public transportation webpages
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";


/*
 * Gather data
 */
if(isset($_GET["country"]) && strlen($_GET["country"]) == 2) {

	$country["iso"] = 			htmlspecialchars(strtoupper($_GET["country"]));
	$country["name"] = 			ISO_to_country($country["iso"]);

}
else {
	echo 'Choose country.';
	exit;
}


/*
 * Print it out:
 */
?>

	<h3><img class="flag" alt="<?php echo $country["iso"]; ?>" src="static/gfx/flags/<?php echo strtolower($country["iso"]); ?>.png" /> <?php echo $country["name"]; ?></h3>

	<?php pt_list($country["iso"]); ?>
