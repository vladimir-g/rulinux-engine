<?php
$section_id = (int)$_GET['id'];
require 'classes/core.php';
$sect = $sectionsC->get_section($section_id);
$section_name = $sect['name'];
$title = ' - '.$section_name;
$rss_link='rss_from_sect_'.$section_id;
require 'header.php';
require 'themes/'.$theme.'/templates/view_section/top.tpl.php';
$subsct = $sectionsC->get_subsections($section_id);
for($i=0; $i<count($subsct); $i++)
{
	$subsection_id = $subsct[$i]['sort'];
	$subsection_name = $subsct[$i]['name'];
	$thr_count = $sectionsC->get_subsection_thr_count($section_id, $subsection_id);
	$subsection_thr_count = $thr_count['subsection_thr_count'];
	$subsection_thr_day = $thr_count['subsection_thr_day'];
	$subsection_thr_hour = $thr_count['subsection_thr_hour'];
	$subsection_description = $subsct[$i]['description'];
	$page = $sect['rewrite'];
	$subsect_link = $page.'_'.$subsection_id.'_page_1';
	require 'themes/'.$theme.'/templates/view_section/middle.tpl.php';
}
$edit_profile = $profile_link.':edit';
require 'themes/'.$theme.'/templates/view_section/bottom.tpl.php';
require 'footer.php';
?>