<?php
require 'classes/core.php';
$thread_id = (int)$_GET['newsid'];
$page = (int)$_GET['page'];
$coreC->update_sessions_table(session_id(),$_SESSION[user_id], $thread_id);
$section = $sectionsC->get_section_by_tid($thread_id);
$section_id = $section['id'];
$section_name = $section['name'];
$section_link = $section['link'];
$subsection_id = $section['subsection_id'];
$subsection_name = $section['subsection_name'];
$subsection_link = $section['subsection_link'];
$prev = $threadsC->get_previous_thread($thread_id);
$next = $threadsC->get_next_thread($thread_id);
$thread_previous_link = 'message.php?newsid='.$prev['id'].'&page=1';
$thread_previous_subject = $prev['subject'];
$thread_next_link = 'message.php?newsid='.$next['id'].'&page=1';
$thread_next_subject = $next['subject'];
$topic_start = $messagesC->get_topic_start_message($thread_id);
$message_subject = $thread_subject = $topic_start['subject'];
$message_comment = $topic_start['comment'];
$msg_autor = $usersC->get_user_info($topic_start['uid']);
$coreC->validate_boolean($msg_autor['banned']) ? $message_autor = '<s>'.$msg_autor['nick'].'</s>' :$message_autor = $msg_autor['nick'];
$message_autor_profile_link = '/profile.php?user='.$msg_autor['nick'];
if(!$coreC->validate_boolean($topic_start['show_ua']))
	$message_useragent = '';
else
	$message_useragent = $topic_start['useragent'];
