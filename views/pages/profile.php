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
    		<td><?php echo $profile["location"]; ?></td>
    	</tr>
    	<?php endif; ?>
    	
    	
    	<?php if(!empty($profile["country"])): ?>
    	<tr>
    		<td><b><?php echo _("Current country"); ?></b></td>
    		<td><?php echo ISO_to_country($profile["country"]); ?> <img class="flag" alt="" src="static/gfx/flags/png/<?php echo strtolower($profile["country"]); ?>.png" /></td>
    	</tr>
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
    	
    	
    </tbody>
</table>

<?php if(!empty($profile["google_latitude"])): ?>
<iframe src="http://www.google.com/latitude/apps/badge/api?user=<?php echo urlencode($profile["google_latitude"]); ?>&type=iframe&maptype=roadmap" width="400" height="400" frameborder="0"></iframe>
<?php endif; ?>


<div class="clear"></div>




<?php endif; ?>