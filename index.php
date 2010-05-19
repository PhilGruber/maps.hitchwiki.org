<?php 

require_once "rpc.php";

?><html>
    <head>
        <?php $server->javascript("rpc"); ?>
        <script src="http://openlayers.org/api/OpenLayers.js"></script>
        <script src="main.js"></script>
        <link rel='stylesheet' type='text/css' href='main.css' />
    </head>
    <body onload='init();'>
        <div id='map'> </div>
    </body>
</html>
