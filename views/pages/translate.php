
<h3><?php printf(_("Hitchwiki Maps is currently available in %s languages"), count($settings["valid_languages"])); ?>:</h3>

<ul class="clean" style="width: 300px;">
<?php 
// Print out a list of current languages
foreach($settings["valid_languages"] as $code => $lang) {

	echo '<li><a href="./?page=translate&amp;lang='.$code.'" title="'._("Choose language").'"><img class="flag" alt="" src="static/gfx/flags/png/'.strtolower(substr($code, -2)).'.png" /> '.$lang.'</a> ('.$settings["languages_in_english"][$code].')</li>';

}

?>
</ul>

<h3><?php echo _("Help us with translating!"); ?></h3>

<p><?php echo _("If you would like to add content in your own language, or translate all visible interface texts, contact us so we can put up a new language."); ?></p>

<p><a href="mailto:<?php echo $settings["email"]; ?>"><?php echo $settings["email"]; ?></a></p>