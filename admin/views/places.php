<?php
/*
 * Hitchwiki Maps Admin: places.php
 */

if(isset($user) && $user["admin"]===true): ?>

<h1>Places</h1>


		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;">
		    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		    <strong><?php echo _("Alert"); ?>:</strong> Not here yet!</p>
		</div>


<?php endif; // user check ?>