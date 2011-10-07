<?php
final class users extends object
{
	static $baseC = null;
	function __construct()
	{
		self::$baseC = new base;
	}
	function change_users_group($uid, $gid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$sel = self::$baseC->select('users', '', '*', $where_arr);
		if(!empty($sel))
		{
			$where_arr = array(array("key"=>'id', "value"=>$gid, "oper"=>'='));
			$gr_sel = self::$baseC->select('groups', '', '*', $where_arr);
			if(!empty($gr_sel))
			{
				$ret = self::$baseC->update('users', 'gid', $gid, 'id', $uid);
				return $ret;
			}
			else 
				return -2;
		}
		else 
			return -1;
	}
	function get_group($id)
	{
		if ($id == 'all')
		{
			$sel = self::$baseC->select('groups', '', '*');
			return $sel;
		}
		elseif((int)$id > 0)
		{
			$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
			$sel = self::$baseC->select('groups', '', '*', $where_arr);
			return $sel[0];
		}
	}
	function add_group($name, $description)
	{
		$where_arr = array(array("key"=>'name', "value"=>$name, "oper"=>'='));
		$sel = self::$baseC->select('groups', '', 'id', $where_arr);
		if(empty($sel))
		{
			$gr_arr = array(array('name', $name), array('description', $description));
			self::$baseC->insert('groups', $gr_arr);
			return 1;
		}
		else
			return -1;
	}
	function get_users($begin, $end)
	{
		$sel = self::$baseC->select('users', '', '*', '', '', 'nick', 'ASC', $begin, $end);
		if(!empty($sel))
			return $sel;
		else
			return -1;
	}
	function get_uid_by_nick($nick)
	{
		$where_arr = array(array("key"=>'nick', "value"=>$nick, "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'id', $where_arr);
		if(!empty($sel))
			return $sel[0]['id'];
		else
			return -1;
	}
	function get_message_count_by_nick($nick)
	{
		$cnt = self::$baseC->query('SELECT count(*) AS cnt FROM comments WHERE uid = (SELECT id FROM users WHERE nick = \'::0::\')', 'assoc_array', array($nick));
		return $cnt[0]['cnt'];
	}
	function get_user_info($uid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$sel = self::$baseC->select('users', '', '*', $where_arr);
		if(!empty($sel))
		{
			if($uid==1)
			{
				$sel[0]['blocks'] = empty($_COOKIE['blocks']) ? $sel[0]['blocks'] : $_COOKIE['blocks'];
				$sel[0]['news_on_page'] = empty($_COOKIE['news_on_page']) ? $sel[0]['news_on_page'] : $_COOKIE['news_on_page'];
				$sel[0]['comments_on_page'] = empty($_COOKIE['comments_on_page']) ? $sel[0]['comments_on_page'] : $_COOKIE['comments_on_page'];
				$sel[0]['threads_on_page'] = empty($_COOKIE['threads_on_page']) ? $sel[0]['threads_on_page'] : $_COOKIE['threads_on_page'];
				$sel[0]['show_avatars'] = empty($_COOKIE['show_avatars']) ? $sel[0]['show_avatars'] : $_COOKIE['show_avatars'];
				$sel[0]['show_ua'] = empty($_COOKIE['show_ua']) ? $sel[0]['show_ua'] : $_COOKIE['show_ua'];
				$sel[0]['show_resp'] = empty($_COOKIE['show_resp']) ? $sel[0]['show_resp'] : $_COOKIE['show_resp'];
				$sel[0]['theme'] = empty($_COOKIE['theme']) ? $sel[0]['theme'] : $_COOKIE['theme'];
				$sel[0]['gmt'] = empty($_COOKIE['gmt']) ? $sel[0]['gmt'] : $_COOKIE['gmt'];
				$sel[0]['filters'] = empty($_COOKIE['filters']) ? $sel[0]['filters'] : $_COOKIE['filters'];
				$sel[0]['mark'] = empty($_COOKIE['mark']) ? $sel[0]['mark'] : $_COOKIE['mark'];
				$sel[0]['sort_to'] = empty($_COOKIE['sort_to']) ? $sel[0]['sort_to'] : $_COOKIE['sort_to'];
			}
			return $sel[0];
		}
		else
			return -1;
	}
	function get_additional_user_info($uid)
	{
		$param_arr = array($uid);
		$topics_dates = self::$baseC->query('SELECT min(timest) AS min, max(timest) AS max FROM comments WHERE id IN (SELECT min(id) FROM comments WHERE uid = \'::0::\' GROUP BY tid ORDER BY tid)', 'assoc_array', $param_arr);
		$comments_dates = self::$baseC->query('SELECT min(timest) AS min, max(timest) AS max FROM comments WHERE id NOT IN (SELECT min(id) FROM comments WHERE uid = \'::0::\' GROUP BY tid ORDER BY tid)', 'assoc_array', $param_arr);
		$comments_count = self::$baseC->query('SELECT count(*) AS cnt FROM comments WHERE uid = \'::0::\'', 'assoc_array', $param_arr); 
		$topics_count = self::$baseC->query('SELECT count(*) AS cnt FROM (SELECT min(id) FROM comments WHERE uid = \'::0::\' GROUP BY tid ORDER BY tid) AS t', 'assoc_array', $param_arr); 
		$ret = array("first_topic_date"=>$topics_dates[0]['min'], "last_topic_date"=>$topics_dates[0]['max'], "first_comment_date"=>$comments_dates[0]['min'], "last_comment_date"=>$comments_dates[0]['max'], "comments_count"=>$comments_count[0]['cnt'], "topics_count"=>$topics_count[0]['cnt']);
		return $ret;
	}
	function get_user_theme()
	{
		$where_arr = array(array("key"=>'id', "value"=>$_SESSION['user_id'], "oper"=>'='));
		$sel = self::$baseC->select('users', '', '*', $where_arr);
		if(!empty($sel))
		{
			$usr_th = $sel[0]['theme'];
		}
		else
			return -1;
		if($_SESSION['user_id'] == 1)
			$usr_th = empty($_COOKIE['theme']) ? $usr_th : $_COOKIE['theme'];
		$where_arr = array(array("key"=>'id', "value"=>$usr_th, "oper"=>'='));
		$theme = self::$baseC->select('themes', '', '*', $where_arr);
		if (!is_dir('themes/'.$theme[0]['directory']))
		{
			$themes = self::$baseC->select('themes', '', '*');
			$theme = False;
			foreach ($themes as $item)
			{
				if (is_dir('themes/'.$item['directory']))
				{
					$theme = array($item);
					break;
				}
			}
			if (!$theme)
				return -1;
		}
		return $theme[0];
	}
	function get_users_count()
	{
			$sel = self::$baseC->select('users', '', 'count(*) AS cnt', '', '', '', '');
			if(!empty($sel))
				return $sel[0]['cnt'];
			else
				return -1;
	}
	function user_exists($nick)
	{
		$param_arr = array($nick);
		$ret = self::$baseC->query('SELECT id FROM users WHERE nick = \'::0::\'', 'assoc_array', $param_arr);
		if(empty($ret))
			return false;
		else
			return true;
	}
	function send_accept_mail($address, $nick, $password)
	{
		$where_arr = array(array("key"=>'name', "value"=>'appect_mail_subject', "oper"=>'='));
		$subj = self::$baseC->select('settings', '', 'value', $where_arr, 'AND');
		$subject = $subj[0]['value'];
		$where_arr = array(array("key"=>'name', "value"=>'appect_mail_text', "oper"=>'='));
		$txt = self::$baseC->select('settings', '', 'value', $where_arr, 'AND');
		$message = str_replace('[user]', $nick, $txt[0]['value']);
		$message = str_replace('[site]', $_SERVER['HTTP_HOST'], $message);
		$where_arr = array(array("key"=>'name', "value"=>'register_pass_phrase', "oper"=>'='));
		$pass_phrase = self::$baseC->select('settings', '', 'value', $where_arr, 'AND');
		$link = '<a href="'.$_SERVER['HTTP_HOST'].'/register.php?action=register&login='.$nick.'&password='.$password.'&email='.$address.'&hash='.md5($nick.$password.$pass_phrase[0]['value']).'">'.$_SERVER['HTTP_HOST'].'/register.php?action=register&login='.$nick.'&password='.$password.'&email='.$address.'&hash='.md5($nick.$password.$pass_phrase[0]['value']).'</a>';
		$message = str_replace('[link]', $link, $message);
		$headers= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From: root <root@rulinux.net>\r\n";
		$headers .= "Cc: root@rulinux.net\r\n";
		$headers .= "Bcc: root@rulinux.net\r\n";
		$ret = mail($address, $subject, $message, $headers);
		return $ret;
	}
	function add_user($nick, $pass, $name, $lastname, $gender, $email, $show_email, $im, $show_im, $country, $city,$additional, $gmt)
	{
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
					return -2;
			$current_date = gmdate("y-m-d H:i:s");
			$pass = md5($pass);
			$user_arr = array(array('gid', '1'), array('nick', $nick), array('password', $pass), array('name', $name), array('lastname', $lastname), array('birthday', '2011-03-29 12:31:26') , array('gender', $gender), array('email', $email), array('show_email', $show_email), array('im', $im), array('show_im', $show_im), array('country', $country), array('city', $city), array('photo', ''), array('register_date', $current_date), array('last_visit', $current_date), array('captcha', '-1'), array('blocks', 'authorization:l:1,links:l:2,gallery:l:3,tracker:l:4'), array('additional', $additional), array('news_on_page', '10'), array('comments_on_page', '50'), array('threads_on_page', '30'), array('show_avatars', 'false'), array('show_ua', 'true'), array('show_resp', 'false'), array('theme', '1'), array('gmt', $gmt), array('filters', ''), array('mark', '1'), array('banned', 'false'), array('sort_to', 'false'));
			$ret = self::$baseC->insert('users', $user_arr);
			return $ret;
	}
	function ban_user($id, $state)
	{
			$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
			$sel = self::$baseC->select('users', '', '*', $where_arr);
			if(!empty($sel))
			{
				$ret = self::$baseC->update('users', 'banned', $state, 'id', $id);
				return $ret;
			}
			else
				return -1;
	}
	function user_banned($uid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'banned', $where_arr, '', 'banned');
		if(!empty($sel))
		{
			if($sel[0]['banned']=='f')
				return 0;
			elseif ($sel[0]['banned']=='0')
				return 0;
			elseif ($sel[0]['banned']=='t')
				return 1;
			elseif ($sel[0]['banned']=='1')
				return 1;
			else 
				return -2;
		}
		else
			return -1;
	}
	function modify_user_info($field, $value, $id)
	{
		if($id != 1)
		{
			$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
			$sel = self::$baseC->select('users', '', '*', $where_arr);
			if(!empty($sel))
			{
				$ret = self::$baseC->update('users', $field, $value, 'id', $id);
				return $ret;
			}
			else
				return -1;
		}
		else
		{
			setcookie ($field, $value,time()+31536000);
			return 1;
		}
	}
	function modify_user_read_settings($id, $theme, $news_on_page, $comments_on_page, $threads_on_page, $show_photos, $show_ua, $sort_to, $show_resp, $mark)
	{
		$theme = (int)$theme;
		$news_on_page = (int)$news_on_page;
		$comments_on_page = (int)$comments_on_page;
		$threads_on_page = (int)$threads_on_page;
		$show_photos = empty($show_photos) ? 0 : 1;
		$show_ua = empty($show_ua) ? 0 : 1;
		$sort_to = empty($sort_to) ? 0 : 1;
		$show_resp = empty($show_resp) ? 0 : 1;
		if($id!=1)
		{
			$param_arr = array($id, $theme, $news_on_page, $comments_on_page, $threads_on_page, $show_photos, $show_ua, $sort_to, $show_resp, $mark);
			$ret = self::$baseC->query('UPDATE users SET theme = \'::1::\', news_on_page = \'::2::\', comments_on_page = \'::3::\', threads_on_page = \'::4::\', show_avatars = \'::5::\', show_ua = \'::6::\', sort_to = \'::7::\', show_resp = \'::8::\', mark = \'::9::\'  WHERE id = \'::0::\'', 'assoc_array', $param_arr);
			return $ret;
		}
		else
		{
			setcookie ('theme', $theme,time()+31536000);
			setcookie ('news_on_page', $news_on_page,time()+31536000);
			setcookie ('comments_on_page', $comments_on_page,time()+31536000);
			setcookie ('threads_on_page', $threads_on_page,time()+31536000);
			setcookie ('show_avatars', $show_photos,time()+31536000);
			setcookie ('show_ua', $show_ua,time()+31536000);
			setcookie ('sort_to', $sort_to,time()+31536000);
			setcookie ('show_resp', $show_resp,time()+31536000);
			setcookie ('mark', $mark,time()+31536000);
			return 1;
		}
	}
	function modify_user_info_settings($id, $user_name, $user_lastname, $gender, $user_email, $show_email, $user_im, $show_im, $country, $city, $additional)
	{
		$user_name = htmlspecialchars($user_name);
		$user_lastname = htmlspecialchars($user_lastname);
		$gender = empty($gender) ? 0 : 1;
		if(!filter_var($user_email, FILTER_VALIDATE_EMAIL))
		{
			echo 'e-mail указан не верно';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		$show_email = empty($show_email) ? 0 : 1;
		if(!empty($user_im))
		{
			if(!filter_var($user_im, FILTER_VALIDATE_EMAIL))
			{
				echo 'IM указан не верно';
				include 'themes/'.$theme.'/templates/footer.tpl.php';
				exit();
			}
		}
		$show_im = empty($show_im) ? 0 : 1;
		$country = htmlspecialchars($country);
		$city = htmlspecialchars($city);
		$additional = htmlspecialchars($additional);
		$photo = htmlspecialchars($photo);
		$param_arr = array($id, $user_name, $user_lastname, $gender, $user_email, $show_email, $user_im, $show_im, $country, $city, $additional);
		$ret = self::$baseC->query('UPDATE users SET name = \'::1::\', lastname = \'::2::\', gender = \'::3::\', email = \'::4::\', show_email = \'::5::\', im = \'::6::\', show_im = \'::7::\', country = \'::8::\', city = \'::9::\', additional = \'::10::\'  WHERE id = \'::0::\'', 'assoc_array', $param_arr);
		return $ret;
	}
	function filter_users($filter, $value)
	{
		if($_SESSION['user_admin']!=1)
		{
			$where_arr = array(array("key"=>$filter, "value"=>$value, "oper"=>'='));
			$sel = self::$baseC->select('users', '', '*', $where_arr);
			if(!empty($sel))
				return $sel[0];
			else 
				return -2;
		}
		else return -1;
	}
	function get_captcha_level($id)
	{
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'captcha', $where_arr);
		return $sel[0]['captcha'];
	}
	function set_filter($id, $str)
	{
		if($id!=1)
		{
			$ret = self::$baseC->update('users', 'filters', $str, 'id', $id);
			return $ret;
		}
		else
		{
			setcookie ('filters', $str, time()+31536000);
			return 1;
		}
	}
	function get_filter($id)
	{
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'filters', $where_arr);
		if($id==1)
			$sel[0]['filters'] = empty($_COOKIE['filters']) ? $sel[0]['filters'] : $_COOKIE['filters'];
		return $sel[0]['filters'];
	}
	function get_blocks($id)
	{
		$ret = array();
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'blocks', $where_arr);
		$str = $sel[0]['blocks'];
		if($id==1)
			$str = empty($_COOKIE['blocks']) ? $sel[0]['blocks'] : $_COOKIE['blocks'];
		$blocks_arr = explode(",", $str);
		for($i=0; $i<count($blocks_arr);$i++)
		{
			$block = explode(":", $blocks_arr[$i]);
			$ret[$i]=array("name"=>$block[0], "position"=>$block[1], "sort"=>$block[2]);
		}
		return $ret;
	}
}
?>