<h2><label for="show_country"><?php echo _("Public transport catalog"); ?>:</label> <select id="show_country" name="show_country" style="margin-left: 20px; font-size:17px;">
	<option value=""><?php echo _("Select"); ?></option>
	<?php 
	
	/* 
	 * List public transportation -countries
	 */
	 
	// Built up a query
	$query = "SELECT `country` FROM `t_ptransport` GROUP BY `country` ORDER BY `country` ASC";

	// Gather data
	start_sql();
	$result = mysql_query($query);
	if (!$result) {
	   die("Error: SQL query failed with countrycodes()");
	}
	
	while ($row = mysql_fetch_array($result)) {
		
		echo '<option value="'.$row["country"].'">'.ISO_to_country($row["country"]).'</option>';
	}
	
	?>
</select></h2>

<script type="text/javascript">
	$(function() {
	
		// Copy contents of the div to use when selecting "select"
		var startingData = $("#public_t").html();

		$("#show_country").change( function () { 
		
			var country = $(this).val();

			// When selecting "select", show the world map
			if(country=="") {

				$("#public_t").html(startingData);

			} else {
		
				// Hide infotable if visible
				if( $("#public_t").is(":visible") ) { $("#public_t").slideUp("fast"); }
				
				// Get country info
				$.ajax({
				  url: 'lib/public_transportation.php?format=html&country=' + country,
				  success: function(data) {
				  
					// Push info to the div
					$("#public_t").html(data);
				
					// Show infotable if hidden
					if( $("#public_t").is(":hidden") ) { $("#public_t").slideDown("fast"); }
					
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
