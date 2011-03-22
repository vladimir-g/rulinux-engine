<?
$message_id = (int)$_GET['cid'];
$thread_id = (int)$_GET['answerto'];
include 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = 'Добавить коментарий';
include 'links.php';
include 'themes/'.$theme.'/templates/header.tpl.php';

if(!empty($_POST['sbm']))
{
	if (empty($_POST['subject']))
	{
		echo '<fieldset style="border: 1px dashed #ffffff">Не заполнено поле \'Тема\'</fieldset>';
		define(SUBJ_SET, false);
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	else
		define(SUBJ_SET, true);
		
	if (empty($_POST['comment']))
	{
		echo '<fieldset style="border: 1px dashed #ffffff">Не заполнено поле \'Ваш коментарий\'</fieldset>';
		define(COMM_SET, false);
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	else
		define(COMM_SET, true);

}
ini_set(’magic_quotes_runtime’, 0);
ini_set(’magic_quotes_sybase’, 0);
if (SUBJ_SET && COMM_SET && $_POST['sbm'] == 'Поместить')
{
	if ((users::user_banned($_SESSION['user_id']) == 0) || $_SESSION['user_id'] == '')
	{
		if ($_SESSION['user_id'] == '' || users::get_captcha_level($_SESSION['user_id']) > -1)
		{
			if(isset($_SESSION['captcha_keystring'] ) && $_SESSION['captcha_keystring']  == $_POST['keystring'])
			{
				messages::add_message($_POST['subject'], $_POST['comment'], $thread_id, $message_id);
				die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'message.php?newsid='.$thread_id.'&page=1">');  
				//header('location: message.php?newsid='.$thread_id.'&page=1');
			}
			else 
			{
				echo '<fieldset style="border: 1px dashed #ffffff">Неверно введен ответ с картинки</fieldset>';
				include 'themes/'.$theme.'/templates/footer.tpl.php';
				exit();
			}
		}
		else
		{
			$md5 = md5(rand().date("Y-m-d H:i:s"));
			messages::add_message($_POST['subject'], $_POST['comment'], $thread_id, $message_id, $md5);
			$param_arr = array($thread_id);
			$sel = base::query('SELECT id,md5 FROM comments WHERE tid = \'::0::\' AND id>(SELECT min(id) FROM comments WHERE tid=\'::0::\')','assoc_array', $param_arr);
			for($i=0;$i<count($sel);$i++)
			{
				if($sel[$i]['md5']==$md5)
					$message_number = $i+1;
			}
			$page = ceil($message_number/$uinfo['comments_on_page']);
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'message.php?newsid='.$thread_id.'&page='.$page.'">');  
			//header('location: message.php?newsid='.$thread_id.'&page='.$page);
		}
	}
	else
	{
		echo '<fieldset style="border: 1px dashed #ffffff">Ошибка! Постинг из-под данного аккаунта был заблокирован модератором</fieldset>';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}

if (SUBJ_SET && COMM_SET && $_POST['sbm'] == 'Предпросмотр')
{
	$subj = $message_subject = $_POST['subject'];
	$comment = $message_comment = $_POST['comment'];
	$message_timestamp = date("Y-m-d H:i:s");
	$msg_autor = users::get_user_info($_SESSION['user_id']);
	$message_autor = $msg_autor['nick'];
	$message_autor_profile_link = '/profile.php?user='.$message_autor;
	$message_useragent = $_SERVER['HTTP_USER_AGENT'];
}
else
{
	$msg = messages::get_message($message_id);
	$message_subject = $msg['subject'];
	$subj = 'Re:'.$message_subject;
	$subj = preg_replace('/(Re\:){1,}/', 'Re:', $subj);
	$message_comment = $msg['comment'];
	$message_timestamp = $msg['timest'];
	$msg_autor = users::get_user_info($msg['uid']);
	$message_autor = $msg_autor['nick'];
	$message_autor_profile_link = '/profile.php?user='.$message_autor;
	$message_useragent = $msg['useragent'];
}



if ($_SESSION['user_id'] == '' || users::get_captcha_level($_SESSION['user_id']) > -1)
$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
else
$captcha = '';
include 'themes/'.$theme.'/templates/comment/comment.tpl.php';


include 'themes/'.$theme.'/templates/footer.tpl.php';
?>


