<?php 

require_once("../config.php"); 

start_sql();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
    <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>DEV - Translate countrynames</title>
</head><body>
<?php

# A tool with you can list countrynames in certain language

# Limit to use with XML-request
#if(isset($_GET["limit"]) && !empty($_GET["limit"])) $limit = htmlspecialchars($_GET["limit"]);
#else $limit = false;

if(isset($_GET["locale"]) && !empty($_GET["locale"])) $locale = htmlspecialchars($_GET["locale"]);
else $locale = false;

if(isset($_GET["iso"]) && !empty($_GET["iso"])) $locale_short = htmlspecialchars($_GET["iso"]);
else $locale_short = false;

if($locale_short != false && $locale != false) {
	
	echo '<pre>';
	$i=0;
	
	$data = readURL("http://ws.geonames.org/countryInfoCSV?lang=".strtolower($locale_short));
	
	$lines = explode("\n",$data);
	foreach($lines as $num => $line) {
		if($num != 0 && !empty($line)) {
			$line = explode("\t",$line);
			
			if(!empty($line[4])) {
				$query = "UPDATE `country` SET `".$locale."` = '".mysql_real_escape_string( $line[4] )."' WHERE `iso` = '".$line[0]."';";
				echo $query."\n";
			}
		}
	}
	
	/*
	# XML-request, one qall per line (SLOW):
	$codes = countrycodes();
	foreach($codes as $iso => $name) {
	
	
		$xmlstr = readURL("http://ws.geonames.org/countryInfo?lang=".strtolower($locale_short)."&country=".$iso);
		$xml = new SimpleXMLElement($xmlstr);
		
		$query = "UPDATE `country` SET `".$locale."` = '".mysql_real_escape_string( (string)$xml->country->countryName )."' WHERE `iso` = '".$iso."';";
		echo $query."\n";
		#$result = mysql_query($query);
		
		if($limit===$i) break;
		$i++;
	
	}
	*/
	echo '</pre>';
}
else {
?>
<h2>Translate languagenames</h2>
<form method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

	<b>Language ISO code:</b> <input type="text" size="2" value="" name="iso" /> <small>eg. "de" for German</small>
	<br /><br />
	<b>Language locale:</b> <input type="text" size="5" value="" name="locale" /> <small>eg. "de_DE" for German</small>
	<br /><br /><!--
	<b>Limit:</b> <input type="text" size="2" value="" name="limit" /> <small>Limit query amount for testing it. Empty = no limit.</small>
	<br /><br />-->
	<input type="submit" value="Translate" />

</form>

<?php
}
?>
</body></html>