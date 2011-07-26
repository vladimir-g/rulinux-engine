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
}
?>