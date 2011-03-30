<?php
$subsection_id = (int)$_GET['id'];
include 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.' - Регистрация нового пользователя';
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
include 'links.php';
include 'themes/'.$theme.'/templates/header.tpl.php';

if($_SESSION['user_id']!=1)
{
	echo '<br><fieldset><legend>Вы уже зарегистрированны</legend><p align="center">Вы уже зарегистрированны на нашем сайте.</p></fieldset>';
	include 'themes/'.$theme.'/templates/footer.tpl.php';
	exit();
}
if($_POST['first_smb'])
{
	if(isset($_SESSION['captcha_keystring'] ) && $_SESSION['captcha_keystring']  == $_POST['keystring'])
	{
		if($_POST['password_2'] != $_POST['password_1'])
		{
			echo '<br><fieldset><legend>Не совпадают пароли</legend><p align="center">Текст в поле Пароль не совпадает с текстом введенным в поле Подверждения пароля</p></fieldset>';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		if (!preg_match('/^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/', $_POST['nick']))
		{
			echo '<fieldset style="border: 1px dashed #ffffff">Ошибка регистрации!<br>Правила формирования ника:<ul><li>Только латинские буквы, цифры и символы _ и -<li>Начинается только с латинской буквы<li>Не менее 2 (двух) символов</ul> Если что-то забыл, то используемый регэксп расскажет об остальном: /^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/</fieldset>';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		if(!filter_var($_POST['e-mail'], FILTER_VALIDATE_EMAIL)) 
		{
			echo '<br><fieldset><legend>Не валидный e-mail</legend><p align="center">Указанный вами электронный адрес не прошел проверку на валидность</p></fieldset>';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		$usr_exsts = users::user_exists($_POST['nick']);
		if($usr_exsts)
		{
			echo '<br><fieldset><legend>Такой пользователь уже существует</legend><p align="center">Вы не можете зарегистрировать пользователя с таким именем, так как такой пользователь уже существует.</p></fieldset>';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		$sndmail = users::send_accept_mail($_POST['e-mail'], $_POST['nick'], $_POST['password_1']);
		if($sndmail)
		{
			echo '<br><fieldset><legend>На ваш адрес отправлено письмо для подтверждения регистрации</legend><p align="center">На ваш адрес отправлено письмо дла подтверждения регистрации. Для продолжения регистрации нажмите на ссылку указанную в письме.</p></fieldset>';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		else
		{
			echo '<br><fieldset><legend>Письмо не было отправленно</legend><p align="center">Письмо не было отправленно. Возможно это связанно с неправильными настройками сервера.</p></fieldset>';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	else 
	{
		echo '<fieldset style="border: 1px dashed #ffffff">Неверно введен ответ с картинки</fieldset>';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else if($_GET['action']=='register')
{
	$pass_phrase = core::get_settings_by_name('register_pass_phrase');
	if($_GET['hash'] != md5($_GET['login'].$_GET['password'].$pass_phrase))
	{
		echo '<br><fieldset><legend>Логин, пароль или хеш указанные в ссылке не верны.</legend><p align="center">Ваш логин и пароль указанный в ссылке не прошел проверку на соответствие с хешем. Регистрация будет превана.</p></fieldset>';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	if(!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) 
	{
		echo '<br><fieldset><legend>Не валидный e-mail</legend><p align="center">Указанный вами электронный адрес не прошел проверку на валидность</p></fieldset>';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	$time = date("Y-m-d H:i:s");
	$nick = $_GET['login'];
	$pass = $_GET['password'];
	$email = $_GET['email'];
	include 'themes/'.$theme.'/templates/register/second_page.tpl.php';
	
}
else if($_POST['action']=='second_sbm')
{
	if(!empty($_POST['im']))
	{
		if(!filter_var($_POST['im'], FILTER_VALIDATE_EMAIL)) 
		{
			echo '<br><fieldset><legend>Не валидный jabber</legend><p align="center">Указанный вами jabber адрес не прошел проверку на валидность</p></fieldset>';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	$nick = $_POST['nick'];
	$pass = $_POST['pass'];
	$name = $_POST['user_name'];
	$lastname = $_POST['user_lastname'];
	$gender = $_POST['gender'];
	$email = $_POST['user_email'];
	$show_email = filter_var($_POST['showEmail'], FILTER_VALIDATE_BOOLEAN);
	$im = $_POST['user_im'];
	$show_im = filter_var($_POST['showIM'], FILTER_VALIDATE_BOOLEAN);
	$country = $_POST['user_country'];
	$city = $_POST['user_city'];
	$additional = $_POST['user_additional'];
	$gmt = $_POST['user-gmt'];
	echo $nick.' '.$pass.' '.$name.' '.$lastname.' '.$gender.' '.$email.' '.$show_email.' '.$im.' '.$show_im.' '.$country.' '.$city.' '.$additional.' '.$gmt;
	$ret= users::add_user($nick, $pass, $name, $lastname, $gender, $email, $show_email, $im, $show_im, $country, $city,$additional, $gmt);
	if($ret<0)
	{
		echo '<br><fieldset><legend>Вы не были зарегистрированны</legend><p align="center">Регистрация прошла неуспешно. Возможно это связано с настройками Б.Д.</p></fieldset>';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	else
	{
		echo '<br><fieldset><legend>Регистрация прошла успешно</legend><p align="center">Вы были зарегистрированны на сайте. Теперь вы можете войти на сайт под своим именем.</p></fieldset>';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else
{
	$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
	include 'themes/'.$theme.'/templates/register/first_page.tpl.php';
}



include 'themes/'.$theme.'/templates/footer.tpl.php';
?>