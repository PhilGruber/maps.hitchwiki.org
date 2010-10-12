<?php
/* 
 * Hitchwiki - mediawiki_login.php
 *
 * MediaWiki Login trough API
 * http://www.mediawiki.org/wiki/API:Login
 * http://www.mediawiki.org/wiki/User:Patrick_Nagel/Login_with_snoopy_post-1.15.3
 *
 * Requires:
 * lib/Snoopy/Snoopy.class.php (http://snoopy.sourceforge.net/)
 *
 * Creative Commons Attribution/Share-Alike License
 * http://creativecommons.org/licenses/by-sa/3.0/
 *
 */

if (empty($argv[1])) { print("Specify the part of the URL after 'https://my.private.wiki/wiki/index.php/' as argument.\n"); exit; }

require_once "Snoopy/Snoopy.class.php";

# Settings
$snoopy = new Snoopy;
#$snoopy->curl_path="/usr/bin/curl";
$wikiroot = "http://hitchwiki.org/";
$api_url = $wikiroot . "/api.php";


# Login via api.php
$login_vars['action'] = "login";
$login_vars['lgname'] = "botusername";
$login_vars['lgpassword'] = "botpassword";
$login_vars['format'] = "php";

## First part
$snoopy->submit($api_url,$login_vars);
$response = unserialize($snoopy->results);
$login_vars['lgtoken'] = $response[login][token];
$snoopy->cookies = getCookieHeaders($snoopy->headers);

## Second part, now that we have the token
$snoopy->submit($api_url,$login_vars);

# Fetch the page
$login_vars['action'] = "render";
$urlpart=$argv[1];
$snoopy->submit($wikiroot . "/index.php?title=" . $urlpart, $login_vars);
print($snoopy->results);


# This function parses Snoopy's header array and returns a nice array of cookies
function getCookieHeaders($headers){
	$cookies = array();
	foreach($headers as $header)
		if(preg_match("/Set-Cookie: ([^=]*)=([^;]*)/", $header, $matches))
			$cookies[$matches[1]] = $matches[2];
	return $cookies;
} 


?>