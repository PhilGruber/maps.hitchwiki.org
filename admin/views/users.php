<?php
/*
 * Hitchwiki Maps Admin: users.php
 */

if(isset($user) && $user["admin"]===true): ?>

<?php 

start_sql();




/*
 * GET ACTIONS (confirms)
 */
 
 
if(isset($_GET["promote"])):
	$confirm_name = "promote";
	$confirm_id = $_GET["promote"];
	$confirm_txt = 'Are you sure you want to promote "'.username($_GET["promote"]).'" to be an admin?';

 
elseif(isset($_GET["demote"])):
	$confirm_name = "demote";
	$confirm_id = $_GET["demote"];
	$confirm_txt = 'Are you sure you want to demote "'.username($_GET["promote"]).'" to be normal user?';
	
 
elseif(isset($_GET["remove"])):
	$confirm_name = "remove";
	$confirm_id = $_GET["remove"];
	$confirm_txt = 'Are you sure you want to remove user "'.username($_GET["remove"]).'"?';
	
// End of get-actions
endif;

// Show confirm now
if(isset($confirm_id)):
?>
<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; margin: 20px 0;" id="info_bubble"> 
    <p><span class="ui-icon ui-icon-circle-check" style="float: left; margin-right: .3em;"></span> 
    <form method="post" action="./?page=users" style="display:inline;" id="confirm_form">
    <?php 
    	echo '<input type="hidden" name="'.$confirm_name.'" value="'.strip_tags($confirm_id).'" />'; 
    	echo $confirm_txt.'<br /><br />';
    
    ?><button id="yes">Yes</button><button id="cancel">Cancel</button></form></p>
</div>
<script type="text/javascript">
$(function() {

	// Yes
    $("#info_bubble #yes").button({
        icons: {
            primary: 'ui-icon-check'
        }
    }).click(function(e) {
    	e.preventDefault();
		$("#confirm_form").submit();
	});
	

	// Cancel
    $("#info_bubble #cancel").button({
        icons: {
            primary: 'ui-icon-cancel'
        }
    }).click(function(e) {
    	e.preventDefault();
		$("#info_bubble").fadeOut('slow');
	});
	
});
</script>
<?php
endif;




/*
 * POST(and some get) ACTIONS
 */

if(isset($_POST["promote"])):

	$result = mysql_query("UPDATE `t_users` SET `admin` = '1' WHERE `t_users`.`id` = ".mysql_real_escape_string($_POST["promote"])." LIMIT 1");
	if (!$result) die("Error: SQL query failed.");
	
	$info_txt = username($_POST["promote"]).' promoted to be admin.';


elseif(isset($_POST["demote"])):

	$result = mysql_query("UPDATE `t_users` SET `admin` = NULL WHERE `t_users`.`id` = ".mysql_real_escape_string($_POST["demote"])." LIMIT 1");
	if (!$result) die("Error: SQL query failed.");
	
	$info_txt = username($_POST["demote"]).' demoted to be normal user.';


elseif(isset($_POST["remove"])):

	$result = mysql_query("DELETE FROM `t_users` WHERE `t_users`.`id` = ".mysql_real_escape_string($_POST["remove"])." LIMIT 1");
	if (!$result) die("Error: SQL query failed.");
	
	$info_txt = username($_POST["remove"]).' removed permanently.';


elseif(isset($_GET["edit"])):
	?>
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;"> 
		    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
		    <strong><?php echo _("Alert"); ?>:</strong> Editing users isn't in use yet.</p>
		</div>
	<?php


elseif(isset($_GET["user"])):

	include("../views/pages/profile.php");

// End of post-actions
endif;
 
 
 
/*
 * Show info bubble
 */
 
if(isset($info_txt)): 
?>
<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; margin: 20px 0;" id="info_bubble"> 
    <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span> 
    <?php echo $info_txt; ?></p>
</div>
<script type="text/javascript">
$(function() {
	$("#info_bubble").delay(2500).fadeOut('slow');
});
</script>
<?php
endif;
 
 



