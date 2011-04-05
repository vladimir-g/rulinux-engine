<?php
require 'classes/core.php';
auth_user('root', 'root', false);
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$title = $site_name;
require 'classes/faq.class.php';
require 'links.php';
require 'themes/'.$theme.'/templates/main_header.tpl.php';
require 'themes/'.$theme.'/templates/index/nav.tpl.php';
require 'themes/'.$theme.'/templates/index/top.tpl.php';
$threads_count = threads::get_news_count();
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
$gal = threads::get_all_news($begin, $threads_on_page);
for($i=0; $i<count($gal); $i++)
{
	$comment_id = $gal[$i]['cid'];
	$subject = $gal[$i]['subject'];
	$image = sections::get_subsection_icon($gal[$i]['subsection']);
	$subsection_image = 'themes/'.$theme.'/icons/'.$image;
	$comment = $gal[$i]['comment'];
	$usr = users::get_user_info($gal[$i]['uid']);
	in_array($usr['banned'], $true_arr) ? $author = '<s>'.$usr['nick'].'</s>' :$author = $usr['nick'];
	$author_profile = 'profile.php?id='.$usr['nick'];
	$timestamp = $gal[$i]['timest'];
	$thread_id = $gal[$i]['id'];
	$count = threads::get_comments_count($thread_id);
	$comments_count = core::declOfNum($count, array('сообщение', 'сообщения', 'сообщений'));
	require 'themes/'.$theme.'/templates/news/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/footer.tpl.php';
require 'themes/'.$theme.'/templates/index/bottom.tpl.php';
//$column_class
require 'themes/'.$theme.'/templates/index/column_top.tpl.php';
//$boxlet_content
require 'themes/'.$theme.'/templates/index/boxlet.tpl.php';
require 'themes/'.$theme.'/templates/index/column_bottom.tpl.php';


?>
