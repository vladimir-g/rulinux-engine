<?php
$filename = 'blocks/'.$directory.'/templates/top.tpl.php';
$file = fopen($filename, "r") or die("Can't open file!");
$boxlet_content = fread($file, filesize($filename));
fclose($file); 
$questions = faq::get_questions();
if($questions>0)
{
	for($z=0; $z<count($questions); $z++)
	{
		$filename = 'blocks/'.$directory.'/templates/question.tpl.php';
		$file = fopen($filename, "r") or die("Can't open file!");
		$boxlet_content = $boxlet_content.fread($file, filesize($filename));
		fclose($file); 
		$quest = strip_tags($questions[$z]['question']);
		$boxlet_content = str_replace('[question]', $quest, $boxlet_content);
		$link = 'faq#q'.$questions[$z]['id'];
		$boxlet_content = str_replace('[link]', $link, $boxlet_content);
	}
}
$boxlet_content = str_replace('[title]', $name, $boxlet_content);
?>