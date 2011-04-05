<?php
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.' - Правила сайта';
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
$rules = core::get_settings_by_name(rules);
echo $rules;
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>