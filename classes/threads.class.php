<?php
final class threads  extends object
{
	static $baseC = null;
	function __construct()
	{
		self::$baseC = new base;
	}
	function get_threads_count($section, $subsection)
	{
		$param_arr = array($section, $subsection);
		$sel = self::$baseC->query('SELECT count(*) AS cnt FROM threads where section = \'::0::\' AND subsection = \'::1::\'','assoc_array', $param_arr);
		return $sel[0]['cnt'];
	}
	function get_comments_count($tid)
	{
		$param_arr = array($tid);
		$sel = self::$baseC->query('SELECT count(*) AS cnt FROM comments where tid = \'::0::\'','assoc_array', $param_arr);
		return $sel[0]['cnt']-1;
	}
	function get_threads_on_page($section, $subsection, $begin= 0, $end = '', $uinfo)
	{
		$param_arr = array($section, $subsection);
		$sel = self::$baseC->query('SELECT max(id) AS max FROM threads WHERE section = \'::0::\' AND subsection = \'::1::\'', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		if(!self::validate_boolean($uinfo['sort_to']))
			$sort = 'c.timest';
		else
			$sort = 'c.changing_timest';
		if($section == 2)
		{
			$param_arr = array($section, $subsection, $sort, $end, $begin);
			$sel = self::$baseC->query('SELECT t.id, t.attached, c.subject, t.changing_timest, t.timest, c.uid FROM threads t INNER JOIN comments c ON t.cid = c.id WHERE t.section =\'::0::\' AND t.subsection = \'::1::\' AND approved = true ORDER BY t.attached <>true ASC, ::2:: DESC LIMIT ::3:: OFFSET ::4::', 'assoc_array', $param_arr);
		}
		else
		{
			$param_arr = array($section, $subsection, $sort, $end, $begin);
			$sel = self::$baseC->query('SELECT t.id, t.attached, c.subject, t.changing_timest, t.timest, c.uid FROM threads t INNER JOIN comments c ON t.cid = c.id WHERE t.section =\'::0::\' AND t.subsection = \'::1::\' ORDER BY t.attached <>true ASC, ::2:: DESC LIMIT ::3:: OFFSET ::4::', 'assoc_array', $param_arr);
		}
		return $sel;
	}
	function get_gallery($subsection, $begin= 0, $end = '')
	{
		$param_arr = array($subsection);
		$sel = self::$baseC->query('SELECT max(id) AS max FROM threads WHERE section = 3 AND subsection = \'::0::\'', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$param_arr = array($subsection, $end, $begin);
		$ret = self::$baseC->query('SELECT t.id, t.cid, t.attached, t.approved, t.approved_by, t.approve_timest, t.file, t.file_size, t.image_size, t.extension, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.cid = c.id WHERE t.approved=true AND t.section=3 AND t.subsection = \'::0::\' ORDER BY t.attached <>true ASC, id DESC LIMIT ::1:: OFFSET ::2::', 'assoc_array', $param_arr);
		return $ret;
	}
	function get_news($subsection, $begin= 0, $end = '')
	{
		$param_arr = array($subsection);
		$sel = self::$baseC->query('SELECT max(id) AS max FROM threads WHERE section = 1 AND subsection = \'::0::\'', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$param_arr = array($subsection, $end, $begin);
		$ret = self::$baseC->query('SELECT t.id, t.cid, t.attached, t.prooflink, t.approved, t.approved_by, t.approve_timest, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.cid = c.id WHERE t.approved=true AND t.section=1 AND t.subsection = \'::0::\' ORDER BY t.attached <>true ASC, id DESC LIMIT ::1:: OFFSET ::2::', 'assoc_array', $param_arr);
		return $ret;
	}
	function get_thread_info($id)
	{
		$timest_day = gmdate('Y-m-d H:i:s',strtotime('-1 day'));
		$timest_hour = gmdate('Y-m-d H:i:s',strtotime('-1 hour'));
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$thr = self::$baseC->select('threads', '', '*', $where_arr);
		$where_arr = array(array("key"=>'id', "value"=>$thr[0]['cid'], "oper"=>'='));
		$cmnt = self::$baseC->select('comments', '', 'subject, timest, uid', $where_arr);
		$param_arr = array($id);
		$param_arr = array($id, $timest_day, $timest_hour);
		$sel = self::$baseC->query('SELECT count(1) AS cnt_all, sum(CASE WHEN timest > \'::1::\' THEN 1 ELSE 0 END) AS cnt_24h, sum(CASE WHEN timest > \'::2::\' THEN 1 ELSE 0 END) AS cnt_1h FROM comments WHERE tid =\'::0::\'', 'assoc_array', $param_arr);
		$ret = array("id"=>$thr[0]['id'], "thread_subject"=>$cmnt[0]['subject'], "uid"=>$cmnt[0]['uid'], "comments_in_thread_all"=>$sel[0]['cnt_all'], "comments_in_thread_day"=>$sel[0]['cnt_24h'], "comments_in_thread_hour"=>$sel[0]['cnt_1h'], "attached"=>$thr[0]['attached'], "section"=>$thr[0]['section'], "subsection"=>$thr[0]['subsection'], "prooflink"=>$thr[0]['prooflink']);
		return $ret;
	}
	function get_thread_times($id)
	{
		$timest_day = gmdate('Y-m-d H:i:s',strtotime('-1 day'));
		$timest_hour = gmdate('Y-m-d H:i:s',strtotime('-1 hour'));
		$param_arr = array($id, $timest_day, $timest_hour);
		$sel = self::$baseC->query('SELECT count(1) AS cnt_all, sum(CASE WHEN timest > \'::1::\' THEN 1 ELSE 0 END) AS cnt_24h, sum(CASE WHEN timest > \'::2::\' THEN 1 ELSE 0 END) AS cnt_1h FROM comments WHERE tid =\'::0::\'', 'assoc_array', $param_arr);
		$ret = array("comments_in_thread_all"=>$sel[0]['cnt_all'], "comments_in_thread_day"=>$sel[0]['cnt_24h'], "comments_in_thread_hour"=>$sel[0]['cnt_1h']);
		return $ret;
	}
	function get_news_count()
	{
		$param_arr = array('1');
		$sel = self::$baseC->query('SELECT count(*) AS cnt FROM threads where section = \'::0::\'','assoc_array', $param_arr);
		return $sel[0]['cnt'];
	}
	function get_all_news($begin= 0, $end = '')
	{
		$param_arr = array($subsection);
		$sel = self::$baseC->query('SELECT max(id) AS max FROM threads WHERE section = 1', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$param_arr = array($subsection, $end, $begin);
		$ret = self::$baseC->query('SELECT t.id, t.cid, t.attached, t.prooflink, t.approved, t.approved_by, t.approve_timest, t.subsection, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.cid = c.id WHERE t.approved=true AND t.section=1 ORDER BY t.attached <>true ASC, id DESC LIMIT ::1:: OFFSET ::2::', 'assoc_array', $param_arr);
		return $ret;
	}
	function get_unconfirmed()
	{
		$ret = self::$baseC->query('SELECT t.id, t.cid, t.section, t.subsection, t.approved, t.approved_by, t.approve_timest, t.file, t.file_size, t.image_size, t.extension, c.subject, c.comment, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.cid = c.id WHERE t.approved=false AND (t.section=1 OR t.section=2 OR t.section=3) ORDER BY t.attached <>true ASC, id DESC', 'assoc_array', array());
		return $ret;
	}
	function move_thread($tid, $section, $subsection)
	{
		$tid = (int)$tid;
		$section = (int)$section;
		$subsection = (int)$subsection;
		$param_arr = array($tid, $section, $subsection);
		$ret = self::$baseC->query('UPDATE threads SET section = \'::1::\' , subsection = \'::2::\' WHERE id = \'::0::\'', 'assoc_array', $param_arr);
		return $ret;
	}
	function attach_thread($tid, $state)
	{
		$tid = (int)$tid;
		$state = self::validate_boolean($state);
		$param_arr = array($tid, $state);
		$ret = self::$baseC->query('UPDATE threads SET attached = \'::1::\' WHERE id = \'::0::\'', 'assoc_array', $param_arr);
		return $ret;
	}
	function approve_thread($tid)
	{
		$tid = (int)$tid;
		$approved_by = $_SESSION['user_id'];
		$approve_timest = gmdate("Y-m-d H:i:s");
		$param_arr = array($tid, $approved_by, $approve_timest);
		$ret = self::$baseC->query('UPDATE threads SET approved = true , approved_by = \'::1::\', approve_timest=\'::2::\' WHERE id = \'::0::\'', 'assoc_array', $param_arr);
		return $ret;
	}
	
	function get_previous_thread($tid)
	{
		$tid = (int)$tid;
		$ret = array();
		$param_arr = array($tid);
		$id = self::$baseC->query('SELECT max(id) AS id FROM threads WHERE id<\'::0::\' AND section = (SELECT section FROM threads WHERE id=\'::0::\') AND subsection = (SELECT subsection FROM threads WHERE id=\'::0::\');', 'assoc_array', $param_arr);
		if(!empty($id[0]['id']))
		{
			$param_arr = array($id[0]['id']);
			$subj = self::$baseC->query('SELECT subject FROM comments WHERE id = (SELECT cid FROM threads WHERE id = \'::0::\');', 'assoc_array', $param_arr);
			$ret = array("subject"=>$subj[0]['subject'], "id"=>$id[0]['id']);
			return $ret;
		}
		else
			return $ret;
	}
	
	function get_next_thread($tid)
	{
		$tid = (int)$tid;
		$ret = array();
		$param_arr = array($tid);
		$id = self::$baseC->query('SELECT min(id) AS id FROM threads WHERE id>\'::0::\' AND section = (SELECT section FROM threads WHERE id=\'::0::\') AND subsection = (SELECT subsection FROM threads WHERE id=\'::0::\');', 'assoc_array', $param_arr);
		if(!empty($id[0]['id']))
		{
			$param_arr = array($id[0]['id']);
			$subj = self::$baseC->query('SELECT subject FROM comments WHERE id = (SELECT cid FROM threads WHERE id = \'::0::\');', 'assoc_array', $param_arr);
			$ret = array("subject"=>$subj[0]['subject'], "id"=>$id[0]['id']);
			return $ret;
		}
		else
			return $ret;
	}
	
	function get_msg_number($thread_id, $md5)
	{
		$param_arr = array($thread_id);
		$sel = self::$baseC->query('SELECT id,md5 FROM comments WHERE tid = \'::0::\' AND id>(SELECT min(id) FROM comments WHERE tid=\'::0::\')','assoc_array', $param_arr);
		for($i=0;$i<count($sel);$i++)
		{
			if($sel[$i]['md5']==$md5)
			{
				$message_number = $i+1;
				$msg_id = $sel[$i]['id'];
			}
		}
		return array("message_number"=>$message_number, "msg_id"=>$msg_id);
	}
	
	function get_msg_number_by_cid($message_id)
	{
		$param_arr = array($message_id);
		$thr = self::$baseC->query('SELECT tid FROM comments WHERE id = \'::0::\'','assoc_array', $param_arr);
		$param_arr = array($thr[0]['tid']);
		$sel = self::$baseC->query('SELECT id FROM comments WHERE tid = \'::0::\' AND id>(SELECT min(id) FROM comments WHERE tid=\'::0::\') ORDER BY id','assoc_array', $param_arr);
		$msg_id = $message_id;
                for($i=0;$i<count($sel);$i++)
		{
			if($sel[$i]['id'] == $message_id)
			{
				$message_number = $i+1;
				$msg_id = $sel[$i]['id'];
			}
		}
		return array($message_number, $thr[0]['tid'], $msg_id);
	}
	
	function get_tid_by_cid($cid)
	{
		$where_arr = array(array("key"=>'cid', "value"=>$cid, "oper"=>'='));
		$sel = self::$baseC->select('threads', '', '*', $where_arr, 'AND');
		return $sel;
	}
	
	function get_msg_number_by_tid($thread_id, $cid)
	{
		$param_arr = array($thread_id);
		$sel = self::$baseC->query('SELECT id FROM comments WHERE tid = \'::0::\' ORDER BY id ASC','assoc_array', $param_arr);
		for($t=0;$t<count($sel);$t++)
		{
			if($sel[$t]['id']==$cid)
				$message_number = $t;
		}
		return $message_number;
	}
}
?>