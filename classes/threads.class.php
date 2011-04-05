<?php

class threads
{

	function get_threads_count($section, $subsection)
	{
		$param_arr = array($section, $subsection);
		$sel = base::query('SELECT count(*) AS cnt FROM threads where section = \'::0::\' AND subsection = \'::1::\'','assoc_array', $param_arr);
		return $sel[0]['cnt'];
	}
	
	function get_comments_count($tid)
	{
		$param_arr = array($tid);
		$sel = base::query('SELECT count(*) AS cnt FROM comments where tid = \'::0::\'','assoc_array', $param_arr);
		return $sel[0]['cnt']-1;
	}

	function get_threads_on_page($section, $subsection, $begin= 0, $end = '')
	{
		$param_arr = array($section, $subsection);
		$sel = base::query('SELECT max(id) AS max FROM threads WHERE section = \'::0::\' AND subsection = \'::1::\'', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$uinfo = users::get_user_info($_SESSION['user_id']);
		if(in_array($uinfo['sort_to'], array('f', '0')))
			$sort = 'c.timest';
		else
			$sort = 'c.changing_timest';
		if($section == 2)
		{
			$param_arr = array($section, $subsection, $sort, $end, $begin);
			$sel = base::query('SELECT t.id, t.attached, c.subject, c.changing_timest, c.timest FROM threads t INNER JOIN (SELECT tm.tid, tm.subject, tm.timest, max(ch.changing_timest) AS changing_timest FROM comments ch INNER JOIN (SELECT tid, timest, subject FROM comments WHERE timest IN (SELECT min(timest) FROM comments WHERE tid IN (SELECT id FROM threads WHERE section=\'::0::\' AND subsection=\'::1::\' AND approved = true)  GROUP BY tid)) tm ON ch.tid = tm.tid WHERE ch.tid IN (SELECT id FROM threads WHERE section=\'::0::\' AND subsection=\'::1::\' AND approved = true)  GROUP BY ch.tid, tm.timest, tm.subject, tm.tid) c ON t.id = c.tid WHERE t.section =\'::0::\' AND t.subsection = \'::1::\' AND approved = true ORDER BY t.attached <>true ASC, ::2:: DESC LIMIT ::3:: OFFSET ::4::', 'assoc_array', $param_arr);
		}
		else
		{
			$param_arr = array($section, $subsection, $sort, $end, $begin);
			$sel = base::query('SELECT t.id, t.attached, c.subject, c.changing_timest, c.timest FROM threads t INNER JOIN (SELECT tm.tid, tm.subject, tm.timest, max(ch.changing_timest) AS changing_timest FROM comments ch INNER JOIN (SELECT tid, timest, subject FROM comments WHERE timest IN (SELECT min(timest) FROM comments WHERE tid IN (SELECT id FROM threads WHERE section=\'::0::\' AND subsection=\'::1::\')  GROUP BY tid)) tm ON ch.tid = tm.tid WHERE ch.tid IN (SELECT id FROM threads WHERE section=\'::0::\' AND subsection=\'::1::\')  GROUP BY ch.tid, tm.timest, tm.subject, tm.tid) c ON t.id = c.tid WHERE t.section =\'::0::\' AND t.subsection = \'::1::\' ORDER BY t.attached <>true ASC, ::2:: DESC LIMIT ::3:: OFFSET ::4::', 'assoc_array', $param_arr);
		}
		return $sel;
	}
	
	function get_gallery($subsection, $begin= 0, $end = '')
	{
		$param_arr = array($subsection);
		$sel = base::query('SELECT max(id) AS max FROM threads WHERE section = 3 AND subsection = \'::0::\'', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$param_arr = array($subsection, $end, $begin);
		$ret = base::query('SELECT t.id, t.cid, t.approved, t.approved_by, t.approve_timest, t.file, t.file_size, t.image_size, t.extension, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.id = c.tid WHERE t.approved=true AND c.id IN (SELECT cid FROM threads WHERE t.section=3 AND t.subsection = \'::0::\') ORDER BY t.attached <>true ASC, id DESC LIMIT ::1:: OFFSET ::2::', 'assoc_array', $param_arr);
		return $ret;
	}
	
	function get_news($subsection, $begin= 0, $end = '')
	{
		$param_arr = array($subsection);
		$sel = base::query('SELECT max(id) AS max FROM threads WHERE section = 1 AND subsection = \'::0::\'', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$param_arr = array($subsection, $end, $begin);
		$ret = base::query('SELECT t.id, t.cid, t.approved, t.approved_by, t.approve_timest, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.id = c.tid WHERE t.approved=true AND c.id IN (SELECT cid FROM threads WHERE t.section=1 AND t.subsection = \'::0::\') ORDER BY t.attached <>true ASC, id DESC LIMIT ::1:: OFFSET ::2::', 'assoc_array', $param_arr);
		return $ret;
	}
	
	function get_thread_info($id)
	{
		$timest_day = gmdate('Y-m-d H:i:s',strtotime('-1 day'));
		$timest_hour = gmdate('Y-m-d H:i:s',strtotime('-1 hour'));
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$thr = base::select('threads', '', '*', $where_arr);
		$where_arr = array(array("key"=>'id', "value"=>$thr[0]['cid'], "oper"=>'='));
		$cmnt = base::select('comments', '', 'subject, timest, uid', $where_arr);
		$param_arr = array($id);
		$cmnts_in_thr_all = base::query('SELECT count(*) AS cnt FROM comments WHERE tid =\'::0::\'', 'assoc_array', $param_arr);
		
		if(empty($cmnts_in_thr_all[0]['cnt']))
			$cmnts_in_thr_all[0]['cnt'] = '-';
		$param_arr = array($timest_day, $id);
		$cmnts_in_thr_day = base::query('SELECT ALL count(*) AS cnt FROM comments WHERE timest > \'::0::\' AND tid = \'::1::\'', 'assoc_array', $param_arr);
		
		if(empty($cmnts_in_thr_day[0]['cnt']))
			$cmnts_in_thr_day[0]['cnt'] = '-';
		$param_arr = array($timest_hour, $id);
		$cmnts_in_thr_hour = base::query('SELECT ALL count(*) AS cnt FROM comments WHERE timest > \'::0::\' AND tid = \'::1::\'', 'assoc_array', $param_arr);
		
		if(empty($cmnts_in_thr_hour[0]['cnt']))
			$cmnts_in_thr_hour[0]['cnt'] = '-';
		$ret = array("id"=>$thr[0]['id'], "thread_subject"=>$cmnt[0]['subject'], "uid"=>$cmnt[0]['uid'], "comments_in_thread_all"=>$cmnts_in_thr_all[0]['cnt'], "comments_in_thread_day"=>$cmnts_in_thr_day[0]['cnt'], "comments_in_thread_hour"=>$cmnts_in_thr_hour[0]['cnt'], "attached"=>$thr[0]['attached']);
		return $ret;
	}

	function get_news_count()
	{
		$param_arr = array('1');
		$sel = base::query('SELECT count(*) AS cnt FROM threads where section = \'::0::\'','assoc_array', $param_arr);
		return $sel[0]['cnt'];
	}
	
	function get_all_news($begin= 0, $end = '')
	{
		$param_arr = array($subsection);
		$sel = base::query('SELECT max(id) AS max FROM threads WHERE section = 1', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$param_arr = array($subsection, $end, $begin);
		$ret = base::query('SELECT t.id, t.cid, t.approved, t.approved_by, t.approve_timest, t.subsection, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.id = c.tid WHERE t.approved=true AND c.id IN (SELECT cid FROM threads WHERE t.section=1) ORDER BY t.attached <>true ASC, id DESC LIMIT ::1:: OFFSET ::2::', 'assoc_array', $param_arr);
		return $ret;
	}
	
	function get_unconfirmed()
	{
		$ret = base::query('SELECT t.id, t.cid, t.section, t.subsection, t.approved, t.approved_by, t.approve_timest, t.file, t.file_size, t.image_size, t.extension, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.id = c.tid WHERE t.approved=false AND c.id IN (SELECT cid FROM threads WHERE t.section=1 OR t.section=2 OR t.section=3) ORDER BY t.attached <>true ASC, id DESC', 'assoc_array');
		return $ret;
	}
}

?>