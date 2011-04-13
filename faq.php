<?php
require 'classes/core.php';
$title = ' - Часто задаваемые вопросы';
require 'header.php';
require 'themes/'.$theme.'/templates/faq/top.tpl.php';
$questions = faq::get_questions();
for($i=0; $i<count($questions); $i++)
{
	$subject = $questions[$i]['subject'];
	$question = $questions[$i]['question'];
	if(core::validate_boolean($questions[$i]['answered']))
		$answer = $questions[$i]['answer'];
	else
		$answer = 'ждите ответа';
	require 'themes/'.$theme.'/templates/faq/middle.tpl.php';
}
require 'themes/'.$theme.'/templates/faq/bottom.tpl.php';
require 'footer.php';
?>