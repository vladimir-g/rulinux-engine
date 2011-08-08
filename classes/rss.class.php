<?php
final class rss extends object
{
	static $baseC = null;
	function __construct()
	{
		self::$baseC = new base;
	}
	function get_all($uinfo)
	{
		$ret = array();
		$param_arr = array($uinfo['threads_on_page']);
		$sel = self::$baseC->query('SELECT id, tid, subject, comment, timest FROM comments WHERE id IN (SELECT cid FROM threads) LIMIT \'::0::\'', 'assoc_array', $param_arr);
		if(!empty($sel))
		{
			for($i=0; $i<count($sel); $i++)
			{
				$time = strtotime($sel[$i]['timest']);
				$ret[$i] = array("title"=>$sel[$i]['subject'], "time"=>$time, "description"=>$sel[$i]['comment'], "link"=>'message.php?newsid='.$sel[$i]['tid'].'&page=1');
			}
		}
		return $ret;
	}
	
	function get_section($section, $uinfo)
	{
		$ret = array();
		$param_arr = array($section, $uinfo['threads_on_page']);
		$sel = self::$baseC->query('SELECT id, tid, subject, comment, timest FROM comments WHERE id IN (SELECT cid FROM threads WHERE section = \'::0::\')  LIMIT \'::1::\'', 'assoc_array', $param_arr);
		if(!empty($sel))
		{
			for($i=0; $i<count($sel); $i++)
			{
				$time = strtotime($sel[$i]['timest']);
				$ret[$i] = array("title"=>$sel[$i]['subject'], "time"=>$time, "description"=>$sel[$i]['comment'], "link"=>'message.php?newsid='.$sel[$i]['tid'].'&page=1');
			}
		}
		return $ret;
	}
	
	function get_subsection($section, $subsection, $uinfo)
	{
		$ret = array();
		$param_arr = array($section, $subsection, $uinfo['threads_on_page']);
		$sel = self::$baseC->query('SELECT id, tid, subject, comment, timest FROM comments WHERE id IN (SELECT cid FROM threads WHERE section = \'::0::\' AND subsection=\'::1::\')  LIMIT \'::2::\'', 'assoc_array', $param_arr);
		if(!empty($sel))
		{
			for($i=0; $i<count($sel); $i++)
			{
				$time = strtotime($sel[$i]['timest']);
				$ret[$i] = array("title"=>$sel[$i]['subject'], "time"=>$time, "description"=>$sel[$i]['comment'], "link"=>'message.php?newsid='.$sel[$i]['tid'].'&page=1');
			}
		}
		return $ret;
	}
	
	function get_thread($tid, $uinfo)
	{
		$ret = array();
		$param_arr = array($tid, $uinfo['comments_on_page']);
		$sel = self::$baseC->query('SELECT id, tid, subject, comment, timest FROM comments WHERE tid = \'::0::\'  LIMIT \'::1::\'', 'assoc_array', $param_arr);
		if(!empty($sel))
		{
			for($i=0; $i<count($sel); $i++)
			{
				$time = strtotime($sel[$i]['timest']);
				$message_number = threads::get_msg_number_by_tid($tid, $sel[$i]['id']);
				$page = ceil($message_number/$uinfo['comments_on_page']);
				if($page == 0)
					$page = 1;
				$link = 'message.php?newsid='.$tid.'&page='.$page.'#'.$sel[$i]['id'];
				$ret[$i] = array("title"=>$sel[$i]['subject'], "time"=>$time, "description"=>$sel[$i]['comment'], "link"=>$link);
			}
		}
		return $ret;
	}
	function get_title($section, $subsection='', $tid='')
	{
		$ret = array();
		if(!empty($tid))
		{
			$param_arr = array($tid);
			$sel = self::$baseC->query('SELECT c.thread_name AS thread_name, sect.name AS section_name, subsect.name AS subsection_name FROM sections sect INNER JOIN (SELECT c.subject AS thread_name, t.section, t.subsection FROM comments c INNER JOIN (SELECT cid, section, subsection FROM threads WHERE id=\'::0::\') t ON c.id = t.cid) c ON c.section = sect.id INNER JOIN subsections subsect ON c.subsection = subsect.sort', 'assoc_array', $param_arr);
			$ret=array("section_name"=>$sel[0]['section_name'], "subsection_name"=>$sel[0]['subsection_name'], "thread_name"=>$sel[0]['thread_name']);
		}
		else
		{
			if(!empty($subsection))
			{
				$where_arr = array(array("key"=>'sort', "value"=>$subsection, "oper"=>'='), array("key"=>'section', "value"=>$section, "oper"=>'='));
				$subsect = self::$baseC->select('subsections', '', 'name', $where_arr, 'AND');
				$where_arr = array(array("key"=>'id', "value"=>$section, "oper"=>'='));
				$sect = self::$baseC->select('sections', '', 'name', $where_arr, 'AND');
				$ret=array("section_name"=>$sect[0]['name'], "subsection_name"=>$subsect[0]['name'], "thread_name"=>'');
			}
			else
			{
				$where_arr = array(array("key"=>'id', "value"=>$section, "oper"=>'='));
				$sect = self::$baseC->select('sections', '', 'name', $where_arr, 'AND');
				$ret=array("section_name"=>$sect[0]['name'], "subsection_name"=>'', "thread_name"=>'');
			}
		}
		return $ret;
	}
}
?>