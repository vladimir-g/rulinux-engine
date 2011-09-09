<?php
require_once 'TIniFileEx.class.php';
require_once "../classes/base/base_interface.php";
require_once "../classes/config.class.php";
require_once 'install.class.php';

if(install::is_installed())
{
	echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Первичная инициализация уже была проведена</head></html>';
	exit;
}

if(empty($_POST['sbm']))
{
	require_once 'templates/install_top.tpl.php';
	$modules = install::get_db_modules();
	for($i=0; $i<count($modules); $i++)
	{
		$module = $modules[$i]['name'];
		require 'templates/install_middle.tpl.php';
	}
	require_once 'templates/install_bottom.tpl.php';
}
else
{
	if(empty($_POST['login']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указан логин СУБД</head></html>';
		exit;
	}
	if(empty($_POST['password']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указан пароль СУБД</head></html>';
		exit;
	}
	if(empty($_POST['host']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указан хост СУБД</head></html>';
		exit;
	}
	if(empty($_POST['db_port']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указан порт СУБД</head></html>';
		exit;
	}
	if(empty($_POST['db_name']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указано имя БД</head></html>';
		exit;
	}
	if(empty($_POST['pass_phrase']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указана парольная фраза</head></html>';
		exit;
	}
	if(empty($_POST['admin_login']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указан логин администратора</head></html>';
		exit;
	}
	if(empty($_POST['admin_pass']))
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не указан пароль администратора</head></html>';
		exit;
	}
	$db_set = install::set_db_settings($_POST['db_module'], $_POST['login'], $_POST['password'], $_POST['host'], $_POST['db_port'],  $_POST['db_name'], $_POST['charset']);
	if($db_set<0)
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не удалось выставить настройки БД</head></html>';
		exit;
	}
	$modules = install::get_db_modules();
	for($i=0; $i<count($modules); $i++)
	{
		if($_POST['db_module'] == $modules[$i]['name'])
		{
			$sql_file = $modules[$i]['sql'];
			break;
		}
	}
	$sql = install::create_data($_POST['binarys'], $sql_file);
	if($sql<0)
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не удалось создать или заполнить таблицы в БД</head></html>';
		exit;
	}
	$adm = install::create_root($_POST['admin_login'], $_POST['admin_pass']);
	if($adm<0)
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не удалось создать учетную запись администратора</head></html>';
		exit;
	}
	$dir = install::create_directories();
	if($dir<0)
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не удалось создать директории необходимые для сайта</head></html>';
		exit;
	}
	$set = install::set_settings($_POST['title'], $_POST['pass_phrase']);
	if($set<0)
	{
		echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">Не удалось внести изменения в настройки сайта, вы можете сделать это позднее в разделе администратора</head></html>';
		exit;
	}
	install::finish_installation();
	die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].'">');  
	
}
?>