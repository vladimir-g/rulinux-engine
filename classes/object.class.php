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
		
		if($_SESSION['user_id'] == 1)
		{
			if(!empty($_COOKIE['gmt']))
				$gmt = $_COOKIE['gmt'];
		}
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

	/* Set provided array keys to empty string if corresponding value isn't set */
	function set_missing_array_keys(&$array, $keys)
	{
		for ($i = 0; $i < count($keys); $i++)
		{
			if (!isset($array[$keys[$i]]))
				$array[$keys[$i]] = '';
		}
	}

	/* From CakePHP */
	function truncate($text, $length, $html = true, $ending = '...')
	{
		if ($html) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			$totalLength = mb_strlen(strip_tags($ending));
			$openTags = array();
			$truncate = '';

			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];

				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}

					$truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = mb_substr($text, 0, $length - mb_strlen($ending));
			}
		}
		$spacepos = mb_strrpos($truncate, ' ');
		if (isset($spacepos))
		{
                        if ($html) {
                                $bits = mb_substr($truncate, $spacepos);
                                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                                if (!empty($droppedTags)) {
                                        foreach ($droppedTags as $closingTag) {
                                                if (!in_array($closingTag[1], $openTags)) {
                                                        array_unshift($openTags, $closingTag[1]);
                                                }
                                        }
                                }
                        }
                        $truncate = mb_substr($truncate, 0, $spacepos);
                }
		$truncate .= $ending;

		if ($html) {
			foreach ($openTags as $tag) {
				$truncate .= '</'.$tag.'>';
			}
		}
		
		return $truncate;
	}
	
	/* Execute htmlentities function with some predefined arguments */
	function html_escape($str)
	{
		return htmlentities($str, ENT_COMPAT | ENT_HTML401, 'UTF-8');
	}
}
?>
