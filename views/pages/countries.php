<h2><?php echo _("Countries"); ?></h2>

<label for="show_country"><?php echo _("Country"); ?>:</label> <select id="show_country" name="show_country">
	<option value=""><?php echo _("Select"); ?></option>
	<?php list_countries("option", "name", false, false); ?>
</select>
<script type="text/javascript">
	$(function() {

		$("#show_country").change( function () { 
			//$("div").text("Something was selected").show().fadeOut(1000); 
			alert("test");
		});

	});
</script>