<?php
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$title = $site_name;
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register.php">Регистрация</a> <a href="login.php">Вход</a>' : '<a href="login.php?logout">Выход</а>';
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
if(isset($_GET['logout']))
{
	$_SESSION['user_id']='';
	$_SESSION['user_admin']='';
	$_SESSION['user_moder']='';
	$_SESSION['user_name']='';
	setcookie('login', '', time()-3600);
	setcookie('password', '', time()-3600);
	session_destroy();
	die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].'">');  
}
else
{
	if($_SESSION['user_id']!= 1)
	{
		$legend = 'Вы уже авторизованны на сайте';
		$text = 'Вы уже авторизованны на сайте. Если вы хотите войти под другим ником, То вам необходимо <a href="login.php?logout">разлогиниться</a>';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
	if(!empty($_POST['login']))
	{
		if(isset($_POST['user']) && isset($_POST['password']))
		{
			$_POST['user'] = preg_replace('/[\'\/\*\s]/', '', $_POST['user']);
			auth_user($_POST['user'], $_POST['password'], false);
		}
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].'">');  
	}
	else
		include 'themes/'.$theme.'/templates/login/form.tpl.php';
}
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>