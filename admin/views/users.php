<?php
/*
 * Hitchwiki Maps Admin: users.php
 */

if(isset($user) && $user["admin"]===true): ?>

<h1>Users</h1>

<?php if(isset($_GET["remove"]) OR isset($_GET["edit"]) OR isset($_GET["user"])): ?>
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;"> 
		    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
		    <strong><?php echo _("Alert"); ?>:</strong> Not in use yet!</p>
		</div>
<?php endif; ?>


<?php

	// Built up a query
	$query = "SELECT * FROM `t_users` ORDER BY `admin` DESC";
	
	// Gather data
	start_sql();
	$result = mysql_query($query);
	if (!$result) {
	   die("Error: SQL query failed.");
	}
	
	$usercount = mysql_num_rows($result);
	
	// If some results, print out
	if($usercount >= 1) {
?>

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
				<a href="./?page=users&amp;remove=<?php echo $row["id"]; ?>" class="remove_user ui-icon ui-icon-trash align_right" title="<?php echo _("Remove user permanently"); ?>"></a>
				<a href="./?page=users&amp;edit=<?php echo $row["id"]; ?>" class="ui-icon ui-icon-pencil align_right" title="<?php echo _("Edit user"); ?>"></a>
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