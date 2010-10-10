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

<div style="float: left; width: 230px; padding-right: 20px;">

	<label for="email"><?php echo _("Email"); ?></label> <small>(<?php echo _("Required"); ?>)</small><br />
	<input type="text" name="email" id="email" size="25" maxlength="255" value="<?php if(isset($user["email"])) echo htmlspecialchars($user["email"]); ?>" />
	
	<br /><br />

	<label for="name"><?php echo _("Name"); ?></label> <small>(<?php echo _("Required"); ?>)</small><br />
	<input type="text" name="name" id="name" size="25" maxlength="80" value="<?php if(isset($user["name"])) echo htmlspecialchars($user["name"]); ?>" />
	
	<br /><br />

	<label for="password1"><?php echo _("Password"); ?></label> <small>(<?php 
	    	
	    	if($profile_form=="settings") echo _("Leave empty to keep old"); 
	    	else echo _("Required"); 
	    
	    ?>)</small><br />
	<div style="width: 210px;">
	<input type="password" name="password1" id="password1" size="25" maxlength="80" />
	</div>
	
	<br />
	
	<label for="password12"><?php echo _("Password again"); ?></label><br />
	<input type="password" name="password2" id="password2" size="25" maxlength="80" />
		
	<br /><br />
	
</div>
<div style="float: left; width: 350px;">

	
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
	
	<label for="location"><?php echo _("Location"); ?></label> <small>(<?php echo _("Can be anything, most likely a city."); ?>)</small><br />
	<div class="ui-widget">
	<input type="text" name="location" id="location" size="25" maxlength="255" value="<?php if(isset($user["location"])) echo htmlspecialchars($user["location"]); ?>" />
	</div>
	
	<br />
	
	<label for="country"><?php echo _("Current country"); ?></label> <small>(<?php echo _("Map will be centered to here"); ?>)</small><br />
	<select id="country" name="country">
		<option value=""><?php echo _("I'd rather not tell"); ?></option>
		<option value="">-------------</option>
		<?php list_countries("option", "name", false, false, true); ?>
	</select> &nbsp; <img class="flag" alt="" src="" class="hidden" />
	
	<br /><br />
	
	<label for="google_latitude"><?php echo _("Google Latitude user ID"); ?></label><br />
	<input type="text" name="google_latitude" id="google_latitude" size="25" maxlength="80" value="<?php if(isset($user["google_latitude"])) echo htmlspecialchars($user["google_latitude"]); ?>" />
	<br />
	<img src="static/gfx/icons/latitude-icon-small.png" alt="Google Latitude" class="align_left" style="margin: 5px 5px 5px 0;" /><small><?php printf(_('<a href="%s" target="_blank">Enable Google Latitude</a> first and copy here your 20-digit user ID from the bottom of the page.'), 'http://www.google.com/latitude/apps/badge'); ?></small>
	
	<br /><br />
	

</div>

<div style="float: left; width: 600px; padding: 20px 0;" class="clear">

	<!-- save/update -->
	<button id="btn_profile_form"><?php 
		if($profile_form=="settings") echo _("Update"); 
		else echo _("Register!"); 
	?></button>
	
	<!-- cancel -->
	<button id="btn_profile_form_cancel"><?php echo _("Cancel"); ?></button>
	
	<!-- delete profile -->
	<?php if($profile_form=="settings"): ?><button id="btn_delete_profile" class="align_right"><?php echo _("Delete profile"); ?></button><?php endif; ?>
	
</div>

<script type="text/javascript" src="static/js/jquery.pstrength-min.1.2.js"></script>
<script type="text/javascript">
$(function() {

	$('#password1').pstrength();

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
	

	// Autosuggest in Location
	$(function() {
		$("#profile_form input#location").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: "http://ws.geonames.org/searchJSON",
					dataType: "jsonp",
					data: {
						featureClass: "P",
						style: "full",
						maxRows: 5,
						name_startsWith: request.term
					},
					success: function(data) {
						response($.map(data.geonames, function(item) {
							return {
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : ""),
								value: item.name + (item.adminName1 ? ", " + item.adminName1 : "")
								//value: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName
							}
						}))
					}
				})
			},
			minLength: 2,
			select: function(event, ui) {
				$("#profile_form input#location").val(ui.item.label);
				//$("#profile_form #country").val(ui.item.countryCode.toLowerCase());
			},
			open: function() {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function() {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		});
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


    // submit form
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
    	
    		show_loading_bar("<?php echo _("Loading..."); ?>");
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
    		
    		
    <?php if($profile_form=="settings"): ?>
    // Delete profile
    $("#btn_delete_profile").button({
        icons: {
            primary: 'ui-icon-trash'
        }
    }).click(function(e) {
    	e.preventDefault();
    	
    	var really_delete = confirm("<?php echo _("Are you sure you want to delete your profile? You cannot undo this action!"); ?>");
    	if(really_delete) {
    		close_page();
    		close_cards();
    		
			maps_debug("Asked to delete profile. Requesting API.");
    		$.getJSON('api/?delete_profile=<?php echo $user["id"]; ?>', function(data) {			

				if(data.success==true) {
					maps_debug("Profile deleted.");
					info_dialog('<?php echo _("Your profile was deleted permanently."); ?>', '<?php echo _("Profile deleted"); ?>', false, true);
				} else {
					maps_debug("Profile was NOT deleted due error: "+data.error);
    				info_dialog('<?php echo _("Error while deleting your profile. Please try again!"); ?>', '<?php echo _("Profile was NOT deleted"); ?>', true);
    			}
    		});
    		
    	} else {
    		close_page();
    		close_cards();
			maps_debug("Profile deletion cancelled.");
    		info_dialog('<?php echo _("You cancelled your profile deletion."); ?>', '<?php echo _("Deletion cancelled"); ?>', true);
    	}
    });
    <?php endif; ?>
    
    
});
</script>

</form>
