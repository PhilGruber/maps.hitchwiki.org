<h2><?php echo _("Complete statistics"); ?></h2>

<p><?php printf( _( 'There are currently %s places marked.' ), '<b>'.total_places().'</b>' ); ?> <a href="./?page=statistics" onclick="open_page('statistics'); return false;"><?php echo _("See more compact statistics."); ?></a></p>

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
			<?php list_countries("tr", "markers"); ?>
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
			<?php list_cities("tr", "markers"); ?>
		</tbody>
	</table>
	
</div>


<div class="clear"></div>