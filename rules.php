<?php
require 'classes/core.php';
$title = ' - Правила сайта';
$rss_link='view-rss.php';
require 'header.php';
$rules = $coreC->get_settings_by_name(rules);
echo $rules;
require 'footer.php';
?>