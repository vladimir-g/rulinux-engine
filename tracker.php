<?php
!empty($_GET['h']) ? $hours = (int)$_GET['h'] : $hours = 3;
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$hours_count = core::declOfNum($hours, array('час', 'часа', 'часов'));
$title = $site_name.' - Последние сообщения за '.$hours_count;
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register.php">Регистрация</a> <a href="login.php">Вход</a>' : '<a href="login.php?logout">Выход</а>';
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
require 'themes/'.$theme.'/templates/tracker/nav.tpl.php';
$messages = messages::get_messages_for_tracker($hours);
$msg_count = count($messages);
require 'themes/'.$theme.'/templates/tracker/top.tpl.php';
for($i=0; $i<count($messages);$i++)
{
	$sect = sections::get_section_by_tid($messages[$i]['tid']);
	$section_link = $sect['link'];
	$section = $sect['name'];
	$subsection_link = $sect['subsection_link'];
	$subsection = $sect['subsection_name'];
	$page = core::get_page_by_tid($messages[$i]['tid'], $messages[$i]['id'], $uinfo['comments_on_page']);
	$link = 'message.php?newsid='.$messages[$i]['tid'].'&page='.$page.'#'.$messages[$i]['id'];
	$subject = $messages[$i]['subject'];
	$author_info = users::get_user_info($messages[$i]['uid']);
	core::validate_boolean($author_info['banned']) ? $author = '<s>'.$author_info['nick'].'</s>' :$author = $author_info['nick'];
	if(core::validate_boolean($uinfo['show_resp']))
	{
		if($messages[$i]['referer']>0)
		{
			$msg_resp = messages::get_message($messages[$i]['referer']);
			$msg_resp_autor = users::get_user_info($msg_resp['uid']);
			$message_resp_user = $msg_resp_autor['nick'];
			$resp = '→'.$message_resp_user;
		}
		else
			$resp = '';
	}
	$timestamp = core::to_local_time_zone($messages[$i]['timest']);
	require 'themes/'.$theme.'/templates/tracker/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/tracker/bottom.tpl.php';
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>