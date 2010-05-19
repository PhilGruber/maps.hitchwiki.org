<?php 
/*
 * Hitchwiki Maps: index.php
 * 2010
 *
 */

require_once "lib/rpc.php";

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <title>Hitchwiki - Maps</title>
    
        <link rel="stylesheet" type="text/css" href="static/css/main.css?cache=<?= date("jnYHis"); ?>" media="all" />
        <?php $server->javascript("rpc"); ?>
        <script src="http://openlayers.org/api/OpenLayers.js" type="text/javascript"></script>
        <script src="static/js/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="static/js/main.js?cache=<?= date("jnYHis"); ?>" type="text/javascript"></script>
        
        <!--
		<link rel="image_src" href="badge.jpg" />
		<link rel="shortcut icon" href="favicon.png" type="image/png" />
		<link rel="bookmark icon" href="favicon.png" type="image/png" />
		-->		
		
		<meta name="description" content="" />
    </head>
    <body onload="init();">

        <div id="map"></div>

    </body>
</html>
