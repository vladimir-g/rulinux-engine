<?php
//ob_start("ob_gzhandler", 9);
$user_theme = $usersC->get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'user_'.$_SESSION['user_name'];
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register">Регистрация</a> <a href="login">Вход</a>' : '<a href="logout">Выход</а>';
if($_SESSION['user_admin'])
	$invitation = '<a href="admin">Админка</a> '.$invitation;
$str = $coreC->get_settings_by_name('title');
$title = $site_name.' - '.$str.$title;
$users_link = 'users';
$mark_link = 'mark';
$search_link = 'search';
$tracker_link = 'tracker';
$not_approved_link = 'unconfirmed';
$faq_link = 'faq';
$rules_link = 'rules';
$news_link = 'news';
$articles_link = 'articles';
$gallery_link = 'gallery';
$forum_link = 'forum';
$links_link = 'links';
require 'themes/'.$theme.'/templates/main_header.tpl.php';
?>