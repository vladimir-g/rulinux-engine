<?
$pagename=$_SERVER['SCRIPT_NAME'];
$pagename=str_replace(getcwd(), '', $pagename);
if (!empty($_GET)){
	$_VALS=array_flip($_GET);
	$pagename=$pagename.'?';
	$c=0;
	foreach ($_GET as $_VAR){
		$pagename=$pagename.$_VALS[$_VAR].'='.$_VAR;
		$c++;
		if ($c<=(sizeof($_GET)-1))
			$pagename=$pagename.'&';
	}
}
$scriptname = $_SERVER['SCRIPT_NAME'];
$scriptname = str_replace(getcwd(), '', $scriptname);
$pid = intval($_GET['id']);
$content['title'] = 'Профиль пользователя';
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');
require_once('classes/messages.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();

$uid = $baseC->get_field_id('users', 'nick', $_GET['user']);
if ($uid <= 0){
	if ($_GET['user'] == 'anonymous'){
		$uname = 'anonymous';
		$user = $usersC->get_user_info($uid);
		define('USER_FOUND', true);
	}
	else
		define('USER_FOUND', false);
}
else{
	$uname = $_GET['user'];
	if($_SESSION['user_admin'] == 1){
		if(isset($_POST['banhammer'])){
			$_POST['banhammer'] = $_POST['banhammer'] ? 0 : 1;
			$baseC->erewrite('users', 'status', $_POST['banhammer'], $uid);
		}
		if(isset($_POST['vim'])){
			if($_POST['vim'] != 2){
				$baseC->erewrite('users', 'gid', 2, $uid);
				$usersC->make_admin($uid);
			}
			if($_POST['vim'] == 2){
				$baseC->del_element('admin', $uid, 'user');
				$baseC->erewrite('users', 'gid', 1, $uid);
			}
		}
		if(isset($_POST['captcha'])){
			$baseC->erewrite('users', 'captcha', $_POST['captcha'], $uid);
		}
	}
	$user = $usersC->get_user_info($uid);
	define('USER_FOUND', true);
}
//if (USER_FOUND){
	if (!isset($_GET['edit']))
	{
		require_once('incs/header.inc.php');
		echo '<!--content section begin-->';
		echo '<h1>'.$content['title'].'</h1>';
		if (!empty($user['photo']))
				echo '<img src="'.$user['photo'].'"><br>';
		echo '<table>';
		echo $user['name'] != '' ? '<tr><td>Имя: </td><td>'.$user['name'].'</td></tr>' : '';
		echo '<tr><td>Имя на сайте: </td><td>'.$user['nick'].'</td></tr>';
		if (($user['nick'] == $_SESSION['user_name'] || $user['show_email'] == 1) || $_SESSION['user_admin'] == 1)
			echo '<tr><td>E-mail: </td><td>'.$user['email'].'</td></tr>';
		if ((($user['nick'] == $_SESSION['user_name'] || $user['show_email'] == 1) || $_SESSION['user_admin'] == 1) && !empty($user['im']))
			echo '<tr><td>IM: </td><td>'.$user['im'].'</td></tr>';
		if ($user['country'] != '' || $user['city'] != '')
		{
			$fromwhere .= $user['country'] != '' ? $user['country'].'' : '';
			if ($user['country'] != '' && $user['city'] != '')
				$fromwhere .= ', ';
			$fromwhere .= $user['city'] != '' ? $user['city'].'' : '';
			echo $user['country'] != '' ? '<tr><td>Откуда: </td><td>'.$fromwhere.'</td></tr>' : '';
		}
		$group = $usersC->get_group($user['gid']);
		$stat = $user['status'] > 0 ? ', Разблокирова' : ', Заблокирова';
		echo '<tr><td>Статус: </td><td>'.$group['name'].$stat.'н</td></tr>';
		echo '<tr><td>Зарегистрирован: </td><td>'.$user['registered'].'</td></tr>';
		echo $user['last_visit'] != '' ? '<tr><td>Последний логин: </td><td>'.$user['last_visit'].'</td></tr>' : '';
		echo '</table>';
		if(!empty($user['additional']))
			echo '<br>Дополнительно: <span style="font-style:italic"><br>'.$user['additional'].'</span><br>';
		echo '<table>';
		echo '<tr><td><hr></td><td></td></tr>';
		$first_topic_news = $baseC->other_query('SELECT MIN(`timestamp`) FROM `[prefix]news` WHERE `by`=\''.$user['nick'].'\'');
		$last_topic_news = $baseC->other_query('SELECT MAX(`timestamp`) FROM `[prefix]news` WHERE `by`=\''.$user['nick'].'\'');
		$first_comment_news = $baseC->other_query('SELECT MIN(`timestamp`) FROM `[prefix]comments` WHERE `uid`='.$uid);
		$last_comment_news = $baseC->other_query('SELECT MAX(`timestamp`) FROM `[prefix]comments` WHERE `uid`='.$uid);
		//$first_topic_forum = $baseC->other_query('SELECT MIN(`posting_date`) FROM `[prefix]forum_threads` WHERE `user_name`=\''.$user['nick'].'\'');
		//$last_topic_forum = $baseC->other_query('SELECT MAX(`posting_date`) FROM `[prefix]forum_threads` WHERE `user_name`=\''.$user['nick'].'\'');
		//$first_comment_forum = $baseC->other_query('SELECT MIN(`posting_date`) FROM `[prefix]forum_messages` WHERE `user_name`=\''.$user['nick'].'\'');
		//$last_comment_forum = $baseC->other_query('SELECT MAX(`posting_date`) FROM `[prefix]forum_messages` WHERE `user_name`=\''.$user['nick'].'\'');
		$count_threads_news = $baseC->other_query('SELECT COUNT(`timestamp`) FROM `[prefix]news` WHERE `by`=\''.$user['nick'].'\'');
		$count_comments_news = $baseC->other_query('SELECT COUNT(`timestamp`) FROM `[prefix]comments` WHERE `uid`='.$uid);
		//$count_threads_forum = $baseC->other_query('SELECT COUNT(`posting_date`) FROM `[prefix]forum_threads` WHERE `user_name`=\''.$user['nick'].'\'');
		//$count_comments_forum = $baseC->other_query('SELECT COUNT(`posting_date`) FROM `[prefix]forum_messages` WHERE `user_name`=\''.$user['nick'].'\'');		
		echo '<tr><td>Первая созданная тема:</td><td>'.$baseC->timeToSTDate(min($first_topic_news[0][0], $first_topic_forum[0][0])).'</td></tr>';
		echo '<tr><td>Последняя созданная тема:</td><td>'.$baseC->timeToSTDate(max($last_topic_news[0][0], $last_topic_forum[0][0])).'</td></tr>';
		echo '<tr><td>Первый комментарий:</td><td>'.$baseC->timeToSTDate(min($first_comment_news[0][0], $first_comment_forum[0][0])).'</td></tr>';
		echo '<tr><td>Последний комментарий:</td><td>'.$baseC->timeToSTDate(max($last_comment_news[0][0], $last_comment_forum[0][0])).'</td></tr>';
		echo '<tr><td>Всего комментариев:</td><td>'.($count_comments_news[0][0] + $count_comments_forum[0][0]).'</td></tr>';
		echo '<tr><td>Всего тем:</td><td>'.($count_threads_news[0][0] + $count_threads_forum[0][0]).'</td></tr>';
		echo '</table>';
		if ($_SESSION['user_admin'] == 1)
		{
			require_once('ucaptcha/plugs.dcfg.php');
			if($user['gid'] == 2)
				$moderator_btn = 'ВИМ';
			else
				$moderator_btn = 'В модераторы';
			if($user['status'] == 1)
				$button = 'Заблокировать';
			else
				$button = 'Разблокировать';
			echo '
			<form action="" method="POST">
			<input type="hidden" name="vim" value="'.$user['gid'].'">
			<input type="submit" value="'.$moderator_btn.'">
			</form>
			';
			echo '
			<form action="" method="POST">
			<input type="hidden" name="banhammer" value="'.$user['status'].'">
			<input type="submit" value="'.$button.'">
			</form>
			';
			echo '<form action="" method="POST">';
			echo 'Уровень капчи: ';
			$cpt_level = $baseC->get_field_by_id('users', 'captcha', $uid);
			echo '<select name="captcha">';
			echo '<option value="'.$cpt_level.'">'.($cpt_level < 0 ? 'Нет' :$cpt_level).'</option>';
			for($i = -1; $i < sizeof($captcha_plug); $i++)
			{
				if($i != $cpt_level)
					echo '<option value="'.$i.'">'.($i < 0 ? 'Нет' :$i).'</option>';
			}
			echo '</select><input type="submit" value="Готово"></form>';
		}
	}
if(($user['nick'] == $_SESSION['user_name']) || ($_SESSION['user_admin'] == 1))
{
	$ok = 1;
}
elseif($user['nick'] == 'anonymous')
{
	if($uid==-1)
	{
		$ok = 1;
	}
}
		if (isset($_GET['edit']))
		{
			if(isset($_POST['action']))
			{
				if($ok == 1)
				{
					switch ($_POST['action'])
					{
						case 'pass':
							if(md5($_POST['old_pass']) == $user['pass'])
							{
								if (strlen($_POST['new_pass']) >= 1)
								{
									if ($_POST['new_pass'] == $_POST['new_pass_retype'])
									{
										$baseC->erewrite('users', 'pass', md5($_POST['new_pass']), $user['id']);
										require_once('incs/header.inc.php');
										echo '<!--content section begin-->';
										echo '<h1>'.$content['title'].'</h1>';
										$username = $user['nick'];
										echo "<fieldset><p align=center>Ваш пароль успешно изменен. <br>Через несколько секунд вы будете перенаправлены в профиль. <br>Если вы не хотите ждать нажмите <a href=/profile.php?user=$username>сюда</a></fieldset>";
										echo "<header><meta http-equiv='Refresh' content='0; url=profile.php?user=$username' /></header>";
									}
									else
										echo '<fieldset style="border: 1px dashed #ffffff">Новый пароль не соответствует его повторению</fieldset>';
								}
								else
									echo '<fieldset style="border: 1px dashed #ffffff">Пустой новый пароль</fieldset>';
							}
							else
								echo '<fieldset style="border: 1px dashed #ffffff">Неверно введен старый пароль</fieldset>';
						break;
						case 'info':
							foreach ($_POST as $key => $post)
								$_POST[$key] = htmlspecialchars($post);
							if (preg_match('/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/', $_POST['user_email']))
							{
								$baseC->erewrite('users', 'name', $_POST['user_name'], $user['id']);
								$baseC->erewrite('users', 'email', $_POST['user_email'], $user['id']);
								$baseC->erewrite('users', 'show_email', $_POST['showEmail'] == 'on' ? 1 : 0, $user['id']);
								$baseC->erewrite('users', 'im', $_POST['user_im'], $user['id']);
								$baseC->erewrite('users', 'show_im', $_POST['showIM'] == 'on' ? 1 : 0, $user['id']);
								$baseC->erewrite('users', 'country', $_POST['user_country'], $user['id']);
								$baseC->erewrite('users', 'city', $_POST['user_city'], $user['id']);
								$baseC->erewrite('users', 'additional', $_POST['user_additional'], $user['id']);
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
									$filename = $_SESSION['user_name'];
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
										$baseC->erewrite('users', 'photo', $uploadfile, $user['id']);
									}
								}
								require_once('incs/header.inc.php');
								echo '<!--content section begin-->';
								echo '<h1>'.$content['title'].'</h1>';
								$username = $user['nick'];
								echo "<fieldset><p align=center>Личные данные изменены. <br>Через несколько секунд вы будете перенаправлены в профиль. <br>Если вы не хотите ждать нажмите <a href=/profile.php?user=$username>сюда</a></fieldset>";
								echo "<header><meta http-equiv='Refresh' content='0; url=profile.php?user=$username' /></header>";
							}
							else
								echo '<fieldset style="border: 1px dashed #ffffff">Недействительный адрес электронной почты</fieldset>';
						break;
						case 'main_page':
							$left_block = '';
							$right_block = '';
							foreach ($_POST as $key => $val)
							{
								if(preg_match('/^([a-z]+)\_pos$/', $key, $found))
								{
									if($val == 1)
										$left_block .= $found[1].':'.$_POST[$found[1].'_sort'].',';
									elseif($val == 2)
										$right_block .= $found[1].':'.$_POST[$found[1].'_sort'].',';
								}
							}
							$left_block = substr($left_block, 0, strlen($left_block)-1);
							$right_block = substr($right_block, 0, strlen($right_block)-1);
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'left_block', $left_block, $user['id']) : setcookie('left_block', $left_block, (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'right_block', $right_block, $user['id']) : setcookie('right_block', $right_block, (time()+60*60*24*9999));
							require_once('incs/header.inc.php');
							echo '<!--content section begin-->';
							echo '<h1>'.$content['title'].'</h1>';
							$username = $user['nick'];
							echo "<fieldset><p align=center>Настройки блоков изменены. <br>Через несколько секунд вы будете перенаправлены в профиль. <br>Если вы не хотите ждать нажмите <a href=/profile.php?user=$username>сюда</a></fieldset>";
							echo "<header><meta http-equiv='Refresh' content='0; url=profile.php?user=$username' /></header>";
						break;
						case 'read':
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'news_on_page', $_POST['news_on_page'], $user['id']) : setcookie('news_on_page', (int)$_POST['news_on_page'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'comments_on_page', $_POST['comments_on_page'], $user['id']) : setcookie('comments_on_page', (int)$_POST['comments_on_page'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'threads_on_page', $_POST['threads_on_page'], $user['id']) : setcookie('threads_on_page', (int)$_POST['threads_on_page'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'show_avatars', (int)$_POST['show_photos'], $user['id']) : setcookie('show_avatars', (int)$_POST['show_photos'], (time()+60*60*24*9999));
							if ($uid > 0)
							{
								(int)$user['id'] > 0 ? $baseC->erewrite('users', 'showUA', (int)$_POST['show_ua'], $user['id']) : setcookie('showUA', (int)$_POST['show_ua'], (time()+60*60*24*9999));
							}
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'show_hid', (int)$_POST['show_hid'], $user['id']) : setcookie('show_hid', (int)$_POST['show_hid'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'show_resp', (int)$_POST['show_resp'], $user['id']) : setcookie('show_resp', (int)$_POST['show_resp'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'show_filthy_lang', (int)$_POST['show_filthy_lang'], $user['id']) : setcookie('show_filthy_lang', (int)$_POST['show_filthy_lang'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'sort_to', (int)$_POST['sort_to'], $user['id']) : setcookie('sort_to', (int)$_POST['sort_to'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $baseC->erewrite('users', 'gmt', (int)$_POST['user-gmt'], $user['id']) : setcookie('gmt', (int)$_POST['user-gmt'], (time()+60*60*24*9999));
							(int)$user['id'] > 0 ? $usersC->modify_user_info('theme', $_POST['themename'], (int)$user['id']) : setcookie('lorng_theme', $_POST['themename'], (time()+60*60*24*9999));
							require_once('incs/header.inc.php');
							echo '<!--content section begin-->';
							echo '<h1>'.$content['title'].'</h1>';
							$username = $user['nick'];
							echo "<fieldset><p align=center>Настройки чтения изменены. <br>Через несколько секунд вы будете перенаправлены в профиль. <br>Если вы не хотите ждать нажмите <a href=/profile.php?user=$username>сюда</a></fieldset>";
							echo "<header><meta http-equiv='Refresh' content='0; url=profile.php?user=$username' /></header>";
						break;
					}
				}
			}
			else
			{
				//$user = $usersC->get_user_info($uid);
				//echo '<font color=red>uid:</font> '.$uid.' user_name = '.$_SESSION['user_name'].' nick: '.$user['nick'];
				if ($ok == 1)
				{
					require_once('incs/header.inc.php');
					echo '<!--content section begin-->';
					echo '<h1>'.$content['title'].'</h1>';
					if ($uid > 0)
					{
						echo '<form action="profile.php?user='.$user['nick'].'&edit" method="post">';
						echo '<fieldset><legend>Смена пароля</legend><table>';
						echo '<tr><td>Старый пароль:</td><td><input type="password" name="old_pass" value=""></td><td></tr>';
						echo '<tr><td>Новый пароль:</td><td><input type="password" name="new_pass" value=""></td><td></tr>';
						echo '<tr><td>Повторите пароль:</td><td><input type="password" name="new_pass_retype" value=""></td><td></tr>';
						echo '</table></fieldset>';
						echo '<input type="submit" value="Сменить пароль"><input type="hidden" name="action" value="pass"></form>';
						echo '<form action="profile.php?user='.$user['nick'].'&edit" method="post" enctype="multipart/form-data">';
						echo '<fieldset><legend>Личные данные</legend>';
						if (!empty($user['photo']))
						echo '<img src="'.$user['photo'].'"><br>';
						echo 'Фотография <ul>
						<li>минимум: 50x50px;
						<li>максимум: 150x150px;
						<li>формат: jpeg, gif или png</ul>';
						echo '<input type="file" name="user_photo"><br><br>';
						echo '<table>';
						echo '<tr><td>Имя:</td><td><input type="text" name="user_name" value="'.$user['name'].'"></td><td>&nbsp;</td></tr>';
						$show_field = $user['show_email'] ? ' checked' : '';
						echo '<tr><td>E-mail:</td><td><input type="text" name="user_email" value="'.$user['email'].'"></td><td><input type="checkbox" name="showEmail"'.$show_field.'>Показывать</td></tr>';
						$show_field = $user['show_im'] ? ' checked' : '';
						echo '<tr><td>IM:</td><td><input type="text"  name="user_im" value="'.$user['im'].'"></td><td><input type="checkbox" name="showIM"'.$show_field.'>Показывать</td></tr>';
						echo '<tr><td>Страна:</td><td><input type="text" name="user_country" value="'.$user['country'].'"></td><td></tr>';
						echo '<tr><td>Город:</td><td><input type="text" name="user_city" value="'.$user['city'].'"></td><td></tr>';
						echo '</table>';
						echo 'Дополнительно:<br><textarea name="user_additional" rows="7" cols="45">'.$user['additional'].'</textarea>';
						echo '</fieldset>';
						echo '<input type="submit" value="Сохранить профиль">
						<input type="hidden" name="action" value="info">
						</form>';
					}
					echo '<form action="profile.php?user='.$user['nick'].'&edit" method="post">';
					echo '<fieldset><legend>Настройка чтения</legend>';
					echo '<strong>Время сервера: '.date('H:i:s d.m.Y').'</strong><br><br><table>';
					$tpl_dirs = $baseC->parse_dir('design', '/^([.]{1,2})$|^(admin)$/');
					$tpls = '';
					foreach ($tpl_dirs as $tpl_dir) 
					{
						$theme_settings = file('design/'.$tpl_dir.'/index.theme');
						$th_settings = array();
						$buff = array();
						foreach($theme_settings as $theme_setting)
						{
							preg_match('/([a-zA-Z0-9\s]+)([\s\=]+)([\'\"]{1})([a-zA-Z0-9\s]+)([\'\"]{1})/', $theme_setting, $buff);
							$th_settings[$buff[1]] = $buff[4];
						}
						if($th_settings['For'] == 'Users')
						{
							if($tpl_dir == $tpl_name)
							{
								$tpls .= '<option value="'.$tpl_dir.'" selected>'.$th_settings['Name'].'</option>';
							}
							else
							{
								$tpls .= '<option value="'.$tpl_dir.'">'.$th_settings['Name'].'</option>';
							}
						}
					}
					echo '<tr><td>Тема оформления:</td><td>';
					echo '<select name="themename">';
					echo $tpls;
					echo '</select></td><td></tr>';
					echo '<tr><td>Время:</td><td>
					<select name="user-gmt">';
					echo '<option value="'.$user['gmt'].'" checked>Время сервера '.(($user['gmt'] > -10 && $user['gmt'] < 10) ? ($user['gmt'] < 0 ? '-' : '+').'0'.abs($user['gmt']) : ($user['gmt'] < 0 ? '-' : '+').abs($user['gmt'])).'</option>';
					for($i = -12; $i <= 12; $i++)
					{
						if(($i < 0 ? '' : '+').$i != $user['gmt'])
							echo '<option value="'.($i > 0 ? '+' : '').$i.'">Время сервера '.(($i > -10 && $i < 10) ? ($i < 0 ? '-' : '+').'0'.abs($i) : ($i < 0 ? '-' : '+').abs($i)).'</option>';
					}
					echo '</select>
					</td><td></tr>';
					echo '<tr><td>Новостей на странице:</td><td><input type="text" name="news_on_page" value="'.$user['news_on_page'].'" maxlength="3"></td><td></tr>';
					echo '<tr><td>Комментариев на странице:</td><td><input type="text" name="comments_on_page" value="'.$user['comments_on_page'].'" maxlength="4"></td><td></tr>';
					echo '<tr><td>Тредов на странице:</td><td><input type="text" name="threads_on_page" value="'.$user['threads_on_page'].'" maxlength="4"></td><td></tr>';
					$show_avas = $user['show_avatars'] ? ' checked' : '';
					$show_ua = $user['showUA'] ? ' checked' : '';
					$show_hid = $user['show_hid'] ? ' checked' : '';
					$show_resp = $user['show_resp'] ? ' checked' : '';
					$show_filthy_lang = $user['show_filthy_lang'] ? ' checked' : '';
					$sort_to = $user['sort_to'] ? ' checked' : '';
					echo '<tr><td><label for="showPhotos">Показывать фотографии:</label></td><td><input type="checkbox" name="show_photos" value="1" id="showPhotos"'.$show_avas.'></td><td></tr>';
					if ($uid > 0)
					{
						echo '<tr><td><label for="showUA">Показывать сигнатуру моего браузера:</label></td><td><input type="checkbox" name="show_ua" value="1" id="showUA"'.$show_ua.'></td><td></tr>';
					}
					echo '<tr><td><label for="show_hid">Показывать скрытые сообщения:</label></td><td><input type="checkbox" name="show_hid" value="1" id="show_hid"'.$show_hid.'></td><td></tr>';
					echo '<tr><td><label for="sort_to">Сортировать по дате изменения:</label></td><td><input type="checkbox" name="sort_to" value="1" id="sort_to"'.$sort_to.'></td><td></tr>';
					echo '<tr><td><label for="show_resp">Показывать автора родительского сообщения в трекере:</label></td><td><input type="checkbox" name="show_resp" value="1" id="show_resp"'.$show_resp.'></td><td></tr>';
					echo '<tr><td><label for="show_filthy_lang">Скрывать сообщения содержащие мат:</label></td><td><input type="checkbox" name="show_filthy_lang" value="1" id="show_filthy_lang"'.$show_filthy_lang.'></td><td></tr>';
					echo '</table></fieldset>';
					echo '<input type="submit" value="Сохранить настройки"><input type="hidden" name="action" value="read"></form>';
					echo '<form action="profile.php?user='.$user['nick'].'&edit" method="post">';
					echo '<fieldset><legend>Конструктор главной страницы</legend>';
					$left = explode(',', $user['left_block']);
					$right = explode(',', $user['right_block']);
					$i = 0;
					foreach($left as $block)
					{
						$left[$i] = explode(':', $block);
						$i++;
					}
					$i = 0;
					foreach($right as $block)
					{
						$right[$i] = explode(':', $block);
						$i++;
					}
					echo '<table width="100%">';
					echo '<tr>';
					echo '<th>Блок</th>';
					echo '<th>Позиция</th>';
					echo '<th>Сортировка</th>';
					echo '</tr>';
					$blocks = array(
						'auth' => false,
						'gall' => false,
						'links' => false,
						'faq' => false,
						'tracker' => false
					);
					foreach ($left as $block){
						switch ($block[0]){
							case 'auth': $name = 'Авторизация'; break;
							case 'gall': $name = 'Галерея'; break;
							case 'links': $name = 'Ссылки'; break;
							case 'faq': $name = 'F.A.Q.'; break;
							case 'tracker': $name = 'Последние 10 сообщений'; break;
							default: $name = 'Неизвестно'; break;
						}
						$blocks[$block[0]] = true;
						echo '<tr>';
						echo '<td style="text-align:center">'.$name.'</td>';
						echo '<td style="text-align:center">';
						echo '<select name="'.$block[0].'_pos">';
						echo '<option value="1">Слева</option>';
						echo '<option value="2">Справа</option>';
						echo '<option value="0">Не используется</option>';
						echo '</select>
						</td>';
						echo '<td style="text-align:center">
						<select name="'.$block[0].'_sort">';
						echo '<option value="'.$block[1].'">'.$block[1].'</option>';
						if ($block[1] != 1)
							echo '<option value="1">1</option>';
						if ($block[1] != 2)
							echo '<option value="2">2</option>';
						if ($block[1] != 3)
							echo '<option value="3">3</option>';
						if ($block[1] != 4)
							echo '<option value="4">4</option>';
						echo '</select>
						</td>';
						echo '</tr>';	
					}	
					foreach ($right as $block){
						switch ($block[0]){
							case 'auth': $name = 'Авторизация'; break;
							case 'gall': $name = 'Галерея'; break;
							case 'links': $name = 'Ссылки'; break;
							case 'faq': $name = 'F.A.Q.'; break;
							case 'tracker': $name = 'Последние 10 сообщений'; break;
							default: $name = 'Неизвестно'; break;
						}
						$blocks[$block[0]] = true;
						echo '<tr>';
						echo '<td style="text-align:center">'.$name.'</td>';
						echo '<td style="text-align:center">';
						echo '<select name="'.$block[0].'_pos">';
						echo '<option value="2">Справа</option>';
						echo '<option value="1">Слева</option>';
						echo '<option value="0">Не используется</option>';
						echo '</select>
						</td>';
						echo '<td style="text-align:center">
						<select name="'.$block[0].'_sort">';
						echo '<option value="'.$block[1].'">'.$block[1].'</option>';
						if ($block[1] != 1)
							echo '<option value="1">1</option>';
						if ($block[1] != 2)
							echo '<option value="2">2</option>';
						if ($block[1] != 3)
							echo '<option value="3">3</option>';
						if ($block[1] != 4)
							echo '<option value="4">4</option>';
						echo '</select>
						</td>';
						echo '</tr>';	
					}
					foreach ($blocks as $bname => $block){
						if ($block == true)
							continue;
						switch ($bname){
							case 'auth': $name = 'Авторизация'; break;
							case 'gall': $name = 'Галерея'; break;
							case 'links': $name = 'Ссылки'; break;
							case 'faq': $name = 'F.A.Q.'; break;
							case 'tracker': $name = 'Последние 10 сообщений'; break;
							default: $name = 'Неизвестно'; break;
						}
						echo '<tr>';
						echo '<td style="text-align:center">'.$name.'</td>';
						echo '<td style="text-align:center">';
						echo '<select name="'.$bname.'_pos">';
						echo '<option value="0">Не используется</option>';
						echo '<option value="1">Слева</option>';
						echo '<option value="2">Справа</option>';
						echo '</select>
						</td>';
						echo '<td style="text-align:center">
						<select name="'.$bname.'_sort">';
						echo '<option value="1">1</option>';
						echo '<option value="2">2</option>';
						echo '<option value="3">3</option>';
						echo '<option value="4">4</option>';
						echo '</select>
						</td>';
						echo '</tr>';	
					}	
					echo '</table>';
					echo '</fieldset>';
					echo '<input type="hidden" name="action" value="main_page">';
					echo '<input type="submit" value="Сохранить настройки"></form>';
				}
				else
				{
						require_once('incs/header.inc.php');
						echo '<!--content section begin-->';
						echo '<br><fieldset><p align=center>Извините вы не можете отредактировать профиль</p></fieldset>';
						echo '<!--content section end-->';
						require_once('incs/bottom.inc.php');
						exit();
				}
			}
		}
		else
		{
			echo '<form action="profile.php" method="get">';
			echo '<input type="hidden" name="user" value="'.$user['nick'].'">';
			echo '<input type="hidden" name="edit" value="1">';
			echo '<input type="submit" value="Править">';
			echo '</form>';
		}

//}
//else
//	echo 'не найден';
?>
<?
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>
