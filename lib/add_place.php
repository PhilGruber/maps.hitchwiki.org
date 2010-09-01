<?php
/*
 * Hitchwiki Maps: add_place.php
 * Show an add new place panel
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";

start_sql();

/*
 * Returns an info-array about logged in user (or false if not logged in) 
 */
$user = current_user();


?><form method="post" action="#" name="add_new_place_form" id="add_new_place_form">

<input type="hidden" id="lat" name="lat" value="" />
<input type="hidden" id="lon" name="lon" value="" />
<input type="hidden" id="locality" name="locality" value="" />
				
<ul id="Navigation">

    <li style="background: #fff;">

		<ul>
			<li>
				<h3 style="display: inline;" class="icon add"><?php echo _("Add place"); ?></h3>
				<div class="align_right">
					<a href="#" onclick="close_add_place(); return false;" class="ui-button ui-corner-all ui-state-default ui-icon ui-icon-closethick align_right" title="<?php echo _("Close"); ?>"></a>
				</div>
			</li>
		</ul>
	</li>

	<li id="loading_row">
		<ul>
			<li class="centered">
				<img src="static/gfx/loading.gif" alt="<?php echo _("Loading"); ?>" />
			</li>
		</ul>
	</li>

	<li id="address_row" style="display:none;">
		<ul>
			<li>
				<input type="hidden" id="country_iso" name="country_iso" value="" />
			    
			    <small id="address"></small>
			    
			    <span id="locality_name" class="icon building"></span>
			    
			    <img class="flag" alt="" src="" class="hidden" /> <span id="country_name"></span>
			    &nbsp;

			</li>
		</ul>
	</li>
	
	<!-- manual country selection as a backup method: -->
	<li id="manual_country_selection">
		<ul>
			<li>
			    <label for="manual_country"><?php echo _("Country"); ?></label> <small>(<?php echo _("Required"); ?>)</small><br />
			    <select id="manual_country" name="manual_country" style="width: 200px;">
					<option value=""><?php echo _("Select"); ?></option>
					<?php list_countries("option", "name", false, false, true); ?>
				</select>
			</li>
		</ul>
	</li>

	<li>
		<ul>
			<li>
			
			    <label for="marker_type"><?php echo _("Type"); ?></label> 
			    <select name="type" id="marker_type">
			    	<option value="1" selected="selected"><?php echo _("Hitchhiking spot"); ?></option>
			    	<option value="2"><?php echo _("Event / Meeting / Gathering"); ?></option>
			    </select>
			    <script type="text/javascript">
			    $(function() {
			    	$("#marker_type").change( function () { 
			    	
			  			if($(this).val() == 1) {
			  			
			  				// Toggle parts for a hitchhiking spot
			  				$("#hitchability_question").show(); 
			  				$("waitingtime_question").show();
			  				
			  				// Toggle Event stuff
			  				$("#event_info").hide();  
			  			
			  			} else if($(this).val() == 2) {
			  			
			  				// Toggle parts for a hitchhiking spot
			  				$("#hitchability_question").hide(); 
			  				$("waitingtime_question").hide();
			  				
			  				// Toggle Event stuff
			  				$("#event_info").show();
			  			}
			    	});
			    
			    });
			    
			    </script>
			    
			</li>
		</ul>
	</li>

	<li>
		<ul>
			<li>
				
				<!-- description in different languages -->
				<label for="select_description"><?php echo _("Description"); ?></label> <select name="select_description" id="select_description">
				    <?php
				    // Print out lang options
				    
				    $selected_tab = $settings["default_language"];
				    foreach($settings["valid_languages"] as $code => $name) {

				        echo '<option value="'.$code.'"';
				        if($code == $settings["language"]) {
				        	echo ' selected="selected"';
				        	$selected_tab = $code;
						}
				        echo '>'.$name.'</option>';
				    }
				    ?>
				</select>
				<div id="descriptions">
				    <?php
				    // Print out lang textareas
				    foreach($settings["valid_languages"] as $code => $name) {
				        echo '<div id="tab-'.$code.'" class="description">';
			    	   	echo '<p><textarea id="description_'.$code.'" class="resizable" rows="4" name="description_'.$code.'"></textarea></p>';
				        echo '</div>';
				    }
				    ?>
				</div>
				<small class="light"><?php echo _("Please fill as many different language descriptions as you can."); ?></small>
				
				<script type="text/javascript">
				$(function() {
				
					// Descreption language selection
				    $("#descriptions .description").hide();
				    $("#descriptions #tab-<?php echo $selected_tab; ?>").show();
				    
				    //var textarea_width = $("#descriptions #tab-<?php echo $selected_tab; ?>").width();
					
				    $("#select_description").change( function () { 
				    	var selected_language = $(this).val();
				    	$(this).blur();
				    	$("#descriptions .description").hide();
				    	$("#descriptions #tab-"+selected_language).show();
				    	/*
						$("#descriptions  #tab-"+selected_language+" .resizable").resizable({
							maxHeight: 150, 
							minHeight: 50, 
							minWidth: textarea_width,
							maxwidth: textarea_width+1,
							handles: "se"
						});
						*/
				    });
				    
				});
				</script>
			</li>
		</ul>
	</li>

	<li id="event_info" style="display:none;">
		<ul>
			<li>
				<label for="start_date" class="icon calendar_view_day">Starting date</label> <input type="text" value="" size="10" name="start_date" id="start_date" class="datepicker" /> <a href="#" id="clear_start_date"><small><?php echo _("Clear"); ?></small></a>
			</li>
			<li>
				<label for="end_date" class="icon calendar_view_day">Ending date</label> <input type="text" value="" size="10" name="end_date" id="end_date" class="datepicker" /> <a href="#" id="clear_end_date"><small><?php echo _("Clear"); ?></small></a>
				
				<script type="text/javascript">
				$(function() {
					// Date picker
					$("#add_new_place_form .datepicker").datepicker({
						showButtonPanel: true
					});
					
					// Clear btns
					$("#add_new_place_form #clear_start_date").click(function(e){
						e.preventDefault();
						$("#add_new_place_form #start_date").val("");
					});
					$("#add_new_place_form #clear_end_date").click(function(e){
						e.preventDefault();
						$("#add_new_place_form #end_date").val("");
					});
				});
				</script>
			</li>

		</ul>
	</li>
	
	<li id="hitchability_question">
		<ul>
			<li>
				<label for="hitchability"><?php echo _("Hitchability"); ?></label> <a href="http://hitchwiki.org/en/Hitchwiki:Hitchability" title="<?php echo _("About Hitchability"); ?>" target="_blank" class="tip"><span>?</span></a><br />
				<div class="hitchability_question">
				    <input type="radio" id="hitchability1" name="hitchability" value="1" /> <label for="hitchability1"><b class="bigger hitchability_color_1">&bull;</b> <?php echo hitchability2textual(1); ?></label><br />
				    <input type="radio" id="hitchability2" name="hitchability" value="2" /> <label for="hitchability2"><b class="bigger hitchability_color_2">&bull;</b> <?php echo hitchability2textual(2); ?></label><br />
				    <input type="radio" id="hitchability3" name="hitchability" value="3" /> <label for="hitchability3"><b class="bigger hitchability_color_3">&bull;</b> <?php echo hitchability2textual(3); ?></label><br />
				    <input type="radio" id="hitchability4" name="hitchability" value="4" /> <label for="hitchability4"><b class="bigger hitchability_color_4">&bull;</b> <?php echo hitchability2textual(4); ?></label><br />
				    <input type="radio" id="hitchability5" name="hitchability" value="5" /> <label for="hitchability5"><b class="bigger hitchability_color_5">&bull;</b> <?php echo hitchability2textual(5); ?></label><br />
				    <input type="radio" id="hitchability0" name="hitchability" value="0" checked="checked" /> <b class="bigger hitchability_color_0">&bull;</b> <label for="hitchability0"><i><?php echo hitchability2textual(0); ?></i></label><br />
				</div>
			</li>

		</ul>
	</li>
	
	<li id="waitingtime_question">
		<ul>
			<li>
				<label for="waitingtime"><?php echo _("Waiting time"); ?></label><br />
				<select id="waitingtime" name="waitingtime">
					<option value="" selected="selected"><?php echo _("I don't know"); ?></option>
					<option value="5"><?php echo nicetime(5); ?></option>
					<option value="10"><?php echo nicetime(10); ?></option>
					<option value="15"><?php echo nicetime(15); ?></option>
					<option value="20"><?php echo nicetime(20); ?></option>
					<option value="30"><?php echo nicetime(30); ?></option>
					<option value="45"><?php echo nicetime(45); ?></option>
					<option value="60"><?php echo nicetime(60); ?></option>
					<option value="90"><?php echo nicetime(90); ?></option>
					<option value="120"><?php echo nicetime(120); ?></option>
					<option value="150"><?php echo nicetime(150); ?></option>
					<option value="180"><?php echo nicetime(180); ?></option>
					<option value="210"><?php echo nicetime(210); ?></option>
					<option value="240"><?php echo nicetime(240); ?></option>
				</select><br />
				<small class="light"><?php echo _("Just add what you've experienced. You can add multiple timings after saving and we will calculate an average out of them."); ?></small>
			</li>

		</ul>
	</li>

	<li>
		<ul>
			<li>
			    
			    <button id="btn_add_place" class="smaller"><?php echo _("Add place"); ?></button>
			    <button id="btn_cancel" class="smaller"><?php echo _("Cancel"); ?></button>
			    
			    
			    <?php 
			    if($user["logged_in"]===true) { 
			    	echo '</li><li>';
			    	echo '<small class="light">';
			    	printf(_('Your name <i>"%s"</i> will be visible with this place.'), htmlspecialchars($user["name"]));
			    	echo '</small>'; 
			    } 
			    ?>
			    
			    <script type="text/javascript">
			    $(function() {
			    	
			    	// add place
			    	$("#btn_add_place").button({
			            icons: {
			                primary: 'ui-icon-check'
			            }
			    	}).click(function(e) {
						e.preventDefault();
						
						maps_debug("Ask to add a place.");
												
						show_loading_bar("Adding...");
						
						// Disable all form elements from editing
						$("#add_new_place_form input, #add_new_place_form textarea").attr('disabled', true);
						
						// Gather data
						var post_lat = $("#add_new_place_form input#lat").val();
						var post_lon = $("#add_new_place_form input#lon").val();
						var post_type = $("#add_new_place_form #marker_type").val();
						var post_waitingtime = $("#add_new_place_form #waitingtime").val();
						var post_locality = $("#add_new_place_form input#locality").val();
						var post_country = $("#add_new_place_form #country_iso").val();
						var post_manual_country = $("#add_new_place_form #manual_country").val();
						var post_hitchability = $("#add_new_place_form #hitchability_question input:checked").val();
						<?php
						// Gather languages
						foreach($settings["valid_languages"] as $code => $name) {
						?>
							var post_description_<?php echo $code; ?> = $("#add_new_place_form textarea#description_<?php echo $code; ?>").val();
						<?php
						}
						?>
						
						maps_debug("Sending a request to the API.");
						// Call API
						$.post('api/?add_place', { 
							lat: post_lat, 
							lon: post_lon, 
							type: post_type,
							waitingtime: post_waitingtime,
							<?php
							foreach($settings["valid_languages"] as $code => $name) {
								echo 'description_'.$code.': post_description_'.$code.', ';
							}
							?>
							locality: post_locality,
							country: post_country, 
							manual_country: post_manual_country, 
							rating: post_hitchability<?php 
								if($user["logged_in"]===true) { echo ', user_id: "'.$user["id"].'"'; } 
						?> }, 
						function(data) {
						
							hide_loading_bar();
								
							if(data.success == true) {
								maps_debug("Place added successfully.");
			    				close_add_place();
			    				info_dialog("<?php echo _("Thank you!"); ?><br /><br /><?php echo _("Place successfully added to the Maps."); ?>","<?php echo _("Place added successfully"); ?>");
			    				showPlacePanel(data.id, true);
							} else {
								maps_debug("Could not add place. Error: "+data.error);
								
								// Enable form elements again
								$("#add_new_place_form input, #add_new_place_form textarea").removeAttr('disabled');
								
								// Show error
								info_dialog("<?php echo _("Could not add place due error."); ?><br /><br /><?php _("Please try again!"); ?>", "<?php echo _("Error"); ?>", true);
							}

						}, "json"
						); // post end
						
			    	
			    	});
			    	
			    	// cancel
			    	$("#btn_cancel").button({
			            icons: {
			                primary: 'ui-icon-cancel'
			            }
			    	}).click(function(e) {
			    		e.preventDefault();
			    		maps_debug("Cancel adding place");
			    		close_add_place();
			    	});
			    });
			    </script>
			</li>

		</ul>
	</li>

</ul>

</form>