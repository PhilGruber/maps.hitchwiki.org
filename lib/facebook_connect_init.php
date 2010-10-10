<?php
/*
 * Init Facebook JavaScript SDK with this file
 * Requires config.php to be loaded before
 *
 * http://developers.facebook.com/docs/reference/javascript/
 */


if(!empty($settings["fb"]["app"]["id"])):

	// Exception for valid FB-locales:
	if($settings["language"] == "en_UK") $fb_locale = "en_US";
	else $fb_locale = $settings["email"];
	
	#<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>

	// Init Script (Asynchronous Loading)
	// http://github.com/facebook/connect-js
	?><div id="fb-root"></div>
	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId  : '<?php echo $settings["fb"]["app"]["id"]; ?>', 
	      status : true, // check login status
	      cookie : true, // enable cookies to allow the server to access the session
	      xfbml  : true  // parse XFBML
	    });
	  };
	
	  (function() {
	    var e = document.createElement('script');
	    e.src = document.location.protocol + '//connect.facebook.net/<?php echo $fb_locale; ?>/all.js';
	    e.async = true;
	    document.getElementById('fb-root').appendChild(e);
	  }());
	</script>
<?php

endif;

?>