/*
 * List all users
 */ 

	// Built up a query
	$query = "SELECT * FROM `t_users` ORDER BY `admin` DESC";
	
	// Gather data
	$result = mysql_query($query);
	if (!$result) {
	   die("Error: SQL query failed.");
	}
	
	$usercount = mysql_num_rows($result);
	
	// If some results, print out
	if($usercount >= 1) {
?>

	<h1>Users</h1>

	<p><?php printf(_("We have %s registered hitchhikers!"), $usercount); ?></p>
	
	<table class="infotable" id="users_list" cellspacing="0" cellpadding="0">
	    <thead>
	    	<tr>
	    		<th><?php echo _("ID"); ?></th>
	    		<th><?php echo _("User"); ?></th>
	    		<th><?php echo _("Member since"); ?></th>
	    		<th><?php echo _("Location"); ?></th>
	    		<th><?php echo _("Country"); ?></th>
	    		<th><?php echo _("Email"); ?></th>
	    		<th><?php echo _("Language"); ?></th>
	    		<th><?php echo _("Latitude"); ?></th>
	    		<th><?php echo _("Gravatar"); ?></th>
	    		<th><?php echo _("Admin"); ?></th>
	    		<th><?php echo _("Manage"); ?></th>
	    	</tr>
	    </thead>
	    <tbody>
	    	<?php
	    	// Print out page rows
			while ($row = mysql_fetch_array($result)) {
	    		echo '<tr valign="top">';
	    		
	    		// ID
	    		echo '<td><a href="./?page=users&amp;user='.$row["id"].'">'.$row["id"].'</a></td>';
	    		
	    		
	    		// Name
	    		echo '<td>'.htmlspecialchars($row["name"]);
	    		if($row["id"] == $user["id"]) echo ' <small class="highlight">&mdash; '._("That's you!").'</small>';
	    		echo '</td>';
	    		
	    		
	    		// Registered
	    		echo '<td style="text-align: right;">'.date("j.n.Y", strtotime($row["registered"])).'</td>';
	    		
	    		
	    		// Location
	    		if(!empty($row["location"])) echo '<td>'.htmlspecialchars($row["location"]).'</td>';
	    		else echo '<td> </td>';
	    		
	    		
	    		// Country
	    		if(!empty($row["country"])) echo '<td>'.ISO_to_country($row["country"]) . ' <img class="flag" alt="" src="../static/gfx/flags/png/'.strtolower($row["country"]).'.png" /></td>';
	    		else echo '<td> </td>';
	    		
	    		
	    		// Email
	    		if(!empty($row["email"])) echo '<td><a href="mailto:'.$row["email"].'">'.htmlspecialchars($row["email"]).'</a></td>';
	    		else echo '<td> </td>';
	    		
	    		
	    		// Language
	    		if(!empty($row["language"])) echo '<td title="'.$row["language"].'">'.$settings["languages_in_english"][$row["language"]].'</td>';
	    		else echo '<td> </td>';
	    		
	    		
	    		// Latitude
	    		if(!empty($row["google_latitude"])) echo '<td><a href="http://www.google.com/latitude/apps/badge/api?user='.urlencode($row["google_latitude"]).'&amp;type=iframe&amp;maptype=roadmap" title="Show position"><img src="../static/gfx/icons/google.gif" alt="Yes" /></a></td>';
	    		else echo '<td> </td>';
	    		
	    		
	    		// Gravatar
	    		if(!empty($row["allow_gravatar"]) && !empty($row["email"])) echo '<td><a href="http://www.gravatar.com/'.md5($row["email"]).'/" title="Show profile"><img src="http://www.gravatar.com/avatar/'.md5($row["email"]).'/?s=16" alt="Yes" /></a></td>';
	    		elseif(!empty($row["allow_gravatar"])) echo '<td><img src="../static/gfx/icons/gravatar.gif" alt="Yes" /></td>';
	    		else echo '<td> </td>';
	    		
	    		
	    		// Is admin?
	    		if($row["admin"] == '1') echo '<td class="icon tux"> </td>';
	    		else echo '<td> </td>';
	    		
	    		
	    		// Tools
				?>
				<td>
				
				<?php if($row["admin"] == '1'): ?>
					<a href="./?page=users&amp;demote=<?php echo $row["id"]; ?>" class="ui-icon ui-icon-person align_right" title="Demote to normal user"</a>
				<?php else: ?>
					<a href="./?page=users&amp;promote=<?php echo $row["id"]; ?>" class="ui-icon ui-icon-star align_right" title="Promote to admin"></a>
				<?php endif; ?>
				
					<a href="./?page=users&amp;remove=<?php echo $row["id"]; ?>" class="remove_user ui-icon ui-icon-trash align_right" title="Remove user permanently"></a>
					<a href="./?page=users&amp;edit=<?php echo $row["id"]; ?>" class="ui-icon ui-icon-pencil align_right" title="Edit user"></a>
				</td>
				<?php
	    		
	    		echo '</tr>';
	    	}
	    	?>
	    </tbody>
	</table>
	

<?php
	} // if found users?
?>
	
		
<?php endif; // user check ?>