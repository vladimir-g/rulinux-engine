<?php
!empty($_GET['h']) ? $hours = (int)$_GET['h'] : $hours = 3;
require 'classes/core.php';
$hours_count = $coreC->declOfNum($hours, array('час', 'часа', 'часов'));
$title = ' - Последние сообщения за '.$hours_count;
$rss_link='rss';
require 'header.php';
require 'themes/'.$theme.'/templates/tracker/nav.tpl.php';
$user_filter = $usersC->get_filter($_SESSION['user_id']);
$user_filter_arr = $filtersC->parse_filter_string($user_filter);
$user_filter_list = $filtersC->get_filter_list($user_filter);
if ($coreC->validate_boolean($uinfo['show_resp']))
	$show_resp = True;
else
	$show_resp = False;
$messages = $messagesC->get_messages_for_tracker($hours, False, $show_resp);
$msg_count = count($messages);
require 'themes/'.$theme.'/templates/tracker/top.tpl.php';
for($i=0; $i<count($messages);$i++)
{
	$msg = $messages[$i];
	$section_link = $msg['rewrite'];
	$section = $msg['sect_name'];
	$subsection_link = $msg['rewrite'].'_'.$msg['sub_id'].'_page_1';
	$subsection = $msg['sub_name'];
	$link = 'thread_'.$msg['tid'].'_comment_'.$msg['id'].'#msg'.$msg['id'];
	$filter_list = $filtersC->get_filter_list($msg['filters']);
	$active_filters = $filtersC->get_active_filters($filter_list, $user_filter_list);
	if ($messagesC->is_filtered($user_filter_arr, $msg['filters']))
	{
		$subject = 'Сообщение отфильтровано в соответствии с вашими настройками фильтрации';
		$is_filtered = true;
	}
	else
	{
		$subject = (!empty($msg['subject'])) ? $msg['subject'] : '(no title)';
		$is_filtered = false;
	}
	$coreC->validate_boolean($msg['banned']) ? $author = '<s>'.$msg['nick'].'</s>' :$author = $msg['nick'];
	if($show_resp)
	{
		if (!empty($msg['resp_user']))
			$resp = '→'.$msg['resp_user'];
		else
			$resp = '';
	}
	$timestamp = $coreC->to_local_time_zone($msg['timest']);
	$filter_link = 'set_filter_'.$msg['id'];
	require 'themes/'.$theme.'/templates/tracker/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/tracker/bottom.tpl.php';
require 'footer.php';
?>