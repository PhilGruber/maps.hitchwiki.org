<?php
/*
 * Hitchwiki Maps: opensearch/index.php
 * Outputs an opensearch XML description
 *
 * http://www.opensearch.org/
 */

/*
 * Load config
 */
require_once "../config.php";

header ("content-type: text/xml");

echo '<?'; ?>xml version="1.0" encoding="utf-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:moz="http://www.mozilla.org/2006/browser/search/">
  <ShortName>Hitchwiki <?php echo _("Maps"); ?></ShortName>
  <Description>Search Hitchwiki <?php echo _("Maps"); ?></Description>
  <InputEncoding>UTF-8</InputEncoding>
  <Image height="16" width="16" type="image/png"><?php echo $settings["base_url"]; ?>/favicon.png</Image>
  <Url type="text/html" method="get" template="<?php echo $settings["base_url"]; ?>/?src=os&amp;q={searchTerms}"/>
  <Url type="application/x-suggestions+xml" template="<?php echo $settings["base_url"]; ?>/opensearch/suggestions.php?q={searchTerms}"/>
  <Url type="application/opensearchdescription+xml" rel="self" template="<?php echo $settings["base_url"]; ?>/opensearch/"/>
  <moz:SearchForm><?php echo $settings["base_url"]; ?>/</moz:SearchForm>
</OpenSearchDescription>