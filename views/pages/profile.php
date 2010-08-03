<?php
/*
 * Hitchwiki Maps: profile.php
 */
 

$profile = $user;

if(!empty($profile)): ?>

<?php if($user["id"] == $profile["id"]): ?><small><em>This is your profile as others see it.</em></small><?php endif; ?>
<h2 title="<?php echo $profile["id"]; ?>"><?php echo $profile["name"]; ?></h2>

<table class="infotable" cellspacing="0" cellpadding="0">
    <tbody>
    	
    	
    	<?php if(!empty($profile["registered"])): ?>
    	<tr>
    		<th><?php echo _("Registered"); ?></th>
    		<td><?php echo date("j.n.Y", strtotime($profile["registered"])); ?></td>
    	</tr>
    	<?php endif; ?>
    	
    	
    	<?php if(!empty($profile["location"])): ?>
    	<tr>
    		<th><?php echo _("Location"); ?></th>
    		<td><?php echo $profile["location"]; ?></td>
    	</tr>
    	<?php endif; ?>
    	
    	
    	<?php if(!empty($profile["country"])): ?>
    	<tr>
    		<th><?php echo _("Country"); ?></th>
    		<td><?php echo ISO_to_country($profile["country"]); ?> <img class="flag" alt="" src="static/gfx/flags/png/<?php echo strtolower($profile["country"]); ?>.png" /></td>
    	</tr>
    	<?php endif; ?>
    	
    	
    	<?php if(!empty($profile["language"])): ?>
    	<tr>
    		<th><?php echo _("Language"); ?></th>
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
	



<div class="clear"></div>

<?php endif; ?>