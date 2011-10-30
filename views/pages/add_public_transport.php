<h2><?php echo _("Add a page to the catalog"); ?></h2>

<?php if($user["logged_in"]===true): ?>

<div class="ui-state-error ui-corner-all hidden" style="padding: 0 .7em; margin: 20px 0;" id="pt_alert">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
    <strong><?php echo _("Alert"); ?>:</strong> <span class="alert_text"></span></p>
</div>

<form method="post" name="add_pt_form" id="add_pt_form">

<div style="float: left; width: 330px; padding-right: 20px;">

<label for="title"><?php echo _("Title"); ?></label> <small><?php echo _("In English."); ?></small><br />
<input type="text" value="" name="title" id="title" size="35" maxlength="255" />

<br /><br />

<label for="url"><?php echo _("Webpage"); ?></label> <small><?php echo _("Required.")." "._("Please try to add an English version of the page."); ?></small><br />
<input type="text" value="" name="url" id="url" size="35" maxlength="255" />

<br /><br />


<label for="city"><?php echo _("City"); ?></label><br />
<input type="text" value="" name="city" id="city" maxlength="80" />

<br /><br />

<label for="country"><?php echo _("Country"); ?></label> <small><?php echo _("Required."); ?></small><br />
<select id="country" name="country">
		<option value=""><?php echo _("Select"); ?></option>
		<?php list_countries("option", "name", false, false, true); ?>
	</select> &nbsp; <img class="flag" alt="" src="" class="hidden" />

<br /><br />

</div>
<div style="float: left; width: 250px;">

<?php
// Types:
foreach(pt_types() as $type => $type_name) {
	echo '<input type="checkbox" value="'.$type.'" name="type_'.$type.'" class="type" id="type_'.$type.'" /> <label for="type_'.$type.'">'.pt_type($type, 'icon_text').'</label><br />';
}
?>

<br />

</div>

<div style="float: left; width: 600px; padding: 20px 0;" class="clear">

	<button id="btn_add_public_transport"><?php echo _("Add page"); ?></button>

	<button id="btn_cancel_public_transport"><?php echo _("Cancel"); ?></button>
</div>

</form>

<script type="text/javascript">
$(function() {

	// Country flag
	$("#add_pt_form #country").change( function () {

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
		$("#add_pt_form input#city").autocomplete({
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
								label: item.name + ", " + item.countryName,
								value: item.name
								//value: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName
							}
						}))
					}
				})
			},
			minLength: 2,
			select: function(event, ui) {
				$("#add_pt_form input#city").val(ui.item.label);
				//$("#add_pt_form input#country").val(ui.item.countryCode);
				/*
					Todo: Select country when selecting a city from the suggestions list
				*/
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


	// Cancel btn
    $("#btn_cancel_public_transport").button({
        icons: {
            primary: 'ui-icon-cancel'
        }
    }).click(function(e) {
    	e.preventDefault();
    	maps_debug("Cancel adding page");
		open_page('public_transport');
	});


	// Add btn
    $("#btn_add_public_transport").button({
        icons: {
            primary: 'ui-icon-plusthick'
        }
    }).click(function(e) {
    	e.preventDefault();

		maps_debug("Adding page to the catalog. Requesting API.");

    	$("#pt_alert").hide();


    	if($("#add_pt_form #url").val() == "" || $(" #add_pt_form #country").val() == "") {
			maps_debug("Some required fields missing...");
			$("#pt_alert .alert_text").text("<?php echo _("Please fill all required fields!"); ?>");
			$("#pt_alert").show();
    	} else {

    		show_loading_bar("<?php echo _("Adding..."); ?>");
    		$("#add_pt_form").hide();

			// Call API

			//"#add_pt_form input[name~='type_']"

			var p_country = $("#add_pt_form #country").val();
			var p_city = $("#add_pt_form #city").val();
			var p_title = $("#add_pt_form #title").val();
			var p_url = $("#add_pt_form #url").val();

			// Seperate multiple types by ;
			var p_type = '';
			$("#add_pt_form input.type:checked").each(function(){
				var this_type = $(this).val();

				if(p_type=="") {
					p_type = this_type;
				}
				else {
					p_type = p_type+';'+this_type;
				}
			});

			$.post('api/?add_public_transport', {
			    country: p_country,
			    city: p_city,
			    title: p_title,
			    url: p_url,
			    user_id: <?php echo $user["id"]; ?>,
			    type: p_type
			},
			    function(data) {
			    	hide_loading_bar();

					if(data.success==true) {

						maps_debug("Page added.");
						info_dialog("<?php echo _("Page added to the catalog."); ?>","<?php echo _("Thank you!"); ?>");
						close_page();

					} else {

						maps_debug("Adding page failed. Error: "+data.error);
						$("#add_pt_form").show();

						$("#pt_alert .alert_text").text("<?php echo _("Error adding page to the catalog. Check required fields and try again."); ?> "+data.error);
						$("#pt_alert").show();

					}

			    }
			,"json"); // post end
		} // else end


    });


});
</script>

<?php else: ?>

	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;">
	    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
	    <?php echo _("You must be logged in to add pages."); ?></p>
	</div>

<?php endif; ?>