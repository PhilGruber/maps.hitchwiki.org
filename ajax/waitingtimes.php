<?php
/*
 * Hitchwiki Maps: waitingtimes.php
 * Show a waiting time log for a place
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";

start_sql();

/*
 * Returns an info-array about logged in user (or false if not logged in) 
 */
$user = current_user();


/* 
 * Check ID
 */
if(!isset($_GET["id"]) OR !is_numeric($_GET["id"])) {
	?>
	<div class="ui-widget">
	    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
	    	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
	    	<?php echo _("Error!"); ?></p>
	    </div>
	</div>
	<?php
	exit;
}


/* 
 * List out
 */

// Build an array
$res = mysql_query("SELECT `fk_user`,`fk_point`,`waitingtime`,`datetime` FROM `t_waitingtimes` WHERE `fk_point` = '".mysql_real_escape_string($_GET["id"])."'");
if(!$res) return $this->API_error("Query failed!");

// Gather data first into an array, so we can tell if there 
// were records by current user, and print out little different <thead>
$current_user_rows = false;
while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {

	$waitingtimes[] = array(
		"datetime" 		=> strtotime($r["datetime"]),
		"waitingtime" 	=> nicetime($r["waitingtime"]),
		"username" 		=> username($r["fk_user"]),
		"user_id" 		=> $r["fk_user"]
	);
	
	if($user["id"] == $r["fk_user"]) $current_user_rows = true;
}

?>
<br />
<table cellpadding="0" cellspacing="0" class="infotable smaller">
	<thead>
	    <tr>
	    	<th><span class="ui-icon ui-icon-calendar"><?php echo _("Date"); ?></span></th>
	    	<th><span class="ui-icon ui-icon-clock"><?php echo _("Waiting time"); ?></span></th>
	    	<th><span class="ui-icon ui-icon-person"><?php echo _("User"); ?></span></th>
	    	<?php if($current_user_rows===true) echo '<th> </th>'; ?>
	    </tr>
	</thead>
	<tbody>
<?php

// Print out the array
foreach($waitingtimes as $waitingtime) {

	echo '<tr>';
	echo '<td title="'.date("r", $waitingtime["datetime"]).'">'.date("j.n.Y", $waitingtime["datetime"]).'</td>';
	echo '<td>'.$waitingtime["waitingtime"].'</td>';
	echo '<td>'.$waitingtime["username"].'</td>';
	
	// Print extra cell if in this list there are some of this users waitingtimes. 
	// Print delete-icon into users own rows
	if($current_user_rows===true && $user["id"] == $waitingtime["user_id"]) echo '<td><a href="#" class="remove_waitingtime ui-icon ui-icon-trash align_right" title="'._("Remove record").'"></a></td>';
	elseif($current_user_rows===true) echo '<td> </td>';
	
	echo '</tr>';
}

?>
	</tbody>
</table>

<script type="text/javascript">
	$(function() {
	
	    // Remove time row
	    $(".remove_waitingtime").click(function(e) {
	    	e.preventDefault();
	    	maps_debug("Remove waiting time");
	    	alert("Not in use yet...");
	    });

	});
</script>
