<?php
/*
 * Hitchwiki Maps: profile_form.php
 * Include this to show settings/registeration form
 *
 * This form works in two ways:
 * - For editing settings
 * - Registeration
 *
 */

// If user is logged in -> settings, if not -> register
if($user["logged_in"]===true) $profile_form = "settings";
else $profile_form = "register";

?>
<div class="ui-state-error ui-corner-all hidden" style="padding: 0 .7em; margin: 20px 0;" id="profile_alert"> 
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
    <strong><?php echo _("Alert"); ?>:</strong> <span class="alert_text"></span></p>
</div>

<div class="ui-state-highlight ui-corner-all hidden" style="padding: 0 .7em; margin: 20px 0;" id="profile_info"> 
    <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span> 
    <span class="info_text"></span></p>
</div>


<form method="post" action="#" id="profile_form">

<div style="float: left; width: 220px;">

	<label for="email"><?php echo _("Email"); ?></label> <small>(<?php echo _("Required"); ?>)</small><br />
	<input type="text" name="email" id="email" size="20" maxlength="255" value="<?php if(isset($user["email"])) echo htmlspecialchars($user["email"]); ?>" />
	
	<br /><br />

	<label for="name"><?php echo _("Name"); ?></label> <small>(<?php echo _("Required"); ?>)</small><br />
	<input type="text" name="name" id="name" size="20" maxlength="80" value="<?php if(isset($user["name"])) echo htmlspecialchars($user["name"]); ?>" />
	
	<br /><br />
	
	<label for="language"><?php echo _("Language"); ?></label><br />
	<select name="language" id="language" title="<?php echo _("Choose language"); ?>">
	    <?php
	    // Print out available languages
	    foreach($settings["valid_languages"] as $code => $name) {
	    	echo '<option value="'.$code.'"';
	    	
	    	if(isset($user["language"]) && $code == $user["language"]) echo ' selected="selected"';
	    	elseif($code == $settings["language"]) echo ' selected="selected"';
	    	
	    	echo '>'.$name.'</option>';
	    }
	    ?>
	</select>
	
	<br /><br />

</div>
<div style="float: left; width: 350px;">


	<label for="password1"><?php echo _("Password"); ?></label> <small>(<?php 
			
			echo _("Min 8 chars.")." ";
			
			if($profile_form=="settings") echo _("Leave empty to keep old"); 
			else echo _("Required"); 
		
		?>)</small><br />
	<input type="password" name="password1" id="password1" size="20" maxlength="80" />
	
	<br /><br />
	
	<label for="password12"><?php echo _("Password again"); ?></label><br />
	<input type="password" name="password2" id="password2" size="20" maxlength="80" />
	
	<br /><br />
	
	<label for="location"><?php echo _("Location"); ?></label> <small>(<?php echo _("Can be anything, most likely a city."); ?>)</small><br />
	<input type="text" name="location" id="location" size="20" maxlength="255" value="<?php if(isset($user["location"])) echo htmlspecialchars($user["location"]); ?>" />
	
	<br /><br />
	
	<label for="country"><?php echo _("Country"); ?></label> <small>(<?php echo _("Map will be centered to here"); ?>)</small><br />
	<select id="country" name="country">
		<option value=""><?php echo _("I'd rather not tell"); ?></option>
		<option value="">-------------</option>
		<?php list_countries("option", "name", false, false, true); ?>
	</select> &nbsp; <img class="flag" alt="" src="" class="hidden" />
	

</div>
<div class="clear"></div>

<br /><br />

<button id="btn_profile_form_cancel"><?php echo _("Cancel"); ?></button>

<button id="btn_profile_form"><?php 
	if($profile_form=="settings") echo _("Update"); 
	else echo _("Register!"); 
?></button>

