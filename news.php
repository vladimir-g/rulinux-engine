<?php
$subsection_id = (int)$_GET['id'];
if(empty($subsection_id))
	$subsection_id = 1;
require 'classes/core.php';
$subsect_arr = $sectionsC->get_subsection(1, $subsection_id);
$sect_arr = $sectionsC->get_section(1);
$recomendations = $subsect_arr['shortfaq'];
$section_name = $sect_arr['name'];
$section_id = 1;
$subsection_name = $subsect_arr['name'];
$subsection_description = $subsect_arr['description'];
$title = ' - '.$section_name.' - '.$subsection_name;
$rss_link='view-rss.php?section=1';
include 'header.php';
require 'themes/'.$theme.'/templates/news/nav_top.tpl.php';

$subsct = $sectionsC->get_subsections(1);
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
$threads_count = $threadsC->get_threads_count(1, $subsection_id);
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
$gal = $threadsC->get_news($subsection_id, $begin, $threads_on_page);
for($i=0; $i<count($gal); $i++)
{
	$comment_id = $gal[$i]['cid'];
	$subject = $gal[$i]['subject'];
	$subsection_image = 'themes/'.$theme.'/icons/'.$subsect_arr['icon'];
	$comment = $gal[$i]['comment'];
	$usr = $usersC->get_user_info($gal[$i]['uid']);
	$coreC->validate_boolean($usr['banned']) ? $author = '<s>'.$usr['nick'].'</s>' : $author = $usr['nick'];
	$author_profile = 'profile.php?id='.$usr['nick'];
	$timestamp = $coreC->to_local_time_zone($gal[$i]['timest']);
	$thread_id = $gal[$i]['id'];
	$count = $threadsC->get_comments_count($thread_id);
	$comments_count = $coreC->declOfNum($count, array('сообщение', 'сообщения', 'сообщений'));
	require 'themes/'.$theme.'/templates/news/middle.tpl.php';
}
require 'footer.php';
?>