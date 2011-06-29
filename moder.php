<?php
require 'classes/core.php';
$title = ' - Модераторская';
if($uinfo['gid']!=2 && $uinfo['gid']!=3)
{
	require 'header.php';
	$legend = 'У вас нет полномочий';
	$text = 'Вы не являетесь модератором или администратором на данном сайте';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
	exit();
}
if($_GET['action']=='move_thread')
{
	if(!empty($_POST['nxt']))
	{
		require 'header.php';
		$tid = (int)$_GET['tid'];
		$referer = $_POST['referer'];
		$section = (int)$_POST['section'];
		$subsections = sections::get_subsections($section);
		require 'themes/'.$theme.'/templates/moder/move_thread/sbm/top.tpl.php';
		for($i=0; $i<count($subsections); $i++)
		{
			$subsection_id = $subsections[$i]['sort'];
			$subsection_name = $subsections[$i]['name'];
			require 'themes/'.$theme.'/templates/moder/move_thread/sbm/middle.tpl.php';
		}
		require 'themes/'.$theme.'/templates/moder/move_thread/sbm/bottom.tpl.php';
		require 'footer.php';
		exit();
	}
	else if(!empty($_POST['cat']))
	{
		$tid = (int)$_GET['tid'];
		$referer = $_POST['referer'];
		$section = (int)$_POST['section'];
		$subsection = (int)$_POST['subsection'];
		if($section == 1)
		{
			require 'header.php';
			require 'themes/'.$theme.'/templates/moder/move_thread/news/news.tpl.php';
			require 'footer.php';
			exit();
		}
		elseif($section == 3)
		{
			require 'header.php';
			
			
			require 'footer.php';
			exit();
		}
		else
		{
			$ret = threads::move_thread($tid, $section, $subsection);
			if(empty($ret))
				die('<meta http-equiv="Refresh" content="0; URL='.$referer.'">');
			else
			{
				require 'header.php';
				$legend = 'Ошибка при перемещении';
				$text = 'Произошла ошибка при перемещении треда в указанный раздел.';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				require 'footer.php';
				exit();
			}
		}
	}
	else if(!empty($_POST['sbm']))
	{
		$tid = (int)$_GET['tid'];
		$referer = $_POST['referer'];
		$section = (int)$_POST['section'];
		$subsection = (int)$_POST['subsection'];
		$ret = threads::move_thread($tid, $section, $subsection);
		if(empty($ret))
			die('<meta http-equiv="Refresh" content="0; URL='.$referer.'">');
		else
		{
			require 'header.php';
			$legend = 'Ошибка при перемещении';
			$text = 'Произошла ошибка при перемещении треда в указанный раздел.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$tid = (int)$_GET['tid'];
		$referer = getenv("HTTP_REFERER");
		$sections = sections::get_section('all');
		require 'themes/'.$theme.'/templates/moder/move_thread/nxt/top.tpl.php';
		for($i=0; $i<count($sections); $i++)
		{
			$section_id = $sections[$i]['id'];
			$section_name = $sections[$i]['name'];
			require 'themes/'.$theme.'/templates/moder/move_thread/nxt/middle.tpl.php';
		}
		require 'themes/'.$theme.'/templates/moder/move_thread/nxt/bottom.tpl.php';
		require 'footer.php';
		exit();
	}
}
else if($_GET['action']=='attach_thread')
{
	threads::attach_thread($_GET['tid'], 'true');
	$referer = getenv("HTTP_REFERER");
	die('<meta http-equiv="Refresh" content="0; URL='.$referer.'">');
}
else if($_GET['action']=='detach_thread')
{
	threads::attach_thread($_GET['tid'], 'false');
	$referer = getenv("HTTP_REFERER");
	die('<meta http-equiv="Refresh" content="0; URL='.$referer.'">');
}
else if($_GET['action']=='approve_thread')
{
	threads::approve_thread($_GET['tid']);
	$referer = getenv("HTTP_REFERER");
	die('<meta http-equiv="Refresh" content="0; URL='.$referer.'">');
}
else
{
	require 'header.php';
	$legend = 'Неизвестное действие';
	$text = 'Неизвестное действие';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
	exit();
}
?>