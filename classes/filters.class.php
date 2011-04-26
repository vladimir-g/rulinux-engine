<?php
class filters
{
	function get_filters()
	{
		$sel = base::select('filters', '', '*');
		return $sel;
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
}
?>