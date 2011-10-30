<?php
/*
 * Hitchwiki Maps: profile.php
 */


$profile = $user;

if(!empty($profile)): ?>

<?php if($user["id"] == $profile["id"]): ?><small><em><?php echo _("This is your profile as others see it."); ?></em></small><?php endif; ?>
<h2 title="<?php echo $profile["id"]; ?>"><?php echo $profile["name"]; ?></h2>

<table class="infotable" cellspacing="0" cellpadding="0" style="float: left; margin-right: 20px;">
    <tbody>

    	<?php if(!empty($profile["registered"])): ?>
    	<tr>
    		<td><b><?php echo _("Member since"); ?></b></td>
    		<td><?php echo date("j.n.Y", strtotime($profile["registered"])); ?></td>
    	</tr>
    	<?php endif; ?>


    	<?php if(!empty($profile["location"])): ?>
    	<tr>
    		<td><b><?php echo _("Location"); ?></b></td>
    		<td><a href="#" id="search_for_this"><?php echo $profile["location"]; ?></a></td>
    	</tr>
    	<?php endif; ?>


    	<?php if(!empty($profile["country"])): ?>
    	<tr>
    		<td><b><?php echo _("Country"); ?></b></td>
    		<td><a href="#" id="search_for_this"><?php echo ISO_to_country($profile["country"]); ?></a> <img class="flag" alt="" src="static/gfx/flags/png/<?php echo strtolower($profile["country"]); ?>.png" /></td>
    	</tr>
    	<?php endif; ?>

    	<?php if(!empty($profile["location"]) OR !empty($profile["country"])): ?>
		<script type="text/javascript">
		    $("a#search_for_this").click(function(e){
		    	e.preventDefault();
		    	search($(this).text(),true);
		    });
		</script>
    	<?php endif; ?>

    	<?php if(!empty($profile["language"])): ?>
    	<tr>
    		<td><b><?php echo _("Language"); ?></b></td>
    		<td><?php echo _($settings["languages_in_english"][$profile["language"]]); ?></td>
    	</tr>
    	<?php endif; ?>


    	<?php if($profile["admin"]===true): ?>
    	<tr>
    		<td colspan="2"><span class="icon tux"><?php echo _("Administrator"); ?></span></td>
    	</tr>
    	<?php endif; ?>

    	<?php
    	/*
		 * Gravatar
		 * http://en.gravatar.com/site/implement/
		 */
    	if(!empty($profile["email"]) && $profile["allow_gravatar"] == "1") {

			$str = file_get_contents( 'http://www.gravatar.com/'.md5($profile["email"]).'.php' );
			$gravatar = unserialize( $str );

			if ( is_array( $gravatar ) && isset( $gravatar['entry'] ) ) {

				?>
    			<tr>
    				<td colspan="2">
						<a href="<?php echo $gravatar['entry'][0]['profileUrl']; ?>"><img src="http://www.gravatar.com/avatar/<?php echo md5($profile["email"]); ?>/?s=200&amp;default=<?php echo urlencode($settings["base_url"]."/static/gfx/blank.gif"); ?>"  alt="<?php echo $gravatar['entry'][0]['displayName']; ?>" /></a>
						<br />
						<small><?php printf(_("Image from your %s"), '<a href="'.$gravatar['entry'][0]['profileUrl'].'" target="_blank" title="A Globally Recognized Avatar">Gravatar</a>'); ?></small>
    				</td>
    			</tr>
    			<?php
    		} // gravatar found
    	} // email ok and gravatar allowed
    	?>

    </tbody>
</table>


<?php if(!empty($profile["google_latitude"])): ?>
<iframe src="http://www.google.com/latitude/apps/badge/api?user=<?php echo urlencode($profile["google_latitude"]); ?>&type=iframe&maptype=roadmap" width="400" height="400" frameborder="0"></iframe>
<?php endif; ?>


<div class="clear"></div>




<?php endif; ?>