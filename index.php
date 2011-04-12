<?php
require 'classes/core.php';
$title = '';
require 'header_main.php';
$blocks = users::get_blocks($_SESSION['user_id']);
$lerf_arr = array();
$right_arr=array();
for($i=0; $i<count($blocks); $i++)
{
	if($blocks[$i]['position']=='l')
		$left_arr[] = $blocks[$i];
	else if($blocks[$i]['position']=='r')
		$right_arr[] = $blocks[$i];
}
if(empty($left_arr) && empty($right_arr))
	$position = '<div>';
else if(empty($left_arr) && !empty($right_arr))
	$position = '<div style="margin-right: 245px;">';
else if(!empty($left_arr) && empty($right_arr))
	$position = '<div style="margin-left: 245px;">';
else if(!empty($left_arr) && !empty($right_arr))
	$position = '<div class="newsblog-in2">';
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
	core::validate_boolean($usr['banned']) ? $author = '<s>'.$usr['nick'].'</s>' :$author = $usr['nick'];
	$author_profile = 'profile.php?id='.$usr['nick'];
	$timestamp = core::to_local_time_zone($gal[$i]['timest']);
	$thread_id = $gal[$i]['id'];
	$count = threads::get_comments_count($thread_id);
	$comments_count = core::declOfNum($count, array('сообщение', 'сообщения', 'сообщений'));
	require 'themes/'.$theme.'/templates/news/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/index/bottom.tpl.php';
if(!empty($left_arr))
{
	$column_class = '';
	require 'themes/'.$theme.'/templates/index/column_top.tpl.php';
	for($i=0; $i<count($left_arr); $i++)
	{
		echo 'name '.$left_arr[$i]['name'].'<br>';
		$blck = core::get_block($left_arr[$i]['name']);
		$name = $blck[0]['description'];
		$directory = $blck[0]['directory'];
		include 'blocks/'.$directory.'/index.php';
		include 'themes/'.$theme.'/templates/index/boxlet.tpl.php';
		$name ='';
		$directory='';
		$boxlet_content='';
	}
	require 'themes/'.$theme.'/templates/index/column_bottom.tpl.php';
}
if(!empty($right_arr))
{
	$column_class = '2';
	require 'themes/'.$theme.'/templates/index/column_top.tpl.php';
	for($i=0; $i<count($right_arr); $i++)
	{
		echo 'name'.$right_arr[$i]['name'];
		$blck = core::get_block($right_arr[$i]['name']);
		$name = $blck[0]['description'];
		$directory = $blck[0]['directory'];
		include 'blocks/'.$directory.'/index.php';
		include 'themes/'.$theme.'/templates/index/boxlet.tpl.php';
		$name ='';
		$directory='';
		$boxlet_content='';
	}
	require 'themes/'.$theme.'/templates/index/column_bottom.tpl.php';
}
require 'themes/'.$theme.'/templates/index/foot.tpl.php';
require 'footer.php';
?>