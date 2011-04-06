<?php
$subsection_id = (int)$_GET['id'];
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$invitation = $_SESSION['user_id'] == 1 ? '<a href="register.php">Регистрация</a> <a href="login.php">Вход</a>' : '<a href="logout.php">Выход</а>';
$subsect_arr = sections::get_subsection(1, $subsection_id);
$sect_arr = sections::get_section(1);
$recomendations = $subsect_arr['shortfaq'];
$section_name = $sect_arr['name'];
$section_id = 1;
$subsection_name = $subsect_arr['name'];
$subsection_description = $subsect_arr['description'];
$title = $site_name.' - '.$section_name.' - '.$subsection_name;
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
require 'themes/'.$theme.'/templates/news/nav_top.tpl.php';

$subsct = sections::get_subsections(1);
for($i=0; $i<count($subsct);$i++)
{
	$subsection_nav_name = $subsct[$i]['name'];
	$subsection_nav_id = $subsct[$i]['sort'];
	if($subsection_id==$subsection_nav_id)
		$selected_nav = 'selected';
	else
		$selected_nav = '';
	require 'themes/'.$theme.'/templates/news/nav_middle.tpl.php';
}
require 'themes/'.$theme.'/templates/news/nav_bottom.tpl.php';

require 'themes/'.$theme.'/templates/news/top.tpl.php';
$threads_count = threads::get_threads_count(1, $subsection_id);
$threads_on_page = $uinfo['threads_on_page'];
$pages_count = ceil(($threads_count)/$threads_on_page);
$pages_count>1 ? $begin=$threads_on_page*($page-1) : $begin = 0;
if($pages_count > 1)
{
	if($page>1)
	{
		$pg = $page-1;
		$pages = $pages.'<a href="news.php?id='.$subsection_id.'&page=1" title=В Начало>←</a>&nbsp;';
		$pages = $pages.'<a href="news.php?id='.$subsection_id.'&page='.$pg.'" title="Назад">≪</a>&nbsp;';
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
				$pages = $pages.'<a href="news.php?id='.$subsection_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	else
	{
		for($p=1; $p<=$pages_count; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="news.php?id='.$subsection_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	if($page<$pages_count)
	{
		$pg = $page+1;
		$pages = $pages.'<a href="news.php?id='.$subsection_id.'&page='.$pg.'" title="Вперед">≫</a>&nbsp;';
		$pages = $pages.'<a href="news.php?id='.$subsection_id.'&page='.$pages_count.'" title="В Конец">→</a>&nbsp;';
	}
}
$gal = threads::get_news($subsection_id, $begin, $threads_on_page);
for($i=0; $i<count($gal); $i++)
{
	$comment_id = $gal[$i]['cid'];
	$subject = $gal[$i]['subject'];
	$subsection_image = 'themes/'.$theme.'/icons/'.$subsect_arr['icon'];
	$comment = $gal[$i]['comment'];
	$usr = users::get_user_info($gal[$i]['uid']);
	core::validate_boolean($usr['banned']) ? $author = '<s>'.$usr['nick'].'</s>' : $author = $usr['nick'];
	$author_profile = 'profile.php?id='.$usr['nick'];
	$timestamp = core::to_local_time_zone($gal[$i]['timest']);
	$thread_id = $gal[$i]['id'];
	$count = threads::get_comments_count($thread_id);
	$comments_count = core::declOfNum($count, array('сообщение', 'сообщения', 'сообщений'));
	require 'themes/'.$theme.'/templates/news/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>