<script type="text/javascript">
$(function() {

	<?php 
	// Set country selection
	if($profile_form=="settings" && !empty($user["country"])) {
		echo '$("#profile_form #country").val("'.$user["country"].'");'; 
		echo '$("#profile_form .flag").attr("src","static/gfx/flags/png/'.strtolower($user["country"]).'.png");';	
	}
	?>
	
	$("#profile_form #country").change( function () { 
		
		var selected_country = $(this).val();
		if(selected_country != "") {
			$("#profile_form .flag").attr("src","static/gfx/flags/png/"+selected_country.toLowerCase()+".png");
			$("#profile_form .flag").show();
		} else {
			$("#profile_form .flag").hide();
		}
	
	});
	

	// Cancel
    $("#btn_profile_form_cancel").button({
        icons: {
            primary: 'ui-icon-cancel'
        }
    }).click(function(e) {
    	e.preventDefault();
    	maps_debug("Cancel: <?php echo $profile_form; ?>");
    	close_page();
    });

    // add place
    $("#btn_profile_form").button({
        icons: {
            primary: 'ui-icon-heart'
        }
    }).click(function(e) {
    	e.preventDefault();
    	maps_debug("Attempting to do: <?php echo $profile_form; ?>.");
    	
    	$("#profile_alert").hide();
    	
    	if($("#profile_form #email").val() == "" || $(" #profile_form #name").val() == ""<?php 
    		if($profile_form=="register"): 
    			?> || $("#profile_form #password1").val() == "" || $("#profile_form #password2").val() == ""<?php 
    		endif; 
    		?>) {
			maps_debug("Some required fields missing...");
			$("#profile_alert .alert_text").text("<?php echo _("Please fill all required fields!"); ?>");
			$("#profile_alert").show();
    	} else {
    	
    		show_loading_bar("Loading...");
    		$("#profile_form").hide();

			// Call API
			var p_email = $("#profile_form #email").val();
			var p_name = $("#profile_form #name").val();
			var p_password1 = $("#profile_form #password1").val();
			var p_password2 = $("#profile_form #password2").val();
			var p_language = $("#profile_form #language").val();
			var p_location = $("#profile_form #location").val();
			var p_country = $("#profile_form #country").val();
			
			$.post('lib/user_settings.php?<?php echo $profile_form; ?>', { 
																			email: p_email, 
																			name: p_name,  
																			password1: p_password1, 
																			password2: p_password2, 
																			language: p_language, 
																			location: p_location, 
																			country: p_country<?php
																			
																			// Send current logged in user ID if we're about to udpate settings...
																			if($profile_form=="settings" && !empty($user["id"])) echo ', user_id: '.$user["id"];
																			
																			?> 
																		}, 
			    function(data) {
			    	hide_loading_bar();
			
					if(data.success==true) {
						<?php if($profile_form=="register"): ?>
							maps_debug("Registeration complete.");
							info_dialog("<?php echo _("Welcome to the Hitchwiki Maps!<br /><br />You can now proceed to login."); ?>","<?php echo _("Registeration complete!"); ?>");
							close_page();
							
							if($("#loginOpener").hasClass("open")==false) {
								$("#loginOpener").addClass("open");
    							$("#loginPanel").slideDown("fast");
    						}
    						$("#loginPanel #email").val(data.email);
    					<?php else: ?>
    						
							maps_debug("Updating settings complete.");
							$("#profile_form").show();
							$("#profile_info .info_text").text("Settings updated!");				
							$("#profile_info").fadeIn(300).delay(5000).fadeOut(300);
							
							// Login information was changed, so ask them to login again
							if(data.login_changed==true) {
								$("#reloadPage input").click();
							}
    					
    					<?php endif; ?>
					} else {
						maps_debug("Process (<?php echo $profile_form; ?>) failed. Error: "+data.error);
						$("#profile_form").show();
						
						$("#profile_alert .alert_text").text("<?php 
							
							if($profile_form=="register") echo _("Registeration failed:"); 
							else echo _("Updating settings failed:");
						
						?> "+data.error);				
						$("#profile_alert").show();
					}
			    	
			    }
			,"json"); // post end
		} // else end
			
    }); // click end
    		
});
</script>

</form>
