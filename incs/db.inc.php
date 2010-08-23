<?
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip=$_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
}
else{
	$ip=$_SERVER['REMOTE_ADDR'];
}
$locked = array('84.203.72.231'/*, '93.80.227.59', '193.233.146.10', '72.14.199.205', '95.135.157.222'*/);
if(array_search($ip, $locked) > 0)
   exit('Сайт временно не доступен');

//header('location: http://83.243.67.50');
//exit('Сайт временно не доступен');
ini_set('register_globals','Off');
$db_name='database_name';
$db_user='user';
$db_host='localhost';
$db_pass='password';
$db_charset='utf8';
$tbl_prefix='';

mysql_connect($db_host, $db_user, $db_pass);
mysql_query('SET NAMES '.$db_charset);
$query = 'SELECT id FROM '.$tbl_prefix.'banned_addr WHERE active=1 AND ip=\''.$ip.'\'';
$res = mysql_db_query($db_name, $query);
if (mysql_numrows($res) > 0)
   exit('Ваш адрес был заблокирован администратором. Для более подробной информации: <strong>temy4@jabbus.org</strong>');
?>
