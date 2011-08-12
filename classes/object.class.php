<?php
class object
{
	function declOfNum($number, $titles)
	{
	    $cases = array (2, 0, 1, 1, 1, 2);
	    return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
	}
	function to_local_time_zone($timest)
	{
		$first_arr = explode(" ", $timest);
		$second_arr = explode("-", $first_arr[0]);
		$third_arr = explode(":", $first_arr[1]);
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
	function trim_array($Input)
	{
		if (!is_array($Input))
			return trim($Input);
		return array_map('self::trim_array', $Input);
	}
}
?>