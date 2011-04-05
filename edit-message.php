<?php
$message_id = (int)$_GET['id'];
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.' - Редактировать сообщение';
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';

if(empty($_POST['sbm']))
{
	$msg = messages::get_message($message_id);
	if(empty($msg))
	{
		$legend = 'Произошла ошибка при выборке сообщения из базы';
		$text = 'Произошла ошибка при выборке сообщения из базы';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	$subj = $msg['subject'];
	$comment = $msg['raw_comment'];
	//$captcha
	require 'themes/'.$theme.'/templates/edit_message/edit_message.tpl.php';
}
else
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
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>