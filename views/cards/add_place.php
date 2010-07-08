<form method="post" action="#" name="add_new_place_form" id="add_new_place_form">
    
    <small><span id="address_row" class="hidden"><b><?php echo _("Address"); ?>:</b> <span id="address"></span></span>&nbsp;</small>
    
    <br /><br />
    
    <label for="type"><?php echo _("Type"); ?></label> 
    <select name="type" id="type">
    	<option value=""><?php echo _("Hitchhiking spot"); ?></option>
    	<option value="">-</option>
    	<option value="">-</option>
    	<option value="">-</option>
    </select>
    
    <br /><br />
    
	<!-- description in different languages -->
    <div id="tabs" class="tabs-bottom">
	<ul>
		<?php
		// Print out lang tabs
		$i=0;
		foreach($settings["valid_languages"] as $code => $name) {
		    if($code == $settings["language"]) $selected_tab = $i;
		    echo '<li><a href="#tabs-'.$code.'">'.strtoupper(substr($code, 0, 2)).'</a></li>';
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
		
		$(".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *") 
 		.removeClass("ui-corner-all ui-corner-top") 
  		.addClass("ui-corner-bottom");
	});
	</script>

    <br /><br />
   
    <label for="hitchability"><?php echo _("Hitchability"); ?></label> <a href="http://hitchwiki.org/en/Hitchwiki:Hitchability" title="<?php echo _("About Hitchability"); ?>" target="_blank" class="help"><span>?</span></a><br />
	<div style="font-size: 11px; display: border:1px solid red; block; width: 340px;">
	<span id="hitchability">
		<input type="radio" id="hitchability1" name="hitchability" /><label for="hitchability1"><?php echo _("Very good"); ?></label>
		<input type="radio" id="hitchability2" name="hitchability" /><label for="hitchability2"><?php echo _("Good"); ?></label>
		<input type="radio" id="hitchability3" name="hitchability" checked="checked" /><label for="hitchability3"><?php echo _("Average"); ?></label>
		<input type="radio" id="hitchability4" name="hitchability" /><label for="hitchability4"><?php echo _("Bad"); ?></label>
		<input type="radio" id="hitchability5" name="hitchability" /><label for="hitchability5"><?php echo _("Senseless"); ?></label>
	</span>
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