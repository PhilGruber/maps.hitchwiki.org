<form method="post" action="#" name="add_new_place_form" id="add_new_place_form">
    
    <small><span id="address_row" class="hidden"><b><?php echo _("Address"); ?>:</b> <span id="address"></span></span>&nbsp;</small>
    
    <br /><br />
    
    <label for="marker_type"><?php echo _("Type"); ?></label> 
    <select name="type" id="marker_type">
    	<option value="2"><?php echo _("Hitchhiking spot"); ?></option>
    	<option value="8"><?php echo _("Bus station"); ?></option>
    	<option value="7"><?php echo _("Waypoint"); ?></option>
    	<option value="100"><?php echo _("Friend"); ?></option>
    	<option value="1"><?php echo _("Home"); ?></option>
    	<option value="4"><?php echo _("Campin"); ?></option>
    	<option value="6"><?php echo _("Hostel"); ?></option>
    	<option value="5"><?php echo _("Other"); ?></option>
    </select> <small>(<span id="visibility_public"><?php echo _("Public"); ?></span><span id="visibility_private" class="hidden"><?php echo _("Private"); ?></span>)</small>
	<script type="text/javascript">
	$(function() {
		$("#marker_type").change( function () { 
		
  			if($(this).val() == 2) {
  			
  				// Toggle public/private text after type
  				$("#visibility_public").show();
  				$("#visibility_private").hide();
  				
  				// Toggle hitchability selection
  				if($("#hitchability_question").is(":hidden")) { 
  					$("#hitchability_question").show();
  				}
  			} else {
  			
  				// Toggle public/private text after type
  				$("#visibility_public").hide();
  				$("#visibility_private").show();
  				
  				// Toggle hitchability selection
  				if($("#hitchability_question").is(":visible")) { 
  					$("#hitchability_question").hide(); 
  				}
  			}
		});
	
	});
	
	</script>
    <br /><br />
    
	<!-- description in different languages class="tabs-bottom" -->
    <div id="tabs">
	<ul>
		<?php
		// Print out lang tabs
		$i=0;
		foreach($settings["valid_languages"] as $code => $name) {
		    if($code == $settings["language"]) $selected_tab = $i;
		    echo '<li><a href="#tabs-'.$code.'" title="'.$name.'">'.strtoupper(substr($code, 0, 2)).'</a></li>';
		    $i++;
		}
		?>
	</ul>
		<?php
		// Print out lang textareas
		foreach($settings["valid_languages"] as $code => $name) {
		    echo '<div id="tabs-'.$code.'">';
		    echo '<label for="description_'.$code.'">'. _("Description").' '.$name.'</label><br />';
		    echo '<p><textarea id="description_'.$code.'" class="resizable" rows="4" name="description[\''.$code.'\']"></textarea></p>';
		    echo '</div>';
		}
		?>
	</div>
	<script type="text/javascript">
	$(function() {
	
		$("#tabs").tabs({ selected: <?php echo $selected_tab; ?> });
		<?php /*
		$(".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *") 
 		.removeClass("ui-corner-all ui-corner-top") 
  		.addClass("ui-corner-bottom");
  		*/ ?>
  		
  		//$(".tabs-bottom").attr("style","");
	});
	</script>

	<div style="display: block; width: 335px;">
	<div id="hitchability_question">
    	<br /><br />
    	<label for="hitchability"><?php echo _("Hitchability"); ?></label> <a href="http://hitchwiki.org/en/Hitchwiki:Hitchability" title="<?php echo _("About Hitchability"); ?>" target="_blank" class="help"><span>?</span></a><br />
		<div style="font-size: 11px;">
		<span id="hitchability">
			<input type="radio" id="hitchability1" name="hitchability" /><label for="hitchability1"><?php echo _("Very good"); ?></label>
			<input type="radio" id="hitchability2" name="hitchability" /><label for="hitchability2"><?php echo _("Good"); ?></label>
			<input type="radio" id="hitchability3" name="hitchability" checked="checked" /><label for="hitchability3"><?php echo _("Average"); ?></label>
			<input type="radio" id="hitchability4" name="hitchability" /><label for="hitchability4"><?php echo _("Bad"); ?></label>
			<input type="radio" id="hitchability5" name="hitchability" /><label for="hitchability5"><?php echo _("Senseless"); ?></label>
		</span>
		</div>
	</div>
	</div>
	<script type="text/javascript">
	$(function() {
		$("#hitchability").buttonset();
	});
	</script>
	
    <br /><br />
    
    <button id="btn_add_place"><?php echo _("Add place"); ?></button>
    <button id="btn_cancel"><?php echo _("Cancel"); ?></button>
    
	<script type="text/javascript">
	$(function() {
		
		// add place
		$("#btn_add_place").button({
            icons: {
                primary: 'ui-icon-check'
            }
		}).click(function(e) {
			e.preventDefault();
			alert("Adding...");
			$("#add_place").dialog('close');
		});
		
		
		// cancel
		$("#btn_cancel").button({
            icons: {
                primary: 'ui-icon-cancel'
            }
		}).click(function(e) {
			e.preventDefault();
			$("#add_place").dialog('close');
		});
	});
	</script>
    
    <!--
    
    <button id="cancel" class="ui-button ui-corner-all ui-button-text-only ui-state-default">
  		<span class="ui-button-text">Cancel</span>
	</button>
    <button id="add_place" class="ui-button ui-corner-all ui-button-text-only ui-state-default">
  		<span class="ui-button-text">Add place</span>
	</button>
   	-->
    <!--
    <div class="align_right">
    	<button class="button" id="cancel"><span class="icon cancel">Cancel</span></button>
    	<button class="button" type="submit" id="add"><span class="icon accept">Add place</span></button>
    </div>
    -->
</form>