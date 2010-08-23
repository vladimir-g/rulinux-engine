<? 
include('incs/db.inc.php');
require_once('classes/forum.class.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');

$baseC = new base();
$pagesC = new pages();
$usersC = new users();
$forumC = new forumClass();

$content['title'] .= 'Редактирование сообщения';
require_once('incs/header.inc.php');
$header=pages::get_templates('header');
$footer=pages::get_templates('footer');
if($_POST['action']==edit)
{
	$cid = $_POST['cid'];
	$tid = base::eread('comments', 'tid', null, 'cid', $cid);
	$subj = $_POST['title'];
	$mes = $_POST['message'];
	$mes = str_replace('\\\\', '\\', $mes);
	//$mes = htmlspecialchars($mes, ENT_NOQUOTES);
	$filthy_lang = base::findFilthyLang($mes);
	$mes = base::strToTeX($mes);
	$raw_mes = $_POST['message'];
	if(forumClass::editMessage($cid, $subj, $mes, $raw_mes, $filthy_lang))
	{
		if((int)$_SESSION['user_login'] > 0)
			$perpage = base::eread('users', 'comments_on_page', '', 'id', $_SESSION['user_login']);
		else
		{
			if(empty($_COOKIE['comments_on_page']))
				$perpage = 50;
			else
				$perpage = $_COOKIE['comments_on_page'];
		}
		$count = base::other_query("SELECT count(cid) FROM comments WHERE tid = $tid AND mconf=0 AND cid <= $cid");
		$pages = floor(($count[0][0]-2)/$perpage);
		echo "<fieldset><p align=center>Коментарий успешно исправлен. <br>Через несколько секунд вы будете перенаправлены в добавленный тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid&page=$pages#$cid>сюда</a></fieldset>";
		echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid&page=$pages#$cid' /></header>";
	}
}
else
{
	$user_name = empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name'];
	$uid = base::eread('users', 'id', null, 'nick', $user_name);
	$cid = $_GET['cid'];
	$tid = base::eread('comments', 'tid', null, 'cid', $cid);
	$fid = base::eread('threads', 'fid', null, 'tid', $tid);
	$forum = base::eread('forums', 'name', null, 'fid', $fid);
	$subject = base::eread('comments', 'subject', null, 'cid', $cid);
	$comment = base::eread('comments', 'raw_comment', null, 'cid', $cid);
	$comment = base::html2TeX($comment);
	if (isset($_GET['cid'])=='') 
	{
		echo "Неизвестный параметр cid";
	}
	else
	{
		if (ereg("[0-9]", $_GET['cid'])) 
		{	
			$dbcnx = @mysql_connect($db_host,$db_user,$db_pass);
			mysql_query('SET NAMES UTF8');
			if (!$dbcnx) 
			{
				echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
				exit();
			}
			if (!@mysql_select_db($db_name, $dbcnx)) 
			{
				echo( "<P>В настоящий момент база данных не доступна, поэтому корректное отображение страницы невозможно.</P>" );
				exit();
			}
			$closed = base::eread('threads', 'closed', null, 'tid', $tid);
			if($closed)
			{
				echo "<fieldset><p align=center>Тред закрыт для постинга. <br>Через несколько секунд вы будете перенаправлены в $forum. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
				echo "<header><meta http-equiv='Refresh' content='0; url=a href=/group.php?group=$fid' /></header>";
			}
			else
			{
				$username = base::eread('users', 'nick', null, 'id', base::eread('comments', 'uid', null, 'cid', $cid));
				if(!$_SESSION['user_admin'] && $username != $_SESSION['user_name'])
				{
					echo "Вы не можете редактировать это сообщение";
					exit();
				}
				?>
				<h1>Редактировать</h1>
				<form method=POST action="edit-message.php">
				<input type="hidden" name="action" value="edit">
				<input type="hidden" name="cid" value="<?=$cid?>">
				Заглавие:
				<input type=text name="title" size=40  value="<?=$subject?>"><br>
				Сообщение:<br>
				<font size=2>(В режиме <i>Tex paragraphs</i> игнорируются переносы 
				строк.<br> Пустая строка (два раза Enter) начинает новый абзац)</font><br>
				<textarea name="message" cols=70 rows=20><?=$comment?></textarea><br>
				<br><br>
				<!--Здесь должна быть капча-->
				<? if ($_SESSION['user_login'] == '' || base::get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1): ?>
				<img src="/ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>
				Введите символы либо ответ (если на картинке задача):
				<br><input type="text" name="keystring"><br>
				<? endif; ?>
				<br>
				<input type=submit value="Изменить">
				<!-- <input type=submit name=preview value="Предпросмотр"> -->
				</form>
				<?
			}
			if(!mysql_close($dbcnx))
			{
				echo("Не удалось завершить соединение");
			}
		}
		else
		{
			echo "Параметр cid должен содержать только цифры";
		}
	}
}
include_once('incs/bottom.inc.php');
?>	
