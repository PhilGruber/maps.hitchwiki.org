<?php
/*
 * Hitchwiki Maps Admin: new_language.php
 */

if(isset($user) && $user["admin"]===true): ?>

<h1>Add new language to the Maps</h1>

		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;">
		    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		    <strong><?php echo _("Alert"); ?>:</strong> Now working yet.</p>
		</div>

<?php

if(isset($_POST["language_code"]) && isset($_POST["language_name"]) && isset($_POST["language_name_en"])) {

	if(empty($_POST["language_code"]) OR empty($_POST["language_name"]) OR empty($_POST["language_name_en"])) {
		// name error
		?>
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;">
		    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
		    <strong><?php echo _("Alert"); ?>:</strong> Empty language names or -code.</p>
		</div>
		<?php
	}
	else {
		// Add stuff to the DB


		/*
		#start_sql();

		$query = "ALTER TABLE `t_countries` ADD `".mysql_real_escape_string($_POST["language_code"])."` VARCHAR( 80 ) NULL DEFAULT NULL COMMENT '".mysql_real_escape_string($_POST["language_name_en"])."' AFTER `en_UK`";

		echo $query;

		$result = mysql_query($query);
		if (!$result) {
	   		die("Error: SQL query failed.");
		}

		$query = "ALTER TABLE `t_points` ADD `".mysql_real_escape_string($_POST["language_code"])."` TEXT NULL DEFAULT NULL COMMENT '".mysql_real_escape_string($_POST["language_name_en"])."' AFTER `en_UK`";

		echo $query;

		$result = mysql_query($query);
		if (!$result) {
	   		die("Error: SQL query failed.");
		}
		*/


		$status .= "Added language to the MySQL DB.<br />";

		// Establish new language .pots etc

		// TODO


		// Show further instructions
		?>
		<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; margin: 20px 0;">
		    <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>

		    <?php echo $status; ?>

		    <blockquote>
		    Now go to the config.php and add this to <span class="highlight">$settings["valid_languages"]</span> -array:
		    <br /><br />
		    <code>"<?php echo htmlspecialchars($_POST["language_code"]); ?>" =&gt; "<?php echo htmlspecialchars($_POST["language_name"]); ?>"</code>
		    <br /><br />
		    ...and add this to <span class="highlight">$settings["languages_in_english"]</span> -array:
		    <br /><br />
		    <code>"<?php echo htmlspecialchars($_POST["language_code"]); ?>" =&gt; "<?php echo htmlspecialchars($_POST["language_name_en"]); ?>"</code>
		    </blockquote>

		    </p>
		</div>
		<?php
	}



} // end if form submit


?>

<form method="post" action="./?page=new_language">

	<label for="language_name">"In Language" in original language</label> <small>Eg. "Auf Deutsch"</small><br />
	<input type="text" value="" name="language_name" id="language_name" />

	<br /><br />

	<label for="language_name_en">Language name in English</label> <small>Eg. "German"</small><br />
	<input type="text" value="" name="language_name_en" id="language_name_en" />

	<br /><br />

	<label for="language_code">Language code</label> <small>Eg. "de_DE"</small><br />
	<input type="text" value="" name="language_code" id="language_code" />

	<br /><br />

	<input type="submit" value="Add" class="button" />

</form>


<?php endif; ?>