<?php
require 'classes/core.php';
if(!empty($_GET['id']))
	$mark_id = (int)$_GET['id'];
else
	$mark_id = $uinfo['mark'];
$mark_info = mark::get_mark_info($mark_id);
$mark_name = $mark_info['name'];
$title = ' - Разметка '.$mark_name;
$rss_link='rss';
require 'header.php';
$description = $mark_info['description'];
$langs = $coreC->get_settings_by_name('langs');
require 'themes/'.$theme.'/templates/mark/main.tpl.php';
require 'footer.php';
?>