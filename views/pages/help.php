<h2><?php echo _("Help"); ?></h2>


<div class="align_left" style="width: 400px; margin: 0 40px 20px 0;">


	<h3><?php echo _("What is this?"); ?></h3>
	<p><?php echo _("This is supposed to be a worldmap for hitchhikers, showing good and bad hitching places. Feel free to add all your favourite hitching places (or even more) to the map."); ?></p>
	
	<h3><?php echo _("How can I add places?"); ?></h3>
	<p><?php echo _("Just click on <i>Add place</i> in the menu. Set the orange marker to the place and click on <i>Add place</i>. Make sure to zoom as close as possible, so the point will be more accurate. It is also helpful if you give your points a rating and maybe a little description (i.e. what kind of place this is or how to get there). Please write description at least in English."); ?></p>
	
	<h3><?php echo _("Can I use HTML tags in descriptions and comments?"); ?></h3>
	<p><?php echo _('No, but you can use <a href="http://en.wikipedia.org/wiki/Markdown" target="_blank">markup</a> syntax.'); ?></p>
	
	<h3><?php echo _("Why should I sign up?"); ?></h3>
	<p><?php echo _("If you are logged in, you will have some more features on this site. I.e. you will be able to modify your places later, and your nickname will be shown on each of your places and comments."); ?></p>
	
	<h3><?php echo _("Why is the map always centered to Europe?"); ?></h3>
	<p><?php echo _("Most of this maps hitchhiking places are in Europe. If you have registered, you can set a point of your current country from settings. The map will center there whenever you log in. You can also login and register with Facebook, since it might be quicker."); ?></p>
	
	
</div>

<div class="align_left" style="width: 300px; margin: 0 0 20px 0;">

	<img src="badge.png" alt="" class="align_right" style="margin: 0 0 20px 20px;" />
	
	<fb:like-box profile_id="133644853341506" width="300" connections="10" stream="false" header="true"></fb:like-box>

	<fb:activity recommendations="true"></fb:activity>

	<script type="text/javascript">
	    $(function() {
	    	FB.XFBML.parse(document.getElementById('pages'));
	    });
	</script>
</div>

<div class="clear"></div>

<br />

<h2><?php echo _("About Hitchwiki Maps"); ?></h2>

<h4><?php echo _("Used technologies"); ?></h4>

<b><?php echo _("Server side"); ?></b>
<ul>
	<li><a href="http://sourceforge.net/projects/phpolait/">PHPOLait</a></li>
	<li><a href="http://sourceforge.net/projects/snoopy/">Snoopy</a></li>
	<li><a href="http://curl.haxx.se/">cURL</a></li>
	<li><a href="http://www.gnu.org/software/gettext/">Gettext</a></li>
	<li><a href="http://michelf.com/projects/php-markdown/">Markdown</a></li>
</ul>

<b><?php echo _("Client side"); ?></b>
<ul>
	<li><a href="http://openlayers.org/">Open Layers</a></li>
	<li><a href="http://jquery.com/">jQuery</a></li>
	<li><a href="http://jqueryui.com/">jQuery UI</a></li>
	<li><a href="http://plugins.jquery.com/project/cookie">jQuery Cookie Plugin</a></li>
	<li><a href="http://plugins.jquery.com/project/pstrength">jQuery Password Strength Field Plugin</a></li>
	<li><a href="http://code.google.com/p/jquery-json/">jQuery JSON</a></li>
	<li><a href="http://www.famfamfam.com/lab/icons/">Fam Fam Fam icons</a></li>
	<li><a href="http://www.aiga.org/content.cfm/symbol-signs">Aiga - Symbol Signs</a></li>
</ul>

<b><?php echo _("Services"); ?></b>
<ul>
	<li><a href="http://ipinfodb.com/">IPInfoDB API</a></li>
	<li><a href="http://www.openstreetmap.org/">Open Street Map</a></li>
	<!--
	<li><a href="http://maps.google.com/">Google Maps</a></li>
	<li><a href="http://maps.bing.com/">Bing Maps</a></li>
	-->
	<li><a href="http://www.geonames.org/">Geonames</a></li>
	<li><a href="http://wiki.openstreetmap.org/wiki/Nominatim">Nominatim</a></li>
	<li><a href="http://en.gravatar.com/">Gravatar</a></li>
	<li><a href="http://latitude.google.com/">Google Latitude</a></li>
</ul>


<h4><?php echo _("People involved"); ?></h4>
<ul>
	<li><a href="http://www.ihminen.org">Mikael Korpela</a></li>
	<li><a href="http://hitchwiki.org/en/User:MrTweek">Philipp Gruber</a></li>
</ul>


<b><?php echo _("Translators"); ?></b>
<ul>
	<li><?php echo _("Finnish"); ?> - <a href="http://www.ihminen.org">Mikael Korpela</a></li>
</ul>

