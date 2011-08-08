<?php
require 'classes/core.php';
$message_id = (int)$_GET['id'];
$title = ' - Редактировать сообщение';
$rss_link='view-rss.php';
if(empty($_POST['sbm']))
{
	if($_SESSION['user_id'] == 1)
	{
		require 'header.php';
		$legend = 'Действие запрещено';
		$text = 'Вы не можете редактировать это сообщение';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	$msg = $messagesC->get_message($message_id);
	if(empty($msg))
	{
		require 'header.php';
		$legend = 'Произошла ошибка при выборке сообщения из базы';
		$text = 'Произошла ошибка при выборке сообщения из базы';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	if($msg['uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
	{
		require 'header.php';
		$subj = $msg['subject'];
		$comment = $msg['raw_comment'];
		$reason = $msg['changed_for'];
		if ($_SESSION['user_id'] == 1 || $usersC->get_captcha_level($_SESSION['user_id']) > -1)
			$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
		else
			$captcha = '';
		$sect = $sectionsC->get_section_by_tid($msg['tid']);
		$sel = $threadsC->get_tid_by_cid($msg['id']);
		if(!empty($sel))
		{
			if($sect['id']==1)
			{
				require 'themes/'.$theme.'/templates/edit_message/news/top.tpl.php';
				$subsect = $sectionsC->get_subsections($sect['id']);
				$thr = $threadsC->get_thread_info($msg['tid']);
				$tid = $msg['tid'];
				$section = $thr['section'];
				$link = $thr['prooflink'];
				for($i=0; $i<count($subsect); $i++)
				{
					$subsection_id = $subsect[$i]['id'];
					$subsection_name = $subsect[$i]['name'];
					if($thr['subsection']-1==$i)
						$selected = 'selected';
					else
						$selected = '';
					require 'themes/'.$theme.'/templates/edit_message/news/middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/edit_message/news/bottom.tpl.php';
			}
			else
				require 'themes/'.$theme.'/templates/edit_message/message/edit_message.tpl.php';
		}
		else
			require 'themes/'.$theme.'/templates/edit_message/message/edit_message.tpl.php';
		require 'footer.php';
	}
}
else
{
	if($_SESSION['user_id'] == 1)
	{
		require 'header.php';
		$legend = 'Действие запрещено';
		$text = 'Вы не можете редактировать это сообщение';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	if($usersC->get_captcha_level($_SESSION['user_id']) > -1)
	{
		if(empty($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring'])
		{
			require 'header.php';
			$legend = 'Неверно введен ответ с картинки';
			$text = 'Неверно введен ответ с картинки';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	if($_POST['msg_uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
	{
		if(empty($_POST['section']))
			$messagesC->edit_message($message_id, $_POST['subject'], $_POST['comment'], $_POST['reason']);
		else
			$messagesC->edit_news($message_id, $_POST['subject'], $_POST['comment'], $_POST['reason'], $_POST['tid'], $_POST['link'], $_POST['subsection_id']);
		$str = $filtersC->set_auto_filter($message_id);
		$val = $messagesC->set_filter($message_id, $str);
		$mess_arr = $threadsC->get_msg_number_by_cid($message_id);
		$message_number = $mess_arr[0];
		$page = ceil($message_number/$uinfo['comments_on_page']);
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'message.php?newsid='.$mess_arr[1].'&page='.$page.'#'.$message_id.'">');  
	}
}
?>