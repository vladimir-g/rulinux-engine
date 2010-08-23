<?

session_start();
include_once('users.class.php');
if (isset($_COOKIE['lorng_login']) && isset($_COOKIE['lorng_password'])){
	$_COOKIE['lorng_login'] = preg_replace('/[\'\/\*\s]/', '', $_COOKIE['lorng_login']);
	auth_user($_COOKIE['lorng_login'], $_COOKIE['lorng_password'], true);
}
/*$ip=$_SESSION['user_ip'];
$l=$_SESSION['user_login'];
$a=$_SESSION['user_admin'];
if ($_SESSION['SID']!='')
	session_destroy();
session_start();
if ($ip!='') {
	if ($ip==getenv("REMOTE_ADDR")){
		session_start();
		$user_login=$l;
		$user_admin=$a;
		$user_ip=getenv("REMOTE_ADDR");
		session_register("user_login");
		session_register("user_admin");
		session_register("user_ip");
	}
	else {
		session_destroy();
	}
}
*/
//if(isset($_COOKIE['myses']))
//{
//	setcookie('myses','',0);
//	mysql_query("UPDATE ".$prefix."log SET pc_info='".addslashes($_COOKIE['myses'])."' WHERE sid='".session_id()."' AND user_id='".$_SESSION['user']."' AND date>NOW() - INTERVAL 2 HOUR LIMIT 1");
//	if(!mysql_affected_rows())
//	{
//		mysql_query("INSERT INTO ".$prefix."log (date,user_id,ip,pc_info,sid) VALUES (NOW(),'".$_SESSION['user']."', '".ip2long($_SERVER['REMOTE_ADDR'])."' ,'".addslashes($_COOKIE['myses'])."' ,'".session_id()."');");
//	}
//}
if(isset($_POST['login']) && isset($_POST['password']))
	$_POST['login'] = preg_replace('/[\'\/\*\s]/', '', $_POST['login']);

if( empty($_SESSION['user_login']) || !isset($_SESSION['user_login']))
{
   session_register("user_login");
   $user_login="";
   session_register("user_admin");
   $user_admin="";
   session_register("user_ip");
   $user_ip="";
   //print 'register<br>';
}
if(isset($_POST['login']) && isset($_POST['password']))
	auth_user($_POST['login'], $_POST['password'], false);

function auth_user($login, $pass, $encrypted){
if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
}
	if (!$encrypted)
		$pass = md5($_POST['password']);
	$result=mysql_query('SELECT id,status,nick,gid FROM '.$GLOBALS['tbl_prefix'].'users WHERE nick=\''.$login.'\' AND pass=\''.$pass."' AND status='1'");
	if($assoc=@mysql_fetch_assoc($result))
	{
		$_SESSION['user_login']=$assoc['id'];
		$_SESSION['user_name']=$assoc['nick'];
		$_SESSION['user_ip']=getenv("REMORE_ARRD");
		setcookie('lorng_login', $login, (time()+60*60*24*9999));
		setcookie('lorng_password', $pass, (time()+60*60*24*9999));
		mysql_query('
					UPDATE `'.$GLOBALS['tbl_prefix'].'users`
					SET `last_visit`=\''.date('Y-m-d H:i:s').'\'
					WHERE `id`='.$assoc['id']
					);
		//print 'login<br>';
		$login='yes';
		$is_admin = mysql_query('SELECT id from admin where user = '.$assoc['id']);
		if (mysql_numrows($is_admin) > 0)
			$_SESSION['user_admin']=1;
		else
			$_SESSION['user_admin']='';
	
}
}

if ((int)$_SESSION['user_login'] > 0){
	$useri = users::get_user_info($_SESSION['user_login']);
	$groupi = users::get_group($useri['gid']);
	$gid = $useri['gid'];
	$permissions = decbin($groupi['rules']);
}
//print '<br>+|'.$_SESSION['user_login'].'|+';
if(isset($_GET['logout']))
{
	$_SESSION['user_login']='';
	$_SESSION['user_admin']='';
	$_SESSION['user_ip']='';
	setcookie('lorng_login', '', time()-3600);
	setcookie('lorng_pass', '', time()-3600);
	session_destroy();
}
?>