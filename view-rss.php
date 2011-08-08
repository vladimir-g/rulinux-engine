<?php
require 'librarys/feedwriter/FeedWriter.php';
require 'classes/core.php';
(int)$section=$_GET['section'];
(int)$subsection=$_GET['subsection'];
(int)$tid=$_GET['newsid'];
if(!empty($section))
{
	if(!empty($subsection))
	{
		if(!empty($tid))
		{
			$feed = $rssC->get_thread($tid, $uinfo);
			$title = $rssC->get_title($section, $subsection, $tid);
			$section_name = ' - '.$title['section_name'];
			$subsection_name = ' - '.$title['subsection_name'];
			$thread_name = ' - '.$title['thread_name'];
		}
		else
		{
			$feed = $rssC->get_subsection($section, $subsection, $uinfo);
			$title = $rssC->get_title($section, $subsection, $tid);
			$section_name = ' - '.$title['section_name'];
			$subsection_name = ' - '.$title['subsection_name'];
			$thread_name = '';
		}
	}
	else
	{
		$feed = $rssC->get_section($section, $uinfo);
		$title = $rssC->get_title($section, $subsection, $tid);
		$section_name = ' - '.$title['section_name'];
		$subsection_name = '';
		$thread_name = '';
	}
}
else
{
	$feed = $rssC->get_all($uinfo);
	$section_name = '';
	$subsection_name = '';
	$thread_name = '';
}

$TestFeed = new FeedWriter(RSS2);
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.$section_name.$subsection_name.$thread_name;
$TestFeed->setTitle($title);
if(!empty($feed))
{
	for($i=0; $i<count($feed); $i++)
	{
		$newItem = $TestFeed->createNewItem();
		$newItem->setTitle($feed[$i]['title']);
		$newItem->setLink($feed[$i]['link']);
		$newItem->setDate($feed[$i]['time']);
		$newItem->setDescription($feed[$i]['description']);
		$TestFeed->addItem($newItem);
	}
}
$TestFeed->genarateFeed();
?>