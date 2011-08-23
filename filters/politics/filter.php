<?php
class politics
{
	function check($message)
	{
		$path = 'filters/politics/politics_full.txt';
		if(file_exists($path))
			$words = file($path);
		else
			$words = array();
		$words = core::trim_array($words);
		$path = 'filters/politics/politics_semi.txt';
		if(file_exists($path))
			$semi_mass_words = file($path);
		else
			$semi_mass_words = array();
		$semi_mass_words = core::trim_array($semi_mass_words);
		$message = strtolower($message);
		$msg_words = explode(" ", $message);
		for($i=0; $i<count($msg_words); $i++)
		{
			$word = strip_tags($msg_words[$i]);
			if(!empty($word))
			{
				if(in_array($word, $words))
					return 1;
			}
		}
		$ret = 0;
		for($i=0; $i<count($msg_words); $i++)
		{
			$word = strip_tags($msg_words[$i]);
			if(!empty($word))
			{
				if(in_array($word, $semi_mass_words))
					$ret = $ret + 0.5;
			}
			if($ret == 1)
				return 1;
		}
		return 0;
	}
}
?>