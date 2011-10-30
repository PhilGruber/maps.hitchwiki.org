<?php
/*
 * Hitchwiki Maps: index.php
 * 2010
 *
 */

/*
 * Initialize Maps
 */
if(@is_file('../config.php')) require_once "../config.php";
else $settings["maintenance_page"] = true;

/*
 * Put up a maintenance -sign
 * Set it up from config.php or test it from ./?maintenance
 */
if(isset($_GET["maintenance"])) $settings["maintenance_page"] = true;
if($settings["maintenance_page"]===true && !in_array($_SERVER['REMOTE_ADDR'], $settings["non_maintenance_ip"])) {
	@include("../maintenance_page.php");
	exit;
}


/*
 * Returns an info-array about logged in user (or false if not logged in)
 * With this we also check if user is logged in by every load
 * You should include this line to every .php where you need to know if user is logged in
 */
$user = current_user();


/*
 * Check if user IS an admin, if Not, redirect to the frontpage
 */
if($user["logged_in"]!==true OR $user["admin"]!==true):

	header("Location: ../");
	exit;

else:
/*
 * Show the admin pages:
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html
	xmlns="http://www.w3.org/1999/xhtml"
	dir="ltr"
	lang="<?php echo shortlang(); ?>">
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <title><?php echo 'Hitchwiki '._("Maps").' - '._("Admin"); ?></title>

        <link rel="stylesheet" type="text/css" href="../static/css/ui-lightness/jquery-ui-1.8.5.custom.css" media="all" />

    	<!-- Scripts -->
        <script type="text/javascript">
		//<![CDATA[

			var locale = "<?php echo $settings["language"]; ?>";

		//]]>
        </script>

        <script src="../static/js/jquery-1.4.2.min.js" type="text/javascript"></script>
		<script src="../static/js/jquery-ui-1.8.5.custom.min.js" type="text/javascript"></script>
        <script src="../static/js/admin.js<?php if($settings["debug"]==true) echo '?cache='.date("jnYHis"); ?>" type="text/javascript"></script>

        <!-- Keep main stylesheet after main.js -->
        <link rel="stylesheet" type="text/css" href="../static/css/main.css<?php if($settings["debug"]==true) echo '?cache='.date("jnYHis"); ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="../static/css/admin.css<?php if($settings["debug"]==true) echo '?cache='.date("jnYHis"); ?>" media="all" />

        <link rel="shortcut icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />
		<link rel="bookmark icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />

		<!--[if lt IE 7]>
		<style type="text/css">
    	    .png,
    	    .icon
    	     { behavior: url(../static/js/iepngfix.htc); }
		</style>
		<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
		<link rel="bookmark icon" href="../favicon.ico" type="image/x-icon" />
		<![endif]-->
    </head>
    <body class="<?php echo $settings["language"]; ?> admin">


		<div id="Content">

		<div id="Header">
			<div id="Logo">
				<h1><a href="http://www.hitchwiki.org/"><span>Hitchwiki</span></a></h1>
				<h2><?php echo _("Maps"); ?></h2>

				<div class="Navigation">
					<a href="http://hitchwiki.org/en/Main_Page"><?php echo _("Wiki"); ?></a> | <a href="http://blogs.hitchwiki.org/"><?php echo _("Blogs"); ?></a> | <a href="http://hitchwiki.org/planet/"><?php echo _("Planet"); ?></a>
				</div>

				<h3><?php echo _("Administration"); ?></h3>

			<!-- /Logo -->
			</div>

    	</div>

    	<ul class="AdminNavigation">
    		<li><a href="./">Dashboard</a></li>
    		<li><a href="./?page=places">Places</a></li>
    		<li><a href="./?page=users">Users</a></li>
    		<li><a href="./?page=new_language">Add new language</a></li>
    		<li><a href="./?page=translate_countrynames">Translate countries</a></li>
    		<li><a href="http://github.com/MrTweek/maps.hitchwiki.org/">@GitHub</a></li>
    		<li><a href="http://maps.hitchwiki.org">maps.hitchwiki.org</a></li>
    	</ul>

    	<div class="AdminContent">
	    	<?php

			/*
			 * Show page
			 */


			if( isset($_GET["page"])):

				$file = "views/".$_GET["page"].".php";

				if( !empty($_GET["page"]) && !ereg('[^0-9A-Za-z_-]', $_GET["page"]) && file_exists($file) ):

					include($file);

				else:
				?>
					<div class="ui-state-error ui-corner-all" style="padding: 0 .7em; margin: 20px 0;">
					    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
					    What you seek for, just isn't there...</p>
					</div>
				<?php
				endif;

			else:

				include("views/dashboard.php");

			endif;

	    	?>
    	</div>

    	</div>

    </body>
</html>
<?php endif; // if admin end ?>