<?php
require_once 'TIniFileEx.class.php';
require_once "../classes/base/base_interface.php";
require_once "../classes/config.class.php";
require_once 'install.class.php';
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
		echo 'Не указан логин СУБД';
		exit;
	}
	if(empty($_POST['password']))
	{
		echo 'Не указан пароль СУБД';
		exit;
	}
	if(empty($_POST['host']))
	{
		echo 'Не указан хост СУБД';
		exit;
	}
	if(empty($_POST['db_port']))
	{
		echo 'Не указан порт СУБД';
		exit;
	}
	if(empty($_POST['db_name']))
	{
		echo 'Не указано имя БД';
		exit;
	}
	if(empty($_POST['pass_phrase']))
	{
		echo 'Не указана парольная фраза';
		exit;
	}
	$db_set = install::set_db_settings($_POST['db_module'], $_POST['login'], $_POST['password'], $_POST['host'], $_POST['db_port'],  $_POST['db_name'], $_POST['charset']);
	if($db_set<0)
	{
		echo 'Не удалось выставить настройки БД';
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
		echo 'Не удалось создать или заполнить таблицы в БД';
		exit;
	}
	$dir = install::create_directories();
	if($dir<0)
	{
		echo 'Не удалось создать директории необходимые для сайта';
		exit;
	}
	$set = install::set_settings($_POST['title'], $_POST['pass_phrase']);
	if($set<0)
	{
		echo 'Не удалось внести изменения в настройки сайта, вы можете сдетать это позднее в разделе администратора';
		exit;
	}
	install::finish_installation();
	die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].'">');  
	
}
?>