<?php
class admin
{
	function remove_thread($tid)
	{
		if(!preg_match("/^[0-9]*$/", $tid))
		{
			$re = '/.*message.php\?newsid=([0-9]*).*/';
			if(preg_match($re, $tid, $matches))
			{
				$tid = $matches[1];
			}
			else
				return 0;
		}
		$ret = base::delete('sessions', 'tid', $tid);
		$ret = base::delete('threads', 'id', $tid);
		$ret = base::delete('comments', 'tid', $tid);
		return $ret;
	}
	
	function remove_message($cid)
	{
		if(!preg_match("/^[0-9]*$/", $cid))
		{
			$re = '/.*message.php\?newsid=([0-9]*)(&page=[0-9]*)?#([0-9]*)?/';
			if(preg_match($re, $cid, $matches))
			{
				$cid = $matches[3];
			}
			else
				return 0;
		}
		$where_arr = array(array("key"=>'cid', "value"=>$cid, "oper"=>'='));
		$sel = base::select('threads', '', '*', $where_arr, 'AND');
		if(empty($sel))
			$ret = base::delete('comments', 'id', $cid);
		else
		{
			$ret = base::delete('sessions', 'tid', $sel[0]['id']);
			$ret = base::delete('threads', 'id', $sel[0]['id']);
			$ret = base::delete('comments', 'tid', $sel[0]['id']);
		}
		return $ret;
	}
	
	function get_setting($name)
	{
		$where_arr = array(array("key"=>'name', "value"=>$name, "oper"=>'='));
		$sel = base::select('settings', '', 'value', $where_arr, 'AND');
		return $sel[0]['value'];
	}
	
	function set_setting($name, $value)
	{
		$ret = base::update('settings', 'value', $value, 'name', $name);
		return $ret;
	}
}
?>