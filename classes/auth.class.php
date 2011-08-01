<?php
if (isset($_COOKIE['login']) && isset($_COOKIE['password']))
{
	$_COOKIE['login'] = preg_replace('/[\'\/\*\s]/', '', $_COOKIE['login']);
	$where_arr = array(array("key"=>'nick', "value"=>$_COOKIE['login'], "oper"=>'='), array("key"=>'password', "value"=>$_COOKIE['password'], "oper"=>'='), array("key"=>'banned', "value"=>'false', "oper"=>'='));
	$sel = base::select('users', '', 'nick', $where_arr, 'AND');
	if(!empty($sel))
	{
		if($sel[0]['nick']==$_COOKIE['login'])
			auth_user($_COOKIE['login'], $_COOKIE['password'], true);
		else
			auth_user('anonymous', '', true);
	}
	else
		auth_user('anonymous', '', true);
}
else
	auth_user('anonymous', '', true);
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
}
function auth_user($login, $pass, $encrypted)
{
	//echo $login.'<br>';
	if (!$encrypted)
		$pass = md5($pass);
	$where_arr = array(array("key"=>'nick', "value"=>$login, "oper"=>'='), array("key"=>'password', "value"=>$pass, "oper"=>'='), array("key"=>'banned', "value"=>'false', "oper"=>'='));
	$sel = base::select('users', '', 'id, nick, gid', $where_arr, 'AND');
	if(!empty($sel))
	{
		$_SESSION['user_id']=$sel[0]['id'];
		$_SESSION['user_name']=$sel[0]['nick'];
		setcookie ('login', $login,time()+31536000);
		setcookie ('password', $pass,time()+31536000);
		$current_date = gmdate('Y-m-d H:i:s');
		base::update('users', 'last_visit', $current_date, 'id', $sel[0]['id']);
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
	}
	//exit();
}
if ((int)$_SESSION['user_id'] > 0)
{
	$useri = users::get_user_info($_SESSION['user_id']);
	$groupi = users::get_group($useri['gid']);
	$gid = $useri['gid'];
}
?>