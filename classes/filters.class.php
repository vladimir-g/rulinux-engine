<?php
class filters
{
	function get_filters()
	{
		$sel = base::select('filters', '', '*');
		return $sel;
	}
        function get_filters_count()
        {
                $sel = base::query('SELECT count(*) AS cnt FROM filters', 'assoc_array', array());
                if($sel>0)
                        return $sel[0]['cnt'];
                else
                        return -1;
        }
	function parse_filter_string($str)
	{
		$ret = array();
		$filters_arr = explode(";", $str);
		for($i=0; $i<count($filters_arr);$i++)
		{
			$filter_status = explode(":", $filters_arr[$i]);
			$ret[$i]=$filter_status;
		}
		return $ret;
	}
	function set_auto_filter($message_id, $filter_str = '')
	{
		$filters = self::get_filters();
		$checked_arr = array();
		$param_arr = array($message_id);
		$msg = base::query('SELECT comment, filters FROM comments WHERE id= \'::0::\'', 'assoc_array', $param_arr);
		if(empty($filter_str))
		{
			$filter_str = $msg[0]['filters'];
		}
		$filters_arr = self::parse_filter_string($filter_str);
		for($i=0; $i<count($filters); $i++)
		{
			include_once 'filters/'.$filters[$i]['file'];
			$filterClass = new $filters[$i]['class'];
			$state = $filterClass->check($msg[0]['comment']);
			for($t=0; $t<count($filters_arr); $t++)
			{
				if($filters_arr[$t][0] == $filters[$i]['id'])
				{
					if($filters_arr[$t][1] == 1)
						$state = '1';
				}
			}
			$checked_arr[$i] = array($filters[$i]['id'], $state);
		}
		foreach($checked_arr as $key => $value)
			$str = $str.$value[0].':'.$value[1].';';
		return $str;
	}
}
?>