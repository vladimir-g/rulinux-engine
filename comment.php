<?
require 'classes/core.php';
if (empty($_GET['cid']) || empty($_GET['answerto']))
{
	header("Status: 404 Not Found");
	die('Message doesn\'t exist');
}
$message_id = (int)$_GET['cid'];
$thread_id = (int)$_GET['answerto'];
$title = ' - Добавить комментарий';
$rss_link = 'rss';
$errors = array();
$is_preview = false;

$subj = $comment = $user_field = '';

if(!empty($_POST['sbm']))
{
	if (empty($_POST['subject']))
		$errors['subject'] = 'Не заполнено поле "Тема"';
	else
		$subj = $_POST['subject'];

	if (empty($_POST['comment']))
		$errors['comment'] = 'Не заполнено поле "Ваш комментарий"';
	else
		$comment = $_POST['comment'];

	if (!empty($_POST['user_field']))
	{
		$errors['user_field'] = 'Заполнено поле не требующее заполнения';
		$user_field = $_POST['user_field'];
	}
	
	if (empty($_SESSION['user_id']) || ($usersC->user_banned($_SESSION['user_id']) != 0))
		$errors['banned'] = 'Постинг из-под данного аккаунта был заблокирован модератором';

	if ($_POST['sbm'] == 'Поместить')
	{
		if (!isset($_POST['keystring']))
			$_POST['keystring'] = null;
		if (!$captchaC->check($_POST['keystring']))
			$errors['captcha'] = 'Неверно введен ответ с картинки';
		$captchaC->reset();
		if (empty($errors))
		{
			$md5 = md5(rand().gmdate("Y-m-d H:i:s"));
			$filters_count = $filtersC->get_filters_count();
			$messagesC->add_message($_POST['subject'], $_POST['comment'], $thread_id, $message_id, $md5);
			$mess_arr = $threadsC->get_msg_number($thread_id, $md5);
			$message_number = $mess_arr['message_number'];
			$msg_id = $mess_arr['msg_id'];
			$page = ceil($message_number/$uinfo['comments_on_page']);
			for($i = 1; $i <= $filters_count; $i++)
			{
				if(!empty($_POST['filter_'.$i]))
					$str = $str.$i.':1;';
				else
					$str = $str.$i.':0;';
			}
			$str = $filtersC->set_auto_filter($msg_id, $str);
			$val = $messagesC->set_filter($msg_id, $str);
			require 'header.php';
			$legend = 'Комментарий успешно добавлен';
			$text = 'Комментарий успешно добавлен<br>Через три секунды вы будете перенаправлены в тред.<br>Если вы не хотите ждать, нажмите <a href="thread_'.$thread_id.'_comment_'.$msg_id.'#msg'.$msg_id.'">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=/thread_'.$thread_id.'_comment_'.$msg_id.'#msg'.$msg_id.'">');
		}
	}
	elseif ($_POST['sbm'] == 'Предпросмотр' && empty($errors))
	{
		$is_preview = true;
		$message_subject = $subj;
		$message_comment = str_to_html($comment);
		$message_timestamp = gmdate("Y-m-d H:i:s");
		$msg_autor = $usersC->get_user_info($_SESSION['user_id']);
		$message_autor = $msg_autor['nick'];
		$message_autor_profile_link = '/profile.php?user='.$message_autor;
		$message_useragent = $_SERVER['HTTP_USER_AGENT'];
	}
	if (!empty($errors))
	{
		$errors['msg'] = 'Сообщение не было отправлено, проверьте правильность заполнения формы';
	}
	$subj = $coreC->html_escape($subj);
}

if (!$is_preview)
{
	/* GET request or non-preview POST with errors */
	$msg = $messagesC->get_message($message_id);
	$message_subject = $msg['subject'];
	if (empty($subj) && empty($errors['subject']))
	{
		$subj = 'Re:'.$message_subject;
		$subj = preg_replace('/(Re\:){1,}/', 'Re:', $subj);
	}
	$message_comment = $msg['comment'];
	$message_timestamp = $coreC->to_local_time_zone($msg['timest']);
	$msg_autor = $usersC->get_user_info($msg['uid']);
	$coreC->validate_boolean($msg_autor['banned']) ? $message_autor = '<s>'.$msg_autor['nick'].'</s>' : $message_autor = $msg_autor['nick'];
	$message_autor_profile_link = 'user_'.$message_autor;
	$message_useragent = $msg['useragent'];
}

/* Captcha */
if ($_SESSION['user_id'] == 1 || $usersC->get_captcha_level($_SESSION['user_id']) > -1)
	$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha" alt="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
else
	$captcha = '';

$form_link = '/comment_into_'.$thread_id.'_on_'.$message_id;
$filters_arr = $filtersC->get_filters();

require 'header.php';
require 'themes/'.$theme.'/templates/comment/comment_top.tpl.php';

for($i=0; $i<count($filters_arr);$i++)
{
	$filterN = $filters_arr[$i]['id'];
	$filter_name = $filters_arr[$i]['name'];

        /* Set checked filters */
        $fil = $i+1;
        if(!empty($_POST['filter_'.$fil]))
          $checked_filter = 'checked';
        else
          $checked_filter = '';

	require 'themes/'.$theme.'/templates/comment/comment_middle.tpl.php';
}
require 'themes/'.$theme.'/templates/comment/comment_bottom.tpl.php';
require 'footer.php';
?>


