<?
$content['title'] = 'Добавить материал';
include('incs/db.inc.php');
require_once('classes/config.class.php');
include_once('classes/antispam.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');
require_once('classes/messages.class.php');

$baseC = new base();
$pagesC = new pages();
$usersC = new users();
$newsC = new news();
$faqC = new faq();

require_once('incs/header.inc.php');
echo '<!--content section begin-->';
if (($_SESSION['user_login'] == '' || $baseC->get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1) && isset($_POST['keystring'])){
	if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring']  != $_POST['keystring']){
		echo '<fieldset style="border: 1px dashed #ffffff">Неверно введен ответ с картинки</fieldset>';
		echo '<!--content section end-->';
		require_once('incs/bottom.inc.php');
		$_SESSION['captcha_keystring'] == '';
		exit();
	}
}
$_SESSION['captcha_keystring'] == '';
?>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<h1><?=$content['title']?></h1>
<? if(!isset($_GET['type'])): ?>
<form action="add-content.php" method="get">
	<br><br>Тип контента:
	<select name="type" style="width:80%">
		<option value="1">Новость</option>
		<option value="2">Скриншот</option>
		<option value="3">Голосование</option>
		<option value="4">Ссылка</option>
	</select><br>
	<input type="submit" value="Далее >>">
</form>
<? else: ?>
<? switch($_GET['type']): ?>
<? case 1: ?>
<h2>Добавить новость</h2>
<?
$aspm = new antispam;
$_POST['news_text'] = $aspm->go($_POST['news_text']);
$_POST['news_title'] = $aspm->process_text($_POST['news_title']);
$_POST['news_link'] = $aspm->process_text($_POST['news_link']);
if($_POST['news_text'] === false || $_POST['news_title'] === false || $_POST['news_link'] === false){
	echo $error = 'Сообщение классифицировано  как спам. Пожалуйста, обратитесь к администрации ресурса.';
	break;
}
foreach($_POST as $var_name => $var_value)
	$_POST[$var_name] = strip_tags($var_value);
