<?php
require 'classes/core.php';
$rss_link='rss';
$action = $_GET['action'];
if($action == 'add_question')
{
	if(isset($_POST['submit_form']))
	{
		if(empty($_POST['subject']))
		{
			require 'header.php';
			$legend = 'Невозможно добавить вопрос';
			$text = 'Невозможно добавить вопрос. Строка заголовка пуста';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if(empty($_POST['comment']))
		{
			require 'header.php';
			$legend = 'Невозможно добавить вопрос';
			$text = 'Невозможно добавить вопрос. Строка коментария пуста';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		$ret = $faqC->add_question($_POST['subject'], $_POST['comment']);
		if($ret<0)
		{
			require 'header.php';
			$legend = 'Невозможно добавить вопрос';
			$text = 'Невозможно добавить вопрос. Возможно недоступна БД';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		else
		{
			require 'header.php';
			$legend = 'Вопрос успешно добавлен';
			$text = 'Вопрос успешно добавлен<br>Через три секунды вы будете перенаправлены на страницу faq.<br>Если вы не хотите ждать, нажмите <a href="faq">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'faq">');  
		}
	}
	else
	{
		require 'header.php';
		$add_question = 'add_question';
		require 'themes/'.$theme.'/templates/faq/add_question.tpl.php';
		require 'footer.php';
		exit();
	}
	
}
else if($action == 'add_answer')
{
        $id = (int)$_GET['id'];
	if(isset($_POST['sbm']))
	{
		if($uinfo['gid']!=2)
		{
			require 'header.php';
			$legend = 'Невозможно добавить ответ';
			$text = 'Невозможно добавить ответ. Вы не являетесь администратором на этом ресурсе';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if(empty($_POST['answer']))
		{
			require 'header.php';
			$legend = 'Невозможно добавить ответ';
			$text = 'Невозможно добавить ответ. Строка ответа пуста';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		$ret = $faqC->response_to_question($id, $_POST['answer']);
		if($ret<0)
		{
			require 'header.php';
			$legend = 'Невозможно добавить ответ';
			$text = 'Невозможно добавить ответ. Возможно недоступна БД';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		else
		{
			require 'header.php';
			$legend = 'Ответ успешно добавлен';
			$text = 'Ответ успешно добавлен<br>Через три секунды вы будете перенаправлены на страницу faq.<br>Если вы не хотите ждать, нажмите <a href="faq">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'faq">');
		}
	}
	else
	{
		require 'header.php';
		$add_answer = 'add_answer_'.$id;
		$question = $faqC->get_question($id);
		$question_id = $question['id'];
		$question_subject = $question['subject'];
		$question_comment = $question['question'];
		require 'themes/'.$theme.'/templates/faq/add_answer.tpl.php';
		require 'footer.php';
		exit();
	}
}
else
{
	$title = ' - Часто задаваемые вопросы';
	require 'header.php';
	$add_question_link = 'add_question';
	require 'themes/'.$theme.'/templates/faq/top.tpl.php';
	$questions = $faqC->get_questions();
	for($i=0; $i<count($questions); $i++)
	{
		$id = $questions[$i]['id'];
		$subject = $questions[$i]['subject'];
		$question = $questions[$i]['question'];
		if($coreC->validate_boolean($questions[$i]['answered']))
			$answer = $questions[$i]['answer'];
		else
			$answer = 'ждите ответа';
		require 'themes/'.$theme.'/templates/faq/middle.tpl.php';
		if(!$coreC->validate_boolean($questions[$i]['answered']))
		{
			if($uinfo['gid']==2)
			{
				$add_answer_link = 'add_answer_'.$questions[$i]['id'];
				require 'themes/'.$theme.'/templates/faq/answer_link.tpl.php';
			}
		}
	}
	require 'themes/'.$theme.'/templates/faq/bottom.tpl.php';
	require 'footer.php';
}
?>