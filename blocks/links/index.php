<?php

$filename = 'blocks/'.$directory.'/templates/top.tpl.php';
$file = fopen($filename, "r") or die("Can't open file!");
$boxlet_content = fread($file, filesize($filename));
fclose($file); 
$links = core::get_links();
if(!empty($links))
{
	for($s=0; $s<count($links); $s++)
	{
		$filename = 'blocks/'.$directory.'/templates/middle.tpl.php';
		$file = fopen($filename, "r") or die("Can't open file!");
		$boxlet_content = $boxlet_content.fread($file, filesize($filename));
		fclose($file); 
		$boxlet_content = str_replace('[link]', $links[$s]['link'], $boxlet_content);
		$boxlet_content = str_replace('[link_name]', $links[$s]['name'], $boxlet_content);
	}
}
$filename = 'blocks/'.$directory.'/templates/bottom.tpl.php';
$file = fopen($filename, "r") or die("Can't open file!");
$boxlet_content = $boxlet_content.fread($file, filesize($filename));
fclose($file);

$boxlet_content = str_replace('[title]', $name, $boxlet_content);
?>