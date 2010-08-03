<?php 
/*
 * Hitchwiki Maps: index.php
 * 2010
 *
 */

/*
 * Initialize Maps
 */
if(@is_file('config.php')) require_once "config.php";
else $settings["maintenance_page"] = true;


/*
 * Put up a maintenance -sign
 * Set it up from config.php or test it from ./?maintenance
 */
if(isset($_GET["maintenance"])) $settings["maintenance_page"] = true;
if($settings["maintenance_page"]===true && !in_array($_SERVER['REMOTE_ADDR'], $settings["non_maintenance_ip"])) {
	@include("maintenance_page.php");
	exit;
}


/*
 * Returns an info-array about logged in user (or false if not logged in) 
 * With this we also check if user is logged in by every load
 * You should include this line to every .php where you need to know if user is logged in
 */
$user = current_user();



/*
 * Map settings
 */
// Zoom, lat, lon, layers
$zoom = (isset($_GET["zoom"]) && ctype_digit($_GET["zoom"])) ? $_GET["zoom"] : '4';

// Centered to Germany (51,9). Projection center would be '49','8.3'
$lat = (isset($_GET["lat"]) && is_numeric($_GET["lat"])) ? $_GET["lat"] : '51';
$lon = (isset($_GET["lon"]) && is_numeric($_GET["lon"])) ? $_GET["lon"] : '9';
#$layers = (isset($_GET["layers"]) && !empty($_GET["layers"])) ? strip_tags($_GET["layers"]) : 'B';
$layers = 'B';

// Markers visible -level
// Limit loading new markers only to this zoom level and deeper (bigger numbers = more zoom)
// Also hides markers-layer before this zoom level and show country places count -labels instead
$default_markersZoomLimit = '7';
$markersZoomLimit = (isset($_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"]) && ctype_digit($_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"])) ? $_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"] : $default_markersZoomLimit;


