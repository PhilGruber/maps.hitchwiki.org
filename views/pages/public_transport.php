<h2><label for="show_country"><?php echo _("Public transport catalog"); ?>:</label> <select id="show_country" name="show_country" style="margin-left: 20px; font-size:17px;">
	<option value=""><?php echo _("Select"); ?></option>
	<?php

	/*
	 * List public transportation -countries
	 */

	// Gather data
	start_sql();
	$result = mysql_query("SELECT `country` FROM `t_ptransport` GROUP BY `country` ORDER BY `country` ASC");
	if (!$result) {
	   die("Error: SQL query failed with countrycodes()");
	}

	while ($row = mysql_fetch_array($result)) {

		echo '<option value="'.$row["country"].'">'.ISO_to_country($row["country"]).'</option>';
	}

	?>
</select></h2>

<div class="ui-state-error ui-corner-all hidden" style="padding: 0 .7em; margin: 20px 0;" id="public_transport_error">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
    <strong><?php echo _("Error"); ?>:</strong> <span class="error_text"></span></p>
</div>

<script type="text/javascript">
	$(function() {

		// Copy contents of the div to use when selecting "select"
		var startingData = $("#public_t").html();

		$("#show_country").change( function () {


			if( $("#public_transport_error").is(":visible") ) { $("#public_transport_error").slideUp(); }

			var country_select = $(this);

			// Show "loading" and disable select
			show_loading_bar("<?php echo _("Loading..."); ?>");
			country_select.attr("disabled", true);

			var country = $(this).val();

			stats("public_transport/"+country);

			// When selecting "select", show the world map
			if(country=="") {

				maps_debug("Catalog: back to start");
				$("#public_t").html(startingData);

				// Hide "loading" and enable select
				hide_loading_bar();
				country_select.removeAttr("disabled");

			} else {

				// Hide infotable if visible
				if( $("#public_t").is(":visible") ) { $("#public_t").slideUp("fast"); }

				// Get public transport catalog
				$("#public_t").load('ajax/public_transportation.php?format=html&country=' + country, function(response, status, xhr) {

					// Hide "loading" and enable select
				  	hide_loading_bar();
					country_select.removeAttr("disabled");

					if (status == "error") {
						maps_debug("Sorry but there was an error: " + xhr.status + " " + xhr.statusText);
						if( $("#public_t").is(":visible") ) { $("#public_t").slideUp("fast"); }
						$("#public_transport_error .error_text").text("<?php echo _("Couldn't load info. Please try again."); ?>");
						$("#public_transport_error").slideDown();
					}
					else {
				  		maps_debug("Got catalog for "+country+" ("+status+")");

						// Show infotable
						$("#public_t").slideDown("fast");

						// Empty result?
						if($("#public_t").text() == "") {
							$("#public_transport_error .error_text").text("<?php echo _("Couldn't load info. Please try again."); ?>");
							$("#public_transport_error").slideDown();
						}
					}
				});

			}

		});

	});
</script>

<div id="public_t">

	<p><?php echo _("Find timetables for the public transport."); ?></p>

</div>


<?php if($user["logged_in"]===true): ?>

<br /><br />

<button id="btn_add_public_transport"><?php echo _("Add a page to the catalog"); ?></button>

<script type="text/javascript">
$(function() {

	$("#btn_add_public_transport").button({
	    icons: {
	        primary: 'ui-icon-plusthick'
	    }
	}).click(function(e) {
		e.preventDefault();
		open_page('add_public_transport');
    });
});
</script>

<?php endif; ?>
