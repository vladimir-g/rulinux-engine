<?php
final class auth extends object
{
	static $baseC = null;
	function __construct()
	{
		self::$baseC = new base;
		if(empty($_SESSION['is_openid']) || !$_SESSION['is_openid'])
		{
			if (isset($_COOKIE['login']) && isset($_COOKIE['password']))
			{
				$_COOKIE['login'] = preg_replace('/[\'\/\*\s]/', '', $_COOKIE['login']);
				$login = strtolower($_COOKIE['login']);
				$where_arr = array(array("key"=>'lower(nick)', "value"=>$login, "oper"=>'='), array("key"=>'password', "value"=>$_COOKIE['password'], "oper"=>'='), array("key"=>'banned', "value"=>'false', "oper"=>'='));
				$sel = self::$baseC->select('users', '', 'nick', $where_arr, 'AND');
				if(!empty($sel))
				{
					$lower_nick = strtolower($sel[0]['nick']);
					if($lower_nick==$login)
					{
						self::auth_user($login, $_COOKIE['password'], true);
					}
					else
						self::auth_user('anonymous', '', true);
				}
				else
					self::auth_user('anonymous', '', true);
			}
			else
				self::auth_user('anonymous', '', true);
		}
		if( empty($_SESSION['user_id']) || !isset($_SESSION['user_id']))
		{
			session_register("user_id");
			$user_id="1";
			session_register("user_name");
			$user_name="";
			session_register("user_admin");
			$user_admin="";
			session_register("user_moder");
			$user_moder="";
			session_register("is_openid");
			$is_openid=false;
		}
	}
	function auth_user($login, $pass="", $encrypted=false, $openid=false)
	{
		if (!$encrypted)
			$pass = md5($pass);
		$login = strtolower($login);
		$where_arr = array();
		if(!$openid)
			$where_arr = array(array("key"=>'lower(nick)', "value"=>$login, "oper"=>'='), array("key"=>'password', "value"=>$pass, "oper"=>'='), array("key"=>'banned', "value"=>'false', "oper"=>'='));
		else 
			$where_arr = array(array("key"=>'lower(openid)', "value"=>$login, "oper"=>'='), array("key"=>'banned', "value"=>'false', "oper"=>'='));
		$sel = self::$baseC->select('users', '', 'id, nick, gid', $where_arr, 'AND');
		if(!empty($sel))
		{
			$_SESSION['user_id']=$sel[0]['id'];
			$_SESSION['user_name']=$sel[0]['nick'];
			setcookie ('login', $login,time()+31536000);
			setcookie ('password', $pass,time()+31536000);
			$current_date = gmdate('Y-m-d H:i:s');
			self::$baseC->update('users', 'last_visit', $current_date, 'id', $sel[0]['id']);
			$login='yes';
			if($sel[0]['gid']==3)
			{
				$_SESSION['user_moder']=1;
				$_SESSION['user_admin']='';
			}
			elseif ($sel[0]['gid']==2)
			{
				$_SESSION['user_moder']='';
				$_SESSION['user_admin']=1;
			}
			else 
			{
				$_SESSION['user_moder']='';
				$_SESSION['user_admin']='';
			}
			if($openid)
				$_SESSION['is_openid']=true;
		}
	}
}
?>