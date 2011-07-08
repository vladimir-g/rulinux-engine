<?php
require 'feedwriter/FeedWriter.php';
require 'classes/core.php';
require 'classes/rss.class.php';
(int)$section=$_GET['section'];
(int)$subsection=$_GET['subsection'];
(int)$tid=$_GET['newsid'];
if(!empty($section))
{
	if(!empty($subsection))
	{
		if(!empty($tid))
		{
			$feed = rss::get_thread($tid);
			$title = rss::get_title($section, $subsection, $tid);
			$section_name = ' - '.$title['section_name'];
			$subsection_name = ' - '.$title['subsection_name'];
			$thread_name = ' - '.$title['thread_name'];
		}
		else
		{
			$feed = rss::get_subsection($section, $subsection);
			$title = rss::get_title($section, $subsection, $tid);
			$section_name = ' - '.$title['section_name'];
			$subsection_name = ' - '.$title['subsection_name'];
			$thread_name = '';
		}
	}
	else
	{
		$feed = rss::get_section($section);
		$title = rss::get_title($section, $subsection, $tid);
		$section_name = ' - '.$title['section_name'];
		$subsection_name = '';
		$thread_name = '';
	}
}
else
{
	$feed = rss::get_all();
	$section_name = '';
	$subsection_name = '';
	$thread_name = '';
}

$TestFeed = new FeedWriter(RSS2);
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.$section_name.$subsection_name.$thread_name;
//$description = 'description';
$TestFeed->setTitle($title);
//$TestFeed->setLink('http://xdan.ru');
//$TestFeed->setDescription($description);
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