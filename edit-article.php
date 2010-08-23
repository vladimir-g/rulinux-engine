<? 
include('incs/db.inc.php');
require_once('classes/art.class.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('incs/header.inc.php');
$header=pages::get_templates('header');
$footer=pages::get_templates('footer');
if($_POST['action']==edit)
{
	$aid = $_POST['id'];
	$tid = base::eread('comments', 'tid', null, 'cid', $cid);
	$subj = $_POST['title'];
	$mes = $_POST['message'];
	$mes = str_replace('\\\\', '\\', $mes);
	$mes = htmlspecialchars($mes, ENT_NOQUOTES);
	$dir = base::eread('articles', 'imgdir', null, 'id', $aid);
	$mes = base::artTexParser($mes, $dir);
	if(artClass::editArticle($aid, $subj, $mes))
	{
		echo "<fieldset><p align=center>Статья успешно исправлена. <br>Через несколько секунд вы будете перенаправлены в исправленную статью. <br>Если вы не хотите ждать нажмите <a href=/view-article.php?aid=$aid>сюда</a></fieldset>";
		echo "<header><meta http-equiv='Refresh' content='0; url=view-article.php?aid=$aid' /></header>";
	}
}
else
{
	$user_name = empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name'];
	$uid = base::eread('users', 'id', null, 'nick', $user_name);
	$id = $_GET['id'];
	$subject = base::eread('articles', 'title', null, 'id', $id);
	$comment = base::eread('articles', 'body', null, 'id', $id);
	$comment = base::artHtml2TeX($comment);
	if (isset($_GET['id'])=='') 
	{
		echo "Неизвестный параметр id";
	}
	else
	{
		if (ereg("[0-9]", $_GET['id'])) 
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
			$username = base::eread('users', 'nick', null, 'id', base::eread('article', 'uid', null, 'id', $id));
			if(!$_SESSION['user_admin'] && $username != $_SESSION['user_name'])
			{
				echo "Вы не можете редактировать это сообщение";
				exit();
			}
			?>
			<h1>Редактировать</h1>
			<form method=POST action="edit-article.php">
			<input type="hidden" name="action" value="edit">
			<input type="hidden" name="id" value="<?=$id?>">
			Заглавие:
			<input type=text name="title" size=40  value="<?=$subject?>"><br>
			Сообщение:<br>
			<font size=2>(В режиме <i>Tex paragraphs</i> игнорируются переносы 
			строк.<br> Пустая строка (два раза Enter) начинает новый абзац)</font><br>
			<textarea name="message" cols=70 rows=20><?=$comment?></textarea><br>
			<br><br>
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
			if(!mysql_close($dbcnx))
			{
				echo("Не удалось завершить соединение");
			}
		}
		else
		{
			echo "Параметр id должен содержать только цифры";
		}
	}
}
include_once('incs/bottom.inc.php');
?>	
