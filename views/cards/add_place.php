<h4 id="country_row" class="hidden"><?php echo _("Add new place to"); ?> <span id="country"></span></h4>

<form method="post" action="#" name="add_new_place">
    <small>
    	<span id="lat"></span> N, <span id="lon"></span> E<br />
    	<span id="address_row" class="hidden"><b>Address:</b> <span id="address"></span></span>
    </small>
    <br /><br />
    <label for="description">Description</label><br />
    <textarea id="description" rows="4" name="description">Lorem description ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.</textarea>

    <br /><br />
   
    <label for="hitchability">Hitchability</label> <a href="http://hitchwiki.org/en/Hitchwiki:Hitchability" title="<?php echo _("About Hitchability"); ?>" target="_blank" class="help"><span>?</span></a><br />
	<div style="font-size: 11px; display: border:1px solid red; block; width: 340px;">
	<span id="hitchability">
		<input type="radio" id="hitchability1" name="hitchability" /><label for="hitchability1">Very good</label>
		<input type="radio" id="hitchability2" name="hitchability" /><label for="hitchability2">Good</label>
		<input type="radio" id="hitchability3" name="hitchability" checked="checked" /><label for="hitchability3">Average</label>
		<input type="radio" id="hitchability4" name="hitchability" /><label for="hitchability4">Bad</label>
		<input type="radio" id="hitchability5" name="hitchability" /><label for="hitchability5">Senseless</label>
	</span>
	</div>
	<script type="text/javascript">
	$(function() {
		$("#hitchability").buttonset();
	});
	</script>
	
    <br /><br />
    <label for="type">Type</label> 
    <select name="type" id="type">
    	<option value="">Hitchhiking spot</option>
    	<option value="">-</option>
    	<option value="">-</option>
    	<option value="">-</option>
    </select>
    
    <br /><br />
    
    <button id="btn_add_place">Add place</button>
    <button id="btn_cancel">Cancel</button>
    
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