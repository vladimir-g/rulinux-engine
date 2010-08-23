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

$content['title'] .= 'Форум - Добавить тред';
require_once('incs/header.inc.php');
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
if($_POST['action']==add)
{
	if (($baseC->get_field_by_id('users', 'status',  $_SESSION['user_login'], 'id') == 1) || $_SESSION['user_login'] == '')
	{
		$timestamp = mktime(date('H')-$h_date, date('i'), 0, date('m'), date('d'), date('Y'));//$_POST['timestamp'];
		$uid = $_POST['uid'];
		$useragent = $_POST['useragent'];
		$ip = $baseC->GetRealIp($_POST['ip']);
		$fid = $_POST['fid'];
		$subj = $_POST['title'];
		if(isset($_POST['mconf']) && $_SESSION['user_admin'] == 1)
			$mconf = (int)$_POST['mconf'];
		else
			$mconf = 0;
		if(empty($subj))
		{
			echo "<br><fieldset><p align=center>Пустой заголовок</p></fieldset>";
			include_once('incs/bottom.inc.php');
			exit();
		}
		$message = $_POST['message'];
		if(empty($message))
		{
			echo "<br><fieldset><p align=center>Пустое сообщение</p></fieldset>";
			include_once('incs/bottom.inc.php');
			exit();
		}
		$message = str_replace('\\\\', '\\', $message);
		$raw_comment = $_POST['message'];
		$filthy_lang = $baseC->findFilthyLang($message);
		$message = $baseC->strToTeX($message);
		if ($_SESSION['user_login'] == '' || $baseC->get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1)
		{
			if(isset($_SESSION['captcha_keystring'] ) && $_SESSION['captcha_keystring']  == $_POST['keystring'])
			{
				
				if($forumC->addThread($fid, $uid, $timestamp, $subj, $message, $ip, $useragent, $raw_comment, $mconf, $filthy_lang))
				{
					$thrid = $baseC->other_query("SELECT tid FROM threads WHERE fid='$fid' AND uid='$uid' AND ip='$ip' AND posting_date = '$timestamp';");
					$tid = $thrid[0][0];
					if(isset($_POST['mconf']) && $_SESSION['user_admin'] == 1)
					{
						$baseC->erewrite('settings', 'value', $fid.':'.$tid, 'last_conf', 'name');
					}
					echo "<fieldset><p align=center>Коментарий успешно добавлен. <br>Через несколько секунд вы будете перенаправлены в добавленный тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid>сюда</a></fieldset>";
					echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid' /></header>";
				}
			}
			else
			{
				$content['title'] .= 'Неверный ответ';
				require_once('incs/header.inc.php');
				echo '<!--content section begin-->';
				echo '<fieldset style="border: 1px dashed #ffffff">Неверно введен ответ с картинки<br>Через несколько секунд вы будете перенаправлены назад. <br>Если вы не хотите ждать нажмите <a href="javascript:history.back(1)">сюда</a></fieldset>';
				//echo '<header><meta http-equiv=\'Refresh\' content=\'0; url={$_SERVER[\'HTTP_REFERER\']}\' /></header>';
				echo '<!--content section end-->';
				require_once('incs/bottom.inc.php');
				exit();
			}
		}
		else
		{
			if($forumC->addThread($fid, $uid, $timestamp, $subj, $message, $ip, $useragent, $raw_comment, $mconf, $filthy_lang))
			{
				$thrid = $baseC->other_query("SELECT tid FROM threads WHERE fid='$fid' AND uid='$uid' AND ip='$ip' AND posting_date = '$timestamp';");
				$tid = $thrid[0][0];
				if(isset($_POST['mconf']) && $_SESSION['user_admin'] == 1)
				{
					$baseC->erewrite('settings', 'value', $fid.':'.$tid, 'last_conf', 'name');
				}
				echo "<fieldset><p align=center>Коментарий успешно добавлен. <br>Через несколько секунд вы будете перенаправлены в добавленный тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid>сюда</a></fieldset>";
				echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid' /></header>";
			}
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
else
{
	if (isset($_GET['fid'])=='') 
	{
		echo "Неизвестный параметр fid";
	}
	else
	{
		if (ereg("[0-9]", $_GET['fid'])) 
		{	
				$ip = getenv ("REMOTE_ADDR");
				$agent = $_SERVER['HTTP_USER_AGENT'];
				$user_name = empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name'];
				$uid = $baseC->eread('users', 'id', null, 'nick', $user_name);
				$fid = $_GET['fid'];
				//$date = mktime(date('H')-$h_date, date('i'), 0, date('m'), date('d'), date('Y'));
				if(isset($_GET['mfid']) && $_SESSION['user_admin'] == 1)
					$foradd = ' новую конференцию для модераторов';
				else
					$foradd = '';
				?> 
				<h1>Добавить тему в форум</h1>
				Просьба ко всем, добавляющим темы в форум:
				<ul>
				<li><b>Прочитайте <a href="/wiki/en/lor-faq">FAQ</a></b>! Возможно, ваш вопрос уже содержится в нашем сборнике ответов на часто задаваемые вопросы.
				<li><b>Пишите в правильный форум!</b> Выберете подходящий по теме вашего вопроса раздел форума, например
				вопросы по администрированию системы нужно задавать в Admin, а
				не в General и т.п.
				<li><b>Пишите осмысленный заголовок</b>. Придумайте осмысленный заголовок теме. Сообщения с бессмысленными загловками ("Помогите!", "Вопрос", ...), как правило, остаются без ответа.
				<!--<li>Не включайте без нужды режим преформатированного текста,
				это бивает форматирование сайта. -->
				</ul>
				<h1>Добавить<?=$foradd;?></h1>
				<form method=POST action="/add-message.php">
				<input type="hidden" name="action" value="add">
				<!-- <input type="hidden" name="timestamp" value="<?print $date?>"> -->
				<input type="hidden" name="uid" value="<?print $uid?>">
				<input type="hidden" name="useragent" value="<?print $agent?>">
				<input type="hidden" name="ip" value="<?print $ip?>">
				<input type="hidden" name="fid" value="<?print $fid?>">
				Заглавие:
				<input type=text name="title" size=40><br>
				Сообщение:<br>
				<font size=2>(В режиме <i>Tex paragraphs</i> игнорируются переносы 
				строк.<br> Пустая строка (два раза Enter) начинает новый абзац)</font><br>
				<textarea name="message" cols=70 rows=20></textarea><br>
				<!-- <select name=mode>
				<option value=ntobr  >User line break
				<option value=tex  >TeX paragraphs
				</select> -->
				<br><br>
				<? if ($_SESSION['user_login'] == '' || $baseC->get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1): ?>
				<img src="<?=$path?>ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>
				Введите символы либо ответ (если на картинке задача):
				<br><input type="text" name="keystring"><br>
				<? endif; ?>
				<? if(isset($_GET['mfid']) && $_SESSION['user_admin'] == 1): ?>
				<input type="hidden" name="mconf" value="1">
				<? endif; ?>
				<br>
				<input type=submit value="Поместить">
				<!-- <input type=submit name=preview value="Предпросмотр"> -->
				<div style="display:none">Пользователям браузеров без CSS: Поле для проверки, заполнять НЕ НАДО: </div><input type="text" name="user_field" style="display:none" width="0"><br>
				</form>
				<?
		}
		else
		{
			echo "Параметр fid должен содержать только цифры";
		}
	}
}
include_once('incs/bottom.inc.php');
?>	