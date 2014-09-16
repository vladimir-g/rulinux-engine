<?php
require 'classes/core.php';
$title = ' - Добавить материал';
$rss_link = 'rss';
if(empty($_GET['section']))
{
	require 'header.php';
	require 'themes/'.$theme.'/templates/add_content/add/sections.tpl.php';
	require 'footer.php';
	die();
}
$section_id = (int)$_GET['section'];
$errors = array();
$is_preview = false;

$subject = $comment = $user_field = $prooflink = $form_link = '';
$subsection_id = null;
$form_preview_link = 'new_thread_in_sect_'.$section_id;

$comment_errors = array(NEWS_SECTION_ID => 'Не заполнен текст новости',
			ARTICLES_SECTION_ID => 'Не заполнен текст статьи',
			GALLERY_SECTION_ID => 'Не заполнено поле "Описание"',
			FORUM_SECTION_ID => 'Не заполнено поле "Ваш комментарий"');

if (!empty($_POST['submit_form']))
{
	$valid_sections = array(NEWS_SECTION_ID, ARTICLES_SECTION_ID,
				GALLERY_SECTION_ID, FORUM_SECTION_ID);
	if (!in_array($section_id, $valid_sections))
		$section_id = FORUM_SECTION_ID;

	if (empty($_POST['subject']))
		$errors['subject'] = 'Не заполнено поле "Заголовок"';
	else
		$subject = $_POST['subject'];
	
	if (empty($_POST['comment']))
		$errors['comment'] = $comment_errors[$section_id];
	else
	{
		$comment = $_POST['comment'];
		$preview_comment = str_to_html($_POST['comment']);
	}
	
	if (!empty($_POST['user_field']))
	{
		$errors['user_field'] = 'Заполнено поле не требующее заполнения';
		$user_field = $_POST['user_field'];
	}

	if (empty($_POST['subsection_id']))
		$errors['subsection'] = 'Не указана подкатегория';
	else
		$subsection_id = (int)$_POST['subsection_id'];

	$prooflink = !empty($_POST['news_link']) ? $_POST['news_link'] : '';
	
	if ($section_id == GALLERY_SECTION_ID && $_POST['submit_form'] != 'Предпросмотр')
	{
		if (!empty($_FILES['scrot_link']['tmp_name']))
		{
			$blacklist = array(".php", ".phtml", ".php3", ".php4");
			foreach ($blacklist as $item)
			{
				if(preg_match("/$item\$/i", $_FILES['scrot_link']['name']))
					$errors['image_ext'] = 'Неверный тип файла';
			}
			$uploaddir = 'images/gallery/';
			preg_match('/^.+(\.jp[e]?g|\.png|\.gif)$/', basename($_FILES['scrot_link']['name']), $ext);
			$filename = md5(time().basename($_FILES['scrot_link']['name']));
			$ext[1] = substr(basename($_FILES['scrot_link']['name']), strlen(basename($_FILES['scrot_link']['name']))-4, 4);
			$ext[1] = str_replace('.', '', $ext[1]);
			$uploadfile = $uploaddir.$filename.'.'.$ext[1];
			$imageinfo = getimagesize($_FILES['scrot_link']['tmp_name']);
			if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg'  && $imageinfo['mime'] != 'image/png')
			{
				$errors['image_type'] = 'Неверный тип файла';
			}
			if(($_FILES['scrot_link']['size']/1000) > 700)
				$errors['image_size'] = 'Слишком большой размер файла';
			if(($imageinfo[0] < 400 || $imageinfo[0] > 2048) || ($imageinfo[1] < 400 || $imageinfo[1] > 2048))
				$errors['image_res'] = 'Неверное разрешение файла';
		}
		else
			$errors['image_empty'] = 'Файл не выбран';
	}

	if ($_POST['submit_form'] == 'Предпросмотр' && empty($errors))
	{
		$is_preview = true;
		$usr = $usersC->get_user_info($_SESSION['user_id']);
		$author = $usr['nick'];
		$author_profile = 'user_'.$usr['nick'];
		$timestamp = $coreC->to_local_time_zone(gmdate("Y-m-d H:i:s"));
		if (!$coreC->validate_boolean($usr['show_ua']))
			$useragent = '';
		else
			$useragent = $_SERVER['HTTP_USER_AGENT'];
	}
	elseif ($_POST['submit_form'] == 'Отправить')
	{
		if (!isset($_POST['keystring']))
			$_POST['keystring'] = null;
		if (!$captchaC->check($_POST['keystring'])) {
			$errors['captcha'] = 'Неверно введен ответ с картинки';
			$security->log_action('post');
		}
		if (!$security->is_allowed())
				$errors['captcha'] = 'Постинг временно заблокирован, так как '.
						'вы несколько раз неправильно ввели капчу. Подумайте над своим поведением.';
		$captchaC->reset();
		/* Add content */
		if (empty($errors))
		{
			if($section_id == GALLERY_SECTION_ID)
			{
				if (move_uploaded_file($_FILES['scrot_link']['tmp_name'], $uploadfile))
				{
					$coeff = $imageinfo[0]/200;
					$image_width = 200;
					@$image_height = floor($imageinfo[1]/$coeff);
					switch ($imageinfo[2])
					{
					case 1:
						$source = imagecreatefromgif($uploadfile);
						break;
					case 2:
						$source = imagecreatefromjpeg($uploadfile);
						break;
					case 3:
						$source = imagecreatefrompng($uploadfile);
						break;
					}
					$resource = imagecreatetruecolor($image_width, $image_height);
					imagecopyresampled($resource, $source, 0, 0, 0, 0, $image_width, $image_height, $imageinfo[0], $imageinfo[1]);
					imagepng($resource, $uploaddir.'/thumbs/'.$filename.'_small.png');
					$date = gmdate('d.m.Y H:i:s');
					$file = $filename;
					$extension = $ext[1];
					$file_size = $_FILES['scrot_link']['size'];
					$image_size = $imageinfo[0].'x'.$imageinfo[1];
				}
				else
				{
					require 'header.php';
					$legend = 'Не удалось загрузить файл.';
					$text = 'Не удалось загрузить файл.';
					require 'themes/'.$theme.'/templates/fieldset.tpl.php';
					require 'header.php';
					exit();
				}
			}
			else
			{
				$file = '';
				$extension = '';
				$file_size = 0;
				$image_size = '';
				$prooflink = isset($_POST['news_link']) ? $_POST['news_link'] : '';
			}
			$cid = $messagesC->new_thread($subject, $comment, $section_id, $subsection_id, $file, $extension, $file_size, $image_size, $prooflink);
			$filters_count = $filtersC->get_filters_count();
			$str = '';
			for ($i = 1; $i <= $filters_count; $i++)
			{
				if(!empty($_POST['filter_'.$i]))
					$str = $str.$i.':1;';
				else
					$str = $str.$i.':0;';
			}
			$str = $filtersC->set_auto_filter($cid, $str);
			$val = $messagesC->set_filter($cid, $str);
			/* Show success page */
			$legend = 'Тред успешно создан';
			if (in_array($section_id, array(NEWS_SECTION_ID, ARTICLES_SECTION_ID, GALLERY_SECTION_ID)))
			{
				$text = 'Тред успешно создан<br>Через три секунды вы будете перенаправлены в список неподтвержденного.<br>Если вы не хотите ждать, нажмите <a href="unconfirmed">сюда</a>.';
				$link = '<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'unconfirmed">';

			}
			elseif ($section_id == FORUM_SECTION_ID)
			{
				$thr_id = $threadsC->get_tid_by_cid($cid);
				$text = 'Тред успешно создан<br>Через три секунды вы будете перенаправлены в тред.<br>Если вы не хотите ждать, нажмите <a href="thread_'.$thr_id[0]['id'].'_page_1">сюда</a>.';
				$link = '<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'thread_'.$thr_id[0]['id'].'_page_1">';
			}
			require 'header.php';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die($link);			
		}
	}
	if (!empty($errors))
		$errors['msg'] = 'Сообщение не было отправлено, проверьте правильность заполнения формы';
	$subject = $coreC->html_escape($subject);
	$prooflink = $coreC->html_escape($prooflink);
}

