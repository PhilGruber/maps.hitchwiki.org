<h2><label for="show_country"><?php echo _("Countries"); ?>:</label> <select id="show_country" name="show_country" style="margin-left: 20px; font-size:17px;">
	<option value=""><?php echo _("Select"); ?></option>
	<?php list_countries("option", "name", false, true, true); ?>
</select></h2>

<div class="ui-state-error ui-corner-all hidden" style="padding: 0 .7em; margin: 20px 0;" id="countries_error"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
    <strong><?php echo _("Error"); ?>:</strong> <span class="error_text"></span></p>
</div>


<script type="text/javascript">
	$(function() {
	
		// Copy contents of the div to use when selecting "select"
		var startingData = $("#countryinfo").html();

		$("#show_country").change( function () { 
		
			var country_select = $(this);
			
			// Show "loading" and disable select
			show_loading_bar("<?php echo _("Loading..."); ?>");
			country_select.attr("disabled", true);
		
			var country = $(this).val();

			stats("country_info/"+country);
			
			// When selecting "select", show the world map
			if(country=="") {

				maps_debug("Countries: back to start");
				$("#countryinfo").html(startingData);
	
				// Hide "loading" and enable select
				hide_loading_bar();
				country_select.removeAttr("disabled");

			} else {
		
				// Hide infotable if visible
				if( $("#countryinfo").is(":visible") ) { $("#countryinfo").slideUp("fast"); }
				
				
				// Get country info
				$("#countryinfo").load('ajax/country.php?format=html&country=' + country, function(response, status, xhr) {
				
					// Hide "loading" and enable select
				  	hide_loading_bar();
					country_select.removeAttr("disabled");
					
					if (status == "error") {
						maps_debug("Sorry but there was an error: " + xhr.status + " " + xhr.statusText);
						if( $("#countryinfo").is(":visible") ) { $("#countryinfo").slideUp("fast"); }
						$("#countries_error .error_text").text("<?php echo _("Couldn't load info. Please try again."); ?>");
						$("#countries_error").slideDown();
					}
					else {
				  		maps_debug("Got countryinfo for "+country);
				  		
						// Show infotable
						$("#countryinfo").slideDown("fast");
						
						// Empty result? 
						if($("#countryinfo").text() == "") {
							$("#countries_error .error_text").text("<?php echo _("Couldn't load info. Please try again."); ?>");
							$("#countries_error").slideDown();
						}
					}
				});
				
				
			}

		});

	});
</script>

<div id="countryinfo">
	<p><em>"<?php echo _("I haven't been everywhere, but it's on my list."); ?>"</em></p>
	<iframe src="ajax/map_statistics.php?map=3" width="820" height="430" border="0" style="border:0;"></iframe>
</div>