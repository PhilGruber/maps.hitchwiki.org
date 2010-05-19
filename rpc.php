<?php 

require_once("phpolait/phpolait.php");

class rpc {
    function say($str) {
        return "You said $str!";
    }

    function getMarkers($lat, $lng, $count) {
        $res = array();
        while ($count--) {
            $res[$count] = array(rand(12000)/1000-60, rand(36000)/1000-180);
        }
        return "lululul";
        return $res;
    }
}

$server = new JSONRpcServer(new rpc());

?>
