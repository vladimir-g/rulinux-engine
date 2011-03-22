<?php
$subsection_id = (int)$_GET['id'];
$page = (int)$_GET['page'];
include 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$subsect_arr = sections::get_subsection(4, $subsection_id);
$sect_arr = sections::get_section(4);
$recomendations = $subsect_arr['shortfaq'];
$section_name = $sect_arr['name'];
$section_id = 4;
$subsection_name = $subsect_arr['name'];
$subsection_description = $subsect_arr['description'];
$title = $site_name.' - '.$section_name.' - '.$subsection_name;
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
include 'links.php';
include 'themes/'.$theme.'/templates/header.tpl.php';
include 'themes/'.$theme.'/templates/group/nav_top.tpl.php';
$subsct = sections::get_subsections(4);
for($i=0; $i<count($subsct);$i++)
{
	$subsection_nav_name = $subsct[$i]['name'];
	$subsection_nav_id = $subsct[$i]['sort'];
	if($subsection_id==$subsection_nav_id)
		$selected_nav = 'selected';
	else
		$selected_nav = '';
	include 'themes/'.$theme.'/templates/group/nav_middle.tpl.php';
}
include 'themes/'.$theme.'/templates/group/nav_bottom.tpl.php';
include 'themes/'.$theme.'/templates/group/top.tpl.php';
$threads_count = threads::get_threads_count(4, $subsection_id);
$threads_on_page = $uinfo['threads_on_page'];
$pages_count = ceil(($threads_count)/$threads_on_page);
$pages_count>1?	$begin=$threads_on_page*($page-1):$begin = 0;
$thr = threads::get_threads_on_page(4, $subsection_id, $begin, $threads_on_page);
for($i=0; $i<count($thr); $i++)
{
	//$thread_move_link
	//$thread_attach_link
	//echo $cur_thr['attached'].'<br>';
	//if(in_array($cur_thr['attached'], $true_arr))
	//	$attached = '<img src="/themes/'.$theme.'/paper_clip.gif">';
	$thread_id = $thr[$i]['id'];
	$cur_thr = threads::get_thread_info($thread_id);
	$thread_subject = $cur_thr['thread_subject'];
	$thr_autor = users::get_user_info($cur_thr['uid']);
	$thread_author = $thr_autor['nick'];
	$comments_in_thread_all =$cur_thr['comments_in_thread_all'];
	$comments_in_thread_day = $cur_thr['comments_in_thread_day'];
	$comments_in_thread_hour = $cur_thr['comments_in_thread_hour'];
	include 'themes/'.$theme.'/templates/group/middle.tpl.php';
}
if($pages_count > 1)
{
	if($page>1)
	{
		$pg = $page-1;
		$pages = $pages.'<a href="group.php?id='.$subsection_id.'&page=1" title=В Начало>←</a>&nbsp;';
		$pages = $pages.'<a href="group.php?id='.$subsection_id.'&page='.$pg.'" title="Назад">≪</a>&nbsp;';
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
				$pages = $pages.'<a href="group.php?id='.$subsection_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	else
	{
		for($p=1; $p<=$pages_count; $p++)
		{
			if ($p == $page)
				$pages = $pages.'<b>'.($p).'</b>&nbsp;';
			else
				$pages = $pages.'<a href="group.php?id='.$subsection_id.'&page='.$p.'" title="Страница №'.$p.'">'.($p).'</a>&nbsp;';
		}
	}
	if($page<$pages_count)
	{
		$pg = $page+1;
		$pages = $pages.'<a href="group.php?id='.$subsection_id.'&page='.$pg.'" title="Вперед">≫</a>&nbsp;';
		$pages = $pages.'<a href="group.php?id='.$subsection_id.'&page='.$pages_count.'" title="В Конец">→</a>&nbsp;';
	}
}
include 'themes/'.$theme.'/templates/group/bottom.tpl.php';
include 'themes/'.$theme.'/templates/footer.tpl.php';
?>