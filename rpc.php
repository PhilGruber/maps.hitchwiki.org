<?php 

require_once("phpolait/phpolait.php");

class rpc {
    function say($str) {
        return "You said $str!";
    }
}

$server = new JSONRpcServer(new rpc());

?>
