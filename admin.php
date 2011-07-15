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
		
	}
	elseif($_GET['set']=='remove_block')
	{
		
	}
	else
	{
		require 'header.php';
		require 'themes/'.$theme.'/templates/admin/manage_blocks/main.tpl.php';
		require 'footer.php';
		exit();
	}
}
elseif($_GET['action']=='manage_themes_ui')
{
	
	
}
elseif($_GET['action']=='manage_subsections_ui')
{
	
	
}
elseif($_GET['action']=='edit_settings_ui')
{
	if($_GET['set']=='change_pass')
	{
		require 'header.php';
		$pass = admin::get_setting('register_pass_phrase');
		require 'themes/'.$theme.'/templates/admin/edit_settings/change_pass.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='change_letter')
	{
		require 'header.php';
		$subj = admin::get_setting('appect_mail_subject');
		$text = admin::get_setting('appect_mail_text');
		require 'themes/'.$theme.'/templates/admin/edit_settings/change_letter.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='change_rules')
	{
		require 'header.php';
		$rules = admin::get_setting('rules');
		require 'themes/'.$theme.'/templates/admin/edit_settings/change_rules.tpl.php';
		require 'footer.php';
		exit();
	}
	elseif($_GET['set']=='change_title')
	{
		require 'header.php';
		$title = admin::get_setting('title');
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
		$ret = admin::set_setting('register_pass_phrase', $_POST['password']);
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
		$ret = admin::set_setting('appect_mail_subject', $_POST['subject']);
		if($ret>0)
		{
			$ret = admin::set_setting('appect_mail_text', $_POST['text']);
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
		$ret = admin::set_setting('rules', $_POST['rules']);
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
		$ret = admin::set_setting('title', $_POST['title']);
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
		$ret = admin::remove_message($_POST['message']);
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
		$ret = admin::remove_thread($_POST['thread']);
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