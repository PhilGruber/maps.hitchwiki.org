<?php 
/* Hitchwiki Maps - rpc.php
 *
 * Requires:
 * lib/api.php
 * lib/functions.php
 * lib/phpolait/phpolait.php
 *
 */
 
require_once("phpolait/phpolait.php");
require_once("api.php");

start_sql();
$api = new maps_api();


class rpc {
    
    function getMarkers($lt, $lb, $rt, $rb) { // map bounds
    
    	if(!empty($lt) || !empty($lb) || !empty($rt) || !empty($rb)) {
    		return $api->getMarkersByCoords($lt, $lb, $rt, $rb);
    		#return '[{"id":"2504","lat":"60.2081838545239","long":"24.8995685577393"},{"id":"2517","lat":"60.202127708633","long":"24.8825740814209"},{"id":"2518","lat":"60.2037271515713","long":"24.8803853988647"},{"id":"2724","lat":"60.2531008763395","long":"24.883861541748"},{"id":"2726","lat":"60.2119843680319","long":"24.9702286720276"},{"id":"2727","lat":"60.217116760907","long":"24.9778461456299"},{"id":"4260","lat":"60.2117445169447","long":"24.9699711799622"},{"id":"4267","lat":"60.2034925714863","long":"24.9658513069153"}]';
    	}
    	else return '{"error":"true"}';
    }
    
}

$server = new JSONRpcServer(new rpc());

?>
