<?
$pagename=$_SERVER['SCRIPT_NAME'];
$pagename=str_replace(getcwd(), '', $pagename);
if (!empty($_GET)){
	$_VALS=array_flip($_GET);
	$pagename=$pagename.'?';
	$c=0;
	foreach ($_GET as $_VAR){
		$pagename=$pagename.$_VALS[$_VAR].'='.$_VAR;
		$c++;
		if ($c<=(sizeof($_GET)-1))
			$pagename=$pagename.'&';
	}
}
$scriptname = $_SERVER['SCRIPT_NAME'];
$scriptname = str_replace(getcwd(), '', $scriptname);
$pid = intval($_GET['id']);
$content['title'] = 'Регистрация';
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');
require_once('classes/messages.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();

require_once('incs/header.inc.php');
echo '<!--content section begin-->';
?>
<h1><?=$content['title']?></h1>
<?
if(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] == $_POST['keystring']){
	if ($_POST['login_reg'] == '')
		$errMsg .= '<li>Login';
	if ($_POST['pass_reg'] == '')
		$errMsg .= '<li>пароль';
	if ($_POST['pass_reg_1'] == '')
		$errMsg .= '<li>подтверждение пароля';
	if ($_POST['email_reg'] == '')
		$errMsg .= '<li>E-mail';
	if($errMsg != ''){
		echo '<fieldset style="border: 1px dashed #ffffff">Ошибка регистрации! Вы не заполнили обязательные поля:<ul>'.$errMsg.'</ul></fieldset>';
	}
	else{
		if ($_POST['pass_reg'] == $_POST['pass_reg_1']){
			if (!preg_match('/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/', $_POST['email_reg']))
				echo '<fieldset style="border: 1px dashed #ffffff">Ошибка регистрации! Такой e-mail существовать не может. Если это ошибка сервера - предложите лучшее регулярное выражение в Jabber: temy4@jabbus.org</fieldset>';
			else{
				if($baseC->eread('users', 'email', '', 'email', $_POST['email_reg']) != $_POST['email_reg']){
					if (preg_match('/^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/', $_POST['login_reg'])){
						if($usersC->add_user($_POST['login_reg'], $_POST['pass_reg'], '', $_POST['email_reg'], '', '', '', 1, '') == -2){
							echo '<fieldset style="border: 1px dashed #ffffff">Ошибка регистрации! Такой пользователь уже существует</fieldset>';
						}
						else{
							echo '<fieldset style="border: 1px dashed #ffffff">Вы были успешно зарегистрированы!</fieldset>';
							define('FORM_NEEDED', false);
						}
					}
					else{
						echo '<fieldset style="border: 1px dashed #ffffff">
						Ошибка регистрации!<br>
						Правила формирования ника:
						<ul>
						<li>Только латинские буквы, цифры и символы _ и -
						<li>Начинается только с латинской буквы
						<li>Не менее 2 (двух) символов
						</ul>
						Если что-то забыл, то используемый регэксп расскажет об остальном:
						/^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/
						</fieldset>';
					}
				}
				else
					echo '<fieldset style="border: 1px dashed #ffffff">Ошибка регистрации! Такой адрес электронной почты уже существует</fieldset>';
			}
		}
		else
			echo '<fieldset style="border: 1px dashed #ffffff">Ошибка регистрации! Пароли не совпадают</fieldset>';
	}
	if ($_SESSION['captcha_keystring'] != $_POST['keystring'])
		echo '<fieldset style="border: 1px dashed #ffffff">Неправильно введены символы с картинки</fieldset>';
}
?>
<? if(FORM_NEEDED): ?>
<form action="" method="POST">
<table cellspacing="3">
	<tr>
		<td><strong>Login:</strong></td>
		<td><input type="text" name="login_reg" value="<?=$_POST['login_reg']?>"></td>
	<tr>
	<tr>
		<td><strong>Пароль:</strong></td>
		<td><input type="password" name="pass_reg" value="<?=$_POST['pass_reg']?>"></td>
	<tr>
	<tr>
		<td><strong>Пароль (подтверждение):</strong></td>
		<td><input type="password" name="pass_reg_1" value="<?=$_POST['pass_reg_1']?>"></td>
	<tr>
	<tr>
		<td><strong>E-mail:</strong></td>
		<td><input type="text" name="email_reg" value="<?=$_POST['email_reg']?>"></td>
	<tr>
	<tr>
		<td style="vertical-align:top"><strong>Введите символы либо<br>ответ (если на картинке задача):</strong></td>
		<td style="text-align:center"><img src="ucaptcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" id="captcha"><br>Сюда:<br><input type="text" name="keystring" style="width:100%"></td>
	<tr>
</table>
<input type="submit" value="Регистрация">
</form>
<? endif; ?>
<?
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>