<?php
/*
 * Hitchwiki Maps: logout/index.php
 * Logout redirect script
 * Just to make login URL nicer ;-)
 */

$logout = true;
$silent = true;
require_once("../ajax/login.php");

// Reload to the frontpage
header("Location: ../");
exit;
