<?php
class filthylang
{
	function check($message)
	{
		$src = array('A', '6', 'B', '8', '9', 'E', 'Ё', '3', 'Й', 'K', 'M', 'H', 'O', '0', 'P', 'C', 'T', 'Y', 'X', '4', 'a', '6', 'b', '8', '9', 'e', 'ё', '3', 'й', 'k', 'm', 'h', 'o', '0', 'p', 'c', 't', 'y', 'x', '4');
		$trgt = array('А', 'Б', 'В', 'В', 'Д', 'Е', 'Е', 'З', 'И', 'К', 'М', 'Н', 'О', 'О', 'Р', 'С', 'Т', 'У', 'Х', 'Ч', 'а', 'б', 'в', 'в', 'д', 'е', 'е', 'з', 'и', 'к', 'м', 'н', 'о', 'о', 'р', 'с', 'т', 'у', 'х', 'ч');
		$message = str_replace($src, $trgt, $message);
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
		$path = 'filters/filthylang/pizda.txt';
		if(file_exists($path))
			$pizda = file($path);
		else
			$pizda = array();
		$pizda = core::trim_array($pizda);
		$path = 'filters/filthylang/suka.txt';
		if(file_exists($path))
			$suka = file($path);
		else
			$suka = array();
		$suka = core::trim_array($suka);
		$path = 'filters/filthylang/bljad.txt';
		if(file_exists($path))
			$bljad = file($path);
		else
			$bljad = array();
		$bljad = core::trim_array($bljad);
		$path = 'filters/filthylang/eblya.txt';
		if(file_exists($path))
			$eblya = file($path);
		else
			$eblya = array();
		$eblya = core::trim_array($eblya);
		$message = strtolower($message);
		$msg_words = explode(" ", $message);
		for($i=0; $i<count($msg_words); $i++)
		{
			$word = strip_tags($msg_words[$i]);
			if(!empty($word))
			{
				if(in_array($word , $hui))
					return 1;
				else if(in_array($word , $her))
					return 1;
				else if(in_array($word , $pizda))
					return 1;
				else if(in_array($word , $suka))
					return 1;
				else if(in_array($word , $bljad))
					return 1;
				else if(in_array($word , $eblya))
					return 1;
			}
		}
		return 0;
	}
}
?>