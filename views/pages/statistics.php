<h2><?php echo _("Statistics"); ?></h2>

<a href="./?page=complete_statistics" onclick="open_page('complete_statistics'); return false;">More complete statistics...</a>

<p><?php echo printf( _( 'There are currently %s places marked.' ), total_places() ); ?></p>

<div class="align_left" style="margin: 0 40px 20px 0;">

	<h3><?php echo _("Top countries"); ?></h3>
	<table class="infotable" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo _("Country"); ?></th>
				<th><?php echo _("Places"); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php list_countries("tr", "markers", 20); ?>
		</tbody>
	</table>
	
</div>


<div class="align_left" style="margin: 0 40px 20px 0;">

	<h3><?php echo _("Top cities"); ?></h3>
	<table class="infotable" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo _("City"); ?></th>
				<th><?php echo _("Country"); ?></th>
				<th><?php echo _("Places"); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php list_cities("tr", "markers", 20); ?>
		</tbody>
	</table>
	
</div>


<div class="align_left" style="margin: 0 40px 20px 0;">

	<h3><?php echo _("Top users"); ?></h3>
	<table class="infotable" cellspacing="0" cellpadding="0">
	    <thead>
	    	<tr>
	    		<th><?php echo _("User"); ?></th>
	    		<th><?php echo _("Places"); ?></th>
	    	</tr>
	    </thead>
	    <tbody>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    	<tr>
	    		<td>User</td>
	    		<td>1</td>
	    	</tr>
	    </tbody>
	</table>

</div>

<div class="clear"></div>

	<h3><?php echo _("Hitchability"); ?></h3>
	<!-- http://code.google.com/apis/chart/docs/gallery/map_charts.html --
	<img src="http://chart.apis.google.com/chart?cht=t&chs=440x220&chd=s:_&chtm=world&chf=bg,s,faf9f3" alt="<?php echo _("Hitchability"); ?>" />

	<br /><br />-->
	
	<!-- http://code.google.com/apis/visualization/documentation/gallery/intensitymap.html -->
	<iframe src="lib/hitchability_map.php?map=1" width="460" height="250" border="0" style="border:0;"></iframe>
	
	<br /><br />
	
	<!-- http://code.google.com/apis/visualization/documentation/gallery/geomap.html -->
	<iframe src="lib/hitchability_map.php?map=2" width="820" height="430" border="0" style="border:0;"></iframe>

	<!-- http://code.google.com/apis/visualization/documentation/gallery/geomap.html -->
	<iframe src="lib/hitchability_map.php?map=3" width="820" height="430" border="0" style="border:0;"></iframe>
