<?php
require 'classes/core.php';
empty($_GET['page']) ? $page = 1 : $page = (int)$_GET['page'];
$title = ' - Пользователи';
$rss_link='rss';
require 'header.php';
$templatesC = new templates;
$templatesC->set_theme($uinfo['theme']);
$templatesC->set_file('users.tpl');
$templatesC->draw('top');
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
	$templatesC->assign('avatar', $coreC->validate_boolean($uinfo['show_avatars'], 'FILTER_VALIDATE_FAILURE') == 0 || empty($users[$i]['photo'])? 'themes/'.$theme.'/empty.gif' : 'images/avatars/'.$users[$i]['photo']);
	$templatesC->assign('nick', $users[$i]['nick']);
	$templatesC->assign('group_info', $usersC->get_group($users[$i]['gid']));
	$templatesC->assign('group_name', $group_info['name']);
	$templatesC->assign('name', $users[$i]['name']);
	$templatesC->assign('city', !empty($users[$i]['city'])? $users[$i]['city'] : 'город не указан');
	$templatesC->assign('country', !empty($users[$i]['country'])? $users[$i]['country'] : 'страна не указана');
	$templatesC->assign('email', $coreC->validate_boolean($users[$i]['show_email']) ? $users[$i]['email'] : 'скрыт');
	$templatesC->assign('im', $coreC->validate_boolean($users[$i]['show_im']) ? $users[$i]['im'] : 'скрыт');
	$templatesC->draw('middle');
}
$templatesC->clear_variables();
$templatesC->assign('pages', $pages);
$templatesC->draw('bottom');
require 'footer.php';
?>