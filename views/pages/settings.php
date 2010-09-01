<?php
/*
 * Hitchwiki Maps: settings.php
 */



echo '<h2>'._("Settings").'</h2>';

// Show only when logged in
if($user["logged_in"]===true): ?>

	<?php if($user["admin"]===true): ?>
		<p><span class="icon tux"><?php echo _("You are an administrator."); ?></span></p>
	<?php endif; ?>
	
	<?php include('profile_form.php'); ?>

<?php 
// Not logged in?
else: ?>

	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;"> 
	    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
	    <?php echo _("You must be logged in to edit settings."); ?></p>
	</div>

<?php endif; ?>