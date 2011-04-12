<?php

$filename = 'blocks/'.$directory.'/templates/top.tpl.php';
$file = fopen($filename, "r") or die("Can't open file!");
$boxlet_content = fread($file, filesize($filename));
fclose($file); 
$links_count = 3;
for($i=0; $i<$links_count; $i++)
{

	/*
	[link]
	[title]
	*/
	$filename = 'blocks/'.$directory.'/templates/middle.tpl.php';
	$file = fopen($filename, "r") or die("Can't open file!");
	$boxlet_content = $boxlet_content.fread($file, filesize($filename));
	fclose($file); 
	
}
$filename = 'blocks/'.$directory.'/templates/bottom.tpl.php';
$file = fopen($filename, "r") or die("Can't open file!");
$boxlet_content = $boxlet_content.fread($file, filesize($filename));
fclose($file); 

$boxlet_content = str_replace('[title]', $name, $boxlet_content);
?>