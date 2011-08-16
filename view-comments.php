<?php
require 'classes/core.php';
$rss_link='rss';
$title = ' - Последние коментарии';
if(!empty($_GET['offset']))
	$offset = (int)$_GET['offset'];
else
	$offset = 0;
require 'header.php';
if(!empty($_GET['user']))
	$user = (string)$_GET['user'];
else
	$user = $profile_name;
$limit = 50;
if(count($msg)<$limit)
	$next_offset = $offset;
else
	$next_offset = $offset+$limit;
if($offset>$limit)
	$prev_offset = $offset-$limit;
else
	$prev_offset = 0;
if(isset($_GET['resp']))
{
	$backward_link = 'replys_offset_'.$prev_offset.'_'.$user;
	$forward_link = 'replys_offset_'.$next_offset.'_'.$user;
	require 'themes/'.$theme.'/templates/view_comments/resp_top.tpl.php';
	$msg = $messagesC->get_user_reply($user, $limit, $offset);
}
else
{
	$backward_link = 'comments_offset_'.$prev_offset.'_'.$user;
	$forward_link = 'comments_offset_'.$next_offset.'_'.$user;
	require 'themes/'.$theme.'/templates/view_comments/top.tpl.php';
	$msg = $messagesC->get_user_messages($user, $limit, $offset);
}
for($i=0; $i<count($msg); $i++)
{
	$sect = $sectionsC->get_section_by_tid($msg[$i]['tid']);
	$section_link = $sect['rewrite'];
	$section_name = $sect['name'];
	$subsection_link = $sect['rewrite'].'_'.$sect['subsection_id'].'_page_1';
	$subsection_name = $sect['subsection_name'];
	$message_number = $threadsC->get_msg_number_by_tid($msg[$i]['tid'], $msg[$i]['id']);
	$page = ceil($message_number/$uinfo['comments_on_page']);
	if($page == 0)
		$page = 1;
	$link = 'thread_'.$msg[$i]['tid'].'_page_'.$page.'#msg'.$msg[$i]['id'];
	$subject = $msg[$i]['subject'];
	$timestamp = core::to_local_time_zone($msg[$i]['timest']);
	require 'themes/'.$theme.'/templates/view_comments/middle.tpl.php';
}
if(isset($_GET['resp']))
{
	require 'themes/'.$theme.'/templates/view_comments/resp_bottom.tpl.php';
}
else
{
	require 'themes/'.$theme.'/templates/view_comments/bottom.tpl.php';
}
require 'footer.php';
?>