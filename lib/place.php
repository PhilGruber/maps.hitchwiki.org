<?php
/*
 * Hitchwiki Maps: place.php
 * Show a place panel
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";

start_sql();

/* 
 * Gather data
 */
if(isset($_GET["id"]) && is_numeric($_GET["id"])) {

	$place = get_place($_GET["id"],true);

}
else {
	echo '<p>Error: Missing ID!</p>';
	exit;
}


/*
 * Returns an info-array about logged in user (or false if not logged in) 
 */
$user = current_user();


/* 
 * Print stuff out in HTML:
 */
?>


<ul id="Navigation">

    <li style="background: #fff;">

		<ul>
			<li>
				<h3 style="display: inline;"><?php echo _("Hitchhiking spot"); ?></h3>
				<div class="align_right">
					<a class="icon icon_right zoom_in" href="#" onclick="zoomMapIn(<?php echo $place["lat"]; ?>, <?php echo $place["lon"]; ?>, 16);" title="<?php echo _("Zoom in"); ?>"></a>
					&nbsp;
					<!-- TODO:  map.places.unselectAll(); ? -->
					<a href="#" onclick="hidePlacePanel(); return false;" class="ui-button ui-corner-all ui-state-default ui-icon ui-icon-closethick align_right" title="<?php echo _("Close"); ?>"></a>
				</div>
			</li>
		</ul>
	</li>

	<li>
		<ul>
			<li>
				<h3 style="display: inline;"><?php
				
					// Flag
					if(!empty($place["location"]["country"]["iso"])) echo '<img class="flag" alt="'.$place["location"]["country"]["iso"].'" src="static/gfx/flags/png/'.strtolower($place["location"]["country"]["iso"]).'.png" /> ';
					
					// City
					if(!empty($place["location"]["city"])) echo $place["location"]["city"].", ";
					
					// Country
					if(!empty($place["location"]["country"]["name"])) echo $place["location"]["country"]["name"];
					
					// Continent
					#if(!empty($place["location"]["continent"]["name"])) echo $place["location"]["continent"]["name"]."<br />";
					
				?></h3>
			</li>
				
				
			<?php if(!empty($place["description"])): ?>
			<li>
				
				<!-- description in different languages -->
				<label for="select_description"><?php echo _("Description"); ?></label> <select name="select_description" id="select_description">
				    <?php
				    // Print out lang options
				    
				    $selected_tab = $settings["default_language"];
				    foreach($settings["valid_languages"] as $code => $name) {
				    
				        if($code == $settings["language"] && !empty($place["description"][$code])) $selected_tab = $code;
				        
				        echo '<option value="'.$code.'">';
				        
				        if(!empty($place["description"][$code])) echo '&bull; ';
				        else echo '&nbsp;&nbsp;';
				        
				        echo $name;	        
				        echo '</option>';
				        $i++;
				    }
				    ?>
				</select>
				<div id="descriptions">
				    <?php
				    // Print out lang textareas
				    foreach($settings["valid_languages"] as $code => $name) {
				        echo '<div id="tab-'.$code.'" class="description">';
				    	echo '<p>';
				    	
				    	if(!empty($place["description"][$code])) {
				    		echo Markdown($place["description"][$code]);
				    		?><small><a href="#" onclick="info_dialog('Editing is not in use yet, sorry.', 'TODO'); return false;"><?php echo _("Edit"); ?></a></small><?php
				    	} else {
				    		echo '<em>'._("No description available in this language.").'</em> &mdash; <small><a href="#" onclick="writeDescription(\''.$code.'\'); return false;">'._("Write one?").'</a></small>';
				        }
				        echo '</p></div>';
				    }
				    ?>
				</div>
				
				<script type="text/javascript">
				$(function() {
				
					// Descreption language selection
				    $("#descriptions .description").hide();
				    $("#descriptions #tab-<?php echo $selected_tab; ?>").show();
				    
				    $("#select_description").change( function () { 
				    	var selected_language = $(this).val();
				    	$(this).blur();
				    	$("#descriptions .description").hide();
				    	$("#descriptions #tab-"+selected_language).show();
				    });
				});
				
				// Build a description writer
				function writeDescription(language) {
				
				    $("#tab-"+language).html('<textarea rows="4" id="add_description"></textarea><br /><button id="btn_save_description" class="align_right" style="font-size: 11px;"><?php echo _("Save"); ?></button><div class="clear"></div>');
				
				    $("#btn_save_description").button({
				        icons: {
				            primary: 'ui-icon-pencil'
				        }
				    }).click(function(e) {
				    	e.preventDefault();
				    	// Save written description
				    	// TODO!
				    	maps_debug("Save a description");
				    	info_dialog("Saving descriptions isn't working yet, sorry.", "TODO");
				    });
				    
				
				    
				}
				
				</script>
				
				<?php
					// When marker was added and who added it
					echo '<div class="meta"';
					
					if(!empty($place["datetime"])) echo ' title="'.date("r",strtotime($place["datetime"])).'"';
					 
					echo '>';
					
					// Name
					if(isset($place["user"]["name"])) echo '<strong>'.htmlspecialchars($place["user"]["name"]).'</strong>';
					
					// Date
					if(!empty($place["datetime"])) echo ' &mdash; '.date("j.n.Y",strtotime($place["datetime"]));
					
					echo '</div>';
				?>
			</li>
			<?php endif; /* end if empty description */ ?>
			
			
			<li>

				<!-- Hitchability -->
				<?php 
				#<div class="icon hitchability_'.$place["rating"].'"></div>
				echo '<b>'._("Hitchability").':</b> '.hitchability2textual($place["rating"]).' <b class="bigger hitchability_color_'.$place["rating"],'">&bull;</b> <small class="light">('; 
					
					if($place["rating_stats"]["rating_count"] == 1) echo _("1 vote"); 
					else printf(_("%s votes"), $place["rating_stats"]["rating_count"]);
				
				?>)</small>
				
				<?php if($place["rating_stats"]["rating_count"] > 1): ?>
					<br /><small class="light"><?php echo _("Vote distribution"); ?>:</small><br />
					<img src="<?php echo rating_chart($place["rating_stats"], 220); ?>" alt="<?php echo _("Vote distribution"); ?>" />
				<?php endif; ?>
				
			
			<?php
			
			// Check if user has already rated this point, and if, what did one rate?
			$users_rating = false;
			if($user["logged_in"]===true) {
			
				$res4 = mysql_query("SELECT `fk_user`,`fk_point`,`datetime`,`rating` FROM `t_ratings` WHERE `fk_user` = ".mysql_real_escape_string($user["id"])." AND `fk_point` = ".mysql_real_escape_string($place["id"])." LIMIT 1");
   				if(!$res4) return $this->API_error("Query failed! (4)");
				
				// If we have a result
				if(mysql_num_rows($res4) > 0) {
					// Get an ID of row we need to just update
					while($r = mysql_fetch_array($res4, MYSQL_ASSOC)) {
						$users_rating = $r["rating"];
						$users_rating_date = $r["datetime"];
					}
				}
				
			}
			
			?>
				<br /><label for="rate"><?php echo _("Your opinnion"); ?>: </label> 
				<select name="rate" id="rate">
					<?php if($users_rating==false): ?><option value=""><?php echo _("Rate..."); ?></option><?php endif; ?>
					<option value="1"<?php if($users_rating==1) echo ' selected="selected"'; ?>><?php echo hitchability2textual(1); ?></option>
					<option value="2"<?php if($users_rating==2) echo ' selected="selected"'; ?>><?php echo hitchability2textual(2); ?></option>
					<option value="3"<?php if($users_rating==3) echo ' selected="selected"'; ?>><?php echo hitchability2textual(3); ?></option>
					<option value="4"<?php if($users_rating==4) echo ' selected="selected"'; ?>><?php echo hitchability2textual(4); ?></option>
					<option value="5"<?php if($users_rating==5) echo ' selected="selected"'; ?>><?php echo hitchability2textual(5); ?></option>
					<?php /* if($user["logged_in"]===true): ?><option value="clear"><?php echo _("Clear my rating"); ?></option><?php/ endif;  TODO! */ ?>
				</select>
				<?php
				
				if(!empty($users_rating_date)) echo '<br /><small class="light">'._("You rated for this place").' <span title="'.date("r", strtotime($users_rating_date)).'">'.date("j.n.Y", strtotime($users_rating_date)).'</span></small>';
				
				?>
				<script type="text/javascript">
					$(function() {
					
						// Rate a place
				    	$("#rate").change( function () { 
				    	
				    		var rate = $(this).val();
				    		$(this).blur();
				    	
				    		if(rate != "") {
				    			maps_debug("Rating a place with "+rate);
				    			
				    			// Send an api call
								var apiCall = "api/?rate="+rate+"&place_id=<?php 
								
									echo $place["id"]; 
								
									if($user["logged_in"]===true) echo '&user_id='.$user["id"];
								
								?>";
								maps_debug("Calling API: "+apiCall);
								$.getJSON(apiCall, function(data) {
									
									if(data.success == true) {
										maps_debug("Rating saved. Place "+data.point_id+" rating: "+data.rating_stats.exact_rating);
										showPlacePanel(<?php echo $place["id"]; ?>);
									}
									// Oops!
									else {
									    info_dialog("<?php echo _("Rating failed, please try again."); ?>", "<?php echo _("Rating failed"); ?>", true);
									    maps_debug("Rating failed. <br />- Error: "+data.error+"<br />- Data: "+data);
									    $("#rate").val("");
									}
									
								});
								
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
				<div id="comments">
					<h3 style="margin: 0;" class="icon comments"><?php echo _("Comments"); ?> <small class="light">(<span id="comment_counter"><?php echo $place["comments_count"]; ?></span>)</small></h3>
					<ol id="comment_list">
				<?php if(!empty($place["comments"])): ?>
					<?php
					foreach($place["comments"] as $comment) {
						echo '<li class="comment';
						
						// If you're logged in, your own comments will get a special class
						if($user["logged_in"]===true && $user["id"]==$comment["user"]["id"]) { echo ' own_comment'; }
						
						echo '" id="comment-'.$comment["id"].'">';
						
						// Comment content
						echo Markdown($comment["comment"]);
						
						// Nick, date, remove etc.
						echo '<div class="meta"><strong>';
						
						if(isset($comment["user"]["nick"])) echo htmlspecialchars($comment["user"]["nick"]);
						elseif(isset($comment["user"]["name"])) echo htmlspecialchars($comment["user"]["name"]);
						else echo '<i>'._("Anonymous").'</i>';
						
						echo '</strong> &mdash; <span title="'.date("r",strtotime($comment["datetime"])).'">'.date("j.n.Y",strtotime($comment["datetime"])).'</span>';
						
						// Show remove-link for logged in comments owner
						if($user["admin"]===true) {
							?>
							 <a href="#" onclick="removeComment('<?php echo $comment["id"]; ?>'); return false;" class="ui-icon ui-icon-trash align_right" title="<?php echo _("Remove comment permanently"); ?>"></a>
							 <a href="admin/?edit_comment=<?php echo $comment["id"]; ?>" class="ui-icon ui-icon-pencil align_right" title="<?php echo _("Edit comment"); ?>"></a>
							 <?php
						}
						elseif($user["logged_in"]===true && $user["id"]==$comment["user"]["id"]) {
							?>
							 <a href="#" onclick="removeComment('<?php echo $comment["id"]; ?>'); return false;" class="ui-icon ui-icon-trash align_right" title="<?php echo _("Remove comment permanently"); ?>"></a>
							<?php
						}
						
						echo '</div>';
						
						echo '</li>';
					}
					?>
				<?php endif; ?>
					</ol>
				
					<textarea id="write_comment" name="write_comment" rows="1" class="icon comment grey"><?php echo _("Leave a comment..."); ?></textarea>
					<div id="btn_comment_placeholder" style="display:block;padding-bottom:7px;clear: both;"></div>
				
				</div>
				<script type="text/javascript">
					// Write a comment
					$(function() {
				
						// When selecting the textarea for writing a comment
						$("#write_comment").focus(function(){
							
							// Add comment -button if it isn't there yet
							if($("#btn_comment_placeholder").text() == "") {
						
								maps_debug("Opening commenting.");
							
								$("#write_comment")
									.val("")
									.attr("rows","4")
									.removeClass("icon comment grey")
									.attr("style","width:100%;");
								
								$("#btn_comment_placeholder")
									.html('<?php 
										// Add a nick-field only for not-logged in users
										if($user===false): 
											?><input type="text" name="nick" id="nick" value="<?php echo _("Nickname"); ?>" class="align_left grey" size="14" maxlength="80" /><?php 
										else:
											?><strong title="<?php echo _("You are logged in and this name will be visible for others."); ?>"><small class="align_left light"><?php echo $user["name"]; ?></small></strong><?php
										endif;
										
										?><button id="btn_comment" class="align_right smaller"><?php echo _("Comment"); ?></button><br />')
									.attr("style","clear: both; padding:5px 0;");
								
								<?php if($user===false): ?>
								$("#btn_comment_placeholder #nick").focus(function(){
									if($(this).val() == "<?php echo _("Nickname"); ?>") {
										$(this).val("").removeClass("grey");
									}
								});
								<?php endif; ?>
								
								
								$("#btn_comment").button({
				        		    icons: {
				        		        primary: 'ui-icon-comment'
				        		    }
								}).click(function(e) {
									e.preventDefault();
									maps_debug("Adding a comment...");
									
									if($("#write_comment").val() == "") {
										info_dialog("<?php echo _("Please write a comment first."); ?>", "<?php echo _("Comment missing"); ?>");
									} else {
									
										// Update comment to the DB
										//info_dialog("Adding comments isn't working yet, sorry.", "TODO");
										
										// Disable form during sending
										$("#btn_comment").button( "option", "disabled", true );
										$("#write_comment").attr("disabled","disabled");
										<?php if($user===false): ?>$("#btn_comment_placeholder #nick").attr("disabled","disabled");<?php endif; ?>
										show_loading_bar("<?php echo _("Sending..."); ?>");
												
										/*	API is listenin' for these:
										 * - place_id (required)
										 * - comment (required)
										 * - user_id (optional)
										 * - user_nick (optional)
										 */
										 
										// Get data from the form
										var post_comment = $("#write_comment").val();
										
										<?php if($user===false): ?>
										var post_nick = $("#btn_comment_placeholder #nick").val();
										
										// Don't send nickname if nick is default (we will use "anonymous" instead
										if(post_nick == "<?php echo _("Nickname"); ?>") { post_nick = ""; }
										<?php endif; ?>
										
										maps_debug("Comment place <?php echo $place["id"]; ?>");
										
										// Call API
										$.post('api/?add_comment', { place_id: "<?php echo $place["id"]; ?>", comment: post_comment, <?php if($user===false) { echo 'user_nick: post_nick'; } else { echo 'user_id: "'.$user["id"].'"'; } ?> }, 
											function(data) {

												hide_loading_bar();
												
												// Enable form again
												$("#write_comment").removeAttr("disabled");
												$("#btn_comment").button( "option", "disabled", false );
												<?php if($user===false): ?>$("#btn_comment_placeholder #nick").removeAttr("disabled");<?php endif; ?>
												
												// Comment added
												if(data.success == true) {
													maps_debug("Comment #"+data.id+" added to the place "+data.place_id+".");
													
													// Empty textarea
													$("#write_comment").val("");
													
													// Add newly added comment to the panel
													$("#comment_list").append('<li class="comment own_comment" id="comment-'+data.id+'">'+data.comment+'<div class="meta"><strong>'+data.user_nick+'</strong> &mdash; '+data.date+'<?php if($user["logged_in"]===true): ?><a href="#" onclick="removeComment('+data.id+'); return false;" class="ui-icon ui-icon-trash align_right" title="<?php echo _("Remove comment permanently"); ?>"></a><?php endif; ?></div></li>');
												
													var current_comment_count = $("#comments #comment_counter").text();
													$("#comments #comment_counter").text(parseInt(current_comment_count)+1);
												}
												// Oops!
												else {
													info_dialog("<?php echo _("Adding a comment failed, please try again."); ?>", "<?php echo _("Adding a comment failed"); ?>", true);
													maps_debug("Adding comment failed. <br />- Error: "+data.error+"<br />- Data: "+data);
												}

											}, "json"
										); // post end
										 
									} // else if comment was empty * end
									
								}); // button click end
								
							} // add comment-button on focus 
							
						}); // focus listener

					});
				</script>
			</li>
		</ul>
	</li>
	<li>
		<ul>
			<li>
				<div class="icon link"><label for="link_place"><?php echo _("Link to this place:"); ?></label></div>
				<input type="text" id="link_place" value="<?php echo $place["link"]; ?>" class="copypaste" />
				<script type="text/javascript">
					$(function() {
						// Select all from textarea on focus
						$(".copypaste").focus(function(){
						    this.select();
						});
					});
				</script>
				
			</li>
			<li title="<?php echo _("Recommend this place for your Facebook contacts"); ?>">
				<!-- Facebook BTN -->
				<iframe src="http://www.facebook.com/plugins/like.php?locale=<?php echo $settings["language"]; ?>&amp;href=<?php echo urlencode($place["link"]); ?>&amp;layout=button_count&amp;show_faces=true&amp;width=200&amp;action=recommend&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px; margin: 7px 0;" allowTransparency="true"></iframe>
				
			</li>
		</ul>
	</li>


	<?php if(!empty($place["location"]["city"]) OR !empty($place["location"]["country"]["name"])): ?>
	<!-- infolinks -->
	<li>
		<ul>
	    	<?php if(!empty($place["location"]["city"])): ?>
			<li>
	    		<small class="icon magnifier">
	    			<b><?php echo $place["location"]["city"]; ?></b><br />
	    			<a target="_blank" href="http://hitchwiki.org/en/index.php?title=Special%3ASearch&search=<?php echo urlencode($place["location"]["city"]); ?>&go=Go">Hitchwiki</a>, 
	    			<a target="_blank" href="http://en.wikipedia.org/wiki/Special:Search?search=<?php echo urlencode($place["location"]["city"]); ?>">Wikipedia</a>, 
	    			<a target="_blank" href="http://wikitravel.org/en/Special:Search?search=<?php echo urlencode($place["location"]["city"]); ?>&go=Go">Wikitravel</a>
	    		</small>
	    	</li>
	    	<?php endif; ?>

	    	<?php if(!empty($place["location"]["country"]["name"])): ?>
	    	<li>
	    		<small class="icon magnifier">
	    			<b><?php echo $place["location"]["country"]["name"]; ?></b><br />
	    			<a target="_blank" href="http://hitchwiki.org/en/index.php?title=Special%3ASearch&search=<?php echo urlencode($place["location"]["country"]["name"]); ?>&go=Go">Hitchwiki</a>, 
	    			<a target="_blank" href="http://en.wikipedia.org/wiki/Special:Search?search=<?php echo urlencode($place["location"]["country"]["name"]); ?>">Wikipedia</a>, 
	    			<a target="_blank" href="http://wikitravel.org/en/Special:Search?search=<?php echo urlencode($place["location"]["country"]["name"]); ?>&go=Go">Wikitravel</a>, 
	    			<a target="_blank" href="http://www.couchsurfing.org/statistics.html?country_name=<?php echo urlencode($place["location"]["country"]["name"]); ?>">CouchSurfing</a>
	    		</small>
			</li>
	    	<?php endif; ?>

		</ul>
	</li>
	<?php endif; ?>

	
	<li>
		<ul>
			<li>
				
				<!-- Coordinates -->
				<?php 
				/*
				 * Currently these are needed here for JavaScript, but it ain't too good way...
				 */
				?>
				<small id="coordinates" class="light"><span class="lat" title="<?php echo _("Latitude"); ?>"><?php echo $place["lat"]; ?></span>, <span class="lon" title="<?php echo _("Longitude"); ?>"><?php echo $place["lon"]; ?></span></small>
			</li>
		<?php 
		// Show admin menu
		if($user["admin"]===true): ?>
			<li>
				<small class="light icon wrench" style="display: block;">
					&nbsp;
					<a href="admin/?remove_place=<?php echo $place["id"]; ?>" onclick="configrm('Are you sure?');"><?php echo _("Remove place"); ?></a> 
					&bull; 
					<a href="admin/?edit_place=<?php echo $place["id"]; ?>"><?php echo _("See user"); ?></a> 
				</small>
			</li>
		<?php endif; ?>
		
		</ul>
	</li>
</ul>