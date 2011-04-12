<?php
if (get_magic_quotes_gpc()) 
{
	function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}
	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}
class core
{
	function get_settings_by_name($name)
	{
		$where_arr = array(array("key"=>'name', "value"=>$name, "oper"=>'='));
		$sel = base::select('settings', '', 'value', $where_arr, 'AND');
		return $sel[0]['value'];
	}
	function declOfNum($number, $titles)
	{
	    $cases = array (2, 0, 1, 1, 1, 2);
	    return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
	}
	function get_readers_count($tid, $anon=0)
	{
		if($anon==1)
		{
			$param_arr = array($tid);
			$sel = base::query('SELECT count(session_id) AS cnt FROM sessions WHERE tid = \'::0::\' AND uid = 1', 'assoc_array', $param_arr);
		}
		elseif($anon==2)
		{
			$param_arr = array($tid);
			$sel = base::query('SELECT count(session_id) AS cnt FROM sessions WHERE tid = \'::0::\' AND uid != 1', 'assoc_array', $param_arr);
		}
		else
		{
			$param_arr = array($tid);
			$sel = base::query('SELECT count(session_id) AS cnt FROM sessions WHERE tid = \'::0::\'', 'assoc_array', $param_arr);
		}
		return $sel[0]['cnt'];
	}
	function get_readers($tid)
	{
		$where_arr = array(array("key"=>'tid', "value"=>$tid, "oper"=>'='), array("key"=>'uid', "value"=>'1', "oper"=>'>'));
		$usrs = base::select('sessions', '', 'uid', $where_arr, 'AND');
		if(!empty($usrs))
		{
			for($i=0; $i<count($usrs); $i++)
			{
				$where_arr = array(array("key"=>'id', "value"=>$usrs[$i]['uid'], "oper"=>'='));
				$sel = base::select('users', '', 'nick,gid', $where_arr, 'AND');
				$ret[$i] = $sel[0];
			}
			return $ret;
		}
		else
			return -1;
	}
	function update_sessions_table($session_id, $uid, $tid)
	{
		if(gmdate("i")>5)
			$min = gmdate("i")-5;
		else
			$min = 60+gmdate("i")-5;
		$timestamp = gmdate("Y-m-d H").':'.$min.':'.gmdate("s");
		$where_arr = array(array("key"=>'timest', "value"=>$timestamp, "oper"=>'<'));
		$subsect = base::select('sessions', '', '*', $where_arr, 'AND');
		if(!empty($subsect))
		{
			for($i=0; $i<count($subsect); $i++)
				base::delete('sessions', 'id', $subsect[$i]['id']);
		}
		base::delete('sessions', 'session_id', $session_id);
		base::delete('sessions', 'uid', $uid);
		$msg_arr = array(array('session_id', $session_id), array('uid', $uid), array('tid', $tid), array('timest', $timestamp));
		$ret = base::insert('sessions', $msg_arr);
	}
	function search($str, $include, $date, $section, $username)
	{
		$query = 'SELECT * FROM comments WHERE ';
		if($include = 'topics')
			$query = $query.'subject LIKE \'%::0::%\'';
		else if($include = 'comments')
			$query = $query.'comment LIKE \'%::0::%\'';
		else
			$query = $query.'subject LIKE \'%::0::%\' OR comment LIKE \'%::0::%\'';
		if($date=='3month')
		{
			$month = gmdate("m")-3;
			$timestamp = gmdate("Y").'-'.$month.'-'.gmdate("d H:i:s");
			$query = $query.' AND timest > \''.$timestamp.'\'';
		}
		else if($date=='year')
		{
			$month = gmdate("Y")-1;
			$timestamp = $year.'-'.gmdate("m-d H:i:s");
			$query = $query.' AND timest > \''.$timestamp.'\'';
		}
		$section = (int)$section;
		if($section !=0)
			$query = $query.' AND tid IN (SELECT id FROM threads WHERE section = \'::1::\')';
		if(!empty($username))
			$query = $query.' AND uid IN(SELECT id FROM users WHERE nick = \'::2::\')';
		$query = $query.' ORDER BY timest DESC';
		$param_arr = array($str, $section, $username);
		$sel = base::query($query, 'assoc_array', $param_arr);
		return $sel;
	}
	function get_page_by_tid($tid, $msg, $cmnt_on_pg)
	{
		$param_arr = array($tid);
		$sel = base::query('SELECT id FROM comments WHERE tid = \'::0::\' ORDER BY id ASC','assoc_array', $param_arr);
		for($t=0;$t<count($sel);$t++)
		{
			if($sel[$t]['id']==$msg)
				$message_number = $t;
		}
		$page = ceil($message_number/$cmnt_on_pg);
		if($page == 0)
			$page = 1;
		return $page;
	}
	function get_themes()
	{
		$sel = base::query('SELECT * FROM themes ORDER BY id ASC','assoc_array');
		if(!empty($sel))
			return $sel;
		else
			return -1;
	}
	function get_captcha_levels()
	{
		/*заглушка созданная для того, чтобы впоследствии можно было добавлять новые уровни каптчи*/
		$ret = array(array("name"=>'Нет', "value"=>-1), array("name"=>'0', "value"=>0), array("name"=>'1', "value"=>1), array("name"=>'2', "value"=>2), array("name"=>'3', "value"=>3), array("name"=>'4', "value"=>4));
		return $ret;
	}
	function to_local_time_zone($timest)
	{
		$first_arr = split(" ", $timest);
		$second_arr = split("-", $first_arr[0]);
		$third_arr = split(":", $first_arr[1]);
		$year = $second_arr[0];
		$month = $second_arr[1];
		$day = $second_arr[2];
		$hour = $third_arr[0];
		$minute = $third_arr[1];
		$second = $third_arr[2];
		$param_arr = array($_SESSION['user_id']);
		$sel = base::query('SELECT gmt FROM users WHERE id = \'::0::\'','assoc_array', $param_arr);
		if(!empty($sel))
			$gmt = $sel[0]['gmt'];
		else
			$gmt = '+0';
		$timest = date("Y-m-d H:i:s", mktime($hour, $minute, $second, $month, $day, $year)+($gmt*3600));
		return $timest;
	}
	function validate_boolean($val, $fail = '')
	{
		$true_arr = array('t', '1', 'on', 'true', 'yes');
		$false_arr = array('f', '0', 'off', 'false', 'no');
		if($fail != "FILTER_VALIDATE_FAILURE")
		{
			if(in_array($val, $true_arr))
				return 1;
			else
				return 0;
		}
		else
		{
			if(in_array($val, $false_arr))
				return 0;
			else
				return 1;
		}
	}
	function get_block($name='all')
	{
		if($name == 'all')
			$sel = base::query('SELECT * FROM blocks ORDER BY id ASC','assoc_array');
		else
		{
			$param_arr = array($name);
			$sel = base::query('SELECT * FROM blocks WHERE name=\'::0::\' ORDER BY id ASC','assoc_array', $param_arr);
		}
		if(!empty($sel))
			return $sel;
		else
			return -1;
	}
	function get_blocks_count()
	{
		$sel = base::query('SELECT count(*) AS cnt FROM blocks ORDER BY id ASC','assoc_array');
		if(!empty($sel))
			return $sel[0]['cnt'];
		else
			return -1;
	}
}
?>