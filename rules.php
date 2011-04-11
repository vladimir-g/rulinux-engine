<?php
require 'classes/core.php';
$title = ' - Правила сайта';
include 'header.php';
$rules = core::get_settings_by_name(rules);
echo $rules;
require 'footer.php';
?>