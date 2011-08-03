<?php
class filthylang
{
	function check($message)
	{
		$cyrilic = array('А', 'В', 'Е');
		$path = 'filters/filthylang/hui.txt';
		if(file_exists($path))
			$hui = file($path);
		else
			$hui = array();
		$hui = core::trim_array($hui);
		$path = 'filters/filthylang/her.txt';
		if(file_exists($path))
			$her = file($path);
		else
			$her = array();
		$her = core::trim_array($her);
		$message = strtolower($message);
		$msg_words = explode(" ", $message);
		for($i=0; $i<count($msg_words); $i++)
		{
			$word = strip_tags($msg_words[$i]);
			if(in_array($word , $hui))
				return 1;
			else if(in_array($word , $her))
				return 1;
		}
		return 0;
	}
}
?>