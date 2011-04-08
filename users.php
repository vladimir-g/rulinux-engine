<?php
$page = (int)$_GET['page'];
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register.php">Регистрация</a> <a href="login.php">Вход</a>' : '<a href="login.php?logout">Выход</а>';
$title = $site_name.' - Пользователи';
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
require 'themes/'.$theme.'/templates/users/top.tpl.php';

$users_on_page = 20;
$users_count = users::get_users_count();
$pages_count = ceil(($users_count)/$users_on_page);
$pages_count>1?	$begin=$users_on_page*($page-1):$begin = 0;
if($pages_count > 1)
{
	if($page>1)
	{
		$pg = $page-1;
		$pages = $pages.'<a href="users.php?page=1" title=В Начало>←</a>&nbsp;';
		$pages = $pages.'<a href="users.php?page='.$pg.'" title="Назад">≪</a>&nbsp;';
	}
	if($pages_count>10)
	{
		if($page<5)
			$start_page = 1;
		else
			$start_page = $page-4;
			
		if($page>$pages_count-4)
			$end_page = $pages_count;
		else
			$end_page = $page+4;
		for($p=$start_page; $p<=$end_page; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="users.php?page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	else
	{
		for($p=1; $p<=$pages_count; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="users.php?page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	if($page<$pages_count)
	{
		$pg = $page+1;
		$pages = $pages.'<a href="users.php?page='.$pg.'" title="Вперед">≫</a>&nbsp;';
		$pages = $pages.'<a href="users.php?page='.$pages_count.'" title="В Конец">→</a>&nbsp;';
	}
}

$users = users::get_users($begin, $users_on_page);
for($i=0; $i<count($users); $i++)
{
	$avatar = empty($users[$i]['photo'])? 'themes/'.$theme.'/empty.gif' : 'avatars/'.$users[$i]['photo'];
	$nick = $users[$i]['nick'];
	$group_info = users::get_group($users[$i]['gid']);
	$group_name = $group_info['name'];
	$name = $users[$i]['name'];
	$city = !empty($users[$i]['city'])? $users[$i]['city'] : 'город не указан';
	$country = !empty($users[$i]['country'])? $users[$i]['country'] : 'страна не указана';
	$email = core::validate_boolean($users[$i]['show_email']) ? $users[$i]['email'] : 'скрыт';
	$im = core::validate_boolean($users[$i]['show_im']) ? $users[$i]['im'] : 'скрыт';
	require 'themes/'.$theme.'/templates/users/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/users/bottom.tpl.php';
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>