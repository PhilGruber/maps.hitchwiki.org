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



$query = "SELECT `fk_user`,`fk_point`,`waitingtime`,`datetime` FROM `t_waitingtimes` WHERE `fk_point` = '".mysql_real_escape_string($_GET["id"])."'";

// Build an array
$res = mysql_query($query);
if(!$res) return $this->API_error("Query failed!");

?>
<br />
<table cellpadding="0" cellspacing="0" class="infotable smaller">
	<thead>
	    <tr>
	    	<th><span class="ui-icon ui-icon-calendar"><?php echo _("Date"); ?></span></th>
	    	<th><span class="ui-icon ui-icon-clock"><?php echo _("Waiting time"); ?></span></th>
	    	<th><span class="ui-icon ui-icon-person"><?php echo _("User"); ?></span></th>
	    </tr>
	</thead>
	<tbody>
<?php
while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {

	echo '<tr>';
	echo '<td title="'.date("r",strtotime($r["datetime"])).'">'.date("j.n.Y", strtotime($r["datetime"])).'</td>';
	echo '<td>'.nicetime($r["waitingtime"]).'</td>';
	echo '<td>'.username($r["fk_user"]).'</td>';
	echo '</tr>';

}

?>
	</tbody>
</table>