if (isset($_POST['submit_form'])){
	foreach($_POST as $var_name => $var_value){
		if (strlen(str_replace(' ', '', $var_value)) < 3 && $var_name != 'cat_name')
			$error = 'Внимание! Заполнены не все поля'.$var_name;
	}

	if (empty($error)){
		$textarea = $_POST['news_text'];
		//$_POST['news_text'] = stripslashes($_POST['news_text']);
		//$_POST['news_text'] = htmlspecialchars($_POST['news_text'], ENT_NOQUOTES);
		$_POST['news_text'] = $baseC->strToTeX($_POST['news_text']);
		if ($_POST['submit_form'] == 'Предпросмотр'){
			echo '<div class=msg><div class="title">[<a href="#">#</a>]&nbsp;</div><h2 class="nt">'.$_POST['news_title'].'</h2>
			'.$_POST['news_text'].'
			<p style="font-style:italic">'.(empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']).'
		(<a href="profile.php?user='.(empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']).'">*</a>) (19.02.2009 10:55:07)<br></p><br></div><br>';
		}
		else{
			$_POST['news_link'] = str_replace('javascript:', '', $_POST['news_link']);
			$_POST['news_text'] .= '<br>>>> <a href="'.$_POST['news_link'].'">Подробнее</a>';
			if($newsC->add_news($_POST['cat_name'], $_POST['news_title'], $_POST['news_text'], date('d.m.Y H:i:s'), '', 1, 0, (empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']),0) == 1)
				echo '<fieldset style="border: 1px dashed #ffffff">Новость успешно добавлена и ожидает подтверждения модератором</fieldset>';
			echo '<!--content section end-->';
			require_once('incs/bottom.inc.php');
			exit();
		}
	}
	else echo '<fieldset style="border: 1px dashed #ffffff">'.$error.'</fieldset>';
}
?>
<form action="add-content.php?type=1" method="post">
	<?
	$aspm->add_hiddens();
	?>
	<table border="0">
		<tr>
			<td style="vertical-align:top;">Заголовок:</td>
			<td><input type="text" name="news_title" value="<?=$_POST['news_title']?>" style="width:100%"></td>
		</tr>
		<tr>
			<td style="vertical-align:top;">Категория:</td>
			<td>
				<select name="cat_name" style="width:100%">
					<?
					$categories = $baseC->eread('categories', 'name', 'id');
					if (isset($_POST['submit_form']))
						echo '<option value="'.$_POST['cat_name'].'">'.$baseC->get_field_by_id('categories', 'name', $_POST['cat_name']).'</option>';
					foreach ($categories as $cat_id => $cat_name){
						if (isset($_POST['submit_form'])){
							if ($cat_id != $_POST['cat_name'])
								echo '<option value="'.$cat_id.'">'.$cat_name.'</option>';
						}
						else
							echo '<option value="'.$cat_id.'">'.$cat_name.'</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;">Источник (ссылка):</td>
			<td><input type="text" name="news_link" value="<?=$_POST['news_link']?>" style="width:100%"></td>
		</tr>
		<tr>
			<td style="vertical-align:top;">Текст новости:</td>
			<td>
				<textarea name="news_text" id="editor" rows="20" cols="80"><?=$textarea?></textarea>
			</td>
		</tr>
	</table>
<script type="text/javascript">
//CKEDITOR.replace('news_text',{
//toolbar :[
//   ['Format', 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'Image'],
//   ['Table', 'Redo', 'Undo'],
//]
//});
</script>
<? if ($_SESSION['user_login'] == ''): ?>
<img src="ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>
Введите символы либо ответ (если на картинке задача):
<br><input type="text" name="keystring"><br>
<? endif; ?>
<input type="submit" name="submit_form" value="Отправить">&nbsp;<input type="submit" name="submit_form" value="Предпросмотр">
</form>
<? break; ?>
<? case 2: ?>
<h2>Добавить скриншот</h2>
<?
foreach($_POST as $var_name => $var_value)
	$_POST[$var_name] = htmlspecialchars($var_value);
foreach($_POST as $post){
	if(preg_match('/\[url\=/', $post) && preg_match('/\</', $post)){
		$error = 'HTML-код и BB-код запрещены';
		break;
	}
}
if (isset($_POST['submit_form'])){
	foreach($_POST as $var_name => $var_value){
		if (strlen(str_replace(' ', '', $var_value)) < 3)
			$error = 'Внимание! Заполнены не все поля';
	}

	if (empty($error)){
		$blacklist = array(".php", ".phtml", ".php3", ".php4");
		foreach ($blacklist as $item) {
			if(preg_match("/$item\$/i", $_FILES['scrot_link']['name'])) {
				$error ='Неверный тип файла';
				exit;
			}
		}
		$uploaddir = 'gallery/';
		preg_match('/^.+(\.jp[e]?g|\.png|\.gif)$/', basename($_FILES['scrot_link']['name']), $ext);
		$filename = md5(time().basename($_FILES['scrot_link']['name']));
		$ext[1] = substr(basename($_FILES['scrot_link']['name']), strlen(basename($_FILES['scrot_link']['name']))-4, 4);
		$ext[1] = str_replace('.', '', $ext[1]);
		$uploadfile = $uploaddir.$filename.'.'.$ext[1];
		
		$imageinfo = getimagesize($_FILES['scrot_link']['tmp_name']);
		if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg'  && $imageinfo['mime'] != 'image/png') {
			$error = 'Неверный тип файла';
		}
		if(($_FILES['scrot_link']['size']/1000) > 700)
			$error = 'Слишком большой размер файла';
		if(($imageinfo[0] < 400 || $imageinfo[0] > 2048) || ($imageinfo[1] < 400 || $imageinfo[1] > 2048))
			$error = 'Ошибка загрузки файла';
			if (empty($error)){
				if (move_uploaded_file($_FILES['scrot_link']['tmp_name'], $uploadfile)) {
					$coeff = $imageinfo[0]/200;
					$image_width = 200;
					@$image_height = floor($imageinfo[1]/$coeff);
					switch ($imageinfo[2]) {
						case 1: $source = imagecreatefromgif($uploadfile); break;
						case 2: $source = imagecreatefromjpeg($uploadfile); break;
						case 3: $source = imagecreatefrompng($uploadfile); break;
					}
					$resource = imagecreatetruecolor($image_width, $image_height);
					imagecopyresampled($resource, $source, 0, 0, 0, 0, $image_width, $image_height, $imageinfo[0], $imageinfo[1]);
					imagePng($resource, $uploaddir.'/thumbs/'.$filename.'_small.png');
					$date = date('d.m.Y H:i:s');
					$textarea = $_POST['scrot_text'];
					$_POST['scrot_text'] = stripslashes($_POST['scrot_text']);
					$_POST['scrot_text'] = htmlspecialchars($_POST['scrot_text'], ENT_NOQUOTES);
					$_POST['scrot_text'] = $baseC->strToTeX($_POST['scrot_text']);
					$scrot = '
						<table>
							<tr>
								<td style="vertical-align:top"><a href=gallery/'.$filename.'.'.$ext[1].'><img src="gallery/thumbs/'.$filename.'_small.png"></td>
								<td style="vertical-align:top"><p>'.$_POST['scrot_text'].'</p><br>
								<span style="font-style: italic">'.$imageinfo[0].'x'.$imageinfo[1].', '.round(($_FILES['scrot_link']['size']/1000)).' Kb</span><br><br>
								>>> <a href="gallery/'.$filename.'.'.$ext[1].'">Просмотр</a>
								</td>
							<tr>
						</table>
					';
					$nid = $newsC->add_news(0, $_POST['scrot_title'], $scrot, $date, '', 3, 0, (empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']), -1);
					$newsC->add_scrot($_POST['scrot_title'], $_POST['scrot_text'], $filename, $ext[1], round(($_FILES['scrot_link']['size']/1000)), $imageinfo[0].'x'.$imageinfo[1], $date, (empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']), 0, $nid);
					echo '<fieldset style="border: 1px dashed #ffffff">Файл успешно загружен. Скриншот находится в буфере неподтвержденных.</fieldset>';
					echo '<!--content section end-->';
					require_once('incs/bottom.inc.php');
					exit();
				} else {
					echo '<fieldset style="border: 1px dashed #ffffff">Не удалось загрузить файл.</fieldset>';
					echo '<!--content section end-->';
					require_once('incs/bottom.inc.php');
					exit();
				}
			}
			else{
				echo '<fieldset style="border: 1px dashed #ffffff">'.$error.'</fieldset>';
			}
		if ($_POST['submit_form'] == 'Предпросмотр'){
			echo '<div class=msg><div class="title">[<a href="#">#</a>]&nbsp;</div><h2 class="nt">'.$_POST['news_title'].'</h2>
			'.$_POST['news_text'].'
			<p style="font-style:italic">'.(empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']).'
		(<a href="profile.php?user='.(empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']).'">*</a>) (19.02.2009 10:55:07)<br></p><br></div><br>';
		}
		else{
			/*$_POST['news_link'] = str_replace('javascript:', '', $_POST['news_link']);
			$_POST['news_text'] .= '<br><br>>>><a href="'.$_POST['news_link'].'"> Подробнее</a>';
			if($newsC->add_news($_POST['cat_name'], $_POST['news_title'], $_POST['news_text'], date('d.m.Y H:i:s'), '', 1, 0, (empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name'])) == 1)
				echo '<fieldset style="border: 1px dashed #ffffff">Новость успешно добавлена и ожидает подтверждения модератором</fieldset>';
			echo '<!--content section end-->';
			require_once('incs/bottom.inc.php');
			exit();*/
		}
	}
	else echo '<fieldset style="border: 1px dashed #ffffff">'.$error.'</fieldset>';
}
?>
<fieldset style="border: 1px dashed #ffffff">
Требования: <ul>
<li>Ширина x Высота: от 400x400 до 2048x2048 пикселей
<li>Тип: jpeg, gif, png
<li>Размер не более 700 Kb
</ul>
</fieldset>
<form action="add-content.php?type=2" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td style="vertical-align:top;">Заголовок:</td>
			<td><input type="text" name="scrot_title" value="<?=$_POST['scrot_title']?>" style="width:100%"></td>
		</tr>
		<tr>
			<td style="vertical-align:top;">Адрес файла:</td>
			<td><input type="file" name="scrot_link" value="<?=$_POST['scrot_link']?>" style="width:100%"></td>
		</tr>
		<tr>
			<td style="vertical-align:top;">Описание:</td>
			<td>
				<textarea name="scrot_text" id="editor" rows="20" cols="80"><?=$_POST['scrot_text']?></textarea>
			</td>
		</tr>
	</table>
<? if ($_SESSION['user_login'] == '' || $baseC->get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1): ?>
<img src="ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>
Введите символы либо ответ (если на картинке задача):
<br><input type="text" name="keystring"><br>
<? endif; ?>
<input type="submit" name="submit_form" value="Отправить">&nbsp;<input type="submit" name="submit_form" value="Предпросмотр">
</form>
<? break; ?>
<? case 3: ?>
<h2>Добавить голосование</h2>
<?
if (isset($_POST['question']) && !empty($_POST['question'])){
	foreach($_POST as $key => $val){
		$_POST[$key] = trim($val);
		$_POST[$key] = htmlspecialchars($val);
	}
	if (strlen($_POST['question']) < 3)
		$errMsg = '<li> Вопрос не задан или задан некорректно';
	if (empty($_POST['v0']))
		$errMsg .= '<li> Пожалуйста, добавьте варианты начиная с №1';;
	if (empty($errMsg)){
		$vars = array();
		foreach ($_POST as $key => $val){
			if (preg_match('/^v[0-9]{1,2}$/', $key) && !empty($val))
				$vars[] = $val;
		}
		$voteId = $usersC->add_vote($_POST['question'], $vars, (empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name']), (int)$_POST['multichoice']);
		$baseC->other_query('INSERT INTO `[prefix]news` (`isVote`) VALUES('.$voteId.')', 'no');
	}
	else{
		echo '<fieldset style="border: 1px dashed #ffffff"><ul>'.$errMsg.'</ul></fieldset><br>';
	}
}
?>
<form action="add-content.php?type=3" method="post">
	Вопрос:&nbsp;<input type="text" name="question" value="<?=$_POST['question']?>" style="width:250px">
	Вариантов:<select name="varcount" onchange="submit()">
<?
switch($_POST['varcount']){
		case 4:
		echo '<option value="4">4</option>
		<option value="2">2</option>
		<option value="8">8</option>
		<option value="16">16</option>';
		break;
		case 8:
		echo '<option value="8">8</option>
		<option value="2">2</option>
		<option value="4">4</option>
		<option value="16">16</option>';
		break;
		case 16:
		echo '<option value="16">16</option>
		<option value="2">2</option>
		<option value="4">4</option>
		<option value="8">8</option>';
		break;
		default:
		echo '<option value="2">2</option>
		<option value="4">4</option>
		<option value="8">8</option>
		<option value="16">16</option>';
		break;
}
$mchoice = isset($_POST['multichoice']) ? ' checked' : '';
?>
	</select>
	<input type="checkbox" id="multichoice" name="multichoice" value="1" <?=$mchoice?>><label for="multichoice">Мультивыбор</label>
	<hr>
	<ol>
<?
for ($i = 0; $i < ($_POST['varcount'] == 0 ? 2 : $_POST['varcount']); $i++)
	echo '<li><input type="text" name="v'.$i.'" value="'.$_POST['v'.$i].'">';
?>
	</ol>
<? if ($_SESSION['user_login'] == '' || $baseC->get_field_by_id('users', 'captcha', $_SESSION['user_login']) > -1): ?>
<img src="ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>
Введите символы либо ответ (если на картинке задача):
<br><input type="text" name="keystring"><br>
<? endif; ?>
<input type="submit" name="submit_form" value="Отправить">
</form>
<? break; ?>
<? case 4: ?>
<h2>Добавить ссылку</h2>
<? break; ?>
<? endswitch; ?>
<? endif; ?>
<?
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>