<?php
include 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.' - Профиль пользователя '.$user;
$profile_name = $_SESSION['user_name'];
if(!empty($_GET['user']))
	$user = $_GET['user'];
else
	$user = $profile_name;
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
include 'links.php';
include 'themes/'.$theme.'/templates/header.tpl.php';
$uid = users::get_uid_by_nick($user);
$usr = users::get_user_info($uid);

if($_POST['action']=="pass")
{
	if($uid == $uinfo['id'] || $uinfo['gid']==2)
	{
		if(empty($_POST['old_pass']))
		{
			echo 'Введите старый пароль';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}	
		if(empty($_POST['new_pass']))
		{
			echo 'Введите новый пароль';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		if(empty($_POST['new_pass_retype']))
		{
			echo 'Введите подтверждение нового пароля';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		if($_POST['new_pass'] != $_POST['new_pass_retype'])
		{
			echo 'Строка нового пароля не соответствует строке подтверждения';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		if(md5($_POST['old_pass']) != $usr['password'])
		{
			echo 'Неверно введен старый пароль';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		$ret = users::modify_user_info('password', md5($_POST['new_pass']), $uid);
		if($ret == 1)
		{
			echo '<fieldset><legend>Пароль успешно изменен</legend><p align="center">Пароль успешно изменен<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="profile.php?user='.$user.'&edit=1">сюда</a>.</p></fieldset>';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit=1">');  
		}
		else
			echo 'Произошла ошибка при смене пароля';
	}
	else
	{
		echo 'У вас нет полномочий для смены пароля данного пользователя';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else if($_POST['action']=="info")
{	
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
			$uploaddir = 'avatars/';
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
				$val = users::modify_user_info('photo', $filename.'.'.$ext[1], $uid);
				if($val != 1)
				{
					echo 'Произошла ошибка при смене информации';
					include 'themes/'.$theme.'/templates/footer.tpl.php';
					exit();
				}
			}
		}

		$val = users::modify_user_info_settings($uid, $_POST['user_name'], $_POST['user_lastname'], $_POST['gender'], $_POST['user_email'], $_POST['showEmail'], $_POST['user_im'], $_POST['showIM'], $_POST['user_country'], $_POST['user_city'], $_POST['user_additional']);
		echo '<fieldset><legend>Пользовательская информация успешно изменена</legend><p align="center">Пользовательская информация успешно изменена<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="profile.php?user='.$user.'&edit=1">сюда</a>.</p></fieldset>';
		die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit=1">');  
	}
	else
	{
		echo 'У вас нет полномочий для смены информации о данном пользователе';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
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
		$ret = users::set_filter($uid, $str);
		if($ret == 1)
		{
			echo '<fieldset><legend>Настройки фильтров успешно изменены</legend><p align="center">Настройки фильтров успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="profile.php?user='.$user.'&edit=1">сюда</a>.</p></fieldset>';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit=1">');  
		}
		else
		{
			echo 'Произошла ошибка при смене настроек фильтров';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	else
	{
		echo 'У вас нет полномочий для смены настроек фильтров';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else if($_POST['action']=="read")
{
	if($uid == $uinfo['id'] || $uinfo['gid']==2|| $uinfo['gid']==3)
	{
		if($_POST['user-gmt'] != "none")
		{
			$val = users::modify_user_info('gmt', htmlspecialchars($_POST['user-gmt']), $uid);
			if($val != 1)
			{
				echo 'Произошла ошибка при смене часового пояса';
				include 'themes/'.$theme.'/templates/footer.tpl.php';
				exit();
			}
		}
	
		$ret = users::modify_user_read_settings($uid, $_POST['theme'], $_POST['news_on_page'], $_POST['comments_on_page'], $_POST['threads_on_page'], $_POST['show_photos'], $_POST['show_ua'], $_POST['sort_to'], $_POST['show_resp']);
		if($ret >=0)
		{
			echo '<fieldset><legend>Настройки чтения успешно изменены</legend><p align="center">Настройки чтения успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="profile.php?user='.$user.'&edit=1">сюда</a>.</p></fieldset>';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit=1">');  
		}
		else
		{
			echo 'Произошла ошибка при смене настроек чтения';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	else
	{
		echo 'У вас нет полномочий для смены настроек чтения';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else if($_POST['action']=="moder")
{
	if($uinfo['gid']==2|| $uinfo['gid']==3)
	{
		$banned = (int)$_POST['banned'];
		$ban_ret = users::ban_user($uid, $banned);
		$value = (int)$_POST['captcha'];
		$cpt_ret = users::modify_user_info('captcha', $value, $uid);
		if($ban_ret >=0 || $cpt_ret >=0)
		{
			echo '<fieldset><legend>Модераторские настройки успешно изменены</legend><p align="center">Модераторские настройки успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="profile.php?user='.$user.'&edit=1">сюда</a>.</p></fieldset>';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit=1">');  
		}
		else
		{
			echo 'Произошла ошибка при смене модераторских настроек';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	else
	{
		echo 'Вы не имеете полномочий для изменения модераторских настроек';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else if($_POST['action']=="admin")
{
	if($uinfo['gid']==2)
	{
		$group = (int)$_POST['group'];
		$ret = users::modify_user_info('gid', $group, $uid);
		if($ret >=0)
		{
			echo '<fieldset><legend>Администраторские настройки успешно изменены</legend><p align="center">Администраторские настройки успешно изменены<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="profile.php?user='.$user.'&edit=1">сюда</a>.</p></fieldset>';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit=1">');  
		}
		else
		{
			echo 'Произошла ошибка при смене администраторских настроек';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	else
	{
		echo 'Вы не имеете полномочий для изменения администраторских настроек';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else if($_POST['action']=="main_page")
{
	if($user == $profile_name || $uinfo['gid']==2)
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
		$ret = users::modify_user_info('blocks', $blocks_str, $uid);
		if($ret >=0)
		{
			echo '<fieldset><legend>Вид главной страницы успешно изменен</legend><p align="center">Вид главной страницы успешно изменен<br>Через три секунды вы будете перенаправлены на страницу изменения профиля.<br>Если вы не хотите ждать, нажмите <a href="profile.php?user='.$user.'&edit=1">сюда</a>.</p></fieldset>';
			die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'profile.php?user='.$user.'&edit=1">');  
		}
		else
		{
			echo 'Произошла ошибка при смене вида главной страницы';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
	}
	else
	{
		echo 'Вы не имеете полномочий для изменения вида главной страницы';
		include 'themes/'.$theme.'/templates/footer.tpl.php';
		exit();
	}
}
else
{
	if($_GET['edit']!=1)
	{
		$usr_add = users::get_additional_user_info($uid);
		$name = $usr['name'];
		$lastname = $usr['lastname'];
		$gender = in_array($usr['gender'], $true_arr) ? 'Мужской' : 'Женский';
		$birthday = $usr['birthday'];
		$photo = empty($usr['photo']) ? 'themes/'.$theme.'/empty.gif' : 'avatars/'.$usr['photo'];
		if($uinfo['gid']==3 || $uinfo['gid']==2)
			$email = $usr['email'];
		else
			$email = in_array($usr['show_email'], $true_arr) ? $usr['email'] : 'скрыт';
		if($uinfo['gid']==3 || $uinfo['gid']==2)
			$im = $usr['im'];
		else
			$im = in_array($usr['show_im'], $true_arr) ? $usr['im'] : 'скрыт';
		$country = $usr['country'];
		$city = $usr['city'];
		if($usr['gid']==3)
			$status = 'Модератор';
		else if($usr['gid']==2)
			$status = 'Администратор';
		else
			$status = 'Пользователь';
		$status = in_array($usr['banned'], $true_arr) ? $status.", Заблокирован" : $status.", Разблокирован";
		$register_date = $usr['register_date'];
		$last_login = $usr['last_visit'];
		$additional = $usr['additional'];
		$first_topic_date = $usr_add['first_topic_date'];
		$last_topic_date = $usr_add['last_topic_date'];
		$first_comment_date = $usr_add['first_comment_date'];
		$last_comment_date = $usr_add['last_comment_date'];
		$comments_count = $usr_add['comments_count'];
		$topics_count = $usr_add['topics_count'];
		include 'themes/'.$theme.'/templates/profile/middle.tpl.php';
		include 'themes/'.$theme.'/templates/profile/form.tpl.php';
	}
	else
	{
		if($user == $profile_name || $uinfo['gid']==2)
		{
			include 'themes/'.$theme.'/templates/profile/edit.tpl.php';
			include 'themes/'.$theme.'/templates/profile/password_edit/password_edit.tpl.php';
			$name = $usr['name'];
			$lastname = $usr['lastname'];
			$avatar = empty($usr['photo']) ? 'themes/'.$theme.'/empty.gif' : 'avatars/'.$usr['photo'];
			$email = $usr['email'];
			$show_email_ch = in_array($usr['show_email'], $true_arr) ? 'checked' : '';
			$im = $usr['im'];
			$show_im_ch = in_array($usr['show_im'], $true_arr) ? 'checked' : '';
			$country = $usr['country'];
			$city = $usr['city'];
			$additional = $usr['additional'];
			in_array($usr['gender'], $true_arr) ? $checkedMale = 'selected' : $checkedFemale = 'selected';
			include 'themes/'.$theme.'/templates/profile/userinfo_edit/userinfo_edit.tpl.php';
		}
		if($user == $profile_name || $uinfo['gid']==2|| $uinfo['gid']==3)
		{
			$timest = date("Y-m-d H:i:s");
			$news_on_page = $usr['news_on_page'];
			$comments_on_page = $usr['comments_on_page'];
			$threads_on_page = $usr['threads_on_page'];
			$show_photos_ch = in_array($usr['show_avatars'], $true_arr) ? 'checked' : '';
			$show_ua_ch = in_array($usr['show_ua'], $true_arr) ? 'checked' : '';
			$change_date_sort_ch = in_array($usr['sort_to'], $true_arr) ? 'checked' : '';
			$show_resp_ch = in_array($usr['show_resp'], $true_arr) ? 'checked' : '';
			include 'themes/'.$theme.'/templates/profile/settings_edit/top.tpl.php';
			include 'themes/'.$theme.'/templates/profile/settings_edit/theme_top.tpl.php';
			$themes = core::get_themes();
			for($i=0; $i<count($themes); $i++)
			{
				$theme_id = $themes[$i]['id'];
				$theme_name = $themes[$i]['name'];
				if($theme_id == $usr['theme'])
					$theme_checked = 'selected';
				else
					$theme_checked = '';
				include 'themes/'.$theme.'/templates/profile/settings_edit/theme_middle.tpl.php';
			}
			include 'themes/'.$theme.'/templates/profile/settings_edit/theme_bottom.tpl.php';
			include 'themes/'.$theme.'/templates/profile/settings_edit/bottom.tpl.php';
			include 'themes/'.$theme.'/templates/profile/filters_edit/top.tpl.php';
			$filter_str = users::get_filter($uid);
			$filtered = filters::parse_filter_string($filter_str);
			$filters_arr = filters::get_filters();
			for($i=0; $i<count($filters_arr);$i++)
			{
				$filterN = $filters_arr[$i]['id'];
				$filter_name = $filters_arr[$i]['name'];
				if($filtered[$i][1]==0)
					$checked_filter = '';
				else
					$checked_filter = 'checked';
				include 'themes/'.$theme.'/templates/profile/filters_edit/middle.tpl.php';
			}
			$filters_count = count($filters_arr);
			include 'themes/'.$theme.'/templates/profile/filters_edit/bottom.tpl.php';
			if($uinfo['gid']==2 || $uinfo['gid']==3)
			{
				in_array($usr['banned'], $true_arr) ? $banned_y = 'selected' : $banned_n = 'selected';
				include 'themes/'.$theme.'/templates/profile/moder_edit/top.tpl.php';
				$cptch = core::get_captcha_levels();
				for($i=0; $i<count($cptch); $i++)
				{
					$captcha_level = $cptch[$i]["value"];
					$captcha_lvl_name = $cptch[$i]["name"];
					if($usr['captcha'] == $captcha_level)
						$captcha_sel = 'selected';
					else
						$captcha_sel = '';
					include 'themes/'.$theme.'/templates/profile/moder_edit/middle.tpl.php';
				}
				include 'themes/'.$theme.'/templates/profile/moder_edit/bottom.tpl.php';
			}
			if($uinfo['gid']==2)
			{
				include 'themes/'.$theme.'/templates/profile/admin_edit/top.tpl.php';
				$group = users::get_group('all');
				for($i=0; $i<count($group); $i++)
				{
					if($usr['gid'] == $group[$i]['id'])
						$sel = 'selected';
					else
						$sel = '';
					$group_id = $group[$i]['id'];
					$group_name = $group[$i]['name'];
					include 'themes/'.$theme.'/templates/profile/admin_edit/middle.tpl.php';
				}
				include 'themes/'.$theme.'/templates/profile/admin_edit/bottom.tpl.php';
			}
		}
		if($user == $profile_name || $uinfo['gid']==2)
		{
			include 'themes/'.$theme.'/templates/profile/mainpage_edit/mainpage_edit_top.tpl.php';
			$blocks = users::get_blocks($uid);
			for($i=0; $i<count($blocks); $i++)
			{
				$block_id = $i;
				$block_name = $blocks[$i]['name'];
				$sort_val = $blocks[$i]['sort'];
				if($blocks[$i]['position'] == 'l')
				{
					$sel_l = 'selected';
					$sel_r = '';
					$sel_n = '';
				}
				else if($blocks[$i]['position'] == 'r')
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
				}
				include 'themes/'.$theme.'/templates/profile/mainpage_edit/mainpage_edit_middle.tpl.php';
			}
			$blocks_count = count($blocks);
			include 'themes/'.$theme.'/templates/profile/mainpage_edit/mainpage_edit_bottom.tpl.php';
		}
	}
}
include 'themes/'.$theme.'/templates/footer.tpl.php';
?>