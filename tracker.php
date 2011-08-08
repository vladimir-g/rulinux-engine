<?php
!empty($_GET['h']) ? $hours = (int)$_GET['h'] : $hours = 3;
require 'classes/core.php';
$hours_count = $coreC->declOfNum($hours, array('час', 'часа', 'часов'));
$title = ' - Последние сообщения за '.$hours_count;
$rss_link='view-rss.php';
require 'header.php';
require 'themes/'.$theme.'/templates/tracker/nav.tpl.php';
$messages = $messagesC->get_messages_for_tracker($hours);
$msg_count = count($messages);
require 'themes/'.$theme.'/templates/tracker/top.tpl.php';
for($i=0; $i<count($messages);$i++)
{
	$sect = $sectionsC->get_section_by_tid($messages[$i]['tid']);
	$section_link = $sect['link'];
	$section = $sect['name'];
	$subsection_link = $sect['subsection_link'];
	$subsection = $sect['subsection_name'];
	$message_number = threads::get_msg_number_by_tid($messages[$i]['tid'], $messages[$i]['id']);
	$page = ceil($message_number/$uinfo['comments_on_page']);
	if($page == 0)
		$page = 1;
	$link = 'message.php?newsid='.$messages[$i]['tid'].'&page='.$page.'#'.$messages[$i]['id'];
	$subject = $messages[$i]['subject'];
	$author_info = $usersC->get_user_info($messages[$i]['uid']);
	$coreC->validate_boolean($author_info['banned']) ? $author = '<s>'.$author_info['nick'].'</s>' :$author = $author_info['nick'];
	if($coreC->validate_boolean($uinfo['show_resp']))
	{
		if($messages[$i]['referer']>0)
		{
			$msg_resp = $messagesC->get_message($messages[$i]['referer']);
			$msg_resp_autor = $usersC->get_user_info($msg_resp['uid']);
			$message_resp_user = $msg_resp_autor['nick'];
			$resp = '→'.$message_resp_user;
		}
		else
			$resp = '';
	}
	$timestamp = $coreC->to_local_time_zone($messages[$i]['timest']);
	require 'themes/'.$theme.'/templates/tracker/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/tracker/bottom.tpl.php';
require 'footer.php';
?>