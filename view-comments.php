<?php
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.' - Последние коментарии';
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register.php">Регистрация</a> <a href="login.php">Вход</a>' : '<a href="login.php?logout">Выход</а>';
if(!empty($_GET['offset']))
	$offset = (int)$_GET['offset'];
else
	$offset = 0;
if(!empty($_GET['user']))
	$user = (string)$_GET['user'];
else
	$user = $profile_name;
$limit = 50;
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
require 'themes/'.$theme.'/templates/view_comments/top.tpl.php';
$msg = messages::get_user_messages($user, $limit, $offset);
for($i=0; $i<count($msg); $i++)
{
	$sect = sections::get_section_by_tid($msg[$i]['tid']);
	$section_link = $sect['link'];
	$section_name = $sect['name'];
	$subsection_link = $sect['subsection_link'];
	$subsection_name = $sect['subsection_name'];
	$page = core::get_page_by_tid($msg[$i]['tid'], $msg[$i]['id'], $uinfo['comments_on_page']);
	$link = 'message.php?newsid='.$msg[$i]['tid'].'&page='.$page.'#'.$msg[$i]['id'];
	$subject = $msg[$i]['subject'];
	$timestamp = core::to_local_time_zone($msg[$i]['timest']);
	require 'themes/'.$theme.'/templates/view_comments/middle.tpl.php';
}
if(count($msg)<$limit)
	$next_offset = $offset;
else
	$next_offset = $offset+$limit;
if($offset>$limit)
	$prev_offset = $offset-$limit;
else
	$prev_offset = 0;
require 'themes/'.$theme.'/templates/view_comments/bottom.tpl.php';
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>