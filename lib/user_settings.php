<?php
/*
 * Hitchwiki Maps: user_settings.php
 * To register new people and to change settings of registered people
 */

/*
 * Load config to set language and stuff
 */
require_once "../config.php";

start_sql();


// Mode set?
if(!isset($_GET["settings"]) && !isset($_GET["register"])) { echo json_encode( array("error"=>_("Missing method, updating or registering?")) ); exit; }
 
 
/*
 * Validate fields
 */
 
	// User ID
	if(isset($_GET["settings"]) && isset($_POST["user_id"])) {
		// Id is ok?
		if(empty($_POST["user_id"]) && !is_numeric($_POST["user_id"])) { echo json_encode( array("error"=>_("Invalid user ID.")) ); exit; }
		
		// Check if it's current user who is updating this, include a password with info array
		$user = $user = current_user(true);
		if($_POST["user_id"] != $user["id"]) { echo json_encode( array("error"=>_("You don't have permission do update settings for this user.")) ); exit; }
	}
	elseif(isset($_GET["settings"]) && !isset($_POST["user_id"])) { echo json_encode( array("error"=>_("Missing user ID.")) ); exit; }


	// Email
	if(!is_valid_email_address($_POST["email"])) { echo json_encode( array("error"=>_("Invalid email address.")) ); exit; }
	elseif(isset($_GET["register"])) {
		 // Check that email is unique
		$res4 = mysql_query("SELECT `email` FROM `t_users` WHERE `email` = '".mysql_real_escape_string($_POST["email"])."' LIMIT 1");
	   	if(!$res4) { echo json_encode( array("error"=>_("Oops! Something went wrong! Try again.")) ); exit; }
		
		// If we have a result (means email is in use)
		if(mysql_num_rows($res4) > 0) { echo json_encode( array("error"=>_("Email is in use. You need to pick another one.")) ); exit; }
	}
	
	
	// Name
	if(empty($_POST["name"])) { echo json_encode( array("error"=>_("Name missing.")) ); exit; }
	
	
	// Validating password required when a) registering b) always when it's filled in update form
	if(isset($_GET["register"]) OR !empty($_POST["password1"]) OR !empty($_POST["password2"])) { 
		// Password
		if(empty($_POST["password1"]) OR empty($_POST["password2"])) { echo json_encode( array("error"=>_("Password missing.")) ); exit; }
		
		if(strlen($_POST["password1"]) < 8 OR strlen($_POST["password2"]) < 8) { echo json_encode( array("error"=>_("Password too short. It must be at least 8 letters.")) ); exit; }
		
		if($_POST["password1"] != $_POST["password2"]) { echo json_encode( array("error"=>_("Write same password to both password fields.")) ); exit; }
	
		if($_POST["password1"] == $_POST["email"]) { echo json_encode( array("error"=>_("Password can't be same as your email address.")) ); exit; }
		
		if($_POST["password1"] == $_POST["name"]) { echo json_encode( array("error"=>_("Password can't be same as your name.")) ); exit; }
		
		$password = md5($_POST["password1"]);
	}
	
	
	// Check has user changed password or email to new one
	if(isset($password) && isset($_GET["settings"]) && $password==$user["password"]) $login_info_changed = true;
	
	
	// Language
	if(!empty($_POST["language"])) {
		if(!isset($settings["valid_languages"][$_POST["language"]])) { echo json_encode( array("error"=>_("Illegal language code; ".htmlspecialchars($_POST["language"]).".")) ); exit; }
	
		$language = "'".mysql_real_escape_string($_POST["language"])."'";
	}
	else $language = 'NULL';
	
	
	// Location
	if(!empty($_POST["location"])) $location = "'".mysql_real_escape_string(htmlspecialchars($_POST["location"]))."'";
	else $location = 'NULL';
	
	
	// Country
	if(!empty($_POST["country"])) {
		$valid_countries = countrycodes();
		if(!isset($valid_countries[$_POST["country"]])) { echo json_encode( array("error"=>_("Illegal country code; ".htmlspecialchars($_POST["country"]).".")) ); exit; }
		
		$country = "'".mysql_real_escape_string($_POST["country"])."'";
	}
	else $country = 'NULL';
	
	


/*
 * Proceed to the database stuff
 */
 
	// Register new account
	if(isset($_GET["register"])) {
	
		$query = "INSERT INTO `t_users` (
					`id`,
					`name`,
					`password`,
					`email`,
					`registered`,
					`location`,
					`country`,
					`language`
				) VALUES (
					NULL, 
					'".mysql_real_escape_string(htmlspecialchars($_POST["name"]))."', 
					'".$password."', 
					'".mysql_real_escape_string($_POST["email"])."', 
					NOW(), 
					".$location.", 
					".$country.", 
					".$language."
				);";
				
		$res = mysql_query($query);   
		if(!$res) { echo json_encode( array("error"=>_("Oops! Something went wrong! Try again.")) ); exit; } 
	
	} 
	
	// Update old account settings
	elseif(isset($_GET["settings"])) {
	
		$query = "UPDATE `t_users` SET 
					`name` = '".mysql_real_escape_string($_POST["name"])."',";
		
		if(isset($password)) $query .= "`password` = '".$password."',";
		
		$query .= "	`email` = '".mysql_real_escape_string($_POST["email"])."',
					`location` = ".$location.",
					`country` = ".$country.",
					`language` = ".$language." 
				WHERE `id` = ".mysql_real_escape_string($_POST["user_id"])." LIMIT 1;";
	
		$res = mysql_query($query);   
		if(!$res) { echo json_encode( array("error"=>_("Oops! Something went wrong! Try again.")) ); exit; } 
	
	
	} 

// Either a password or email was updated, request to login again
if($login_info_changed===true) {
	check_login($_POST["email"], $password);
}

// If we made it this far... plop out our success!
$output["success"] = true;
$output["email"] = strip_tags($_POST["email"]);
echo json_encode($output);

?>