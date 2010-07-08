
<h4>Download a KML-file of markers:</h4>

<input type="checkbox" name="gmz" value="true" id="gmz" /> <label for="gmz">Zipped (GMZ)</label><br />

<ul class="clean">
	<li><a href="#"><?php echo _("World"); ?></a> <small>(# <?php echo _("markers"); ?>)</small></li>
	<li><a href="#"><?php echo _("Visible area on the map"); ?></a> <small>(# <?php echo _("markers"); ?>)</li>
	<li><label for="country"><?php echo _("Continent"); ?>:</label> <select id="country" name="country">
		<option value=""><?php echo _("Select"); ?></option>
		<option value="europe"><?php echo _("Asia"); ?> (#)</option>
		<option value="africa"><?php echo _("Africa"); ?> (#)</option>
		<option value="north_america"><?php echo _("North America"); ?> (#)</option>
		<option value="south_america"><?php echo _("South America"); ?> (#)</option>
<!--	<option value="antarctica"><?php echo _("Antarctica"); ?> (#)</option>-->
		<option value="australia"><?php echo _("Australia"); ?> (#)</option>
	</select></li>
	<li><label for="country"><?php echo _("Country"); ?>:</label> <select id="country" name="country">
		<option value=""><?php echo _("Select"); ?></option>
		<?php list_countries("option", "name"); ?>
	</select></li>
</ul>

<p><img src="static/gfx/kml_big.png" class="png align_left" alt="" /><a href="http://en.wikipedia.org/wiki/Kml"><b>KML</b></a> is a Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed posuere interdum sem.</p>
