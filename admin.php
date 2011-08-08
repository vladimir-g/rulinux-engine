<?php
require 'classes/core.php';
require 'classes/admin.class.php';
$title = ' - Админка';
$rss_link='view-rss.php';
if($uinfo['gid']!=2)
{
	require 'header.php';
	$legend = 'У вас нет полномочий';
	$text = 'Вы не являетесь администратором на данном сайте';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
	exit();
}

if($_GET['action']=='manage_blocks_ui')
{
	if($_GET['set']=='install_block')
	{	
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_blocks/install_block.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='remove_block')
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_blocks/remove_block_top.tpl.php';
		$blocks = $coreC->get_blocks();
		for($i=0; $i<count($blocks);$i++)
		{
			$id = $blocks[$i]['id'];
			$name = $blocks[$i]['name'];
			$description = $blocks[$i]['description'];
			$directory = $blocks[$i]['directory'];
			require 'themes/'.$theme.'/templates/admin/manage_blocks/remove_block_middle.tpl.php';
		}
		require 'themes/'.$theme.'/templates/admin/manage_blocks/remove_block_bottom.tpl.php';
		require 'footer.php';
		exit();
	}
	else
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_blocks/main.tpl.php';
		require 'footer.php';
		exit();
	}
}
if($_GET['action']=='install_block')
{
	if ($_FILES['file']['size'] > 0)
	{
		$blacklist = array(".php", ".phtml", ".php3", ".php4");
		foreach ($blacklist as $item) 
		{
			if(preg_match("/$item\$/i", $_FILES['file']['name'])) 
			{
				$error = 'block_error';
			}
		}
		if($_FILES['file']['type']!='application/zip')
			$error = 'mime type is incorrect';
		$uploaddir = 'tmp/';
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$uploadfile = $uploaddir.$hash.'.blk';
		if (empty($error))
			move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
		else
			echo 'error '.$error;
		$ret = $adminC->install_block($uploadfile);
		unlink($uploadfile);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_blocks_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка установки блока';
			$text = 'Не получилось установить блок. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
}
if($_GET['action']=='remove_block')
{
	$error = 0;
	foreach( $_POST as $key => $value )
	{
		if(preg_match('/check_([0-9]*)/', $key, $matches))
		{
			$ret = $adminC->remove_block($_POST['directory_'.$matches[1]]);
			if($ret!=1)
				$error = 1;
		}
	}
	if($error)
	{
		require 'header.php';
		$legend = 'Ошибка удаления блока';
		$text = 'Не получилось удалить блок. Возможно недоступна база данных';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	else
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_blocks_ui">');  
}
elseif($_GET['action']=='manage_filters_ui')
{
	if($_GET['set']=='install_filter')
	{	
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_filters/install_filter.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='remove_filter')
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_filters/remove_filter_top.tpl.php';
		$filters = $filtersC->get_filters();
		for($i=0; $i<count($filters);$i++)
		{
			$id = $filters[$i]['id'];
			$name = $filters[$i]['name'];
			$description = $filters[$i]['text'];
			$directory = $filters[$i]['directory'];
			require 'themes/'.$theme.'/templates/admin/manage_filters/remove_filter_middle.tpl.php';
		}
		require 'themes/'.$theme.'/templates/admin/manage_filters/remove_filter_bottom.tpl.php';
		require 'footer.php';
		exit();
	}
	else
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_filters/main.tpl.php';
		require 'footer.php';
		exit();
	}
}
if($_GET['action']=='install_filter')
{
	if ($_FILES['file']['size'] > 0)
	{
		$blacklist = array(".php", ".phtml", ".php3", ".php4");
		foreach ($blacklist as $item) 
		{
			if(preg_match("/$item\$/i", $_FILES['file']['name'])) 
			{
				$error = 'theme_error';
			}
		}
		if($_FILES['file']['type']!='application/zip')
			$error = 'mime type is incorrect';
		$uploaddir = 'tmp/';
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$uploadfile = $uploaddir.$hash.'.fltr';
		if (empty($error))
			move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
		else
			echo 'error '.$error;
		$ret = $adminC->install_filter($uploadfile);
		unlink($uploadfile);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_filters_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка установки фильтра';
			$text = 'Не получилось установить фильтр. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
}
if($_GET['action']=='remove_filter')
{
	$error = 0;
	foreach( $_POST as $key => $value )
	{
		if(preg_match('/check_([0-9]*)/', $key, $matches))
		{
			$ret = $adminC->remove_filter($_POST['directory_'.$matches[1]]);
			if($ret!=1)
				$error = 1;
		}
	}
	if($error)
	{
		require 'header.php';
		$legend = 'Ошибка удаления фильтра';
		$text = 'Не получилось удалить фильтр. Возможно недоступна база данных';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	else
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_filters_ui">');  
}
elseif($_GET['action']=='manage_themes_ui')
{
	if($_GET['set']=='install_theme')
	{	
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_themes/install_theme.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='remove_theme')
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_themes/remove_theme_top.tpl.php';
		$themes = $coreC->get_themes();
		for($i=0; $i<count($themes);$i++)
		{
			$id = $themes[$i]['id'];
			$name = $themes[$i]['name'];
			$description = $themes[$i]['description'];
			$directory = $themes[$i]['directory'];
			require 'themes/'.$theme.'/templates/admin/manage_themes/remove_theme_middle.tpl.php';
		}
		require 'themes/'.$theme.'/templates/admin/manage_themes/remove_theme_bottom.tpl.php';
		require 'footer.php';
		exit();
	}
	else
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_themes/main.tpl.php';
		require 'footer.php';
		exit();
	}
}
if($_GET['action']=='install_theme')
{
	if ($_FILES['file']['size'] > 0)
	{
		$blacklist = array(".php", ".phtml", ".php3", ".php4");
		foreach ($blacklist as $item) 
		{
			if(preg_match("/$item\$/i", $_FILES['file']['name'])) 
			{
				$error = 'theme_error';
			}
		}
		if($_FILES['file']['type']!='application/zip')
			$error = 'mime type is incorrect';
		$uploaddir = 'tmp/';
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$uploadfile = $uploaddir.$hash.'.thm';
		if (empty($error))
			move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
		else
			echo 'error '.$error;
		$ret = $adminC->install_theme($uploadfile);
		unlink($uploadfile);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_themes_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка установки темы оформления';
			$text = 'Не получилось установить тему оформления. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
}
if($_GET['action']=='remove_theme')
{
	$error = 0;
	foreach( $_POST as $key => $value )
	{
		if(preg_match('/check_([0-9]*)/', $key, $matches))
		{
			$count = core::get_themes_count();
			$ret = $adminC->remove_theme($_POST['directory_'.$matches[1]], $count);
			if($ret!=1)
				$error = 1;
		}
	}
	if($error)
	{
		require 'header.php';
		$legend = 'Ошибка удаления темы оформления';
		$text = 'Не получилось удалить тему оформления. Возможно недоступна база данных';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	else
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_themes_ui">');  
}
elseif($_GET['action']=='manage_subsections_ui')
{
	if($_GET['set']=='add_subsection')
	{	
		if(empty($_GET['section']))
		{
			require 'header.php';
			$action = 'add_subsection';
			require 'themes/'.$theme.'/templates/admin/manage_subsections/sections.tpl.php';
			require 'footer.php';
			exit();
		}
		else
		{
			$section = (int)$_GET['section'];
			if(preg_match('/[1-4]{1}/', $section))
			{
				if($section == 1)
				{
					require 'header.php';
					require 'themes/'.$theme.'/templates/admin/manage_subsections/add_news_subsection.tpl.php';
					require 'footer.php';
					exit();
				}
				elseif($section == 4)
				{
					require 'header.php';
					require 'themes/'.$theme.'/templates/admin/manage_subsections/add_forum_subsection.tpl.php';
					require 'footer.php';
					exit();
				}
				else
				{
					require 'header.php';
					require 'themes/'.$theme.'/templates/admin/manage_subsections/add_other_subsection.tpl.php';
					require 'footer.php';
					exit();
				}
			}
			else
			{
				require 'header.php';
				$legend = 'Неизвестный раздел';
				$text = 'Раздела с ID = '.$section.' не существует';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				require 'footer.php';
				exit();
			}
		}
	}
	elseif($_GET['set']=='remove_subsection')
	{
		$section = (int)$_GET['section'];
		if(preg_match('/[1-4]{1}/', $section))
		{
			require 'header.php';
			require 'themes/'.$theme.'/templates/admin/manage_subsections/remove_subsection_top.tpl.php';
			$subsections = $sectionsC->get_subsections($section);
			for($i=0; $i<count($subsections); $i++)
			{
				$id = $subsections[$i]['id'];
				$name = $subsections[$i]['name'];
				$description = $subsections[$i]['description'];
				$rewrite = $subsections[$i]['rewrite'];
				require 'themes/'.$theme.'/templates/admin/manage_subsections/remove_subsection_middle.tpl.php';
			}
			require 'themes/'.$theme.'/templates/admin/manage_subsections/remove_subsection_bottom.tpl.php';
			require 'footer.php';
			exit();
		}
		else
		{
			require 'header.php';
			$action = 'remove_subsection';
			require 'themes/'.$theme.'/templates/admin/manage_subsections/sections.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_subsections/main.tpl.php';
		require 'footer.php';
		exit();
	}
}
elseif($_GET['action']=='add_subsection')
{
	$section = (int)$_POST['section'];
	$name = $_POST['name'];
	$description = $_POST['description'];
	$rewrite = $_POST['rewrite'];
	$shortfaq = $_POST['shortfaq'];
	if ($_FILES['icon']['size'] > 0)
	{
		$blacklist = array(".php", ".phtml", ".php3", ".php4");
		foreach ($blacklist as $item) 
		{
			if(preg_match("/$item\$/i", $_FILES['icon']['name'])) 
			{
				$error = 'subsection_error';
			}
		}
		if($_FILES['icon']['type']!='image/png')
			$error = 'mime type is incorrect';
		$imageinfo = getimagesize($_FILES['icon']['tmp_name']);
		if(($imageinfo[0] < 16 || $imageinfo[0] > 128) || ($imageinfo[1] < 16 || $imageinfo[1] > 128))
				$error = 'Неверное разрешение изображения';
		$uploaddir = 'tmp/';
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$uploadfile = $uploaddir.$hash.'.png';
		if (empty($error))
			move_uploaded_file($_FILES['icon']['tmp_name'], $uploadfile);
		else
		{
			echo 'error '.$error;
			exit();
		}
		$icon = $hash.'.png';
	}
	else
		$icon = '';
	$themes = core::get_themes();
	$ret = $adminC->add_subsection($section, $name, $description, $shortfaq, $rewrite, $icon, $themes);
	if(!is_file($uploadfile))
		unlink($uploadfile);
	if($ret>0)
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_subsections_ui">');  
	else
	{
		require 'header.php';
		$legend = 'Ошибка добавления подраздела';
		$text = 'Не получилось добавить подраздел. Возможно недоступна база данных';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	exit();
}
elseif($_GET['action']=='remove_subsection')
{
	$error = 0;
	foreach( $_POST as $key => $value )
	{
		if(preg_match('/check_([0-9]*)/', $key, $matches))
		{
			$ret = $adminC->remove_subsection($matches[1]);
			if($ret!=1)
				$error = 1;
		}
	}
	if($error)
	{
		require 'header.php';
		$legend = 'Ошибка удаления подраздела';
		$text = 'Не получилось удалить подраздел. Возможно недоступна база данных';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	else
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_subsections_ui">');  
}
if($_GET['action']=='manage_marks_ui')
{
	if($_GET['set']=='install_mark')
	{	
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_marks/install_mark.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='remove_mark')
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_marks/remove_mark_top.tpl.php';
		$marks = $markC->get_marks();
		for($i=0; $i<count($marks);$i++)
		{
			$id = $marks[$i]['id'];
			$name = $marks[$i]['name'];
			$description = htmlspecialchars(substr ($marks[$i]['description'], 0 , 50)).'...';
			
			$file = $marks[$i]['file'];
			require 'themes/'.$theme.'/templates/admin/manage_marks/remove_mark_middle.tpl.php';
		}
		require 'themes/'.$theme.'/templates/admin/manage_marks/remove_mark_bottom.tpl.php';
		require 'footer.php';
		exit();
	}
	else
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_marks/main.tpl.php';
		require 'footer.php';
		exit();
	}
}
if($_GET['action']=='install_mark')
{
	if ($_FILES['file']['size'] > 0)
	{
		$blacklist = array(".php", ".phtml", ".php3", ".php4");
		foreach ($blacklist as $item) 
		{
			if(preg_match("/$item\$/i", $_FILES['file']['name'])) 
			{
				$error = 'mark_error';
			}
		}
		if($_FILES['file']['type']!='application/zip')
			$error = 'mime type is incorrect';
		$uploaddir = 'tmp/';
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$uploadfile = $uploaddir.$hash.'.mrk';
		if (empty($error))
			move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
		else
			echo 'error '.$error;
		$ret = $adminC->install_mark($uploadfile);
		unlink($uploadfile);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_marks_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка установки стиля разметки';
			$text = 'Не получилось установить стиль разметки. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
}
if($_GET['action']=='remove_mark')
{
	$error = 0;
	foreach( $_POST as $key => $value )
	{
		if(preg_match('/check_([0-9]*)/', $key, $matches))
		{
			$count = mark::get_marks_count();
			$ret = $adminC->remove_mark($_POST['file_'.$matches[1]], $count);
			if($ret!=1)
				$error = 1;
		}
	}
	if($error)
	{
		require 'header.php';
		$legend = 'Ошибка удаления стиля разметки';
		$text = 'Не получилось удалить стиль разметки. Возможно недоступна база данных';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	else
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=manage_marks_ui">');  
}
elseif($_GET['action']=='edit_settings_ui')
{
	if($_GET['set']=='change_pass')
	{
		require 'header.php';
		$pass = $adminC->get_setting('register_pass_phrase');
		require 'themes/'.$theme.'/templates/admin/edit_settings/change_pass.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='change_letter')
	{
		require 'header.php';
		$subj = $adminC->get_setting('appect_mail_subject');
		$text = $adminC->get_setting('appect_mail_text');
		require 'themes/'.$theme.'/templates/admin/edit_settings/change_letter.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='change_rules')
	{
		require 'header.php';
		$rules = $adminC->get_setting('rules');
		require 'themes/'.$theme.'/templates/admin/edit_settings/change_rules.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='change_title')
	{
		require 'header.php';
		$title = $adminC->get_setting('title');
		require 'themes/'.$theme.'/templates/admin/edit_settings/change_title.tpl.php';
		require 'footer.php';
		exit();
	}
	else
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/edit_settings/main.tpl.php';
		require 'footer.php';
		exit();
	}
	
}
elseif($_GET['action']=='edit_settings')
{
	if($_POST['set']=='change_pass')
	{
		$ret = $adminC->set_setting('register_pass_phrase', $_POST['password']);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=edit_settings_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка смены парольной фразы';
			$text = 'Не получилось сменить регистрационный пароль. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	elseif($_POST['set']=='change_letter')
	{
		$ret = $adminC->set_setting('appect_mail_subject', $_POST['subject']);
		if($ret>0)
		{
			$ret = $adminC->set_setting('appect_mail_text', $_POST['text']);
			if($ret>0)
				die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=edit_settings_ui">');  
			else
			{
				require 'header.php';
				$legend = 'Ошибка смены текста регистрационного письма';
				$text = 'Не получилось сменить регистрационное письмо. Возможно недоступна база данных';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				require 'footer.php';
				exit();
			}
		}
		else
		{
			require 'header.php';
			$legend = 'Ошибка смены текста регистрационного письма';
			$text = 'Не получилось сменить регистрационное письмо. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	elseif($_POST['set']=='change_rules')
	{
		$ret = $adminC->set_setting('rules', $_POST['rules']);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=edit_settings_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка смены правил сайта';
			$text = 'Не получилось сменить текст правил. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	elseif($_POST['set']=='change_title')
	{
		$ret = $adminC->set_setting('title', $_POST['title']);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=edit_settings_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка смены заголовка сайта';
			$text = 'Не получилось сменить заголовок сайта. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$legend = 'Неизвестное действие';
		$text = 'Параметр set содержит неизвестное значение. Выполнение невозможно';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
elseif($_GET['action']=='remove_message_ui')
{
	require 'header.php';
	require 'themes/'.$theme.'/templates/admin/remove_message.tpl.php';
	require 'footer.php';
	exit();
}
elseif($_GET['action']=='remove_message')
{
	if(empty($_POST['message']))
	{
		require 'header.php';
		$legend = 'Укажите нужное сообщение';
		$text = 'Строка с ID сообщения или его URL пуста. Пожалуйста укажите сообщение для удаления';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	else
	{
		$ret = $adminC->remove_message($_POST['message']);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=remove_message_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка удаления сообщения';
			$text = 'Не получилось удалить сообщение. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
}
elseif($_GET['action']=='remove_thread_ui')
{
	require 'header.php';
	require 'themes/'.$theme.'/templates/admin/remove_thread.tpl.php';
	require 'footer.php';
	exit();
}
elseif($_GET['action']=='remove_thread')
{
	if(empty($_POST['thread']))
	{
		require 'header.php';
		$legend = 'Укажите нужный тред';
		$text = 'Строка с ID терда или его URL пуста. Пожалуйста укажите тред для удаления';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	else
	{
		$ret = $adminC->remove_thread($_POST['thread']);
		if($ret>0)
			die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'admin.php?action=remove_thread_ui">');  
		else
		{
			require 'header.php';
			$legend = 'Ошибка удаления треда';
			$text = 'Не получилось удалить тред. Возможно недоступна база данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
}
else
{
	require 'header.php';
	require 'themes/'.$theme.'/templates/admin/main.tpl.php';
	require 'footer.php';
	exit();
}

?>