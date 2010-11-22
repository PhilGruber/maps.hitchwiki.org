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

if(!isset($_GET["lat"]) && !isset($_GET["lon"]) && !empty($user["country"])) {
	$countryinfo = country_info($user["country"]);
	if($countryinfo!==false) {
		$lat = $countryinfo["lat"];
		$lon = $countryinfo["lon"];
		if(!isset($_GET["zoom"])) $zoom = '5';
	}
}

// Markers visible -level
// Limit loading new markers only to this zoom level and deeper (bigger numbers = more zoom)
// Also hides markers-layer before this zoom level and show country places count -labels instead
$default_markersZoomLimit = '7';
$markersZoomLimit = (isset($_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"]) && ctype_digit($_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"])) ? $_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"] : $default_markersZoomLimit;


if(isset($_GET["place"]) && $_GET["place"] != "" && preg_match ("/^([0-9]+)$/", $_GET["place"])) {
	$place = get_place($_GET["place"], true);
	if($place["error"]!==true) {
		$show_place = htmlspecialchars($_GET["place"]);
	}
	else {
		$show_place_error = true;
		unset($place);
	}
/* 
#Maybe here is a key to select one vector from a stack (JS)? (TODO)

sm = new GeoExt.grid.FeatureSelectionModel({layers: layers});
            t.ok(OpenLayers.Util.indexOf(layer.selectedFeatures,
272	                                         features[0]) < 0,
273	                 "click on row 0 does not select feature 0");
274	            
275	            // select feature 1
276	            // test that the second row is not selected
277	            sm.selectControl.select(features[1]);
278	            t.ok(!sm.isSelected(1),
279	                 "selecting feature 1 does not select row 1");

*/
}

/*
 *  Build a title
 */
