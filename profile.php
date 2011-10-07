<?php
require 'classes/core.php';
if(!empty($_GET['user']))
	$user = $_GET['user'];
else
	$user = $_SESSION['user_name'];
$title = ' - Профиль пользователя '.$user;
$rss_link='rss';
$uid = $usersC->get_uid_by_nick($user);
$usr = $usersC->get_user_info($uid);
$edit_link = 'user_'.$user.':edit';
if($_POST['action']=="pass")
{
	if($uid == $uinfo['id'] || $uinfo['gid']==2)
	{
		if($uid==1)
		{
			require 'header.php';
			$legend = 'Невозможно выполнить действие';
			$text = 'Вы не можете сменить пароль для пользователя anonymous';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if(empty($_POST['old_pass']))
		{
			require 'header.php';
			$legend = 'Введите старый пароль';
			$text = 'Введите старый пароль';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if(empty($_POST['new_pass']))
		{
			require 'header.php';
			$legend = 'Введите новый пароль';
			$text = 'Введите новый пароль';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if(empty($_POST['new_pass_retype']))
		{
			require 'header.php';
			$legend = 'Введите подтверждение нового пароля';
			$text = 'Введите подтверждение нового пароля';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if($_POST['new_pass'] != $_POST['new_pass_retype'])
		{
			require 'header.php';
			$legend = 'Неверно введен пароль';
			$text = 'Строка нового пароля не соответствует строке подтверждения';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if(md5($_POST['old_pass']) != $usr['password'])
		{
			require 'header.php';
			$legend = 'Неверно введен старый пароль';
			$text = 'Неверно введен старый пароль';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		$ret = $usersC->modify_user_info('password', md5($_POST['new_pass']), $uid);
		if($ret == 1)
		{
			require 'header.php';
			$legend = 'Пароль успешно изменен';
			$text = 'Пароль успешно изменен<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="user_'.$user.':edit">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'user_'.$user.':edit">');  
		}
		else
		{
			require 'header.php';
			$legend = 'Произошла ошибка при смене пароля';
			$text = 'Произошла ошибка при смене пароля. Возможно это связано с ошибками при обращении к базе данных';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$legend = 'Вы не можете сменить пароль этому пользователю';
		$text = 'У вас нет полномочий для смены пароля данного пользователя';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
else if($_POST['action']=="info")
{	
	if($uid==1)
	{
		require 'header.php';
		$legend = 'Вы не можете сменить информацию о пользователе anonymous';
		$text = 'Вы не можете сменить информацию о пользователе anonymous';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	if($uid == $uinfo['id'] || $uinfo['gid']==2)
	{
		if ($_FILES['user_photo']['size'] > 0)
		{
			$blacklist = array(".php", ".phtml", ".php3", ".php4");
			foreach ($blacklist as $item) 
			{
				if(preg_match("/$item\$/i", $_FILES['user_photo']['name'])) 
				{
					$error = 'photo_error';
				}
			}
			$uploaddir = 'images/avatars/';
			preg_match('/^.+(\.jp[e]?g|\.png|\.gif)$/', basename($_FILES['user_photo']['name']), $ext);
			$filename = $uinfo['nick'];
			$ext[1] = substr(basename($_FILES['user_photo']['name']), strlen(basename($_FILES['user_photo']['name']))-4, 4);
			$ext[1] = str_replace('.', '', $ext[1]);
			$uploadfile = $uploaddir.$filename.'.'.$ext[1];
			if(file_exists('./'.$uploadfile)) unlink('./'.$uploadfile);
			$imageinfo = getimagesize($_FILES['user_photo']['tmp_name']);
			if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg'  && $imageinfo['mime'] != 'image/png') 
			{
				$error = 'photo_error';
			}
			if(($_FILES['user_photo']['size']/1000) > 30)
				$error = 'Слишком большой размер файла';
			if(($imageinfo[0] < 50 || $imageinfo[0] > 150) || ($imageinfo[1] < 50 || $imageinfo[1] > 150))
				$error = 'Ошибка загрузки файла';
			if (empty($error))
			{
				move_uploaded_file($_FILES['user_photo']['tmp_name'], $uploadfile);
				$val = $usersC->modify_user_info('photo', $filename.'.'.$ext[1], $uid);
				if($val != 1)
				{
					require 'header.php';
					$legend = 'Произошла ошибка при смене информации';
					$text = 'Произошла ошибка при смене информации';
					require 'themes/'.$theme.'/templates/fieldset.tpl.php';
					require 'footer.php';
					exit();
				}
			}
		}
		$val = $usersC->modify_user_info_settings($uid, $_POST['user_name'], $_POST['user_lastname'], $_POST['gender'], $_POST['user_email'], $_POST['showEmail'], $_POST['user_im'], $_POST['showIM'], $_POST['user_country'], $_POST['user_city'], $_POST['user_additional']);
		require 'header.php';
		$legend = 'Пользовательская информация успешно изменена';
		$text = 'Пользовательская информация успешно изменена<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="user_'.$user.':edit">сюда</a>.';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/user_'.$user.':edit">');  
	}
	else
	{
		require 'header.php';
		$legend = 'У вас нет полномочий для смены информации о данном пользователе';
		$text = 'У вас нет полномочий для смены информации о данном пользователе';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
else if($_POST['action']=="filters")
{
	if($uid == $uinfo['id'] || $uinfo['gid']==2|| $uinfo['gid']==3)
	{
		for($i=1; $i<=$_POST['filters_count']; $i++)
		{
			if(!empty($_POST['filter_'.$i]))
				$str = $str.$i.':1;';
			else
				$str = $str.$i.':0;';
		}
		$ret = $usersC->set_filter($uid, $str);
		if($ret == 1)
		{
			require 'header.php';
			$legend = 'Настройки фильтров успешно изменены';
			$text = 'Настройки фильтров успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="user_'.$user.':edit">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'user_'.$user.':edit">');  
		}
		else
		{
			require 'header.php';
			$legend = 'Произошла ошибка при смене настроек фильтров';
			$text = 'Произошла ошибка при смене настроек фильтров';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$legend = 'У вас нет полномочий для смены настроек фильтров';
		$text = 'У вас нет полномочий для смены настроек фильтров';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
else if($_POST['action']=="read")
{
	if($uid == $uinfo['id'] || $uinfo['gid']==2|| $uinfo['gid']==3)
	{
		if($_POST['user-gmt'] != "none")
		{
			$val = $usersC->modify_user_info('gmt', htmlspecialchars($_POST['user-gmt']), $uid);
			if($val != 1)
			{
				require 'header.php';
				$legend = 'Произошла ошибка при смене часового пояса';
				$text = 'Произошла ошибка при смене часового пояса';
				require 'themes/'.$theme.'/templates/fieldset.tpl.php';
				require 'footer.php';
				exit();
			}
		}
	
		$ret = $usersC->modify_user_read_settings($uid, $_POST['theme'], $_POST['news_on_page'], $_POST['comments_on_page'], $_POST['threads_on_page'], $_POST['show_photos'], $_POST['show_ua'], $_POST['sort_to'], $_POST['show_resp'], $_POST['mark']);
		if($ret >=0)
		{
			require 'header.php';
			$legend = 'Настройки чтения успешно изменены';
			$text = 'Настройки чтения успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="user_'.$user.':edit">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'user_'.$user.':edit">');  
		}
		else
		{
			require 'header.php';
			$legend = 'Произошла ошибка при смене настроек чтения';
			$text = 'Произошла ошибка при смене настроек чтения';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$legend = 'У вас нет полномочий для смены настроек чтения';
		$text = 'У вас нет полномочий для смены настроек чтения';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
else if($_POST['action']=="moder")
{
	if($uinfo['gid']==2|| $uinfo['gid']==3)
	{
		if($uid==1)
		{
			require 'header.php';
			$legend = 'Действие запрещено';
			$text = 'Вы не можете банить пользователя anonymous или выставлять ему уровень каптчи';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		$banned = (int)$_POST['banned'];
		$ban_ret = $usersC->ban_user($uid, $banned);
		$value = (int)$_POST['captcha'];
		$cpt_ret = $usersC->modify_user_info('captcha', $value, $uid);
		if($ban_ret >=0 || $cpt_ret >=0)
		{
			require 'header.php';
			$legend = 'Модераторские настройки успешно изменены';
			$text = 'Модераторские настройки успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="user_'.$user.':edit">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'user_'.$user.':edit">');  
		}
		else
		{
			require 'header.php';
			$legend = 'Произошла ошибка при смене модераторских настроек';
			$text = 'Произошла ошибка при смене модераторских настроек';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$legend = 'Действие запрещено';
		$text = 'Вы не имеете полномочий для изменения модераторских настроек';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
else if($_POST['action']=="admin")
{
	if($uid==1)
	{
		require 'header.php';
		$legend = 'Действие запрещено';
		$text = 'Вы не можете менять настройки пользователя anonymous';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	if($uinfo['gid']==2)
	{
		$group = (int)$_POST['group'];
		$ret = $usersC->modify_user_info('gid', $group, $uid);
		if($ret >=0)
		{
			require 'header.php';
			$legend = 'Администраторские настройки успешно изменены';
			$text = 'Администраторские настройки успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="user_'.$user.':edit">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'user_'.$user.':edit">');  
		}
		else
		{
			require 'header.php';
			$legend = 'Произошла ошибка при смене администраторских настроек';
			$text = 'Произошла ошибка при смене администраторских настроек';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$legend = 'Действие запрещено';
		$text = 'Вы не имеете полномочий для изменения администраторских настроек';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
else if($_POST['action']=="main_page")
{
	if($uid == $uinfo['id'] || $uinfo['gid']==2)
	{
		$blocks_str = '';
		for($i=0; $i<(int)$_POST['count'];$i++)
		{
			$name = $i.'_name';
			$position = $i.'_position';
			$sort = $i.'_sort';
			$blocks_str = $blocks_str.$_POST[$name].':'.$_POST[$position].':'.$_POST[$sort].',';
		}
		$blocks_str = substr($blocks_str, 0, strlen($blocks_str)-1);
		$ret = $usersC->modify_user_info('blocks', $blocks_str, $uid);
		if($ret >=0)
		{
			require 'header.php';
			$legend = 'Вид главной страницы успешно изменен';
			$text = 'Вид главной страницы успешно изменен<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="user_'.$user.':edit">сюда</a>.';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit">');  
		}
		else
		{
			require 'header.php';
			$legend = 'Произошла ошибка при смене вида главной страницы';
			$text = 'Произошла ошибка при смене вида главной страницы';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
	}
	else
	{
		require 'header.php';
		$legend = 'Действие запрещено';
		$text = 'Вы не имеете полномочий для изменения вида главной страницы';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
else
{
	require 'header.php';
	if($_GET['edit']!=1)
	{
		$usr_add = $usersC->get_additional_user_info($uid);
		$name = $usr['name'];
		$lastname = $usr['lastname'];
		$gender = $coreC->validate_boolean($usr['gender']) ? 'Мужской' : 'Женский';
		$birthday = $usr['birthday'];
		$photo = $coreC->validate_boolean($uinfo['show_avatars'], 'FILTER_VALIDATE_FAILURE') == 0 || empty($usr['photo']) ? 'themes/'.$theme.'/empty.gif' : 'images/avatars/'.$usr['photo'];
		if($uinfo['gid']==3 || $uinfo['gid']==2)
			$email = $usr['email'];
		else
			$email = $coreC->validate_boolean($usr['show_email']) ? $usr['email'] : 'скрыт';
		if($uinfo['gid']==3 || $uinfo['gid']==2)
			$im = $usr['im'];
		else
			$im = $coreC->validate_boolean($usr['show_im']) ? $usr['im'] : 'скрыт';
		$country = $usr['country'];
		$city = $usr['city'];
		if($usr['gid']==3)
			$status = 'Модератор';
		else if($usr['gid']==2)
			$status = 'Администратор';
		else
			$status = 'Пользователь';
		$status = $coreC->validate_boolean($usr['banned']) ? $status.", Заблокирован" : $status.", Разблокирован";
		$register_date = $coreC->to_local_time_zone($usr['register_date']);
		$last_login = $coreC->to_local_time_zone($usr['last_visit']);
		$additional = $usr['additional'];
		$first_topic_date = $coreC->to_local_time_zone($usr_add['first_topic_date']);
		$last_topic_date = $coreC->to_local_time_zone($usr_add['last_topic_date']);
		$first_comment_date = $coreC->to_local_time_zone($usr_add['first_comment_date']);
		$last_comment_date = $coreC->to_local_time_zone($usr_add['last_comment_date']);
		$comments_count = $usr_add['comments_count'];
		$topics_count = $usr_add['topics_count'];
		$link = 'comments_'.$user;
		require 'themes/'.$theme.'/templates/profile/middle.tpl.php';
		require 'themes/'.$theme.'/templates/profile/form.tpl.php';
	}
	else
	{
		if($uid == 1 && $uinfo['id']!= 1)
		{
			$legend = 'Действие запрещено';
			$text = 'Вы не можете сменить настройки для пользователя anonymous';
			require 'themes/'.$theme.'/templates/fieldset.tpl.php';
			require 'footer.php';
			exit();
		}
		if($uid == $uinfo['id'] || $uinfo['gid']==2)
		{
			if($uid!=1)
			{
				require 'themes/'.$theme.'/templates/profile/edit.tpl.php';
				require 'themes/'.$theme.'/templates/profile/password_edit/password_edit.tpl.php';
				$name = $usr['name'];
				$lastname = $usr['lastname'];
				$avatar = empty($usr['photo']) ? 'themes/'.$theme.'/empty.gif' : 'images/avatars/'.$usr['photo'];
				$email = $usr['email'];
				$show_email_ch = $coreC->validate_boolean($usr['show_email']) ? 'checked' : '';
				$im = $usr['im'];
				$show_im_ch = $coreC->validate_boolean($usr['show_im']) ? 'checked' : '';
				$country = $usr['country'];
				$city = $usr['city'];
				$additional = $usr['additional'];
				$coreC->validate_boolean($usr['gender']) ? $checkedMale = 'selected' : $checkedFemale = 'selected';
				require 'themes/'.$theme.'/templates/profile/userinfo_edit/userinfo_edit.tpl.php';
			}
		}
		if($uid == $uinfo['id'] || $uinfo['gid']==2|| $uinfo['gid']==3)
		{
			$timest = gmdate("Y-m-d H:i:s");
			$news_on_page = $usr['news_on_page'];
			$comments_on_page = $usr['comments_on_page'];
			$threads_on_page = $usr['threads_on_page'];
			$show_photos_ch = $coreC->validate_boolean($usr['show_avatars']) ? 'checked' : '';
			$show_ua_ch = $coreC->validate_boolean($usr['show_ua']) ? 'checked' : '';
			$change_date_sort_ch = $coreC->validate_boolean($usr['sort_to']) ? 'checked' : '';
			$show_resp_ch = $coreC->validate_boolean($usr['show_resp']) ? 'checked' : '';
			require 'themes/'.$theme.'/templates/profile/settings_edit/top.tpl.php';
			require 'themes/'.$theme.'/templates/profile/settings_edit/theme_top.tpl.php';
			$themes = $coreC->get_themes();
			for($i=0; $i<count($themes); $i++)
			{
				$theme_id = $themes[$i]['id'];
				$theme_name = $themes[$i]['name'];
				if($theme_id == $usr['theme'])
					$theme_checked = 'selected';
				else
					$theme_checked = '';
				require 'themes/'.$theme.'/templates/profile/settings_edit/theme_middle.tpl.php';
			}
			require 'themes/'.$theme.'/templates/profile/settings_edit/theme_bottom.tpl.php';
			require 'themes/'.$theme.'/templates/profile/settings_edit/mark_top.tpl.php';
			$marks = $markC->get_marks();
			for($i=0; $i<count($marks); $i++)
			{
				$mark_id = $marks[$i]['id'];
				$mark_name = $marks[$i]['name'];
				if($mark_id == $usr['mark'])
					$mark_checked = 'selected';
				else
					$mark_checked = '';
				require 'themes/'.$theme.'/templates/profile/settings_edit/mark_middle.tpl.php';
			}
			require 'themes/'.$theme.'/templates/profile/settings_edit/mark_bottom.tpl.php';
			require 'themes/'.$theme.'/templates/profile/settings_edit/bottom.tpl.php';
			require 'themes/'.$theme.'/templates/profile/filters_edit/top.tpl.php';
			$filter_str = $usersC->get_filter($uid);
			$filtered = $filtersC->parse_filter_string($filter_str);
			$filters_arr = $filtersC->get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				if($filtered[$i][1]==0)
					$checked_filter = '';
				else
					$checked_filter = 'checked';
				require 'themes/'.$theme.'/templates/profile/filters_edit/middle.tpl.php';
			}
			$filters_count = count($filters_arr);
			require 'themes/'.$theme.'/templates/profile/filters_edit/bottom.tpl.php';
			if($uinfo['gid']==2 || $uinfo['gid']==3)
			{
				$coreC->validate_boolean($usr['banned']) ? $banned_y = 'selected' : $banned_n = 'selected';
				require 'themes/'.$theme.'/templates/profile/moder_edit/top.tpl.php';
				$cptch = $coreC->get_captcha_levels();
				for($i=0; $i<count($cptch); $i++)
				{
					$captcha_level = $cptch[$i]["value"];
					$captcha_lvl_name = $cptch[$i]["name"];
					if($usr['captcha'] == $captcha_level)
						$captcha_sel = 'selected';
					else
						$captcha_sel = '';
					require 'themes/'.$theme.'/templates/profile/moder_edit/middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/profile/moder_edit/bottom.tpl.php';
			}
			if($uinfo['gid']==2)
			{
				require 'themes/'.$theme.'/templates/profile/admin_edit/top.tpl.php';
				$group = $usersC->get_group('all');
				for($i=0; $i<count($group); $i++)
				{
					if($usr['gid'] == $group[$i]['id'])
						$sel = 'selected';
					else
						$sel = '';
					$group_id = $group[$i]['id'];
					$group_name = $group[$i]['name'];
					require 'themes/'.$theme.'/templates/profile/admin_edit/middle.tpl.php';
				}
				require 'themes/'.$theme.'/templates/profile/admin_edit/bottom.tpl.php';
			}
		}
		if($uid == $uinfo['id'] || $uinfo['gid']==2)
		{
			require 'themes/'.$theme.'/templates/profile/mainpage_edit/mainpage_edit_top.tpl.php';
			$usr_blocks = $usersC->get_blocks($uid);
			$blocks = $coreC->get_block('all');
			for($i=0; $i<count($blocks); $i++)
			{
				$block_id = $i;
				$block_name = $blocks[$i]['name'];
				$id =0;//гребаный быдлокод, но я не могу додуматься как сделать красивее
				for($t=0; $t<count($usr_blocks); $t++)
				{
					if($usr_blocks[$t]['name']==$blocks[$i]['name'])
						$id=$t;
				}
				!empty($usr_blocks[$id]['sort']) ? $sort_val = $usr_blocks[$id]['sort'] : $sort_val = 0;
				if($usr_blocks[$id]['position'] == 'l')
				{
					$sel_l = 'selected';
					$sel_r = '';
					$sel_n = '';
				}
				else if($usr_blocks[$id]['position'] == 'r')
				{
					$sel_l = '';
					$sel_r = 'selected';
					$sel_n = '';
				}
				else
				{
					$sel_l = '';
					$sel_r = '';
					$sel_n = 'selected';
				}//конец участка быдлокода требующего чтобы его переписали
				require 'themes/'.$theme.'/templates/profile/mainpage_edit/mainpage_edit_middle.tpl.php';
			}
			$blocks_count = count($blocks);
			require 'themes/'.$theme.'/templates/profile/mainpage_edit/mainpage_edit_bottom.tpl.php';
		}
	}
}
require 'footer.php';
?>