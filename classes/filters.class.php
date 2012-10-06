<?php
final class filters extends object
{
	static $baseC = null;
	private static $filters = array();
	function __construct()
	{
		self::$baseC = new base;
		/* Populate inner cache of filters */
		$filters_res = self::$baseC->select('filters', '', '*');
		foreach ($filters_res as $filter)
			self::$filters[$filter['id']] = $filter;
	}
	function get_filters()
	{
		$sel = self::$baseC->select('filters', '', '*');
		return $sel;
	}
        function get_filters_count()
        {
                $sel = self::$baseC->query('SELECT count(*) AS cnt FROM filters', 'assoc_array', array());
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
		$msg = self::$baseC->query('SELECT comment, filters FROM comments WHERE id= \'::0::\'', 'assoc_array', $param_arr);
		if(empty($filter_str))
		{
			$filter_str = $msg[0]['filters'];
		}
		$filters_arr = self::parse_filter_string($filter_str);
		for($i=0; $i<count($filters); $i++)
		{
			include_once 'filters/'.$filters[$i]['directory'].'/filter.php';
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
		$str = '';
		foreach($checked_arr as $key => $value)
			$str = $str.$value[0].':'.$value[1].';';
		return $str;
	}
	/* Get list of applied filters in readable format */
	function get_filter_list($filter_str)
	{
		$result = array();
		$raw_list = explode(';', $filter_str);
		foreach ($raw_list as $item)
		{
			if ((int)$item[2] == 1)
				$result[(int)$item[0]] = self::$filters[(int)$item[0]];
		}
		return $result;
	}
	/* Get filters used on some item  */
	function get_active_filters($list1, $list2)
	{
		$active_filters_list = array();
		foreach (array_intersect_key($list1, $list2) as $filter)
		{
			$active_filters_list[] = $filter['name'];
		}
		return implode(', ', $active_filters_list);
	}
}
?>