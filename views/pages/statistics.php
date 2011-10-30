<h2><?php echo _("Statistics"); ?></h2>

<p><?php printf( _('There are currently %s places marked.'), '<b>'.total_places().'</b>' ); ?> <a href="./?page=complete_statistics" onclick="open_page('complete_statistics'); return false;"><?php echo _("See more complete statistics."); ?></a></p>


<div class="align_left" style="margin: 0 40px 20px 0;">

	<h3><?php printf( _( 'Top %s countries' ), "20" ); ?></h3>
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

	<h3><?php printf( _( 'Top %s cities' ), "20" ); ?></h3>
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

	<h3><?php printf( _( 'By continents' ), "20" ); ?></h3>
	<table class="infotable" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo _("Continent"); ?></th>
				<th><?php echo _("Places"); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php list_continents("tr", true); ?>
		</tbody>
	</table>


<p>
	<h3><?php echo _("Hitchability"); ?> - <?php echo _("Vote distribution"); ?></h3>
	<img src="<?php echo rating_chart(rating_stats(), 200); ?>" alt="<?php echo _("Vote distribution"); ?>" />
</p>


<!--
	<h3><?php printf( _( 'Top %s users' ), "20" ); ?></h3>
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
-->
</div>

<div class="clear"></div>

	<h3><?php echo _("Place density"); ?></h3>
	<!-- http://code.google.com/apis/chart/docs/gallery/map_charts.html --
	<img src="http://chart.apis.google.com/chart?cht=t&chs=440x220&chd=s:_&chtm=world&chf=bg,s,faf9f3" alt="<?php echo _("Hitchability"); ?>" />

	<br /><br />-->

	<!-- http://code.google.com/apis/visualization/documentation/gallery/intensitymap.html -->
	<!--
	<iframe src="lib/map_statistics.php?map=1" width="460" height="250" border="0" style="border:0;"></iframe>

	<br /><br />
	-->
	<!-- http://code.google.com/apis/visualization/documentation/gallery/geomap.html -->
	<!--
	<iframe src="lib/map_statistics.php?map=2" width="820" height="430" border="0" style="border:0;"></iframe>
	-->
	<!-- http://code.google.com/apis/visualization/documentation/gallery/geomap.html -->
	<iframe src="ajax/map_statistics.php?map=3" width="820" height="430" border="0" style="border:0;"></iframe>
