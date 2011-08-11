<?php
require 'classes/core.php';
$title = '';
$rss_link='rss';
require 'header_main.php';
$blocks = $usersC->get_blocks($_SESSION['user_id']);
$lerf_arr = array();
$right_arr=array();
for($i=0; $i<count($blocks); $i++)
{
	if($blocks[$i]['position']=='l')
	{
		if($coreC->block_exists($blocks[$i]['name']))
			$left_arr[] = $blocks[$i];
	}
	else if($blocks[$i]['position']=='r')
	{
		if($coreC->block_exists($blocks[$i]['name']))
			$right_arr[] = $blocks[$i];
	}
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
$add_link = 'new_thread_in_sect_1';
require 'themes/'.$theme.'/templates/index/top.tpl.php';
$threads_count = $threadsC->get_news_count();
$threads_on_page = $uinfo['threads_on_page'];
$pages_count = ceil(($threads_count)/$threads_on_page);
$pages_count>1 ? $begin=$threads_on_page*($page-1) : $begin = 0;
if($pages_count > 1)
{
	if($page>1)
	{
		$pg = $page-1;
		$pages = $pages.'<a href="news_'.$subsection_id.'_page_1" title=В Начало>←</a>&nbsp;';
		$pages = $pages.'<a href="news_'.$subsection_id.'_page_'.$pg.'" title="Назад">≪</a>&nbsp;';
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
				$pages = $pages.'<a href="news_'.$subsection_id.'_page_'.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	else
	{
		for($p=1; $p<=$pages_count; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="news_'.$subsection_id.'_page_'.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	if($page<$pages_count)
	{
		$pg = $page+1;
		$pages = $pages.'<a href="news_'.$subsection_id.'_page_'.$pg.'" title="Вперед">≫</a>&nbsp;';
		$pages = $pages.'<a href="news_'.$subsection_id.'_page_'.$pages_count.'" title="В Конец">→</a>&nbsp;';
	}
}
$gal = $threadsC->get_all_news($begin, $threads_on_page);
for($i=0; $i<count($gal); $i++)
{
	$comment_id = $gal[$i]['cid'];
	$subject = $gal[$i]['subject'];
	$image = $sectionsC->get_subsection_icon($gal[$i]['subsection']);
	$subsection_image = 'themes/'.$theme.'/icons/'.$image;
	$comment = $gal[$i]['comment'];
	$usr = $usersC->get_user_info($gal[$i]['uid']);
	$coreC->validate_boolean($usr['banned']) ? $author = '<s>'.$usr['nick'].'</s>' :$author = $usr['nick'];
	$author_profile = 'user_'.$usr['nick'];
	$timestamp = $coreC->to_local_time_zone($gal[$i]['timest']);
	$thread_id = $gal[$i]['id'];
	$count = $threadsC->get_comments_count($thread_id);
	$comments_count = $coreC->declOfNum($count, array('сообщение', 'сообщения', 'сообщений'));
	$thr_link = 'thread_'.$thread_id.'_page_1';
	$edit_link = 'message_'.$comment_id.':edit';
	if($coreC->validate_boolean($gal[$i]['attached']))
	{
		$attach_link = 'detach_thread_'.$thread_id;
		$attach_text = 'Открепить';
	}
	else
	{
		$attach_link = 'attach_thread_'.$thread_id;
		$attach_text = 'Прикрепить';
	}
	$cmnt_link = 'comment_into_'.$thread_id.'_on_'.$comment_id;
	require 'themes/'.$theme.'/templates/news/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/index/bottom.tpl.php';
function compare($v1, $v2)
{
	if($v1['sort'] == $v2['sort'])
		return 0;
	return ($v1['sort'] < $v2['sort'])?-1:1;
}
if(!empty($left_arr))
{
	$column_class = '';
	usort($left_arr, 'compare');
	require 'themes/'.$theme.'/templates/index/column_top.tpl.php';
	for($i=0; $i<count($left_arr); $i++)
	{
		$blck = $coreC->get_block($left_arr[$i]['name']);
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
	usort($right_arr, 'compare');
	require 'themes/'.$theme.'/templates/index/column_top.tpl.php';
	for($i=0; $i<count($right_arr); $i++)
	{
		$blck = $coreC->get_block($right_arr[$i]['name']);
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