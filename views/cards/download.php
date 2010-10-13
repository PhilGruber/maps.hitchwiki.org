
<h4><?php echo _("Download a KML-file of markers"); ?></h4>

<form>
<div id="fileloader"></div>

<ul class="clean">
	<li><a href="#" id="download_world"><?php echo _("World"); ?></a> <small>(# <?php echo _("markers"); ?>)</small></li>
	<li><a href="#" id="download_visible"><?php echo _("Visible area on the map"); ?></a> <small>(# <?php echo _("markers"); ?>)</li>
	<li><label for="download_continent"><?php echo _("Continent"); ?>:</label> <select id="download_continent" name="download_continent">
		<option value=""><?php echo _("Select"); ?></option>
		<?php list_continents("option",true); ?>
	</select></li>
	<li><label for="download_country"><?php echo _("Country"); ?>:</label> <select id="download_country" name="download_country">
		<option value=""><?php echo _("Select"); ?></option>
		<?php list_countries("option", "name"); ?>
	</select></li>
</ul>

<script type="text/javascript">
	$(function() {
	
		// Download:	
	
		// Continent
		$("#download_continent").change( function () { 
			var download_value = $(this).val();
			fileloader('continent='+download_value, 'continent-'+download_value);
		});
		
		// Country
		$("#download_country").change( function () { 
			var download_value = $(this).val();
			fileloader('country='+download_value, 'country-'+download_value);
		});
		
		// World
		$("#download_world").click( function () { 
			fileloader('all', 'world');
		});
		
		// Visible area on the map
		$("#download_visible").click( function () { 
			alert("Not in use yet.");
		});
		
		function fileloader(url,name) {
			maps_debug("Downloading a KML file: "+name+".kml");
			$("#fileloader").html('<iframe src="http://www.ihminen.org/maps.hitchwiki.org/api/?format=kml&amp;download='+name+'&amp;'+url+'" width="0" height="0" style="border:0; overflow: hidden; margin: 0;" scrolling="no" frameborder="0" allowtransparency="true"></iframe>');
		
		}
		
	});
</script>
<input type="checkbox" name="gmz" value="true" id="gmz" /> <label for="gmz"><?php echo _("Zipped"); ?> (GMZ)</label><br />

</form>
<br /><br />

<div class="clear">
<p><img src="static/gfx/kml_big.png" class="png align_left" alt="" /><?php printf(_("%s is widely used format for sharing location based information."), '<a href="http://en.wikipedia.org/wiki/Kml"><b>KML</b></a>'); ?></p>
</div>

<br /><br />

<hr />

<p><a href="http://www.openstreetmap.org/export?lat=67.2&lon=-118.5&zoom=4&layers=M" class="icon icon-osm"><?php echo _("Use Open Street Map Export tool"); ?></a></p>