// If place
if(isset($show_place)) {
    $title .= _("a Hitchhiking spot in").' '; 

    // in city, country
    if(!empty($place["location"]["locality"])) $title .= $place["location"]["locality"].', ';

    $title .= $place["location"]["country"]["name"];
    $title .= ' - ';
}
$title .= 'Hitchwiki '.gettext("Maps");
    
    
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html 
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:og="http://opengraphprotocol.org/schema/" 
	<?php 
	// Load schema only if FB-tags are filled in config
	if(!empty($settings["fb"])): ?>xmlns:fb="http://developers.facebook.com/schema/" <?php endif; ?>
	dir="ltr" 
	lang="<?php echo shortlang(); ?>">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<title><?php echo $title; ?> (BETA)</title>
		
		<link href="ajax/js-translation.json.php?lang=<?php echo $settings["language"]; ?>" lang="<?php echo $settings["language"]; ?>" rel="gettext"/>
		<link href="static/css/ui-lightness/jquery-ui-1.8.5.custom.css" media="all" rel="stylesheet" type="text/css" />
		<?php
		
		/*
		 * Map Services
		 * You need to enable these from init_map() in static/js/main.js 
		 * Set API keys and such to the config.php
		 */
		 
		 // Google maps
		if(!empty($settings["google_maps_api_key"])) {
			if($user["logged_in"]===true && empty($user["map_google"])) $print_map_google = false;
			else $print_map_google = true;
			
			if($print_map_google) echo '<script src="http://maps.google.com/maps?file=api&l='.shortlang().'&v=2&key='.$settings["google_maps_api_key"].'"></script>';
		}

		// Yahoo
		if(!empty($settings["yahoo_maps_appid"])) {
			if($user["logged_in"]===true && empty($user["map_yahoo"])) $print_map_yahoo = false;
			else $print_map_yahoo = true;
			
			if($print_map_yahoo) echo '<script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid='.$settings["yahoo_maps_appid"].'" type="text/javascript"></script>';
		}
		
		// MS VirtualEarth
		if($settings["ms_virtualearth"]===true) {
			if($user["logged_in"]===true && empty($user["map_vearth"])) $print_map_vearth = false;
			else $print_map_vearth = true;
			
			if($print_map_vearth) echo '<script src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1&mkt='.str_replace("_", "-", $settings["language"]).'" type="text/javascript"></script>';
		}
		
		?>
		<script src="http://openlayers.org/api/OpenLayers.js" type="text/javascript"></script>
		
		<!-- Scripts -->
		<script type="text/javascript">
		//<![CDATA[

			/*
			 * Misc settings
			 */
			var ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
			var geolocation = "ajax/geolocation_ip_proxy.php";
			var cookie_prefix = "<?php echo $settings["cookie_prefix"]; ?>";
			var geolocation_cookiename = "<?php echo $settings["cookie_prefix"]; ?>_geolocation";
			var geolocation_cookieoptions = { path: '/', expires: 6 }; // expires: hours
			var locale = "<?php echo $settings["language"]; ?>";
			var google_analytics = <?php echo (!empty($settings["google_analytics_id"]) ? 'true' : 'false'); ?>;

			/*
			 * Loaded Map layers
			 */
			var layer_google = <?php echo (!empty($settings["google_maps_api_key"])) ? "true": "false"; ?>;
			var layer_yahoo  = <?php echo (!empty($settings["yahoo_maps_appid"])) ? "true": "false"; ?>;
			var layer_vearth = <?php echo ($settings["ms_virtualearth"]===true) ? "true": "false"; ?>;
			var layer_default = "<?php echo (isset($user["map_default_layer"]) && !empty($user["map_default_layer"])) ? htmlspecialchars($user["map_default_layer"]): 'mapnik'; ?>";

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
		
		<script src="static/js/jquery-1.4.3.min.js" type="text/javascript"></script>
		<script src="static/js/jquery-ui-1.8.5.custom.min.js" type="text/javascript"></script>
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
			$pages = array(
				"help", 
				"statistics", 
				"complete_statistics", 
				"public_transport", 
				"add_public_transport", 
				"translate", 
				"countries", 
				"lost_password", 
				"api", 
				"news", 
				"settings", 
				"register", 
				"profile",
				"users",
				"beta"
			);
			?>
			$(document).ready(function() {
			
				<?php if(isset($_GET["demo"])): ?>
				showCountry('fi');
				<?php endif; ?>
			
				<?php // Open page
				if(isset($_GET["page"]) && in_array($_GET["page"], $pages)): ?>
				
					open_page("<?php echo htmlspecialchars($_GET["page"]); ?>");

				<?php // Open marker
				elseif(isset($show_place)): ?>
				
					showPlacePanel("<?php echo $show_place; ?>", true);

				<?php // Place asked, but didn't exist
				elseif(isset($show_place_error)): ?>
				
					info_dialog("<?php echo _("Sorry, but the place cannot be found.<br /><br />The place you are looking for might have been removed or is temporarily unavailable."); ?>", "<?php echo _("The place cannot be found"); ?>", true);

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
		<meta property="og:title" content="<?php echo $title; ?> (BETA)" />
		<meta property="og:site_name" content="Hitchwiki.org" />
		<meta property="og:description" content="<?php echo _("Find good places for hitchhiking and add your own."); ?>" />
		<meta property="og:image" content="<?php echo $settings["base_url"]; ?>/badge.png" />
		<meta property="og:url" content="<?php echo $settings["base_url"]; ?>/"/>
		<meta property="og:type" content="website" />
		<meta property="og:email" content="<?php echo $settings["email"]; ?>" />
	<?php if(isset($place)): ?>
		<meta property="og:latitude" content="<?php echo $place["lat"]; ?>" />
		<meta property="og:longitude" content="<?php echo $place["lon"]; ?>" />
		<meta property="og:locality" content="<?php echo $place["location"]["locality"]; ?>" />
		<meta property="og:country-name" content="<?php echo $place["location"]["country"]["name"]; ?>" />

		<meta name="geo.position" content="<?php echo $place["lat"].','.$place["lon"]; ?>" />
	<?php endif; ?>

		<?php if(isset($settings["fb"]["admins"]) && !empty($settings["fb"]["admins"])): ?><meta property="fb:admins" content="<?php echo $settings["fb"]["admins"]; ?>" /><?php endif; ?>
		<?php if(isset($settings["fb"]["page_id"]) && !empty($settings["fb"]["page_id"])): ?><meta property="fb:page_id" content="<?php echo $settings["fb"]["page_id"]; ?>" /><?php endif; ?>
		<?php if(isset($settings["fb"]["app"]["id"]) && !empty($settings["fb"]["app"]["id"])): ?><meta property="fb:app_id" content="<?php echo $settings["fb"]["app"]["id"]; ?>" /><?php endif; ?>

		<link rel="home" href="<?php echo $settings["base_url"]; ?>/" title="Hitchwiki <?php echo _("Maps"); ?>" />
		<link rel="help" href="<?php echo $settings["base_url"]; ?>/?page=help" title="Hitchwiki <?php echo htmlspecialchars(_("Help & About")); ?>" />
		<link rel="search" type="application/opensearchdescription+xml" href="<?php echo $settings["base_url"]; ?>/opensearch/" title="Hitchwiki <?php echo _("Maps"); ?>" />
		<?php
		/*
		 * Language versions of the frontpage
		 */ 
		foreach($settings["valid_languages"] as $code => $name) {
			// Don't print current in-use-language page
			if($settings["language"] != $code) echo '<link type="text/html" rel="alternate" hreflang="'.shortlang($code).'" href="'.$settings["base_url"].'/?lang='.$code.'" title="'.$name.'" />';
		}
		?>
		
		<?php if(isset($settings["fb"]["app"]["id"]) && !empty($settings["fb"]["app"]["id"])): ?>
		<div id="fb-root"></div>
		<script>
		/*
		 * Load Facebook JavaScript SDK
		 * http://developers.facebook.com/docs/reference/javascript/
		 */
		  window.fbAsyncInit = function() {
		    FB.init({appId: '<?php echo $settings["fb"]["app"]["id"]; ?>', status: true, cookie: true,
		             xfbml: true});
		  };
		  (function() {
		    var e = document.createElement('script'); e.async = true;
		    e.src = document.location.protocol +
		      '//connect.facebook.net/<?php
		      	
		      	// Localization + a little language fix
		      	if($settings["language"] == "en_UK") echo 'en_US';
		      	else echo $settings["language"]; 
		      	
		      ?>/all.js';
		    document.getElementById('fb-root').appendChild(e);
		  }());
		</script>
		<?php endif; ?>
				
		<!--[if lt IE 7]>
		<style type="text/css"> 
    	    .png,
    	    .icon
    	     { behavior: url(static/js/iepngfix.htc); }
		</style>
		<link rel="shortcut icon" href="<?php echo $settings["base_url"]; ?>/favicon.ico" type="image/x-icon" />
		<link rel="bookmark icon" href="<?php echo $settings["base_url"]; ?>/favicon.ico" type="image/x-icon" />
		<![endif]-->
    </head>
    <body class="<?php echo $settings["language"]; ?>">

		<div id="Content">
	
		<div id="Header">
			<div id="Logo">
				<h1><a href="http://www.hitchwiki.org/"><span>Hitchwiki</span></a></h1>
				<h2><?php echo _("Maps"); ?></h2>

				<div class="Navigation">
					<!--
					<a href="http://hitchwiki.org/en/Main_Page"><?php echo _("Wiki"); ?></a> | <a href="http://blogs.hitchwiki.org/"><?php echo _("Blogs"); ?></a> | <a href="http://hitchwiki.org/planet/"><?php echo _("Planet"); ?></a>
					-->
				 	<b><a href="http://maps.hitchwiki.org/">This is under development! See current working version.</a></b>
				
				</div>

				<h3><?php echo _("Find good places for hitchhiking and add your favorites"); ?></h3>

			<!-- /Logo -->
			</div>
			
			<div id="LoginNavi" class="<?php
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
					
					/*
					 * Icon
					 */
					if($user["admin"]===true) echo 'tux'; // ;-)
					else echo 'user_orange';
					
					echo '"'; //end class
					
					// Gravatar
					//if($user["allow_gravatar"]=="1" && !empty($user["email"])) echo ' style="background-image: url(http://www.gravatar.com/avatar/'.md5($user["email"]).'/?s=16);"';
					
					echo '>'; //end tag
					
					
					/*
					 * Pick one random hello
					 */
					$hello = array(
						"Hello!" => "GB",
						"Tere!" => "EE",
						"Hei!" => "FI",
						"Moi!" => "FI",
						"¡Hola!" => "ES",
						"Shalom!" => "IL",
						"Namaste!" => "NP",
						"Namaste!" => "IN",
						"Labas!" => "LT",
						"Mambo!" => "CG",
						"Bok!" => "HR",
						"Hallo!" => "NL",
						"Hallo!" => "DE",
						"Hej!" => "DK",
						"Hej!" => "SE",
						"Ciào!" => "IT",
						"Sveiki!" => "LV",
						"Moïen!" => "LU",
						"Salamaleikum," => "SN",
						"Čau!" => "SK",
						"Hoezit!" => "ZA",
						"Jambo!" => "KE",
						"Selam!" => "TR"
					);
					$hello_greeting = array_rand($hello,1);
					
					?><span title="<?php printf(_("Hello from %s"), ISO_to_country($hello[$hello_greeting])); ?>"><?php echo $hello_greeting; ?></span> <a href="./?page=profile" id="profile" class="pagelink"><?php echo $user["name"]; ?></a></span></span>

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
							<button type="submit" tabindex="5" class="button align_right"><span class="icon lock"><?php echo _("Login"); ?></span></button>
							<div id="rememberMeRow" class="align_left"><input type="checkbox" value="1" name="remember_me" id="remember_me" tabindex="4" /> <label for="remember_me"><?php echo _("Remember me"); ?></label></div>
							<br />
							<small id="login_meta">
								
								<a href="./?page=lost_password" id="lost_password" class="pagelink"><?php echo _("Lost password?"); ?></a>
								
							</small>
							
							
						</form>
					</div>
				<?php endif; ?>

</div>
			
			<div id="Sidebar">
			
				<ul id="Navigation" role="navigation">
				
					<!-- 1st block -->
					<li>
						<ul>
							<li><h3><?php echo _("Find places"); ?></h3></li>
							<li id="search">
								<form method="get" action="#" id="search_form" name="search" role="search">
									<div class="ui-widget">
									<input type="text" value="" id="q" name="q" />
									<button type="submit" class="search_submit button"> <span class="icon magnifier">&nbsp;</span><span class="hidden"><?php echo _("Search"); ?></span></button>
									<div class="clear"></div>
									</div>
								</form>
								
							</li>
							
							<li id="nearby" style="display:none;">
								<span class="icon map_magnify"><?php echo _("Nearby places from"); ?>:</span><br />
								<ul>
									<li class="locality" style="display:none;"><a href="#" title="<?php echo _("Show the city on the map"); ?>"></a></li>
									<li class="state" style="display:none;"><a href="#" title="<?php echo _("Show the state on the map"); ?>"></a></li>
									<li class="country" style="display:none;"><a href="#" title="<?php echo _("Show the country on the map"); ?>"></a></li>
								</ul>
							</li>
						</ul>
					</li>

					<!-- 2nd block -->
					<li>
						<ul>
							<li><a href="#" id="news" class="icon new pagelink"><b><?php echo _("Ooh! New Maps!"); ?></b></a></li>
							
							<li><a href="#" id="add_place" class="icon add"><?php echo _("Add place"); ?></a></li>
							<li><a href="#" id="tools" class="icon lorry"><?php echo _("Tools"); ?></a></li>
							<?php /*
							<li><a href="#" id="my_points" class="icon table pagelink"><?php echo _("My points"); ?></a></li>
							<li><a href="#" id="new_collection" class="icon table_add pagelink"><?php echo _("New collection"); ?></a></li>
							*/ ?>
							<li><a href="./?page=public_transport" id="public_transport" class="icon pagelink underground"><?php echo _("Public transport"); ?></a></li>
							<li><a href="./?page=countries" id="countries" class="icon world pagelink"><?php echo _("Countries"); ?></a></li>
							<li><a href="#" id="link_here" class="icon link cardlink"><?php echo _("Link here"); ?></a></li>
							<li><a href="#" id="download" class="icon page_white_put cardlink"><?php echo _("Download"); ?></a></li>
							<?php if($user["logged_in"]===true): ?>
							<li><a href="./?page=users" id="users" class="icon user pagelink"><?php echo _("Users"); ?></a></li>
							<?php endif; ?>
							<li><a href="./?page=help" id="help" class="icon help pagelink"><?php echo htmlspecialchars(_("Help & About")); ?></a></li>
							<li><a href="./?page=statistics" id="statistics" class="icon chart_bar pagelink"><?php echo _("Statistics"); ?></a></li>
			    			<?php
			    				// Visible only for admins
			    				if($user["admin"]===true) echo '<li><a href="./admin/" class="icon tux">'._("Admins").'</a></li>';
			    			?>
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
			    		<a href="http://www.facebook.com/Hitchwiki" class="icon facebook" style="margin: 2px 0 0 3px; padding-top: 3px; display: block; float: right;">Facebook</a>
			    	</li>

			    	<li>
			    		<a href="mailto:<?php echo $settings["email"]; ?>" title="<?php echo _("Contact us!"); ?>"><?php echo $settings["email"]; ?></a>
			    	</li>
			    	
			    	<li id="developers">
			    		<a href="http://github.com/Hitchwiki"><?php echo _("Developers"); ?></a>
			    		
			    		&bull;
			    		
			    		<a href="#" id="api" class="pagelink"><?php echo _("API"); ?></a>
			    		
			    		<?php /* toggle log link will be added here from main.js for devs */ ?>
			    		
			    	</li>
			    </ul>
			    

			<!-- /Footer -->
			</div>
			
			
			<!-- /Sidebar -->
			</div>
	        
	        
	        <!-- Adding a alace panel -->
	       <div id="AddPlacePanel">
	       		<h4 class="icon add"><?php echo _("Add place"); ?></h4>
	       	</div>
	        <!-- /Adding a alace panel -->
	        
	        
			<!-- AJAX Content Area for pages-->
			<div id="pages">
				<a href="#" class="close ui-button ui-corner-all ui-state-default ui-icon ui-icon-closethick" title="<?php echo _("Close"); ?>"><?php echo _("Close"); ?></a>
				<div class="page">
					<div class="content"> </div>
				</div>
			</div>
			<!-- /pages -->
	        
	        
			<!-- cards -->
			<div id="cards"></div>
			<!-- /pages -->
	        
	        
	        <!-- The Map -->
	        <div id="map">
	        	<br /><br />
	        	<?php echo _("Turn JavaScript on from your browser."); ?>
			</div>
	       <!-- /map -->
	       
	       
	        <!-- The Place panel -->
	       <div id="PlacePanel"></div>
	       <!-- /Place panel -->
	       
	       
	       <!-- Tools -->
	       <div id="toolsPanel" class="hidden">
	       		<h4 class="icon lorry">
	       			<?php echo _("Tools"); ?>
	       			<a href="#" class="close ui-icon ui-icon-closethick align_right" title="<?php echo _("Close"); ?>"><?php echo _("Close"); ?></a>
	       		</h4>
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
	       

			<div id="map_selector">
				<button id="selected_map" class="ui-corner-bottom"><?php echo _("Map"); ?>: <span class="map_name">Open Street Map</span></button>

				<div id="maplist" class="ui-corner-bottom">
				<ul>
					<li class="first"><a href="#" name="mapnik" class="icon icon-osm<?php if($user["map_default_layer"]=='mapnik' OR empty($user["map_default_layer"]) OR !isset($user["map_default_layer"])) { echo ' selected'; } ?>"><?php echo $map_layers["osm"]["mapnik"]; ?></a></li>
					<li><a href="#" name="osmarender" class="icon icon-osm<?php if($user["map_default_layer"]=='osmarender') { echo ' selected'; } ?>"><?php echo $map_layers["osm"]["osmarender"]; ?></a></li>
					<?php
					
					// Google
					if(!empty($settings["google_maps_api_key"])) {
						foreach($map_layers["google"] as $map => $name) {
				    		echo '<li><a href="#" name="'.$map.'" class="icon icon-google';
				    		if($user["map_default_layer"]==$map) echo ' selected';
				    		echo '">'.$name.'</a></li>';
						}
					}
					
					// Yahoo
					if(!empty($settings["yahoo_maps_appid"])) {
						foreach($map_layers["yahoo"] as $map => $name) {
				    		echo '<li><a href="#" name="'.$map.'" class="icon icon-yahoo';
				    		if($user["map_default_layer"]==$map) echo ' selected';
				    		echo '">'.$name.'</a></li>';
						}
					}
					
					// Virtual Earth
					if($settings["ms_virtualearth"]===true) {
						foreach($map_layers["vearth"] as $map => $name) {
				    		echo '<li><a href="#" name="'.$map.'" class="icon icon-bing';
				    		if($user["map_default_layer"]==$map) echo ' selected';
				    		echo '">'.$name.'</a></li>';
						}
					}
					
				    ?>
				</ul>
				</div>
			</div>

	       
		<!-- /Content -->
		</div>
		
		<!-- for debugging -->
		<div id="log" class="hidden"><b class="handle">Log</b><ul><li /></ul></div>

<?php // Google analytics
if(isset($settings["google_analytics_id"]) && !empty($settings["google_analytics_id"])): ?>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo $settings["google_analytics_id"]; ?>");
pageTracker._trackPageview();
} catch(err) {}</script>  

<?php endif; ?>
    </body>
</html>