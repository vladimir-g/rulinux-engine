<?php
class users
{
	function change_users_group($uid, $gid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$sel = base::select('users', '', '*', $where_arr);
		if(!empty($sel))
		{
			$where_arr = array(array("key"=>'id', "value"=>$gid, "oper"=>'='));
			$gr_sel = base::select('groups', '', '*', $where_arr);
			if(!empty($gr_sel))
			{
				$ret = base::update('users', 'gid', $gid, 'id', $uid);
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
			$sel = base::select('groups', '', '*');
			return $sel;
		}
		elseif((int)$id > 0)
		{
			$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
			$sel = base::select('groups', '', '*', $where_arr);
			return $sel[0];
		}
	}
	
	function add_group($name, $description)
	{
		$where_arr = array(array("key"=>'name', "value"=>$name, "oper"=>'='));
		$sel = base::select('groups', '', 'id', $where_arr);
		if(empty($sel))
		{
			$gr_arr = array(array('name', $name), array('description', $description));
			base::insert('groups', $gr_arr);
			return 1;
		}
		else
			return -1;
	}
	
	function get_users($begin, $end)
	{
		$sel = base::select('users', '', '*', '', '', 'nick', 'ASC', $begin, $end);
		if(!empty($sel))
			return $sel;
		else
			return -1;
	}
	
	function get_uid_by_nick($nick)
	{
		$where_arr = array(array("key"=>'nick', "value"=>$nick, "oper"=>'='));
		$sel = base::select('users', '', 'id', $where_arr);
		if(!empty($sel))
			return $sel[0]['id'];
		else
			return -1;
	}
	
	function get_user_info($uid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$sel = base::select('users', '', '*', $where_arr);
		if(!empty($sel))
			return $sel[0];
		else
			return -1;
	}
	
	function get_additional_user_info($uid)
	{
		$param_arr = array($uid);
		$topics_dates = base::query('SELECT min(timest) AS min, max(timest) AS max FROM comments WHERE id IN (SELECT min(id) FROM comments WHERE uid = \'::0::\' GROUP BY tid ORDER BY tid)', 'assoc_array', $param_arr);
		$comments_dates = base::query('SELECT min(timest) AS min, max(timest) AS max FROM comments WHERE id NOT IN (SELECT min(id) FROM comments WHERE uid = \'::0::\' GROUP BY tid ORDER BY tid)', 'assoc_array', $param_arr);
		$comments_count = base::query('SELECT count(*) AS cnt FROM comments WHERE uid = \'::0::\'', 'assoc_array', $param_arr); 
		$topics_count = base::query('SELECT count(*) AS cnt FROM (SELECT min(id) FROM comments WHERE uid = \'::0::\' GROUP BY tid ORDER BY tid) AS t', 'assoc_array', $param_arr); 
		$ret = array("first_topic_date"=>$topics_dates[0]['min'], "last_topic_date"=>$topics_dates[0]['max'], "first_comment_date"=>$comments_dates[0]['min'], "last_comment_date"=>$comments_dates[0]['max'], "comments_count"=>$comments_count[0]['cnt'], "topics_count"=>$topics_count[0]['cnt']);
		return $ret;
	}
	
	function get_user_theme()
	{
		$where_arr = array(array("key"=>'id', "value"=>$_SESSION['user_id'], "oper"=>'='));
		$sel = base::select('users', '', '*', $where_arr);
		if(!empty($sel))
		{
			$where_arr = array(array("key"=>'id', "value"=>$sel[0]['theme'], "oper"=>'='));
			$theme = base::select('themes', '', '*', $where_arr);
			return $theme[0];
		}
		else
			return -1;
	}
	
	function get_users_count()
	{
			$sel = base::select('users', '', 'count(*) AS cnt', '', '', '', '');
			if(!empty($sel))
				return $sel[0]['cnt'];
			else
				return -1;
	}
	
	function add_user($nick, $pass, $name, $lastname, $birthday, $gender, $email, $show_email, $im, $show_im, $country, $city,$additional, $gmt, $mark)
	{
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
					return -2;
			$current_date = date("y-m-d H:i:s");
			$pass = md5($pass);
			$user_arr = array(array('gid', '1'), array('nick', $nick), array('password', $pass), array('name', $name), array('lastname', $lastname), array('birthday', $birthday) , 	array('gender', $gender), array('email', $email), array('show_email', $show_email), array('im', $im), array('show_im', $show_im), array('country', $country), array('city', $city), array('photo', ''), array('register_date', $current_date), array('last_visit', $current_date), array('captcha', '0'), array('left_block', 'auth:1,links:2,gall:3,tracker:4'), array('right_block', ''), array('additional', $additional), array('news_on_page', '10'), array('comments_on_page', '50'), array('threads_on_page', '30'), array('show_avatars', 'false'), array('show_ua', 'true'), array('show_resp', 'false'), array('theme', '1'), array('gmt', $gmt), array('filters', ''), array('mark', $mark), array('banned', 'false'));
			$ret = base::insert('users', $user_arr);
			return $ret;
	}
	
	function ban_user($id, $state)
	{
			$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
			$sel = base::select('users', '', '*', $where_arr);
			if(!empty($sel))
			{
					$ret = base::update('users', 'banned', $state, 'id', $id);
					return $ret;
			}
			else
					return -1;
	}
	
	function user_banned($uid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$sel = base::select('users', '', 'banned', $where_arr, '', 'banned');
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
			if ($field != '' && $value != '')
			{
					$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
					$sel = base::select('users', '', '*', $where_arr);
					if(!empty($sel))
					{
							$ret = base::update('users', $field, $value, 'id', $id);
							return $ret;
					}
					else
							return -2;
			}
			else 
				return -1;
	}
	
	function modify_user_read_settings($id, $theme, $news_on_page, $comments_on_page, $threads_on_page, $show_photos, $show_ua, $sort_to, $show_resp)
	{
		$theme = (int)$theme;
		$news_on_page = (int)$news_on_page;
		$comments_on_page = (int)$comments_on_page;
		$threads_on_page = (int)$threads_on_page;
		$show_photos = empty($show_photos) ? 0 : 1;
		$show_ua = empty($show_ua) ? 0 : 1;
		$sort_to = empty($sort_to) ? 0 : 1;
		$show_resp = empty($show_resp) ? 0 : 1;
		$param_arr = array($id, $theme, $news_on_page, $comments_on_page, $threads_on_page, $show_photos, $show_ua, $sort_to, $show_resp);
		$ret = base::query('UPDATE users SET theme = \'::1::\', news_on_page = \'::2::\', comments_on_page = \'::3::\', threads_on_page = \'::4::\', show_avatars = \'::5::\', show_ua = \'::6::\', sort_to = \'::7::\', show_resp = \'::8::\'  WHERE id = \'::0::\'', 'assoc_array', $param_arr);
		return $ret;
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
		if(!filter_var($user_im, FILTER_VALIDATE_EMAIL))
		{
			echo 'IM указан не верно';
			include 'themes/'.$theme.'/templates/footer.tpl.php';
			exit();
		}
		$show_im = empty($show_im) ? 0 : 1;
		$country = htmlspecialchars($country);
		$city = htmlspecialchars($city);
		$additional = htmlspecialchars($additional);
		$photo = htmlspecialchars($photo);
		$param_arr = array($id, $user_name, $user_lastname, $gender, $user_email, $show_email, $user_im, $show_im, $country, $city, $additional);
		$ret = base::query('UPDATE users SET name = \'::1::\', lastname = \'::2::\', gender = \'::3::\', email = \'::4::\', show_email = \'::5::\', im = \'::6::\', show_im = \'::7::\', country = \'::8::\', city = \'::9::\', additional = \'::10::\'  WHERE id = \'::0::\'', 'assoc_array', $param_arr);
		return $ret;
		
	}
	
	function filter_users($filter, $value)
	{
		if($_SESSION['user_admin']!=1)
		{
			$where_arr = array(array("key"=>$filter, "value"=>$value, "oper"=>'='));
			$sel = base::select('users', '', '*', $where_arr);
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
		$sel = base::select('users', '', 'captcha', $where_arr);
		return $sel[0]['captcha'];
	}
	
	function set_filter($id, $str)
	{
		$ret = base::update('users', 'filters', $str, 'id', $id);
		return $ret;
	}
	
	function get_filter($id)
	{
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$sel = base::select('users', '', 'filters', $where_arr);
		return $sel[0]['filters'];
	}
}
?>
