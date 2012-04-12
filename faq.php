<?php
require 'classes/core.php';
$rss_link = 'rss';
$action = !empty($_GET['action']) ? $_GET['action'] : null;
$errors = array();
$title = '';

/* Add question */
if ($action == 'add_question')
{
	$subject = $comment = $user_field = '';
	if (isset($_POST['submit_form']))
	{
		if (empty($_POST['subject']))
			$errors['subject'] = 'Не заполнено поле "Заголовок"';
		else
			$subject = $_POST['subject'];

		if (empty($_POST['comment']))
			$errors['comment'] = 'Не заполнен текст вопроса';
		else
			$comment = $_POST['comment'];

		if (!empty($_POST['user_field']))
		{
			$errors['user_field'] = 'Заполнено поле не требующее заполнения';
			$user_field = $_POST['user_field'];
		}

		if (($_SESSION['user_id'] == 1 || $usersC->get_captcha_level($_SESSION['user_id']) > -1) &&
		    (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring']))
			$errors['captcha'] = 'Неверно введен ответ с картинки';	
		$_SESSION['captcha_keystring'] = '';
		if (empty($errors))
		{
			$ret = $faqC->add_question($subject, $comment);
			if ($ret < 0)
			{
				$legend = 'Невозможно добавить вопрос';
				$text = 'Невозможно добавить вопрос. Возможно недоступна БД';
				$link = '';
			}
			else
			{
				$legend = 'Вопрос успешно добавлен';
				$text = 'Вопрос успешно добавлен<br>Через три секунды вы будете перенаправлены на страницу faq.<br>Если вы не хотите ждать, нажмите <a href="faq">сюда</a>.';
				$link = '<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'faq">';
			}
			require 'header.php';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die($link);
		}
		else
			$errors['msg'] = 'Вопрос не был отправлен, проверьте правильность заполнения формы';
	}
	$coreC->set_missing_array_keys($errors, array('msg', 'subject', 'comment', 'captcha', 'user_field'));
	if ($_SESSION['user_id'] == 1 || $usersC->get_captcha_level($_SESSION['user_id']) > -1)
		$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha" alt="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
	else
		$captcha = '';
	$title = ' - Добавить вопрос';
	require 'header.php';
	$add_question = 'add_question';
	require 'themes/'.$theme.'/templates/faq/add_question.tpl.php';
	require 'footer.php';
}
elseif ($action == 'add_answer')
{
	if (empty($_GET['id']))
		die('Question ID must be provided');
	$id = (int)$_GET['id'];
	$answer = '';
	if (isset($_POST['sbm']))
	{
		if ($uinfo['gid'] != 2)
			$errors['permission'] = 'Вы не являетесь администратором данного ресурса';
		
		if (empty($_POST['answer']))
			$errors['answer'] = 'Строка ответа пуста';
		else
			$answer = $_POST['answer'];
		
		if (empty($errors))
		{
			$ret = $faqC->response_to_question($id, $answer);
			if ($ret < 0)
			{
				$legend = 'Невозможно добавить ответ';
				$text = 'Невозможно добавить ответ. Возможно недоступна БД';
				$link = '';
			}
			else
			{
				$legend = 'Ответ успешно добавлен';
				$text = 'Ответ успешно добавлен<br>Через три секунды вы будете перенаправлены на страницу faq.<br>Если вы не хотите ждать, нажмите <a href="faq">сюда</a>.';
				$link = '<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'faq">';
			}
			require 'header.php';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die($link);
		}
		else
			$errors['msg'] = 'Вопрос не был отправлен, проверьте правильность заполнения формы';
	}
	$coreC->set_missing_array_keys($errors, array('msg', 'answer', 'captcha', 'user_field'));
	$add_answer = 'add_answer_'.$id;
	$question = $faqC->get_question($id);
	$question_id = $question['id'];
	$question_subject = $question['subject'];
	$question_comment = $question['question'];
	$title = ' - Добавить ответ';
	require 'header.php';
	require 'themes/'.$theme.'/templates/faq/add_answer.tpl.php';
	require 'footer.php';

}
else
{
	$title = ' - Часто задаваемые вопросы';
	$add_question_link = 'add_question';
	require 'header.php';
	require 'themes/'.$theme.'/templates/faq/top.tpl.php';
	$questions = $faqC->get_questions();
	if ($questions != -1)
	{
		for ($i = 0; $i < count($questions); $i++)
		{
			$id = $questions[$i]['id'];
			$subject = $questions[$i]['subject'];
			$question = $questions[$i]['question'];
			if ($coreC->validate_boolean($questions[$i]['answered']))
				$answer = $questions[$i]['answer'];
			else
				$answer = 'ждите ответа';
			require 'themes/'.$theme.'/templates/faq/middle.tpl.php';
			if (!$coreC->validate_boolean($questions[$i]['answered']))
			{
				if ($uinfo['gid'] == 2)
				{
					$add_answer_link = 'add_answer_'.$questions[$i]['id'];
					require 'themes/'.$theme.'/templates/faq/answer_link.tpl.php';
				}
			}
		}
	}
	require 'themes/'.$theme.'/templates/faq/bottom.tpl.php';
	require 'footer.php';
}
?>