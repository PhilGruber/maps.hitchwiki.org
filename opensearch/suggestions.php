<?php
/*
 * Hitchwiki Maps: opensearch/suggestions.php
 * Outputs suggestions list in opensearch xml format
 */

# TODO TODO TODO TODO

/*
 * Load config
 */
require_once "../config.php";

header ("content-type: text/xml");

echo '<?'; ?>xml version="1.0" encoding="utf-8"?>
<SearchSuggestion xmlns="http://opensearch.org/searchsuggest2" version="2.0">
	<Query><?php echo strip_tags($_GET["q"]); ?></Query>
	<Section title="<?php echo _("Results from Hitchwiki Maps"); ?>">
	<?php /*
		<Item>
			<Text>title</Text>
			<Description>desciption</Description>
			<Url><?php echo $settings["base_url"]; ?>/?place=</Url>
			<Image source="http://.jpg" height="50" width="50" align="middle" />
		</Item>
	*/ ?>
	</Section>
</SearchSuggestion>
