<?php
require 'classes/core.php';
empty($_GET['page']) ? $page = 1 : $page = (int)$_GET['page'];
$title = ' - Пользователи';
$rss_link='rss';
require 'header.php';
require 'themes/'.$theme.'/templates/users/top.tpl.php';
$users_on_page = 20;
$users_count = $usersC->get_users_count();
$pages_count = ceil(($users_count)/$users_on_page);
$pages_count>1?	$begin=$users_on_page*($page-1):$begin = 0;
if($pages_count > 1)
{
	if($page>1)
	{
		$pg = $page-1;
		$pages = $pages.'<a href="users_page_1" title="В начало">←</a>&nbsp;';
		$pages = $pages.'<a href="users_page_'.$pg.'" title="Назад">≪</a>&nbsp;';
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
				$pages = $pages.'<a href="users_page_'.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	else
	{
		for($p=1; $p<=$pages_count; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="users_page_'.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	if($page<$pages_count)
	{
		$pg = $page+1;
		$pages = $pages.'<a href="users_page_'.$pg.'" title="Вперед">≫</a>&nbsp;';
		$pages = $pages.'<a href="users_page_'.$pages_count.'" title="В конец">→</a>&nbsp;';
	}
}

$users = $usersC->get_users($begin, $users_on_page);
for($i=0; $i<count($users); $i++)
{
	$avatar = $coreC->validate_boolean($uinfo['show_avatars'], 'FILTER_VALIDATE_FAILURE') == 0 || empty($users[$i]['photo'])? 'themes/'.$theme.'/empty.gif' : 'images/avatars/'.$users[$i]['photo'];
	$nick = $users[$i]['nick'];
	$group_info = $usersC->get_group($users[$i]['gid']);
	$group_name = $group_info['name'];
	$name = $users[$i]['name'];
	$city = !empty($users[$i]['city'])? $users[$i]['city'] : 'город не указан';
	$country = !empty($users[$i]['country'])? $users[$i]['country'] : 'страна не указана';
	$email = $coreC->validate_boolean($users[$i]['show_email']) ? $users[$i]['email'] : 'скрыт';
	$im = $coreC->validate_boolean($users[$i]['show_im']) ? $users[$i]['im'] : 'скрыт';
	require 'themes/'.$theme.'/templates/users/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/users/bottom.tpl.php';
require 'footer.php';
?>