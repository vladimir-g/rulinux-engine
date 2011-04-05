<?php
$section_id = (int)$_GET['id'];
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$sect = sections::get_section($section_id);
$section_name = $sect['name'];
$title = $site_name.' - '.$section_name;
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
require 'themes/'.$theme.'/templates/view_section/top.tpl.php';
$subsct = sections::get_subsections($section_id);
for($i=0; $i<count($subsct); $i++)
{
	$subsection_id = $subsct[$i]['sort'];
	$subsection_name = $subsct[$i]['name'];
	$thr_count = sections::get_subsection_thr_count($section_id, $subsection_id);
	$subsection_thr_count = $thr_count['subsection_thr_count'];
	$subsection_thr_day = $thr_count['subsection_thr_day'];
	$subsection_thr_hour = $thr_count['subsection_thr_hour'];
	$subsection_description = $subsct[$i]['description'];
	$page = $sect['file'];
	require 'themes/'.$theme.'/templates/view_section/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/view_section/bottom.tpl.php';
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>