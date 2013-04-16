<?php
require 'classes/core.php';
$title = ' - Регистрация нового пользователя';
$rss_link = 'rss';
if ($_SESSION['user_id'] != 1)
{
	require 'header.php';
	$legend = 'Вы уже зарегистрированны';
	$text = 'Вы уже зарегистрированны на нашем сайте.';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
	exit();
}
/* First form submit */
if ($_POST['first_smb'])
{
	$errors = array();
	$coreC->set_missing_array_keys($_POST, array('nick', 'password_1', 'password_2', 'e-mail', 'keystring'));
	if (!isset($_POST['keystring']))
		$_POST['keystring'] = null;
	if (!$captchaC->check($_POST['keystring']))
		$errors[] = 'Неверно введен ответ с картинки';
	$captchaC->reset();
	if($_POST['password_2'] != $_POST['password_1'])
		$errors[] = 'Текст в поле Пароль не совпадает с текстом введенным в поле Подверждения пароля';
	if (!preg_match('/^([a-zA-Z][a-zA-Z0-9\_\-\/\.]*){2,}$/', $_POST['nick']))
		$errors[] = 'Выбран неправильный ник. Правила формирования ника:<ul><li>Только латинские буквы, цифры и символы _ - : / .</li><li>Начинается только с латинской буквы<li>Не менее 2 (двух) символов</li></ul> Если что-то забыл, то используемый регэксп расскажет об остальном: /^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/';
	if(!filter_var($_POST['e-mail'], FILTER_VALIDATE_EMAIL)) 
		$errors[] = 'Указанный вами электронный адрес не прошел проверку на валидность';

	$usr_exsts = $usersC->user_exists($_POST['nick']);
	if ($usr_exsts)
		$errors[] = 'Вы не можете зарегистрировать пользователя с таким именем, так как такой пользователь уже существует.';


	if (!empty($errors)) 
	{
		/* We have errors */
		$legend = 'Ошибка';
		$text = '<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
	}
	else {
		/* Data is valid, send mail */
		$sndmail = $usersC->send_accept_mail($_POST['e-mail'], $_POST['nick'], $_POST['password_1']);
		if ($sndmail)
		{
			$legend = 'На ваш адрес отправлено письмо для подтверждения регистрации';
			$text = 'На ваш адрес отправлено письмо дла подтверждения регистрации. Для продолжения регистрации нажмите на ссылку указанную в письме.';
		}
		else
		{
			$legend = 'Письмо не было отправлено';
			$text = 'Письмо не было отправлено. Возможно это связано с неправильными настройками сервера.';
		}
	}
	require 'header.php';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
}
/* Process registration link from mail */
else if (isset($_GET['action']) && $_GET['action'] == 'register')
{
	$errors = array();
	$pass_phrase = $coreC->get_settings_by_name('register_pass_phrase');
	$coreC->set_missing_array_keys($_GET, array('login', 'password', 'email', 'hash'));
	if($_GET['system']!="openid")
	{
		if ($_GET['hash'] != md5($_GET['login'].$_GET['password'].$pass_phrase))
			$errors[] = 'Ваш логин и пароль указанный в ссылке не прошел проверку на соответствие с хешем. Регистрация будет превана.';
		if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL))
			$errors[] = 'Указанный вами электронный адрес не прошел проверку на валидность';

		require 'header.php';
		if (!empty($errors))
		{
			/* Errors */
			$legend = 'Ошибка';
			$text = '<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		}
		else 
		{
			/* Valid data */
			$time = gmdate("Y-m-d H:i:s");
			$nick = $_GET['login'];
			$pass = $_GET['password'];
			$email = $_GET['email'];
			require 'themes/'.$theme.'/templates/register/second_page.tpl.php';
		}
		require 'footer.php';
	}
	else 
	{
		if ($_GET['hash'] != md5($_GET['openid'].$pass_phrase))
			$errors[] = 'Ваш OpenID указанный в ссылке не прошел проверку на соответствие с хешем. Регистрация будет превана.';
		require 'header.php';
		if (!empty($errors))
		{
			/* Errors */
			$legend = 'Ошибка';
			$text = '<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		}
		else 
		{
			/* Valid data */
			$time = gmdate("Y-m-d H:i:s");
			$openid = $_GET['openid'];
			require 'themes/'.$theme.'/templates/register/second_page_openid.tpl.php';
		}
		require 'footer.php';
	}
	
}
/* Final registration step */
else if ($_POST['action'] == 'second_sbm')
{
	require 'header.php';
	$errors = array();
	$coreC->set_missing_array_keys($_POST, array('nick', 'pass', 'user_name', 'user_lastname', 'gender',
						     'user_im', 'user_email', 'showEmail', 'showIM', 'user_country',
						     'user_city', 'user_additional', 'user-gmt'));
	if (!empty($_POST['user_im']))
	{
		if (!filter_var($_POST['user_im'], FILTER_VALIDATE_EMAIL)) 
		{
			$errors[] = 'Указанный вами jabber адрес не прошел проверку на валидность';
		}
	}
	if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) 
		$errors[] = 'Указанный вами электронный адрес не прошел проверку на валидность';
	if (!preg_match('/^([a-zA-Z][a-zA-Z0-9\_\-\/\.]*){2,}$/', $_POST['nick']))
		$errors[] = 'Выбран неправильный ник. Правила формирования ника:<ul><li>Только латинские буквы, цифры и символы _ и -</li><li>Начинается только с латинской буквы<li>Не менее 2 (двух) символов</li></ul> Если что-то забыл, то используемый регэксп расскажет об остальном: /^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/';


	if (!empty($errors))
	{

		$legend = 'Ошибка';
		$text = '<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
	}
	else
	{
		$nick = $_POST['nick'];
		$pass = $_POST['pass'];
		$openid = $_POST['openid'];
		$name = $_POST['user_name'];
		$lastname = $_POST['user_lastname'];
		$gender = $_POST['gender'];
		$email = $_POST['user_email'];
		$show_email = $coreC->validate_boolean(filter_var($_POST['showEmail'], FILTER_VALIDATE_BOOLEAN));
		$im = $_POST['user_im'];
		$show_im = $coreC->validate_boolean(filter_var($_POST['showIM'], FILTER_VALIDATE_BOOLEAN));
		$country = $_POST['user_country'];
		$city = $_POST['user_city'];
		$additional = $_POST['user_additional'];
		$gmt = $_POST['user-gmt'];
		$ret= $usersC->add_user($nick, $pass, $name, $lastname, $gender, $email, $show_email, $im, $show_im, $country, $city,$additional, $gmt, $openid);
		if ($ret < 0)
		{
			$legend = 'Вы не были зарегистрированны';
			switch ($ret)
			{
			case -2:
				$text = 'Вы не можете зарегистрировать пользователя с таким именем или OpenID, так как такой пользователь уже существует';
				break;
			default:				
				$text = 'Произошла ошибка при обращении к базе данных';
			}
		}
		else
		{
			$legend = 'Регистрация прошла успешно';
			$text = 'Вы были зарегистрированны на сайте. Теперь вы можете войти на сайт под своим именем.';
		}
	}
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
}
/* Show first form */
else
{
	require 'header.php';
	$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha" alt="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
	require 'themes/'.$theme.'/templates/register/first_page.tpl.php';
	require 'footer.php';
}
?>
