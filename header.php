<?php
//ob_start("ob_gzhandler", 9);
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register.php">Регистрация</a> <a href="login.php">Вход</a>' : '<a href="login.php?logout">Выход</а>';
$str = core::get_settings_by_name('title');
$title = $site_name.' - '.$str.$title;
$users_link = 'users.php';
$mark_link = 'mark.php';
$search_link = 'search.php';
$tracker_link = 'tracker.php';
$not_approved_link = 'view-all.php';
$faq_link = 'faq.php';
$rules_link = 'rules.php';
$news_link = 'view-section.php?id=1';
$articles_link = 'view-section.php?id=2';
$gallery_link = 'view-section.php?id=3';
$forum_link = 'view-section.php?id=4';
require 'themes/'.$theme.'/templates/header.tpl.php';
?>