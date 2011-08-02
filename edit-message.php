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
	$msg = messages::get_message($message_id);
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
		if ($_SESSION['user_id'] == 1 || users::get_captcha_level($_SESSION['user_id']) > -1)
			$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
		else
			$captcha = '';
		$sect = sections::get_section_by_tid($msg['tid']);
		$where_arr = array(array("key"=>'cid', "value"=>$msg['id'], "oper"=>'='));
		$sel = base::select('threads', '', '*', $where_arr, 'AND');
		if(!empty($sel))
		{
			if($sect['id']==1)
			{
				require 'themes/'.$theme.'/templates/edit_message/news/top.tpl.php';
				$subsect = sections::get_subsections($sect['id']);
				$thr = threads::get_thread_info($msg['tid']);
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
	if(users::get_captcha_level($_SESSION['user_id']) > -1)
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
			messages::edit_message($message_id, $_POST['subject'], $_POST['comment'], $_POST['reason']);
		else
			messages::edit_news($message_id, $_POST['subject'], $_POST['comment'], $_POST['reason'], $_POST['tid'], $_POST['link'], $_POST['subsection_id']);
		$str = filters::set_auto_filter($message_id);
		$val = messages::set_filter($message_id, $str);
		$param_arr = array($message_id);
		$thr = base::query('SELECT tid FROM comments WHERE id = \'::0::\'','assoc_array', $param_arr);
		$param_arr = array($thr[0]['tid']);
		$sel = base::query('SELECT id FROM comments WHERE tid = \'::0::\' AND id>(SELECT min(id) FROM comments WHERE tid=\'::0::\') ORDER BY id','assoc_array', $param_arr);
		for($i=0;$i<count($sel);$i++)
		{
			if($sel[$i]['id'] == $message_id)
				$message_number = $i+1;
		}
		$page = ceil($message_number/$uinfo['comments_on_page']);
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'message.php?newsid='.$thr[0]['tid'].'&page='.$page.'#'.$message_id.'">');  
	}
}
?>