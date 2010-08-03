<?php
/*
 * Hitchwiki Maps: login.php
 * Login script
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";

start_sql();
		
// You can debug login cookies by using ./lib/login.php?debug
if(isset($_GET["debug"])) {

	echo "<pre>email: ".$_COOKIE[$settings["cookie_prefix"]."email"]."\nPassword: ".$_COOKIE[$settings["cookie_prefix"]."password"]."</pre>";

}
// Logout script
elseif(isset($_GET["logout"]) or isset($logout)) {

	// Unset cookies
	if(isset($_COOKIE[$settings["cookie_prefix"]."email"])) setcookie($settings["cookie_prefix"]."email", false, time()-(60*60*24*365), "/");
	
	if(isset($_COOKIE[$settings["cookie_prefix"]."password"])) setcookie($settings["cookie_prefix"]."password", false, time()-(60*60*24*365), "/");

	if(!isset($silent)) echo json_encode( array('login' => false) );

} 
// Login script
else {

	if(isset($_POST) && check_login($_POST["email"], md5($_POST["password"])) !== false) {

		// Remember me -checkbox
		if(isset($_POST["remember"]) && $_POST["remember"]=="1") $login_status["time"] = time()+(60*60*24*365); // the cookie will last one year
		else $login_status["time"] = 0; // the cookie will expire at the end of the session (when the browser closes)
		
		// Set cookies
		setcookie($settings["cookie_prefix"]."email", $_POST["email"], $login_status["time"], "/");
		setcookie($settings["cookie_prefix"]."password", md5($_POST["password"]), $login_status["time"], "/");
		
		// Login OK
		$login_status['login'] = true;

	// Login failed
	} else $login_status['login'] = false;
	
	
	/* 
	 * Output login status in JSON
	 */
	echo json_encode($login_status);

}

?>