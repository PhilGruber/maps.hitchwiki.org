
<p><?php echo _("Link to this view"); ?></p>

<label for="weblink" class="icon link"><?php echo _("Weblink"); ?></label><br />
<textarea class="copypaste" id="clip_link"></textarea>

<br /><br />

<label for="code_for_hitchwiki" class="icon tag"><?php echo _("Code for hitchwiki"); ?></label><br />
<textarea rows="3" class="copypaste" id="clip_wiki"></textarea>

<br /><br />

<label for="embed_code" class="icon tag"><?php echo _("Code for embedding"); ?></label><br />
<textarea rows="3" cols="5" class="copypaste" id="clip_embed"></textarea><br />
<small><?php echo _("You can embed a map to your own blog for example."); ?></small>


<script type="text/javascript">
	$(function() {
	
		function update_clips() {
		
			var map_center = map.getCenter().transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
			
			var str_lat = map_center.lat;
			var str_lon = map_center.lon;
			var str_zoom = map.getZoom();
			
			var str_link = '<?php echo $settings["base_url"]; ?>/?zoom='+str_zoom+'&lat='+str_lat+'&lon='+str_lon;
			var str_wiki = '&lt;map lat=&quot;'+str_lat+'&quot; lng=&quot;'+str_lon+'&quot; zoom=&quot;'+str_zoom+'&quot; view=&quot;0&quot; float=&quot;right&quot; /&gt;';
			var str_embed = '&lt;iframe src=&quot;<?php echo $settings["base_url"]; ?>/widget.php?lat='+str_lat+'&amp;lng='+str_lon+'&amp;zoom='+str_zoom+'&quot; frameborder=&quot;0&quot; width=&quot;480px&quot; height=&quot;350px&quot;&gt;&lt;/iframe&gt;';
			
			$("#clip_link").html(str_link);
			$("#clip_wiki").html(str_wiki);
			$("#clip_embed").html(str_embed);
		}
		
		update_clips();
		
		// Select all from textarea on focus
		$(".copypaste").focus(function(){
		    this.select();
		});
	});
</script>