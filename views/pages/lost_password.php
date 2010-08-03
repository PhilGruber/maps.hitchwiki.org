<h2><?php echo _("Lost password?"); ?></h2>


	<label for="email"><?php echo _("Your email address"); ?></label><br />
	<input type="text" name="email" id="email" size="30" maxlength="255" style="font-size: 17px; line-height: 17px; padding: 5px 6px;" />
	
	<br /><br />
	
    <button id="btn_reset"><?php echo _("Reset your password"); ?></button>
    
	<script type="text/javascript">
	$(function() {
		
		// add place
		$("#btn_reset").button({
            icons: {
                primary: 'ui-icon-power'
            }
		}).click(function(e) {
			e.preventDefault();
			alert("Reset");
		});
		
	});
	</script>