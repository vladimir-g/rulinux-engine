<?php
$user = $_SESSION['user_name'];
if($_SESSION['user_id']==1)
{
	$filename = 'blocks/'.$directory.'/templates/login.tpl.php';
	$file = fopen($filename, "r") or die("Can't open file!");
	$boxlet_content = fread($file, filesize($filename));
	fclose($file); 
}
else
{
	if($_SESSION['user_admin']==1)
		$privilegies = 'Администратор';
	else if($_SESSION['user_moder']==1)
		$privilegies = 'Модератор';
	else
		$privilegies = 'Пользователь';
	$filename = 'blocks/'.$directory.'/templates/user_info.tpl.php';
	$file = fopen($filename, "r") or die("Can't open file!");
	$boxlet_content = fread($file, filesize($filename));
	fclose($file); 
	$boxlet_content = str_replace('[privilegies]', $privilegies, $boxlet_content);
}
$boxlet_content = str_replace('[user]', $user, $boxlet_content);
$boxlet_content = str_replace('[title]', $name, $boxlet_content);
?>