if(isset($_GET["place"]) && $_GET["place"] != "" && preg_match ("/^([0-9]+)$/", $_GET["place"])) {
	$show_place = htmlspecialchars($_GET["place"]);
	$place = get_place($_GET["place"], true);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" dir="ltr" lang="<?php echo shortlang(); ?>">
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <title><?php
    
    	// If place
    	if(isset($show_place)) {
    		echo _("a Hitchhiking spot").' in '; 
    		// in city, country
    		if(!empty($place["location"]["city"])) echo $place["location"]["city"].', ';
    		
    		echo $place["location"]["country"]["name"];
    		
    		echo ' - ';
    	}
    ?>Hitchwiki <?php echo _("Maps"); ?></title>
    
        <link rel="stylesheet" type="text/css" href="static/css/ui-lightness/jquery-ui-1.8.2.custom.css" media="all" />

        <!-- RPC -->
        <?php 
        	#$server->javascript("rpc"); 
        ?>

        <!-- Map Services -->
        <!-- You need to enable these from init_map() in static/js/main.js -->
        <!--
        <script src="http://maps.google.com/maps?file=api&l=<?php echo substr($settings["language"], 0, 2); /* ISO_639-1 ('en_UK' => 'en') */ ?>&v=2&key=<?php echo $settings["google_maps_api_key"]; ?>"></script>
        <script src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1&mkt=<?php echo str_replace("_", "-", $settings["language"]); ?>" type="text/javascript"></script>
        <script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=<?php echo $settings["yahoo_maps_appid"]; ?>" type="text/javascript"></script>
        -->
        <script src="http://openlayers.org/api/OpenLayers.js" type="text/javascript" type="text/javascript"></script>
    
    	<!-- Scripts -->
        <script type="text/javascript">
		//<![CDATA[
        	/*
        	 * Misc settings
        	 */
        	var ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
			var geolocation = "lib/ipinfodb/ip_proxy.php";
			var cookie_prefix = "<?php echo $settings["cookie_prefix"]; ?>";
			var geolocation_cookiename = "<?php echo $settings["cookie_prefix"]; ?>_geolocation";
			var geolocation_cookieoptions = { path: '/', expires: 24 };
			var locale = "<?php echo $settings["language"]; ?>";

			/*
			 * Default map settings
			 */
			var lat = <?php echo $lat; ?>;
			var lon = <?php echo $lon; ?>;
			var layers = '<?php echo $layers; ?>';
			var zoom = <?php echo $zoom; ?>;
			var markersZoomLimit = <?php echo $markersZoomLimit; ?>; 
	
		//]]>
        </script>
        
        <?php /* if(!empty($settings["google_maps_api_key"])): ?>
        <script src="http://maps.google.com/maps?file=api&v=2&key=<?php echo $settings["google_maps_api_key"]; ?>&sensor=false" type="text/javascript"></script>
        <?php endif; */ ?>
        
        <script src="static/js/jquery-1.4.2.min.js" type="text/javascript"></script>
		<script src="static/js/jquery-ui-1.8.2.custom.min.js" type="text/javascript"></script>
		<script src="static/js/jquery.cookie.js" type="text/javascript"></script>
		<script src="static/js/jquery.json-2.2.min.js" type="text/javascript"></script>
        <script src="static/js/main.js<?php if($settings["debug"]==true) echo '?cache='.date("jnYHis"); ?>" type="text/javascript"></script>
        
        <!-- Keep main stylesheet after main.js -->
        <link rel="stylesheet" type="text/css" href="static/css/main.css<?php if($settings["debug"]==true) echo '?cache='.date("jnYHis"); ?>" media="all" />
        
        <script type="text/javascript">
		//<![CDATA[
			<?php
			
        	/*
        	 * Open JS-pages requested by GET 'page'
        	 */
        	 
        	// Allowed page names
			$pages = array("help", "statistics", "complete_statistics", "translate", "countries", "lost_password", "api", "news", "settings", "register", "profile");
			?>
			$(document).ready(function() {
			
				<?php // Open page
				if(isset($_GET["page"]) && in_array($_GET["page"], $pages)): ?>
				
					open_page("<?php echo htmlspecialchars($_GET["page"]); ?>");

				<?php // Open marker
				elseif(isset($show_place)): ?>
				
					showPlacePanel("<?php echo $show_place; ?>",true);

				<?php // Perform search
				elseif(isset($_GET["q"]) && !empty($_GET["q"])): ?>
				
					search("<?php echo strip_tags($_GET["q"]); ?>");
					
				<?php endif; ?>
			});
		//]]>
        </script>
		<link rel="shortcut icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />
		<link rel="bookmark icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />
		<link rel="image_src" href="<?php echo $settings["base_url"]; ?>/badge.png" />
		<link rel="apple-touch-icon" href="<?php echo $settings["base_url"]; ?>/badge-57x57.png" />

		<meta name="description" content="<?php echo _("Find good places for hitchhiking and add your own."); ?>" />
		
		<!-- The Open Graph Protocol - http://opengraphprotocol.org/ -->
		<meta property="og:title" content="Maps" />
		<meta property="og:site_name" content="Hitchwiki.org" />
		<meta property="og:description" content="<?php echo _("Find good places for hitchhiking and add your own."); ?>" />
		<meta property="og:image" content="<?php echo $settings["base_url"]; ?>/badge.png" />
		<meta property="og:url" content="<?php echo $settings["base_url"]; ?>/"/>
		<meta property="og:type" content="website" />
		<meta property="og:email" content="<?php echo $settings["email"]; ?>" />
	<?php if(isset($place)): ?>
		<meta property="og:latitude" content="<?php echo $place["lat"]; ?>" />
		<meta property="og:longitude" content="<?php echo $place["lon"]; ?>" />
		<meta property="og:locality" content="<?php echo $place["location"]["city"]; ?>" />
		<meta property="og:country-name" content="<?php echo $place["location"]["country"]["name"]; ?>" />
	<?php endif; ?>

		<link rel="search" type="application/opensearchdescription+xml" href="<?php echo $settings["base_url"]; ?>/opensearch/" title="Hitchwiki <?php echo _("Maps"); ?>" />
		
		<!--[if lt IE 7]>
		<style type="text/css"> 
    	    .png,
    	    .icon
    	     { behavior: url(static/js/iepngfix.htc); }
		</style>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<link rel="bookmark icon" href="favicon.ico" type="image/x-icon" />
		<![endif]-->
    </head>
    <body class="<?php echo $settings["language"]; ?>">
		
		<!-- AJAX Content Area for pages and cards-->
		<div id="pages"></div>
		<div id="cards"></div>
		
		<div id="Content">
	
		<div id="Header">
			<div id="Logo">
				<h1><a href="http://www.hitchwiki.org/"><span>Hitchwiki</span></a></h1>
				<h2><?php echo _("Maps"); ?></h2>

				<div class="Navigation">
					<a href="http://hitchwiki.org/en/Main_Page"><?php echo _("Wiki"); ?></a> | <a href="http://blogs.hitchwiki.org/"><?php echo _("Blogs"); ?></a> | <a href="http://hitchwiki.org/planet/"><?php echo _("Planet"); ?></a>
				</div>

				<h3><?php echo _("Find good places for hitchhiking and add your favorites"); ?></h3>

			<!-- /Logo -->
			</div>
			
<?php /*
			<div id="map_selector">
				<button id="selected_map"><?php echo _("Map"); ?>: Open Street Map</button>

				<div id="maplist" class="ui-corner-bottom ui-corner-tr">
				<ul>
				    <li><input type="radio" id="map_osm" name="maplist" class="ui-corner-top" checked="checked" /><label for="map_osm">Open Street map</label></li>
				    <li><input type="radio" id="map_goostr" name="maplist" /><label for="map_goostr">Google Maps - <?php echo _("Street"); ?></label></li>
				    <li><input type="radio" id="map_goosat" name="maplist" /><label for="map_goosat">Google Maps - <?php echo _("Satellite"); ?></label></li>
				    <li><input type="radio" id="map_goosatl" name="maplist" /><label for="map_goosatl">Google Maps - <?php echo _("Satellite with labels"); ?></label></li>
				    <li><input type="radio" id="map_yahoo" name="maplist" class="ui-corner-top" /><label for="map_yahoo">Yahoo Maps</label></li>
				    <li><input type="radio" id="map_bing" name="maplist" class="ui-corner-bottom" /><label for="map_bing">Bing Maps</label></li>
				</ul>
				</div>
			</div>
*/ ?>
			<div id="Login" class="<?php
				if($user["logged_in"]===true) echo 'logged_in';
				else echo 'logged_out';
				
			?>">
				<?php 
				// User is logged in:
				if($user["logged_in"]===true): ?>
				
					<ul class="align_right" id="loginSidemenu">
						<li><a href="./?page=profile" id="profile" class="pagelink"><?php echo _("Profile"); ?></a></li>
						<li><a href="./?page=settings" id="settings" class="pagelink"><?php echo _("Settings"); ?></a></li>
						<li><a href="./logout/" id="logout"><?php echo _("Logout"); ?></a></li>
					</ul>
					<span id="Hello"><span class="icon <?php
					
					// Icon
					if($user["admin"]===true) echo 'tux'; // ;-)
					else echo 'user_orange';
					
					?>"><?php echo _("Hello!"); ?> <a href="./?page=profile" id="profile" class="pagelink"><?php echo $user["name"]; ?></a></span></span>

				<?php 
				// User is NOT logged in:
				else: ?>
				
					<ul class="align_right" id="loginSidemenu">
						<li><a href="./?page=why_to_register" id="why_to_register" class="pagelink"><?php echo _("Why to register?"); ?></a></li>
						<li><a href="./?page=register" id="register" class="pagelink"><?php echo _("Register!"); ?></a></li>
					</ul>

					<a href="#" id="loginOpener" class="icon lock align_right"><?php echo _("Login"); ?></a>
				
				<?php endif; ?>
			<!-- /Login -->
			</div>
		
		<!-- /Header -->
		</div>
		<div id="Login">
				<?php /* By submitting this with JS, you can reload this page and map will be as it was, if you fill lat/lon/zoom inputs and change post->get */ ?>
				<form method="post" action="./" id="reloadPage" class="hidden">
				    <input type="submit" />
				</form>
				<?php 
				// User is logged in:
				if($user===false): ?>
					<div id="loginPanel">
						<div class="loading"></div>
						<form action="#" method="post" name="login" id="login_form">
							<label for="email"><?php echo _("Email"); ?></label><br />
							<input tabindex="2" type="text" value="" name="email" id="email" /><br />
							<br />
							<label for="password"><?php echo _("Password"); ?></label><br />
							<input tabindex="3" type="password" value="" name="password" id="password" /><br />
							<br />
							<button type="submit" id="submit" tabindex="5" class="button align_right"><span class="icon lock"><?php echo _("Login"); ?></span></button>
							<div id="rememberMeRow" class="align_left"><input type="checkbox" value="1" name="remember_me" id="remember_me" tabindex="4" /> <label for="remember_me"><?php echo _("Remember me"); ?></label></div>
							<br />
							<small id="lostPasswordRow"><a href="./?page=lost_password" id="lost_password" class="pagelink"><?php echo _("Lost password?"); ?></a></small>
						</form>
					</div>
				<?php endif; ?>

</div>
			
			<div id="Sidebar">
			
				<ul id="Navigation" role="Navigation">
				
					<!-- 1st block -->
					<li>
						<ul>
							<li><h3><?php echo _("Find places"); ?></h3></li>
							<li id="search">
								<form method="get" action="#" id="search_form" name="search">
									<div class="ui-widget">
									<input type="text" value="" id="q" name="q" />
									<button type="submit" id="submit" class="button"> <span class="icon magnifier">&nbsp;</span><span class="hidden"><?php echo _("Search"); ?></span></button>
									<div class="clear"></div>
									</div>
								</form>
								
							</li>
							
							<li id="nearby" class="hidden">
								<span class="icon map_magnify"><?php echo _("Nearby places from"); ?>:</span><br />
								<ul>
									<li class="city hidden"><a href="#" title="<?php echo _("Show on the map"); ?>"></a></li>
									<li class="state hidden"><a href="#" title="<?php echo _("Show on the map"); ?>"></a></li>
									<li class="country hidden"><a href="#" title="<?php echo _("Show on the map"); ?>"></a></li>
								</ul>
							</li>
						</ul>
					</li>

					<!-- 2nd block -->
					<li>
						<ul>
							<li><a href="#" id="news" class="icon new pagelink"><b><?php echo _("Ooh! New Maps!"); ?></b></a></li>
							
							<li><a href="#" id="add_place" class="icon add cardlink"><?php echo _("Add place"); ?></a></li>
							<li><a href="#" id="tools" class="icon lorry"><?php echo _("Tools"); ?></a></li>
							<li><a href="./?page=countries" id="countries" class="icon world pagelink"><?php echo _("Countries"); ?></a></li>
							<?php /*
							<li><a href="#" id="my_points" class="icon table pagelink"><?php echo _("My points"); ?></a></li>
							<li><a href="#" id="new_collection" class="icon table_add pagelink"><?php echo _("New collection"); ?></a></li>
							*/ ?>
							<li><a href="#" id="link_here" class="icon link cardlink"><?php echo _("Link here"); ?></a></li>
							<li><a href="#" id="download" class="icon tag cardlink"><?php echo _("Download KML"); ?></a></li>
							<li><a href="./?page=help" id="help" class="icon help pagelink"><?php echo _("Help & About"); ?></a></li>
							<li><a href="./?page=statistics" id="statistics" class="icon chart_bar pagelink"><?php echo _("Statistics"); ?></a></li>
						</ul>
					</li>
					
					<!-- 3rd block -->
					<li>
						<ul>
				    		<li>
				    		<!--<label for="language"><h3><?php echo _("Choose language"); ?></h3></label>-->
				    		<form method="get" action="./" name="language_selection" id="language_selection">
				    			<select name="lang" id="language" title="<?php echo _("Choose language"); ?>">
				    				<?php
				    				// Print out available languages
				    				foreach($settings["valid_languages"] as $code => $name) {
				    					echo '<option value="'.$code.'"';
				    					
				    					if($code == $settings["language"]) echo ' selected="selected"';
				    					
				    					echo '>'.$name.'</option>';
				    				}
				    				?>
				    			</select>
				    			<input type="submit" id="submit" class="button" value="&raquo;" />
				    		</form>
				    		<small><a href="./?page=translate" id="translate" class="pagelink"><?php echo _("Help us with translating!"); ?></a></small>
				    		</li>
						</ul>
					</li>
				
				</ul>


			<div id="Footer">
			    <ul>
			    	<li>
			    		<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/" title="<?php echo _("Licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License"); ?>"><img alt="Creative Commons License" src="static/gfx/cc-by-sa.png"/></a>
			    		&nbsp;
			    		<a href="http://www.facebook.com/pages/Hitchwiki/133644853341506" class="icon facebook" style="margin: 2px 0 0 3px; display: block; float: right;">Facebook</a>
			    	</li>

			    	<li>
			    		<a href="mailto:<?php echo $settings["email"]; ?>" title="<?php echo _("Contact us!"); ?>"><?php echo $settings["email"]; ?></a>
			    	</li>
			    	
			    	<li id="developers">
			    		<a href="http://github.com/MrTweek/maps.hitchwiki.org/"><?php echo _("Developers"); ?></a>
			    		
			    		&bull;
			    		
			    		<a href="#" id="api" class="pagelink"><?php echo _("API"); ?></a>
			    		
			    		<!-- toggle log link will be added here for devs -->
			    	</li>
			    </ul>
			    

			<!-- /Footer -->
			</div>
			
			
			<!-- /Sidebar -->
			</div>
	        
	        
	        <!-- The Map -->
	        <div id="map">
	        	<br /><br />
	        	<?php echo _("Turn JavaScript on from your browser."); ?>
			</div>
	       <!-- /map -->
	       
	        <!-- The Place panel -->
	       <div id="PlacePanel"></div>
	       <!-- /Place panel -->
	       
	       <!-- tools -->
	       <div id="toolsPanel" class="hidden">
	       		<h4 class="icon lorry"><?php echo _("Tools"); ?></h4>
				<div id="controlToggle">
				
				        <span class="icon cursor">
				        	<input type="radio" name="type" value="none" id="noneToggle" onclick="toggleControl(this);" checked="checked" />
				        	<label for="noneToggle"><?php echo _("Navigate"); ?></label>
				        </span><br />
				        
				        <span class="icon vector">
                			<input type="radio" name="type" value="line" id="lineToggle" onclick="toggleControl(this);" />
                			<label for="lineToggle"><?php echo _("Measure distance"); ?></label>
				        </span><br />
				        
				        <span class="icon shape_handles">
				        	<input type="radio" name="type" value="polygon" id="polygonToggle" onclick="toggleControl(this);" />
				        	<label for="polygonToggle"><?php echo _("Measure area"); ?></label>
				        </span><br />
				        
				        <?php /* 
				        Note that the geometries drawn are planar geometries and the metrics returned by the measure control are planar 
				        measures by default. If your map is in a geographic projection or you have the appropriate projection definitions 
				        to transform your geometries into geographic coordinates, you can set the "geodesic" property of the control to 
				        true to calculate geodesic measures instead of planar measures.
				        
				        <input type="checkbox" name="geodesic" checked="checked" id="geodesicToggle" onclick="toggleGeodesic(this);" />
				        <label for="geodesicToggle"><?php echo _("Use geodesic measures"); ?></label>
				        <br />
				        */ ?>
				        
				    	<div class="align_right clear"><?php echo _("Measure"); ?>: <span id="toolOutput">-</span></div>
				    	
				    	<hr />
				    	
				    	<label class="icon zoom"><?php echo _("Show markers after zoom level"); ?>:</label>
				    	<div id="zoom_slider"></div>
				    	
				    	<span class="align_left"><?php echo _("Default"); ?>: <?php echo $default_markersZoomLimit; ?></span>
				    	<span class="align_right">
				    		<b id="zoom_slider_amount"></b><span id="zoomlevel">
				    											<span class="z_continent hidden"> - <?php echo _("Continent level"); ?></span>
				    											<span class="z_country hidden"> - <?php echo _("Country level"); ?></span>
				    											<span class="z_city hidden"> - <?php echo _("City level"); ?></span>
				    											<span class="z_streets hidden"> - <?php echo _("Street level"); ?></span>
				    										</span>
				    	</span>

				</div>
	       </div>
	       <!-- /tools -->

	        <!-- Placeholder for simple error/info -dialog. see info_dialog(); from main.js for more. -->
	       <div id="dialog-message"></div>
	       
	       <!-- Loading -bar -->
	       <div id="loading-bar"><small class="title"></small></div>
	       
		<!-- /Content -->
		</div>
		
		<!-- for debugging -->
		<div id="log" class="hidden"><ul></ul></div>
		
    </body>
</html>