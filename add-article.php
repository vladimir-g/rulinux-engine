<? 
include('incs/db.inc.php');
require_once('classes/art.class.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');

$baseC = new base();
$pagesC = new pages();
$usersC = new users();
$artC = new artClass();

if($_POST['action']==addArticle)
{
	$timestamp = $_POST['timestamp'];
	$uid = $_POST['uid'];
	$fid = $_POST['fid'];
	$subj = $_POST['title'];
	$message = $_POST['message'];
	if(empty($subj))
	{
		$content['title'] .= 'Пустой заголовок';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		echo "<fieldset><p align=center>Пустой заголовок. По этой причине статья не может быть добавлена. <br>Через несколько секунд вы будете перенаправлены в форму добавления статьи. <br>Если вы не хотите ждать нажмите <a href=/add-article.php?group=$fid.php>сюда</a></fieldset>";
		echo "<header><meta http-equiv='Refresh' content='0; url=add-article.php?group=$fid' /></header>";
		exit();
	}
	if(empty($message))
	{
		$content['title'] .= 'Пустое сообщение';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		echo "<fieldset><p align=center>'Пустое сообщение. По этой причине статья не может быть добавлена. <br>Через несколько секунд вы будете перенаправлены в форму добавления статьи. <br>Если вы не хотите ждать нажмите <a href=/add-article.php?group=$fid.php>сюда</a></fieldset>";
		echo "<header><meta http-equiv='Refresh' content='0; url=add-article.php?group=$fid' /></header>";
		exit();
	}
	$message = str_replace('\\\\', '\\', $message);
	//$message = htmlspecialchars($message, ENT_NOQUOTES);
	$message = $baseC->strToTex($message);
	if($artC->addArticle($fid, $subj, $message, $timestamp, $uid))
	{
		$thrid = $baseC->other_query("SELECT tid FROM threads WHERE fid='$fid' AND uid='$uid' AND ip='$ip' AND posting_date = '$timestamp';");
		$tid = $thrid[0][0];
		setcookie('dir','');
		$content['title'] .= 'Статья добавлена';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		echo "<fieldset><p align=center>Статья успешно добавлена. Теперь она ожидает подтверждения модератором. <br>Через несколько секунд вы будете перенаправлены в список неподтвержденного. <br>Если вы не хотите ждать нажмите <a href=/view-all.php>сюда</a></fieldset>";
		echo "<header><meta http-equiv='Refresh' content='0; url=view-all.php' /></header>";
	}
	else
	{
		$content['title'] .= 'Не могу добавить статью';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		echo "<fieldset><p align=center>Статья не может быть добавлена.</p></fieldset>";
	}
}
/*elseif($_POST['action']==addImage)
{
	if ($_FILES['image']['size'] > 0)
	{
		$fid = $_POST['fid'];
		$uid = $_POST['uid'];
		$blacklist = array(".php", ".phtml", ".php3", ".php4");
		foreach ($blacklist as $item) 
		{
			if(preg_match("/$item\$/i", $_FILES['image']['name'])) 
			{
				$error = 'photo_error';
			}
		}
		$uploaddir = 'artimages/';
		if(!file_exists($uploaddir))
		{
			mkdir($uploaddir);
		}
		if(isset($_COOKIE['dir']))
		{
			$dir = $_COOKIE['dir'];
		}
		else
		{
			$dir = md5(time()).'/';
			if(!file_exists($uploaddir.$dir))
			{
				mkdir($uploaddir.$dir);
			}
		}
		setcookie('dir', $dir);
		preg_match('/(^.+)(\.jp[e]?g|\.png|\.gif)$/', basename($_FILES['image']['name']), $ext);
		$filename = md5(time()).'_'.$ext[1];
		$ext[2] = substr(basename($_FILES['image']['name']), strlen(basename($_FILES['image']['name']))-4, 4);
		$ext[2] = str_replace('.', '', $ext[2]);
		$uploadfile = $uploaddir.$dir.$filename.'.'.$ext[2];
		if(file_exists('./'.$uploadfile))
		{
			echo 'Файл с таким именем уже существует. Он будет заменен загружаемым файлом';
			unlink('./'.$uploadfile);
		}
		$imageinfo = getimagesize($_FILES['image']['tmp_name']);
		if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg'  && $imageinfo['mime'] != 'image/png') 
		{
			$error = 'photo_error';
		}
		if (empty($error))
		{
			move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile);
			echo "<header><meta http-equiv='Refresh' content='0; url=add-article.php?group=$fid' /></header>";
		}
		
	}
}*/
elseif($_GET[group]==0)
{
	$content['title'] .= 'Статьи - Добавить статью';
	require_once('incs/header.inc.php');
	$header=$pagesC->get_templates('header');
	$footer=$pagesC->get_templates('footer');
	?>
	<form method=GET action="/add-article.php">
	<br>
	Выберите раздел: 
	<select name=group>
	<?
	$sel = $baseC->other_query("SELECT forum_id, name FROM forums ORDER BY sort");
	foreach($sel as $val)
	{
		if($val['forum_id']==$_GET['group'])
		{
			?>
			<option value=<? print $val['forum_id']?> selected><? print $val['name']?></option>
			<?
		}
		else
		{
			?>
			<option value=<? print $val['forum_id']?>><? print $val['name']?></option>
			<?
		}
	}
	?>
	</select> 
	<input type=submit value="Выбрать">
	</form>
	<?
}
else
{
	$content['title'] .= 'Статьи - Добавить статью';
	require_once('incs/header.inc.php');
	$header=$pagesC->get_templates('header');
	$footer=$pagesC->get_templates('footer');
	$user_name = empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name'];
	$uid = $baseC->eread('users', 'id', null, 'nick', $user_name);
	$fid = $_GET['group'];
	$date = mktime(date('H')-$h_date, date('i'), 0, date('m'), date('d'), date('Y'));
	/*if($_COOKIE['dir'])
	{
		$file_list = scandir('artimages/'.$_COOKIE['dir']);
		?> 
		<fieldset>
		<legend>Добавленные файлы</legend>
		<br>
		<?
		foreach($file_list as $file)
		{
			if($file!='.' && $file!='..')
				echo '<a href=/artimages/'.$_COOKIE['dir'].$file.'>'.$file.'</a> ';
		}
		?></fieldset><?
	}*/
	?>
	<h1>Добавить статью</h1>
	Просьба ко всем, добавляющим статьи:
	<ul>
	<li><b>Для разметки текста в статьях используется T<sub>E</sub>X-like синтаксис.</b> Подробности <a href="/page.php?id=2">тут</a>
	<li><b>Пишите статьи в правильный раздел!</b> Выберете подходящий по теме вашего вопроса раздел, например
	статьи по администрированию системы нужно писать в Admin, а
	не в General и т.п.
	<li><b>Пишите осмысленный заголовок</b>. Придумайте осмысленный заголовок статье. Статьи с бессмысленными загловками как правило, не читаются и проводить поиск по ним гораздо сложнее.
	</ul>
	<!-- <h2>Добавить изображения</h2>
	<form method=POST action="/add-article.php" enctype="multipart/form-data">
	<input type="hidden" name="action" value="addImage">
	<input type="hidden" name="fid" value="<?=$fid?>">
	<input type="hidden" name="uid" value="<?=$uid?>">
	Путь к изображению: <input name='image' type='file'> <input type=submit value="Поместить">
	</form> -->
	<h2>Добавить текст</h2>
	<form method=POST action="/add-article.php">
	<input type="hidden" name="action" value="addArticle">
	<input type="hidden" name="timestamp" value="<?=$date?>">
	<input type="hidden" name="uid" value="<?=$uid?>">
	<input type="hidden" name="fid" value="<?=$fid?>">
	Заглавие:
	<input type=text name="title" size=40 value="<?=$_POST['title']?>"><br>
	Сообщение:<br>
	<font size=2>(В режиме <i>Tex paragraphs</i> игнорируются переносы 
	строк.<br> Пустая строка (два раза Enter) начинает новый абзац)</font><br>
	<textarea name="message" cols=70 rows=20><?=$_POST['message']?></textarea><br>
	<br><br>
	<? if ($_SESSION['user_login'] == '' || $baseC->get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1): ?>
	<img src="<?=$path?>ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>
	Введите символы либо ответ (если на картинке задача):
	<br><input type="text" name="keystring"><br>
	<? endif; ?>
	<br>
	<input type=submit value="Поместить">
	<div style="display:none">Пользователям браузеров без CSS: Поле для проверки, заполнять НЕ НАДО: </div><input type="text" name="user_field" style="display:none" width="0"><br>
	</form>
	<?
}
include_once('incs/bottom.inc.php');
?>	