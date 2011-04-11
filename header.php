<?php
ob_start("ob_gzhandler", 9);
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register.php">Регистрация</a> <a href="login.php">Вход</a>' : '<a href="login.php?logout">Выход</а>';
$title = $site_name.$title;
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
?>