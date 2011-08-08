<?php
final class messages extends object
{
	static $baseC = null;
	function __construct()
	{
		self::$baseC = new base;
	}
	function new_thread($subject, $message, $section='4', $subsection='1', $file = '', $extension = '', $file_size = '0', $image_size = '', $prooflink='')
	{
		$thr = self::$baseC->query('SELECT MAX(id) AS tid FROM threads', 'assoc_array');
		$tid = $thr[0]['tid']+1;
		$raw_message = $message;
		$subject = htmlspecialchars($subject);
		$message = str_to_html($message);
		$useragent = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
		$uid = $_SESSION['user_id'];
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'show_ua', $where_arr, 'AND');
		$show_ua = $sel[0]['show_ua'];
		$timest = $changing_timest = gmdate("Y-m-d H:i:s");
		$referer = (int)$referer;
		$filters = '';
		$md5 = md5(rand().$timest);
		$msg_arr = array(array('tid', $tid), array('uid', $uid), array('referer', $referer), array('timest', $timest), array('subject', $subject) , array('comment', $message), array('raw_comment', $raw_message), array('useragent', $useragent), array('changing_timest', $changing_timest), array('changed_by', '0'), array('changed_for', ''), array('filters', $filters), array('show_ua', $show_ua), array('md5', $md5));
		$ret = self::$baseC->insert('comments', $msg_arr);
		$subsection = (int)$subsection;
		$section = (int)$section;
		$where_arr = array(array("key"=>'md5', "value"=>$md5, "oper"=>'='));
		$sel = self::$baseC->select('comments', '', 'id', $where_arr, 'AND');
		$cid = $sel[0]['id'];
		$attached = 'false';
		$approved = 'false';
		$approved_by = '0';
		$approve_timest = $timest;
		$msg_arr = array(array('id', $tid), array('cid', $cid), array('section', $section), array('subsection', $subsection), array('attached', $attached), array('approved', $approved), array('approved_by', $approved_by), array('approve_timest', $approve_timest) , array('file', $file), array('file_size', $file_size), array('image_size', $image_size), array('extension', $extension), array('md5', $md5), array('prooflink', $prooflink));
		$ret = self::$baseC->insert('threads', $msg_arr);
		$where_arr = array(array("key"=>'md5', "value"=>$md5, "oper"=>'='));
		$sel = self::$baseC->select('threads', '', 'id', $where_arr, 'AND');
		$tid = $sel[0]['id'];
		$ret = self::$baseC->update('comments', 'tid', $tid, 'md5', $md5);
		return $cid;

	}
	function add_message($subject, $message, $tid, $referer, $md5)
	{
		$raw_message = $message;
		$subject = htmlspecialchars($subject);
		$message = str_to_html($message);
		$useragent = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
		$tid = (int)$tid;
		$uid = $_SESSION['user_id'];
		$where_arr = array(array("key"=>'id', "value"=>$_SESSION['user_id'], "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'show_ua', $where_arr, 'AND');
		$show_ua = $sel[0]['show_ua'];
		$timest = $changing_timest = gmdate("Y-m-d H:i:s");
		$referer = (int)$referer;
		$filters = '';
		$msg_arr = array(array('tid', $tid), array('uid', $uid), array('referer', $referer), array('timest', $timest), array('subject', $subject) , array('comment', $message), array('raw_comment', $raw_message), array('useragent', $useragent), array('changing_timest', $changing_timest), array('changed_by', '0'), array('filters', $filters), array('show_ua', $show_ua), array('md5', $md5));
		$ret = self::$baseC->insert('comments', $msg_arr);
	}
	function get_messages_for_tracker($hours)
	{
		if($hours>1)
			$str = '- '.$hours.' hours';
		else
			$str = '- 1 hour';
		$timestamp = gmdate('Y-m-d H:i:s', strtotime($str));
		$where_arr = array(array("key"=>'timest', "value"=>$timestamp, "oper"=>'>'));
		$sel = self::$baseC->select('comments', '', '*', $where_arr, 'AND', 'timest', 'DESC');
		return $sel;
	}
	function edit_message($id, $subject, $message, $reason)
	{
		$raw_message = $message;
		$subject = htmlspecialchars($subject);
		$message = str_to_html($message);
		$changing_timest = gmdate("Y-m-d H:i:s");
		$changed_by = $_SESSION['user_id'];
		$changed_for = htmlspecialchars($reason);
		$param_arr = array($subject, $raw_message, $message, $changing_timest, $changed_by, $changed_for, $id);
		$ret = self::$baseC->query('UPDATE comments SET subject=\'::0::\', raw_comment=\'::1::\', comment=\'::2::\', changing_timest=\'::3::\', changed_by=\'::4::\', changed_for=\'::5::\' WHERE id= \'::6::\'', 'assoc_array', $param_arr);
		return $ret;
	}
	function edit_news($id, $subject, $message, $reason, $tid, $link, $subsection)
	{
		$raw_message = $message;
		$subject = htmlspecialchars($subject);
		$message = str_to_html($message);
		$changing_timest = gmdate("Y-m-d H:i:s");
		$changed_by = $_SESSION['user_id'];
		$changed_for = htmlspecialchars($reason);
		$param_arr = array($subject, $raw_message, $message, $changing_timest, $changed_by, $changed_for, $id);
		$ret = self::$baseC->query('UPDATE comments SET subject=\'::0::\', raw_comment=\'::1::\', comment=\'::2::\', changing_timest=\'::3::\', changed_by=\'::4::\', changed_for=\'::5::\' WHERE id= \'::6::\'', 'assoc_array', $param_arr);
		$param_arr = array($link, $subsection, $tid);
		$ret = self::$baseC->query('UPDATE threads SET prooflink=\'::0::\', subsection=\'::1::\' WHERE id = \'::2::\'', 'assoc_array', $param_arr);
		return $ret;
	}
	function get_messages_count($tid)
	{
		$tid = (int)$tid;
		$param_arr = array($tid);
		$sel = self::$baseC->query('SELECT count(*) AS cnt FROM comments WHERE tid = \'::0::\'', 'assoc_array', $param_arr);
		return $sel[0]['cnt'];
	}
	function get_message($id)
	{
		$id = (int)$id;
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$sel = self::$baseC->select('comments', '', '*', $where_arr, 'AND');
		return $sel[0];
	}
	function get_topic_start_message($tid)
	{
		$tid = (int)$tid;
		$where_arr = array(array("key"=>'id', "value"=>$tid, "oper"=>'='));
		$sel = self::$baseC->select('threads', '', '*', $where_arr, 'AND');
		$where_arr = array(array("key"=>'id', "value"=>$sel[0]['cid'], "oper"=>'='));
		$ret = self::$baseC->select('comments', '', '*', $where_arr, 'AND');
		return $ret[0] + $sel[0];
	}
	function get_comments_on_page($tid, $begin = 0, $end = '')
	{
		$tid = (int)$tid;
		$param_arr = array($tid);
		$sel = self::$baseC->query('SELECT max(id) AS max, min(id) AS min FROM comments WHERE tid = \'::0::\'', 'assoc_array', $param_arr);
		if(empty($end))
			$end = $sel[0]['max'];
		$where_arr = array(array("key"=>'tid', "value"=>$tid, "oper"=>'='), array("key"=>'id', "value"=>$sel[0]['min'], "oper"=>'>'));
		$sel = self::$baseC->select('comments', '', '*', $where_arr, 'AND', 'id', 'ASC', $begin, $end);
		return $sel;
	}
	function set_filter($cid, $str)
	{
		$ret = self::$baseC->update('comments', 'filters', $str, 'id', $cid);
		return $ret;
	}
	function get_filter($cid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$cid, "oper"=>'='));
		$sel = self::$baseC->select('comments', '', 'filters', $where_arr);
		return $sel[0]['filters'];
	}
	function is_filtered($cid, $user_filter_arr)
	{
		$msg_filter = self::get_filter($cid);
		$msg_filter_arr = filters::parse_filter_string($msg_filter);
		for($i=0; $i<count($user_filter_arr);$i++)
		{
			if($user_filter_arr[$i][1] == 1)
			{
				if($msg_filter_arr[$i][1] == 1)
					return 1;
			}
		}
		return 0;
	}
	function get_user_messages($user, $limit, $offset)
	{
		$param_arr = array($user, $limit, $offset);
		$ret = self::$baseC->query('SELECT c.id, c.tid, c.subject, c.timest, t.section, t.subsection  FROM comments c INNER JOIN (SELECT id, section, subsection FROM threads WHERE id IN (SELECT tid FROM comments WHERE uid = (SELECT id FROM users WHERE nick = \'::0::\'))) t ON c.tid = t.id ORDER BY c.id DESC LIMIT ::1:: OFFSET ::2::','assoc_array',$param_arr);
		return $ret;
	}
	function get_user_reply($user, $limit, $offset)
	{
		
		$param_arr = array($user, $limit, $offset);
		$ret = self::$baseC->query('SELECT c.id, c.tid, c.subject, c.comment, c.timest, t.section, t.subsection  FROM comments c INNER JOIN (SELECT id, section, subsection FROM threads WHERE id IN (SELECT tid FROM comments WHERE uid = (SELECT id FROM users WHERE nick = \'::0::\'))) t ON c.tid = t.id WHERE c.referer IN (SELECT id FROM comments WHERE uid=(SELECT id FROM users WHERE nick = \'::0::\')) ORDER BY c.id DESC LIMIT ::1:: OFFSET ::2::','assoc_array',$param_arr);
		return $ret;
	}
}
?>