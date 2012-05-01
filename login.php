<?php
require 'classes/core.php';
$title = '';
$rss_link='rss';
if(isset($_GET['logout']))
{
// 	require 'header.php';
	$_SESSION['user_id']='';
	$_SESSION['user_admin']='';
	$_SESSION['user_moder']='';
	$_SESSION['user_name']='';
	$_SESSION['openid']=false;
	setcookie('login', '', time()-3600);
	setcookie('password', '', time()-3600);
	session_destroy();
// 	$legend = 'Вы разлогинились';
// 	$text = 'Вы разлогинились. Если у вас отключена переадресация нажмите <a href="/">сюда</a>';
// 	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].'">');
}
else
{
	if($_SESSION['user_id']!= 1)
	{
		require 'header.php';
		$legend = 'Вы уже авторизованны на сайте';
		$text = 'Вы уже авторизованны на сайте. Если вы хотите войти под другим ником, То вам необходимо <a href="logout">разлогиниться</a>';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	if($_GET['openid_mode'] == 'id_res')
	{
		$openid = new SimpleOpenID;
		$openid->SetIdentity($_GET['openid_identity']);
		$openid_validation_result = $openid->ValidateWithServer();
		if ($openid_validation_result == true)
		{
			$identity = $openid->GetIdentity();
			$identity = preg_replace('#^http://(.*)#sim', '$1', $identity);
			$identity = preg_replace('#^https://(.*)#sim', '$1', $identity);
			$identity = preg_replace('#(.*)\/$#sim', '$1', $identity);
			if($usersC->openid_exists($identity))
			{
				$authC->auth_user($identity, '', false, true);
				require 'header.php';
				$legend = 'Вы авторизованны на сайте';
				$text = 'Вы авторизованны на сайте. Если у вас отключена переадресация нажмите <a href="/">сюда</a>';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].'">');
			}
			else 
			{
				$pass_phrase = $coreC->get_settings_by_name('register_pass_phrase');
				$link = '/register.php?action=register&system=openid&openid='.$identity.'&hash='.md5($identity.$pass_phrase);
				die('<meta http-equiv="Refresh" content="0; URL='.$link.'">');
			}
		}
		else if($openid->IsError() == true)
		{
			require 'header.php';
			$error = $openid->GetError();
			$legend = 'Неудачная авторизация';
			$text = 'Неудачная авторизация. Код ошибки '. $error['code'] .'. '.$error['description'];
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
		}
		else
		{
			require 'header.php';
			$legend = 'Неудачная авторизация';
			$text = 'Неудачная авторизация. Неизвестная ошибка';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
		}
		exit();
	}
	else if ($_GET['openid_mode'] == 'cancel')
	{
		$legend = 'Неудачная авторизация';
		$text = 'Неудачная авторизация. Действие отменено пользователем';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	}
	if(!empty($_POST['login']))
	{
		if($_POST['auth_system'] == 'this')
		{
			if(isset($_POST['user']) && isset($_POST['password']))
			{
				$_POST['user'] = preg_replace('/[\'\/\*\s]/', '', $_POST['user']);
				$authC->auth_user($_POST['user'], $_POST['password'], false);
			}
			require 'header.php';
			$legend = 'Вы авторизованны на сайте';
			$text = 'Вы авторизованны на сайте. Если у вас отключена переадресация нажмите <a href="/">сюда</a>';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].'">');
		}
		else 
		{
			if ($_POST['openid_action'] == "login")
			{
				$openid = new SimpleOpenID;
				$openid->SetIdentity($_POST['openid_url']);
				$openid->SetTrustRoot('http://' . $_SERVER["HTTP_HOST"]);
				$openid->SetRequiredFields(array('email','fullname'));
				$openid->SetOptionalFields(array('dob','gender','postcode','country','language','timezone'));
				if ($openid->GetOpenIDServer())
				{
					$openid->SetApprovedURL('http://' . $_SERVER["HTTP_HOST"].'/login.php');
					$openid->Redirect();
				}
				else
				{
					$error = $openid->GetError();
					echo "ERROR CODE: " . $error['code'] . "<br>";
					echo "ERROR DESCRIPTION: " . $error['description'] . "<br>";
				}
				exit;
			}
		}
		
	}
	else
	{
		require 'header.php';
		$coreC->include_theme_file($theme, 'templates/login/login.tpl.php');
		require 'footer.php';
	}
}
?>