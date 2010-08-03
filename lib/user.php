<?php
/*
 * Hitchwiki Maps: user.php
 * Show information about users
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";

/*
 * Returns an info-array about logged in user (or false if not logged in) 
 */
$user = current_user();

start_sql();


/* 
 * Gather data
 */
if(isset($_GET["id"]) && is_numeric($_GET["id"])) {

	$user["id"] = $_GET["id"];
	
   $res = mysql_query("SELECT * FROM `users`");
   while($r = mysql_fetch_row($res)) {
       $user["id"] = $r[0];
       $user["name"] = $r[1];
       $user["email"] = $r[3];
       $user["registered"] = $r[4];
       $user["location"] = $r[5];
       $user["country"] = $r[6];
       $user["language"] = $r[7];
       #$user["admin"] = $r[8];
   }

}
else {
	echo 'Choose user.';
	exit;
}


/* 
 * Choose format
 */
if(isset($_GET["format"]) && strtolower($_GET["format"]) == "json") $format = "json";
elseif(isset($_GET["format"]) && strtolower($_GET["format"]) == "html") $format = "html";
else $format = "array";

/* 
 * Print it out in:
 * json (default) | html | array (debugging)
 */
 
// Array
if($format == 'array') {

	echo '<pre>';
	print_r($user);
	echo '</pre>';

}
elseif($format == 'json') {

	echo json_encode($user);

} 
// HTML
elseif($format == 'html') {
?>

<div class="align_left" style="margin: 0 40px 20px 0;">

	<h3><?php echo $user["name"]; ?></h3>
	
	<table class="infotable" cellspacing="0" cellpadding="0">
	    <tbody>
	    
	    	<?php 
	    	// Registered
	    	if(!empty($user["registered"])): ?>
	    	<tr>
	    		<td><b><?php echo _("Registered"); ?></b></td>
	    		<td><?php echo date("j.n.Y", strtotime($user["registered"])); ?></td>
	    	</tr>
	    	<?php endif; ?>
	    	
	    	<?php 
	    	// Location + country
	    	if(!empty($user["location"]) OR !empty($user["country"])): ?>
	    	<tr>
	    		<td><b><?php echo _("Location"); ?></b></td>
	    		<td><?php 
	    			
	    			// Location
	    			if(!empty($user["location"])) echo $user["location"].", ";
	    			
	    			// Country + flag
	    			if(!empty($user["country"])) echo ISO_to_country($user["country"]).' <img class="flag" alt="'.$user["country"].'" src="static/gfx/flags/png/'.strtolower($user["country"]).'.png" />';

	    		?></td>
	    	</tr>
	    	<?php endif; ?>
	    	
	    </tbody>
	</table>
	
</div>


<div class="align_left" style="margin: 0 40px 20px 0;">

<?php if(!empty($user["cities_count"])): ?>
	<h3><?php 
	
	if($user["cities_count"] < 5) $top = $user["cities_count"];
	else $top = "5";
	
	printf( _( 'Top %s cities' ), $top ); ?></h3>
	<table class="infotable" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo _("City"); ?></th>
				<th><?php echo _("Places"); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php /* list_cities("tr", "markers", 5, true, false, $user["id"]); */ ?>
		</tbody>
	</table>
<?php endif; ?>
	
	
<?php if(!empty($user["countries_count"])): ?>
	<h3><?php 
	
	if($user["countries_count"] < 5) $top = $user["countries_count"];
	else $top = "5";
	
	printf( _( 'Top %s countries' ), $top ); ?></h3>
	<table class="infotable" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo _("Country"); ?></th>
				<th><?php echo _("Places"); ?></th>
			</tr>
		</thead>
		<tbody>
		
		</tbody>
	</table>
<?php endif; ?>

</div>

<?php /*
<div class="align_left" style="margin: 0 0 20px 0;">
	
	<h3><img class="flag" alt="<?php echo $country["iso"]; ?>" src="static/gfx/flags/png/<?php echo strtolower($country["iso"]); ?>.png" /> <?php echo $country["name"]; ?></h3>

	
</div>
<?php */ ?>
<div class="clear"></div>

<?php
}

?>