$message_timestamp = $coreC->to_local_time_zone($topic_start['timest']);
$message_id = $topic_start['id'];
$message_set_filter_link = 'set-filter.php?id='.$message_id;
$message_add_answer_link = 'comment.php?answerto='.$thread_id.'&cid='.$message_id;
$message_edit_link = 'edit-message.php?id='.$message_id;
$messages_count = $messagesC->get_messages_count($thread_id);
if(!empty($topic_start['changed_by']))
{
	$usr = $usersC->get_user_info($topic_start['changed_by']);
	$reason = empty($topic_start['changed_for']) ? '"не указана"' : $topic_start['changed_for'];
	$changed = '<b><i>Отредактированно '.$usr['nick'].' по причине '.$reason.'</b></i>';
}
$title = ' - '.$section_name.' - '.$subsection_name.' - '.$thread_subject;
$rss_link = 'view-rss.php?section='.$section_id.'&subsection='.$subsection_id.'&newsid='.$thread_id;
require 'header.php';
$comments_on_page = $uinfo['comments_on_page'];
$pages_count = ceil(($messages_count-1)/$comments_on_page);
$pages_count>1?	$begin=$comments_on_page*($page-1):$begin = 0;
$r_count = $coreC->get_readers_count($thread_id);
$readers_count = $coreC->declOfNum($r_count, array('пользователь', 'пользователя', 'пользователей'));
$readers = 'Анонимных: '.$coreC->get_readers_count($thread_id, 1).'<br>Зарегистрированных: '.$coreC->get_readers_count($thread_id, 2).' <br>';
$rdrs_arr = $coreC->get_readers($thread_id);
for($i=0;$i<count($rdrs_arr);$i++)
{
	if($rdrs_arr[$i]['gid']==2)
		$rdrs = $rdrs.', <div class="root">'.$rdrs_arr[$i]['nick'].'</div>';
	elseif($rdrs_arr[$i]['gid']==3)
		$rdrs = $rdrs.', <div class="moder"'.$rdrs_arr[$i]['nick'].'</div>';
	else
		$rdrs = $rdrs.', <b>'.$rdrs_arr[$i]['nick'].'</b>';
}
$rdrs = substr_replace($rdrs, '', 0, 2);
$readers = $readers.$rdrs;
if($pages_count > 1)
{
	if($page>1)
	{
		$pg = $page-1;
		$pages = $pages.'<a href="message.php?newsid='.$thread_id.'&page=1" title=В Начало>←</a>&nbsp;';
		$pages = $pages.'<a href="message.php?newsid='.$thread_id.'&page='.$pg.'" title="Назад">≪</a>&nbsp;';
	}
	if($pages_count>10)
	{
		if($page<5)
			$start_page = 1;
		else
			$start_page = $page-4;
			
		if($page>$pages_count-4)
			$end_page = $pages_count;
		else
			$end_page = $page+4;
		for($p=$start_page; $p<=$end_page; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="message.php?newsid='.$thread_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	else
	{
		for($p=1; $p<=$pages_count; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="message.php?newsid='.$thread_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	if($page<$pages_count)
	{
		$pg = $page+1;
		$pages = $pages.'<a href="message.php?newsid='.$thread_id.'&page='.$pg.'" title="Вперед">≫</a>&nbsp;';
		$pages = $pages.'<a href="message.php?newsid='.$thread_id.'&page='.$pages_count.'" title="В Конец">→</a>&nbsp;';
	}
}
require 'themes/'.$theme.'/templates/message/nav_form.tpl.php';
switch($section_id)
{
	case 1:
		$news_approve_moder_name = $usersC->get_user_info($topic_start['approved_by']);
		if($coreC->validate_boolean($topic_start['approved']))
			$approve = 'Подтверждено: '.$news_approve_moder_name['nick'].'(<a href="profile.php?user='.$news_approve_moder_name['nick'].'">*</a>) ('.$topic_start['approve_timest'].')';
		if(!empty($topic_start['prooflink']))
			$prooflink='>>> <a href="'.$topic_start['prooflink'].'">Подробнее</a>';
		require 'themes/'.$theme.'/templates/message/news.tpl.php';
		break;
	case 2:
		$news_approve_moder_name = $usersC->get_user_info($topic_start['approved_by']);
		if($coreC->validate_boolean($topic_start['approved']))
			$approve = 'Подтверждено: '.$news_approve_moder_name['nick'].'(<a href="profile.php?user='.$news_approve_moder_name['nick'].'">*</a>) ('.$topic_start['approve_timest'].')';
		require 'themes/'.$theme.'/templates/message/article.tpl.php';
		break;
	case 3:
		$news_approve_moder_name = $usersC->get_user_info($topic_start['approved_by']);
		if($coreC->validate_boolean($topic_start['approved']))
			$approve = 'Подтверждено: '.$news_approve_moder_name['nick'].'(<a href="profile.php?user='.$news_approve_moder_name['nick'].'">*</a>) ('.$topic_start['approve_timest'].')';
		$gallery_file_name = $topic_start['file'];
		$gallery_file_extension = $topic_start['extension'];
		$gallery_image_size = $topic_start['image_size'];
		$gallery_file_size = $topic_start['file_size'];
		
		require 'themes/'.$theme.'/templates/message/gallery.tpl.php';
		break;
	case 4:
		require 'themes/'.$theme.'/templates/message/forum.tpl.php';
		break;
	default:
		require 'themes/'.$theme.'/templates/message/forum.tpl.php';
		break;
}
require 'themes/'.$theme.'/templates/message/nav.tpl.php';
if($messages_count>1)
{
	$cmnt = $messagesC->get_comments_on_page($thread_id, $begin, $comments_on_page);
	for($i=0; $i<count($cmnt); $i++)
	{
		$message_id = $cmnt[$i]['id'];
		$message_set_filter_link = 'set-filter.php?id='.$message_id;
		$msg_resp = $messagesC->get_message($cmnt[$i]['referer']);
		$message_resp_title = $msg_resp['subject'];
		$message_resp_timestamp = $coreC->to_local_time_zone($msg_resp['timest']);
		$msg_resp_autor = $usersC->get_user_info($msg_resp['uid']);
		$message_resp_user = $msg_resp_autor['nick'];
		$message_resp_link = 'message.php?newsid='.$thread_id.'#'.$cmnt[$i]['referer'];
		$message_edit_link = 'edit-message.php?id='.$message_id;
		$message_subject = $cmnt[$i]['subject'];
		$user_filter = users::get_filter($_SESSION['user_id']);
		$user_filter_arr = filters::parse_filter_string($user_filter);
		if($messagesC->is_filtered($cmnt[$i]['id'], $user_filter_arr))
			$message_comment = 'Это сообщение отфильтрованно в соответствии с вашими настройками фильтрации. <br>Для того чтобы прочесть это сообщение отключите фильтр в профиле или нажмите <a href="show-message.php?id='.$message_id.'">сюда</a>.';
		else
			$message_comment = $cmnt[$i]['comment'];
		$msg_autor = $usersC->get_user_info($cmnt[$i]['uid']);
		$coreC->validate_boolean($msg_autor['banned']) ? $message_autor = '<s>'.$msg_autor['nick'].'</s>' :$message_autor = $msg_autor['nick'];
		$message_autor_profile_link = '/profile.php?user='.$msg_autor['nick'];
		if(!$coreC->validate_boolean($cmnt[$i]['show_ua']))
			$message_useragent = '';
		else
			$message_useragent = $cmnt[$i]['useragent'];
		$message_timestamp = $coreC->to_local_time_zone($cmnt[$i]['timest']);
		$message_add_answer_link = 'comment.php?answerto='.$thread_id.'&cid='.$message_id;
		$message_avatar = empty($msg_autor['photo'])? 'themes/'.$theme.'/empty.gif' : 'images/avatars/'.$msg_autor['photo'];
		if(!empty($cmnt[$i]['changed_by']))
		{
			$usr = $usersC->get_user_info($cmnt[$i]['changed_by']);
			$reason = empty($cmnt[$i]['changed_for']) ? '"не указана"' : $cmnt[$i]['changed_for'];
			$changed = '<b><i>Отредактированно '.$usr['nick'].' по причине '.$reason.'</b></i>';
		}
		else
			$changed='';
		require 'themes/'.$theme.'/templates/message/message.tpl.php';
	}
	require 'themes/'.$theme.'/templates/message/nav.tpl.php';
}
require 'themes/'.$theme.'/templates/message/thread_readers.tpl.php';
require 'footer.php';
?>