<?php 

require_once("phpolait/phpolait.php");

class rpc {
    function say($str) {
        return "You said $str!";
    }

    function getMarkers($lat, $lng, $count) {
        $res = array();
        while ($count--) {
            $res[$count] = array(rand(0,12000)/100-60, rand(0,36000)/100-180);
        }
        return $res;
    }
}

$server = new JSONRpcServer(new rpc());

?>
