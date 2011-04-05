<?php
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$title = $site_name.' - Часто задаваемые вопросы';
require 'classes/faq.class.php';
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
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
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>