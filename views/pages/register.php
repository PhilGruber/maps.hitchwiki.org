<h2><?php echo _("Register!"); ?></h2>

<?php if($user===false): ?>

	<?php include('../lib/profile_form.php'); ?>

<?php else: ?>

	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;"> 
	    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
	    <?php echo _("You are already registered!"); ?></p>
	</div>

<?php endif; ?>