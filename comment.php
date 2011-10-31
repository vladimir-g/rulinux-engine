<?
require 'classes/core.php';
$message_id = (int)$_GET['cid'];
$thread_id = (int)$_GET['answerto'];
$title = ' - Добавить коментарий';
$rss_link='rss';
if(!empty($_POST['sbm']))
{
	if (empty($_POST['subject']))
	{
		require 'header.php';
		$legend = 'Не заполнено поле \'Тема\'.';
		$text = 'Не заполнено поле \'Тема\'';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		define(SUBJ_SET, false);
		require 'footer.php';
		exit();
	}
	else
		define(SUBJ_SET, true);
		
	if (empty($_POST['comment']))
	{
		require 'header.php';
		$legend = 'Не заполнено поле \'Ваш коментарий\'';
		$text = 'Не заполнено поле \'Ваш коментарий\'';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		define(COMM_SET, false);
		require 'footer.php';
		exit();
	}
	else
		define(COMM_SET, true);

	if (!empty($_POST['user_field']))
	{
		require 'header.php';
		$legend = 'Заполнено поле не требующее заполнения.';
		$text = 'Заполнено поле не требующее заполнения';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		define(SUBJ_SET, false);
		require 'footer.php';
		exit();
	}
	else
		define(SUBJ_SET, true);
}
ini_set(’magic_quotes_runtime’, 0);
ini_set(’magic_quotes_sybase’, 0);
if (SUBJ_SET && COMM_SET && $_POST['sbm'] == 'Поместить')
{
	if (($usersC->user_banned($_SESSION['user_id']) == 0) || $_SESSION['user_id'] == '')
	{
		$md5 = md5(rand().gmdate("Y-m-d H:i:s"));
		$filters_count = $filtersC->get_filters_count();
		if ($_SESSION['user_id'] == 1 || $usersC->get_captcha_level($_SESSION['user_id']) > -1)
		{
			if(isset($_SESSION['captcha_keystring'] ) && $_SESSION['captcha_keystring']  == $_POST['keystring'])
			{
				$messagesC->add_message($_POST['subject'], $_POST['comment'], $thread_id, $message_id, $md5);
				$mess_arr = $threadsC->get_msg_number($thread_id, $md5);
				$message_number = $mess_arr['message_number'];
				$msg_id = $mess_arr['msg_id'];
				$page = ceil($message_number/$uinfo['comments_on_page']);
				for($i=1; $i<=$filters_count; $i++)
				{
					if(!empty($_POST['filter_'.$i]))
						$str = $str.$i.':1;';
					else
						$str = $str.$i.':0;';
				}
				$str = $filtersC->set_auto_filter($msg_id, $str);
				$val = $messagesC->set_filter($msg_id, $str);
				require 'header.php';
				$legend = 'Коментарий успешно добавлен';
				$text = 'Коментарий успешно добавлен<br>Через три секунды вы будете перенаправлены в тред.<br>Если вы не хотите ждать, нажмите <a href="thread_'.$thread_id.'_page_'.$page.'#msg'.$msg_id.'">сюда</a>.';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'thread_'.$thread_id.'_page_'.$page.'#msg'.$msg_id.'">');
			}
			else 
			{
				require 'header.php';
				$legend = 'Неверно введен ответ с картинки';
				$text = 'Неверно введен ответ с картинки';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				require 'footer.php';
				exit();
			}
		}
		else
		{
			$messagesC->add_message($_POST['subject'], $_POST['comment'], $thread_id, $message_id, $md5);
			$mess_arr = $threadsC->get_msg_number($thread_id, $md5);
			$message_number = $mess_arr['message_number'];
			$msg_id = $mess_arr['msg_id'];
			$page = ceil($message_number/$uinfo['comments_on_page']);
			for($i=1; $i<=$filters_count; $i++)
			{
				if(!empty($_POST['filter_'.$i]))
					$str = $str.$i.':1;';
				else
					$str = $str.$i.':0;';
			}
			$str = $filtersC->set_auto_filter($msg_id, $str);
			$val = $messagesC->set_filter($msg_id, $str);
			require 'header.php';
			$legend = 'Коментарий успешно добавлен';
			$text = 'Коментарий успешно добавлен<br>Через три секунды вы будете перенаправлены в тред.<br>Если вы не хотите ждать, нажмите <a href="thread_'.$thread_id.'_page_'.$page.'#msg'.$msg_id.'">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'thread_'.$thread_id.'_page_'.$page.'#msg'.$msg_id.'">');  
		}
	}
	else
	{
		require 'header.php';
		$legend = 'Вы не можете отправить сообщение';
		$text = 'Постинг из-под данного аккаунта был заблокирован модератором';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
require 'header.php';
if (SUBJ_SET && COMM_SET && $_POST['sbm'] == 'Предпросмотр')
{
	$subj = $message_subject = $_POST['subject'];
	$comment = $_POST['comment'];
	$message_comment = str_to_html($_POST['comment']);
	$message_timestamp = gmdate("Y-m-d H:i:s");
	$msg_autor = $usersC->get_user_info($_SESSION['user_id']);
	$message_autor = $msg_autor['nick'];
	$message_autor_profile_link = '/profile.php?user='.$message_autor;
	$message_useragent = $_SERVER['HTTP_USER_AGENT'];
	$user_field = $_POST['user_field'];
}
else
{
	$msg = $messagesC->get_message($message_id);
	$message_subject = $msg['subject'];
	$subj = 'Re:'.$message_subject;
	$subj = preg_replace('/(Re\:){1,}/', 'Re:', $subj);
	$message_comment = $msg['comment'];
	$message_timestamp = $coreC->to_local_time_zone($msg['timest']);
	$msg_autor = $usersC->get_user_info($msg['uid']);
	$coreC->validate_boolean($msg_autor['banned']) ? $message_autor = '<s>'.$msg_autor['nick'].'</s>' : $message_autor = $msg_autor['nick'];
	$message_autor_profile_link = 'user_'.$message_autor;
	$message_useragent = $msg['useragent'];
}



if ($_SESSION['user_id'] == 1 || $usersC->get_captcha_level($_SESSION['user_id']) > -1)
	$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha" alt="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
else
	$captcha = '';
$form_link = 'comment_into_'.$thread_id.'_on_'.$message_id;
require 'themes/'.$theme.'/templates/comment/comment_top.tpl.php';

$filters_arr = $filtersC->get_filters();
for($i=0; $i<count($filters_arr);$i++)
{
	$filterN = $filters_arr[$i]['id'];
	$filter_name = $filters_arr[$i]['name'];
	if (SUBJ_SET && COMM_SET && $_POST['sbm'] == 'Предпросмотр')
	{
		$fil = $i+1;
		if(!empty($_POST['filter_'.$fil]))
			$checked_filter = 'checked';
		else
			$checked_filter = '';
	}
	else
		$checked_filter = '';
	require 'themes/'.$theme.'/templates/comment/comment_middle.tpl.php';
}
require 'themes/'.$theme.'/templates/comment/comment_bottom.tpl.php';

require 'footer.php';
?>


