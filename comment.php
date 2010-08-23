<?
$pagename=$_SERVER['SCRIPT_NAME'];
$pagename=str_replace(getcwd(), '', $pagename);
if (!empty($_GET))
{
	$_VALS=array_flip($_GET);
	$pagename=$pagename.'?';
	$c=0;
	foreach ($_GET as $_VAR)
	{
		$pagename=$pagename.$_VALS[$_VAR].'='.$_VAR;
		$c++;
		if ($c<=(sizeof($_GET)-1))
		$pagename=$pagename.'&';
	}
}
$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$pid=intval($_GET['id']);
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();

$cid = $_GET['cid'];
$tid = $_GET['answerto'];
$fid = $_GET['fid'];
if($fid != 0)
{
	$closed = base::eread('threads', 'closed', '', 'tid', $tid);
	if($closed)
	{
		$content['title'] .= 'Тред закрыт для постинга';
		require_once('incs/header.inc.php');
		echo '<b>Тред закрыт для постинга</b>';
		require_once('incs/bottom.inc.php');
		exit();
	}
}
if(isset($_POST['subject']))
{
	if (($_POST['subject'] != ''))
		define(SUBJ_SET, true);
	else
	{
		define(SUBJ_SET, false);
		$content['title'] .= 'Поле тема пустое';
		require_once('incs/header.inc.php');
		echo '<!--content section begin-->';
		echo 'Не был передан параметр \'Тема\'';
		echo '<!--content section end-->';
		require_once('incs/bottom.inc.php');
		exit();
	}
	if ($_POST['comment'] != '')
		define(COMM_SET, true);
	else
	{
		define(COMM_SET, false);
		$content['title'] .= 'Пустое поле коментарий';
		require_once('incs/header.inc.php');
		echo '<!--content section begin-->';
		echo 'Пустой комментарий';
		echo '<!--content section end-->';
		require_once('incs/bottom.inc.php');
		exit();
	}
}
ini_set(’magic_quotes_runtime’, 0);
ini_set(’magic_quotes_sybase’, 0);
$raw_comment = $_POST['comment'];
$_POST['comment'] = str_replace('\\\\', '\\', $_POST['comment']);
$subject = $_POST['subject'];
$filthy_lang = base::findFilthyLang($_POST['comment']);
$add['comment'] = base::strToTeX($_POST['comment']);
if (SUBJ_SET && COMM_SET && $_POST['sbm'] == 'Поместить')
{
	if ((base::get_field_by_id('users', 'status',  $_SESSION['user_login'], 'id') == 1) || $_SESSION['user_login'] == '')
	{
		//if($filthy_lang)
		//{
		//	$add['comment'] = $add['comment'].' Матерная лексика';
		//}
		if ($_SESSION['user_login'] == '' || base::get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1)
		{
			if(isset($_SESSION['captcha_keystring'] ) && $_SESSION['captcha_keystring']  == $_POST['keystring'])
			{
				//if (base::eread('comments', 'deleted', '', 'cid', $cid) < 1)
				//{
					$res = users::add_comment($tid, base::eread('users', 'id', null, 'nick', $_SESSION['user_name']), $cid, $fid, $subject, $add['comment'], $raw_comment, $filthy_lang);
					//header('location: message.php?newsid='.$tid.'#'.$res);
					header('location: jump.php?newsid='.$tid.'&forum='.$fid.'&to='.$res);
				//}
				//else echo '<fieldset style="border: 1px dashed #ffffff">Комментарий, на который вы отвечаете был удален</fieldset>';
			}
			else 
			{
				$content['title'] .= 'Неверный ответ';
				require_once('incs/header.inc.php');
				echo '<!--content section begin-->';
				echo '<fieldset style="border: 1px dashed #ffffff">Неверно введен ответ с картинки</fieldset>';
				echo '<!--content section end-->';
				require_once('incs/bottom.inc.php');
				exit();
			}
		}
		else
		{
			//if (base::eread('comments', 'deleted', '', 'cid', $cid) < 1)
			//{
				$res = users::add_comment($tid, base::eread('users', 'id', null, 'nick', $_SESSION['user_name']), $cid, $fid, $subject,$add['comment'], $raw_comment, $filthy_lang);
				header('location: jump.php?newsid='.$tid.'&forum='.$fid.'&to='.$res);
			//}
			//else echo '<fieldset style="border: 1px dashed #ffffff">Комментарий, на который вы отвечаете был удален</fieldset>';
		}
	}
	else
	{
		$content['title'] .= 'Вы заблокированы';
		require_once('incs/header.inc.php');
		echo '<!--content section begin-->';
		echo '<fieldset style="border: 1px dashed #ffffff">Ошибка! Постинг из-под данного аккаунта был заблокирован модератором</fieldset>';
		echo '<!--content section end-->';
		require_once('incs/bottom.inc.php');
		exit();
	}
}
$content['title'] .= 'Добавить комментарий';
require_once('incs/header.inc.php');
echo '<!--content section begin-->';
if ((int)$tid >= 0)
{
	if ($cid == 0)
	{
		$tmp['subj'] = base::get_field_by_id('news', 'title', $tid, 'id');
		$tmp['uid'] = base::get_field_by_id('news', 'by', $tid, 'id');
		$tmp['timestamp'] = base::get_field_by_id('news', 'timestamp', $tid, 'id');
		$tmp['comment'] = base::get_field_by_id('news', 'text', $tid, 'id');
		$tmp['comment'] = str_replace('[q]', '<p style="font-style: italic">> ', $tmp['comment']);
		$tmp['comment'] = str_replace('[/q]', '</p>', $tmp['comment']);
		$user = $tmp['uid'];
	}
	else
	{
		$tmp['subj'] = base::get_field_by_id('comments', 'subject', $cid, 'cid');
		$tmp['uid'] = base::get_field_by_id('comments', 'uid', $cid, 'cid');
		$tmp['timestamp'] = base::get_field_by_id('comments', 'timestamp', $cid, 'cid');
		$tmp['comment'] = base::get_field_by_id('comments', 'comment', $cid, 'cid');
		$tmp['comment'] = str_replace('[q]', '<p style="font-style: italic">> ', $tmp['comment']);
		$tmp['comment'] = str_replace('[/q]', '</p>', $tmp['comment']);
		$user = $tmp['uid'] <= 0 ? 'anonymous' : base::get_field_by_id('users', 'nick', $tmp['uid']);
	}
	if (isset($_POST['subject']) && isset($_POST['comment']))
	{
		if (SUBJ_SET && COMM_SET)
		{
			$tmp = array();
			$tmp['subj'] = $_POST['subject'];
			$tmp['comment'] = base::strToTeX($_POST['comment']);
			$user = $_SESSION['user_name'] == '' ? 'anonymous' : $_SESSION['user_name'];
			$tmp['timestamp'] = date('Y-m-d H:i:s');
		}
	}
	if ($user == $_SESSION['user_name'])
		$answerto = '[<a href="#">Удалить</a>]'.$answerto;
	//echo '<div id="pcmm" class="comment-body">';
	if ($cid == 0)
		echo '<div class="comment-head">'.$answerto.'</div>';
	//echo $add['comment'];
	//echo $tmp['comment'];
	echo '<div id="'.$comment_id.'" class="msg">
	<div class="title">[<a href="#'.$comment_id.'">#</a>]</div>
	<h2 style="padding:0px; margin:0px;">'.$tmp['subj'].'</h2><br>
	'.$tmp['comment'].'<br>
	<div style="float:left;"><span style="font-style:italic">'.$user.' (<a href="profile.php?user='.$user.'">*</a>)&nbsp;('.base::timeToSTDate($tmp['timestamp']).')</span>'.$additional.'
	</div><br><br>
	</div>';
	/*echo '</p>
	<span style="font-style:italic">'.$user.' (<a href="profile.php?user='.$user.'">*</a>) ('.$tmp['timestamp'].')</span>
	<br>
	</div><br>';*/
	if ($tmp['subj'] != '0' && (int)$tmp['subj'] > -1)
		$subj = $tmp['subj'];
	else
	{
		if (isset($_GET['news']))
		{
			$subj = base::get_field_by_id('news', 'title', $tid, 'id');
		}
	}
	$comment = $_POST['comment'];
}
else
{
	echo '<div class="error" align="center" style="text-align:center; height:100%;vertical-align:middle">Ошибка!<br /> 
	Указан некорректный номер комментария
	</div>';
}
//if ($cid == 0)
	$subj = 'Re: '.$subj;
$subj = preg_replace('/(Re\:\s){1,}/', 'Re: ', $subj);
?>
<form action="comment.php?answerto=<?=$tid?>&cid=<?=$cid?>&fid=<?=$fid?>" method="post">
   <span style="font-size:10pt">Тема:</span><br>
   <input type="text" name="subject" style="width:60%" value="<?=$subj;?>"><br>
   <span style="font-size:10pt">Ваш комментарий:</span><br>
   <textarea name="comment" id="comment" style="height:55%;width:60%;" rows="15"><?=$comment?></textarea><br>
<? if ($_SESSION['user_login'] == '' || base::get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1): ?>
<img src="ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>
Введите символы либо ответ (если на картинке задача):
<br><input type="text" name="keystring"><br>
<? endif; ?>
	<input type="submit" value="Поместить" name="sbm">
   <input type="submit" value="Предпросмотр" name="sbm">
</form>
<?
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>