/* Fill errors array with empty strings to prevent php notices */
$coreC->set_missing_array_keys($errors, array('msg', 'subject', 'comment', 'user_field', 'captcha', 'image_ext', 
					      'image_type', 'image_size', 'image_res', 'image_empty', 'subsection'));
require 'header.php';
if ($_SESSION['user_id'] == 1 || $usersC->get_captcha_level($_SESSION['user_id']) > -1)
	$captcha = '<img src="ucaptcha/index.php?'.session_name().'='.session_id().'" id="captcha" alt="captcha"><br>Введите символы либо ответ (если на картинке задача):<br><input type="text" name="keystring"><br>';
else
	$captcha = '';

$subsect = $sectionsC->get_subsections($section_id);
switch ($section_id)
{
case NEWS_SECTION_ID:
	if ($subsection_id !== null)
		$subsection_image = '/themes/'.$theme.'/icons/'.$sectionsC->get_subsection_icon($subsection_id);
	$tpl = 'news';
	break;
case ARTICLES_SECTION_ID:
	$tpl = 'articles';
	break;
case GALLERY_SECTION_ID:
	$tpl = 'gallery';
	break;
case FORUM_SECTION_ID:
	$tpl = 'forum';
	break;
default:
	$legend = 'Такой категории не существует';
	$text = 'Такой категории не существует';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
	exit();
}
/* Include templates */
require 'themes/'.$theme.'/templates/add_content/add/'.$tpl.'_top.tpl.php';
if(empty($_GET['subsection']))
{
	require 'themes/'.$theme.'/templates/add_content/add/select_top.tpl.php';
	for($i=0; $i<count($subsect); $i++)
	{
		$subsect_id = $subsect[$i]['sort'];
		$subsection_name = $subsect[$i]['name'];
		if ($i == $subsection_id - 1)
			$selected = 'selected';
		else
			$selected = '';
		require 'themes/'.$theme.'/templates/add_content/add/select_middle.tpl.php';
	}
	require 'themes/'.$theme.'/templates/add_content/add/select_bottom.tpl.php';
}
else
{
	$subsect_id = $subsect[(int)$_GET['subsection'] - 1]['sort'];
	require 'themes/'.$theme.'/templates/add_content/add/select.tpl.php';
}
require 'themes/'.$theme.'/templates/add_content/add/'.$tpl.'_middle.tpl.php';
$filters_arr = $filtersC->get_filters();
for($i=0; $i<count($filters_arr);$i++)
{
	$filterN = $filters_arr[$i]['id'];
	$filter_name = $filters_arr[$i]['name'];
	$num = $i + 1;
	if (!empty($_POST['filter_'.$num]))
		$checked_filter = 'checked';
	else
		$checked_filter = '';
	require 'themes/'.$theme.'/templates/add_content/filters.tpl.php';
}
require 'themes/'.$theme.'/templates/add_content/add/'.$tpl.'_bottom.tpl.php';
require 'footer.php';
?>