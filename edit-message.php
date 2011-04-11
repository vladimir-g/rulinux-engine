<?php
require 'classes/core.php';
$message_id = (int)$_GET['id'];
$title = ' - Редактировать сообщение';
include 'header.php';
if(empty($_POST['sbm']))
{
	if($_SESSION['user_id'] == 1)
	{
		$legend = 'Действие запрещено';
		$text = 'Вы не можете редактировать это сообщение';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	$msg = messages::get_message($message_id);
	if(empty($msg))
	{
		$legend = 'Произошла ошибка при выборке сообщения из базы';
		$text = 'Произошла ошибка при выборке сообщения из базы';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	if($msg['uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
	{
		$subj = $msg['subject'];
		$comment = $msg['raw_comment'];
		if ($_SESSION['user_id'] == 1 || users::get_captcha_level($_SESSION['user_id']) > -1)
			$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
		else
			$captcha = '';
		require 'themes/'.$theme.'/templates/edit_message/edit_message.tpl.php';
	}
}
else
{
	if($_SESSION['user_id'] == 1)
	{
		$legend = 'Действие запрещено';
		$text = 'Вы не можете редактировать это сообщение';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	if(users::get_captcha_level($_SESSION['user_id']) > -1)
	{
		if(empty($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring'])
		{
			$legend = 'Неверно введен ответ с картинки';
			$text = 'Неверно введен ответ с картинки';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	if($_POST['msg_uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
	{
		messages::edit_message($message_id, $_POST['subject'], $_POST['comment'], $_POST['reason']);
		$param_arr = array($message_id);
		$thr = base::query('SELECT tid FROM comments WHERE id = \'::0::\'','assoc_array', $param_arr);
		$param_arr = array($thr[0]['tid']);
		$sel = base::query('SELECT id FROM comments WHERE tid = \'::0::\' AND id>(SELECT min(id) FROM comments WHERE tid=\'::0::\')','assoc_array', $param_arr);
		for($i=0;$i<count($sel);$i++)
		{
			if($sel[$i]['md5']==$md5)
				$message_number = $i+1;
		}
		$page = ceil($message_number/$uinfo['comments_on_page']);
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'message.php?newsid='.$thr[0]['tid'].'&page='.$page.'">');  
	}
}
require 'footer.php';
?>