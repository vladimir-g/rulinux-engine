<?php

session_start();
//include_once('users.class.php');
if (isset($_COOKIE['lorng_login']) && isset($_COOKIE['lorng_password']))
{
	$_COOKIE['lorng_login'] = preg_replace('/[\'\/\*\s]/', '', $_COOKIE['lorng_login']);
	auth_user($_COOKIE['lorng_login'], $_COOKIE['lorng_password'], true);
}

if(isset($_POST['login']) && isset($_POST['password']))
	$_POST['login'] = preg_replace('/[\'\/\*\s]/', '', $_POST['login']);

if( empty($_SESSION['user_id']) || !isset($_SESSION['user_id']))
{
	session_register("user_id");
	$user_id="";
	session_register("user_admin");
	$user_admin="";
	session_register("user_moder");
	$user_moder="";
	//session_register("user_ip");
	//$user_ip="";
}
if(isset($_POST['login']) && isset($_POST['password']))
	auth_user($_POST['login'], $_POST['password'], false);

function auth_user($login, $pass, $encrypted)
{
	if (!$encrypted)
		$pass = md5($pass);
	$where_arr = array(array("key"=>'nick', "value"=>$login, "oper"=>'='), array("key"=>'password', "value"=>$pass, "oper"=>'='), array("key"=>'banned', "value"=>'false', "oper"=>'='));
	$sel = base::select('users', '', 'id, nick, gid', $where_arr, 'AND');
	if(!empty($sel))
	{
		$_SESSION['user_id']=$sel[0]['id'];
		$_SESSION['user_name']=$sel[0]['nick'];
		setcookie('lorng_login', $login, (time()+60*60*24*9999));
		setcookie('lorng_password', $pass, (time()+60*60*24*9999));
		$current_date = date('Y-m-d H:i:s');
		base::update('users', 'last_visit', $current_date);
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
}

if ((int)$_SESSION['user_id'] > 0)
{
	$useri = users::get_user_info($_SESSION['user_id']);
	$groupi = users::get_group($useri['gid']);
	$gid = $useri['gid'];
	//$permissions = decbin($groupi['rules']);
}
//print '<br>+|'.$_SESSION['user_id'].'|+';
if(isset($_GET['logout']))
{
	$_SESSION['user_id']='';
	$_SESSION['user_admin']='';
	//$_SESSION['user_ip']='';
	setcookie('lorng_login', '', time()-3600);
	setcookie('lorng_pass', '', time()-3600);
	session_destroy();
}
?>