<?php
include 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
$title = $site_name.' - Часто задаваемые вопросы';
include 'classes/faq.class.php';
include 'links.php';
include 'themes/'.$theme.'/templates/header.tpl.php';
include 'themes/'.$theme.'/templates/faq/top.tpl.php';
$questions = faq::get_questions();
for($i=0; $i<count($questions); $i++)
{
	$subject = $questions[$i]['subject'];
	$question = $questions[$i]['question'];
	if(in_array($questions[$i]['answered'], $true_arr))
		$answer = $questions[$i]['answer'];
	else
		$answer = 'ждите ответа';
	include 'themes/'.$theme.'/templates/faq/middle.tpl.php';
}
include 'themes/'.$theme.'/templates/faq/bottom.tpl.php';
include 'themes/'.$theme.'/templates/footer.tpl.php';
?>