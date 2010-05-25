<?php 
/*
 * Hitchwiki Maps: index.php
 * 2010
 *
 */

/* 
 * Settings
 */
$key["google_maps"] = 		"";


require_once "lib/rpc.php";

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <title>Hitchwiki - Maps</title>
    
        <link rel="stylesheet" type="text/css" href="static/css/main.css?cache=<?= date("jnYHis"); ?>" media="all" />
        <?php $server->javascript("rpc"); ?>
        <!-- Map Services -->
        <!--
        <script src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $key["google_maps"]; ?>"></script>
        <script src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1&mkt=en-us" type="text/javascript"></script>
        <script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=euzuro-openlayers" type="text/javascript"></script>
        -->
        <script src="http://openlayers.org/api/OpenLayers.js" type="text/javascript" type="text/javascript"></script>
    
        <script src="static/js/jquery-1.4.2.min.js" type="text/javascript"></script>
		<script src="static/js/jquery.cookie.js" type="text/javascript"></script>
		<script src="static/js/jquery.json-2.2.min.js" type="text/javascript"></script>
        <script src="static/js/main.js?cache=<?= date("jnYHis"); ?>" type="text/javascript"></script>
        <script type="text/javascript">
        
        	/*
        	 * Use to get Users current location
        	 */
        	var ip = "<?php print $_SERVER['REMOTE_ADDR'];?>";
			var geolocation = "lib/ipinfodb/ip_proxy.php";
			var cookiename = 'geo_location';
			var cookieoptions = { path: '/', expires: 10 };
			
        </script>
		<link rel="shortcut icon" href="/favicon.png" type="image/png" />
		<link rel="bookmark icon" href="/favicon.png" type="image/png" />
		<link rel="image_src" href="/badge.png" />
		
		<meta name="description" content="Find good places for hitchhiking and add your own." />
    </head>
    <body>
		
		<div id="Content">
	
		<div id="Header">
			<div id="Logo">
				<h1>Hitchwiki</h1>
				<h2>Maps</h2>
				
				<div class="Navigation">
					<a href="http://hitchwiki.org/en/Main_Page">Wiki</a> | <a href="http://blogs.hitchwiki.org/">Blogs</a> | <a href="http://hitchwiki.org/planet/">Planet</a>
				</div>
				
				<h3>&mdash; Find good places for hitchhiking and add your favorites</h3>
				
			<!-- /Logo -->
			</div>

			<div id="Login">
					<ul class="align_right login_menu">
						<li><a href="#">Why to register?</a></li>
						<li><a href="#">Register!</a></li>
						<li><a href="#">Lost password?</a></li>
					</ul>
				<form action="#" method="post" name="login">
					<table cellpadding="0" cellspacing="0" border="0" class="align_left">
						<tr valign="middle">
							<td><label for="username">Username</label></td>
							<td><input type="text" value="" name="username" id="username" /></td>
							<td><input type="checkbox" value="1" name="remember_me" id="remember_me" /> <label for="remember_me">Remember me</label></td>
						</tr>
						<tr valign="middle">
							<td><label for="password">Password</label></td>
							<td><input type="password" value="" name="password" id="password" /></td>
							<td><button type="submit" class="button"><span class="icon lock">Login</span></button></td>
						</tr>
					</table>
				</form>
			<!-- /Login -->
			</div>
		
		<!-- /Header -->
		</div>
	
			<div id="Sidebar">
			
				<ul id="Navigation" role="Navigation">
				
					<!-- 1st block -->
					<li>
						<ul>
							<li><h3>Find places</h3></li>
							<li id="search">
								<form method="get" action="#" id="search_form" name="search">
									<table cellpadding="0" cellspacing="0" border="0">
										<tr valign="middle">
											<td><input type="text" value="" id="q" name="q" /></td>
											<td><button type="submit" id="submit" class="button"><span class="icon magnifier">&nbsp;</span><span class="hidden">Search</span></button></td>
										</tr>
									</table>
								</form>
								
							</li>
							
							<li title="Show nearby places" id="nearby" class="hidden">
								<span class="icon map_magnify">Nearby places from:</span><br />
								<ul>
									<li class="city hidden"><a href="#"></a></li>
									<li class="state hidden"><a href="#"></a></li>
									<li class="country hidden"><a href="#"></a></li>
								</ul>
							</li>
						</ul>
					</li>
					
					<!-- 2nd block -->
					<li>
						<ul>	
							<li><h3>Tools</h3></li>
							<li><a href="#" class="icon add">Add place</a></li>
							<li><a href="#" class="icon link">Link here</a></li>
							<li><a href="#" class="icon table_add">New collection</a></li>
							<li><a href="#" class="icon table">All points</a></li>
							<li><a href="#" class="icon table">My points</a></li>
							<li><a href="#" class="icon tag">Download KML</a></li>
							<li><a href="#" class="icon help">Help</a></li>
							<li><a href="#" class="icon chart_bar">Statistics</a></li>
						</ul>
					</li>
					
					<!-- 3rd block -->
					<li>
						<ul>
							<!--
							<li><h3>Language</h3></li>
				    		<li><a href="#">Auf Deutsch</a></li>
				    		<li><a href="#">En Español</a></li>
				    		<li><a href="#">Suomeksi</a></li>
				    		<li><a href="#">по-pусский</a></li>
				    		-->
				    		<li>
				    		<label for="language"><h3>Choose language</h3></label>
				    		<form method="get" action="./">
				    			<select name="language" id="language">
				    				<option name="en" selected="selected">In English</option>
				    				<option name="de">Auf Deutsch</option>
				    				<option name="es">En Español</option>
				    				<option name="fi">Suomeksi</option>
				    				<option name="ru">по-pусский</option>
				    			</select>
				    		</form>
				    		<small><a href="#">Help us with translating!</a></small>
				    		</li>
						</ul>
					</li>
				
				</ul>
				
			
			<div id="Footer">
			    <ul>
			    	<li><a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" title="Licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License"><img alt="Creative Commons License" src="static/gfx/cc-by-sa.png"/></a></li>
		    	
			    	<li><a href="mailto:info@hitchwiki.org" title="Contact us!">info@hitchwiki.org</a></li>
			    	
			    	<li><a href="http://github.com/MrTweek/maps.hitchwiki.org">Developers</a></li>
			    </ul>
			    	
			<!-- /Footer -->
			</div>
			
			
			<!-- /Sidebar -->
			</div>
	        
	        
	        <!-- The Map -->
	        <div id="map"><?php /*
	        	<br /><br />
	        	Turn JavaScript on from your browser.
	        	
	        	<!-- popups -->
	        	<div class="card add_new_place" id="add_new_place">
	        	<form method="post" action="#" name="add_new_place">
	        		<h4>Add new place to <a href="#">United Kingdom</a></h4>
	        		<small>
	        			<b>Lat:</b> 64.54323, <b>Lon:</b> 23.32453<br />
	        			<b>Address:</b> Street 35, 33210 City
	        		</small>
					<br /><br />
	        		<label for="description">Description</label><br />
	        		<textarea id="description" rows="4" name="description">Lorem description ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.</textarea>

					<br /><br />
					<label for="rating">Rating</label>
					<br />
					<label for="type">Type</label>
					
					<br /><br />
					
					<div class="align_right">
						<button class="button" id="cancel" /><span class="icon cancel">Cancel</span></button>
						<button class="button" type="submit" id="add"><span class="icon accept">Add place</span></button>
					</div>
				</form>
	        	</div>
	        	
	        	<div class="card hh_place bad" id="hh_place_1">
	        		<h4>Bad hitchhiking place in <a href="#">United Kingdom</a></h4>
	        		<small>Address Street 35, 33210 City</small>
					<br /><br />
	        		Lorem description ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.

					<br /><br />
					Rating: bad (2/5)<br />
					Elevation: 200m<br />
					Published by: <a href="#">Joe Doe</a><br />
					<a href="#">Link to this</a>
	        	</div>
	        	
	        	
	        	<div class="card hh_place good" id="hh_place_2">
	        		<h4>Good hitchhiking place in <a href="#">United Kingdom</a></h4>
	        		<small>Address Street 35, 33210 City</small>
					<br /><br />
					Rating: good (4/5)<br />
					Elevation: 50m<br />
					<a href="#">Link to this</a>
	        	</div>
	        	
	        	
	        	<!-- /popups -->
	        	
	       */ ?></div>
	       <!-- /map -->


		<!-- /Content -->
		</div>
			


    </body>
</html>