<h2><label for="show_country"><?php echo _("Countries"); ?>:</label> <select id="show_country" name="show_country" style="margin-left: 20px; font-size:17px;">
	<option value=""><?php echo _("Select"); ?></option>
	<?php list_countries("option", "name", false, true, true); ?>
</select></h2>


<script type="text/javascript">
	$(function() {
	
		// Copy contents of the div to use when selecting "select"
		var startingData = $("#countryinfo").html();

		$("#show_country").change( function () { 
		
			var country = $(this).val();

			// When selecting "select", show the world map
			if(country=="") {

				$("#countryinfo").html(startingData);

			} else {
		
				// Hide infotable if visible
				if( $("#countryinfo").is(":visible") ) { $("#countryinfo").slideUp("fast"); }
				
				// Get country info
				$.ajax({
				  url: 'lib/country.php?format=html&country=' + country,
				  success: function(data) {
				  
					// Push info to the div
					$("#countryinfo").html(data);
				
					// Show infotable if hidden
					if( $("#countryinfo").is(":hidden") ) { $("#countryinfo").slideDown("fast"); }
					
				  }
				});
			}

		});

	});
</script>

<div id="countryinfo">
	<p><em>"<?php echo _("I haven't been everywhere, but it's on my list."); ?>"</em></p>
	<iframe src="lib/map_statistics.php?map=3" width="820" height="430" border="0" style="border:0;"></iframe>
</div>