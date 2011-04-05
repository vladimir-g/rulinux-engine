<?php
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
if(!empty($_GET['id']))
	$mark_id = (int)$_GET['id'];
else
	$mark_id = $uinfo['mark'];
$mark_info = mark::get_mark_info($mark_id);
$mark_name = $mark_info['name'];
$title = $site_name.' - Разметка '.$mark_name;
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
echo '<h2>Разметка '.$mark_name.'</h2>';
echo $mark_info['description'];
echo '<br /><br />';
$langs = core::get_settings_by_name('langs');
echo $langs;
echo '<br />';
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>