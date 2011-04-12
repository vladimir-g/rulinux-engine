<?php
$subsection_id = (int)$_GET['id'];
require 'classes/core.php';
$subsect_arr = sections::get_subsection(3, $subsection_id);
$sect_arr = sections::get_section(3);
$recomendations = $subsect_arr['shortfaq'];
$section_name = $sect_arr['name'];
$section_id = 3;
$subsection_name = $subsect_arr['name'];
$subsection_description = $subsect_arr['description'];
$title = ' - '.$section_name.' - '.$subsection_name;
require 'header.php';
require 'themes/'.$theme.'/templates/gallery/nav_top.tpl.php';

$subsct = sections::get_subsections(3);
for($i=0; $i<count($subsct);$i++)
{
	$subsection_nav_name = $subsct[$i]['name'];
	$subsection_nav_id = $subsct[$i]['sort'];
	if($subsection_id==$subsection_nav_id)
		$selected_nav = 'selected';
	else
		$selected_nav = '';
	require 'themes/'.$theme.'/templates/gallery/nav_middle.tpl.php';
}
require 'themes/'.$theme.'/templates/gallery/nav_bottom.tpl.php';

require 'themes/'.$theme.'/templates/gallery/top.tpl.php';
$threads_count = threads::get_threads_count(3, $subsection_id);
$threads_on_page = $uinfo['threads_on_page'];
$pages_count = ceil(($threads_count)/$threads_on_page);
$pages_count>1 ? $begin=$threads_on_page*($page-1) : $begin = 0;
if($pages_count > 1)
{
	if($page>1)
	{
		$pg = $page-1;
		$pages = $pages.'<a href="gallery.php?id='.$subsection_id.'&page=1" title=В Начало>←</a>&nbsp;';
		$pages = $pages.'<a href="gallery.php?id='.$subsection_id.'&page='.$pg.'" title="Назад">≪</a>&nbsp;';
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
				$pages = $pages.'<a href="gallery.php?id='.$subsection_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	else
	{
		for($p=1; $p<=$pages_count; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="gallery.php?id='.$subsection_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	if($page<$pages_count)
	{
		$pg = $page+1;
		$pages = $pages.'<a href="gallery.php?id='.$subsection_id.'&page='.$pg.'" title="Вперед">≫</a>&nbsp;';
		$pages = $pages.'<a href="gallery.php?id='.$subsection_id.'&page='.$pages_count.'" title="В Конец">→</a>&nbsp;';
	}
}
$gal = threads::get_gallery($subsection_id, $begin, $threads_on_page);
for($i=0; $i<count($gal); $i++)
{
	$comment_id = $gal[$i]['cid'];
	$subject = $gal[$i]['subject'];
	$comment = $gal[$i]['comment'];
	$img_link = '/gallery/'.$gal[$i]['file'].'.'.$gal[$i]['extension'];
	$img_thumb_link = '/gallery/thumbs/'.$gal[$i]['file'].'_small.png';
	$size = $gal[$i]['image_size'].', '.$gal[$i]['file_size'];
	$usr = users::get_user_info($gal[$i]['uid']);
	core::validate_boolean($usr['banned']) ? $author = '<s>'.$usr['nick'].'</s>' : $author = $usr['nick'];
	$author_profile = 'profile.php?id='.$usr['nick'];
	$timestamp = core::to_local_time_zone($gal[$i]['timest']);
	$thread_id = $gal[$i]['id'];
	$count = threads::get_comments_count($thread_id);
	$comments_count = core::declOfNum($count, array('сообщение', 'сообщения', 'сообщений'));
	require 'themes/'.$theme.'/templates/gallery/middle.tpl.php';
}
require 'footer.php';
?>