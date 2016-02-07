<?php
class images
{
	function check($message)
	{
		$re = '#img src#suim';
		if(preg_match($re, $message))
			return 1;
		else
			return 0;
	}
}
?>