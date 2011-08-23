<?php
ini_set('register_globals','Off');
//$db_name='lorng';
//$db_user='rluser';
//$db_host='localhost';
//$db_pass='rluser';
$db_charset='utf8';
$tbl_prefix='';


$db_name='rulinux';
$db_user='rulinux';
$db_host='nixl.net';
$db_pass='kRd8HdPy1tGa';

$connection = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect to database');;
mysql_select_db($db_name);
mysql_query('SET CHARACTER SET \''.$db_charset.'\'');
/**************************конвертация*таблички*users*************************************/
/*$query = 'SELECT * FROM users';
$res = mysql_query($query);
while ($arr = mysql_fetch_assoc($res))
{
	if($arr['gid']==2)
		$gid = 3;
	else
		$gid = $arr['gid'];
	$nick = '\''.$arr['nick'].'\'';
	if($nick == 'anonymous')
		continue;
	$password = '\''.$arr['pass'].'\'';
	$name = '\''.$arr['name'].'\'';
	$lastname = '\'\'';
	if($arr['birthday'] != '0000-00-00')
		$birthday = '\''.$arr['birthday'].' 00:00:00'.'\'';
	else
		$birthday = '\'2000-01-01 00:00:00\'';
	if($arr['gender'] == '1' || $arr['gender']=='m' || empty($arr['gender']))
		$gender = '\'1\'';
	else
		$gender = '\'0\'';
	if(!empty($arr['email']))
		$email = '\''.$arr['email'].'\'';
	else
		$email = '\'\'';
	$show_email = '\''.$arr['show_email'].'\'';
	if(!empty($arr['im']))
		$im = '\''.$arr['im'].'\'';
	else
		$im = '\'\'';
	$show_im = '\''.$arr['show_im'].'\'';
	if(!empty($arr['country']))
		$country = '\''.$arr['country'].'\'';
	else
		$country = '\'\'';
	if(!empty($arr['city']))
		$city = '\''.$arr['city'].'\'';
	else
		$city = '\'\'';
	if(!empty($arr['photo']))
		$photo = '\''.$arr['photo'].'\'';
	else
		$photo = '\'\'';
	$register_date = '\''.$arr['registered'].' 00:00:00'.'\'';
	if($arr['last_visit'] != '0000-00-00')
		$last_visit = '\''.$arr['last_visit'].' 00:00:00'.'\'';
	else
		$last_visit = '\'2000-01-01 00:00:00\'';
	$captcha = '\''.$arr['captcha'].'\'';
	$blocks = '';
	$blocks_arr = array("authorization"=>'auth', "tracker"=>'tracker', "gallery"=>'gall', "faq"=>'faq', "links"=>'links');
	foreach($blocks_arr as $key => $value)
	{
		$lblk_arr = split(',', $arr['left_block']);
		for($l=0; $l<count($lblk_arr); $l++)
		{
			$fil = split(':', $lblk_arr[$l]);
			for($fl=0; $fl<count($fil); $fl++)
			{
				if($value == $fil[0])
				{
					$blocks = $blocks.$key.':l:'.$fil[1].';';
					continue 3;
				}
			}
		}
		$rblk_arr = split(',', $arr['right_block']);
		for($l=0; $l<count($rblk_arr); $l++)
		{
			$fil = split(':', $rblk_arr[$l]);
			for($fl=0; $fl<count($fil); $fl++)
			{
				if($value == $fil[0])
				{
					$blocks = $blocks.$key.':r:'.$fil[1].';';
					continue 3;
				}
			}
		}
		$blocks = $blocks.$key.':n:1;';
	}
	$blocks = '\''.substr($blocks, 0, strlen($blocks)-1).'\'';
	$additional = '\''.$arr['additional'].'\'';
	$news_on_page = $arr['news_on_page'];
	$comments_on_page = $arr['comments_on_page'];
	$threads_on_page = $arr['threads_on_page'];
	$show_avatars = '\''.$arr['show_avatars'].'\'';
	$show_ua = '\''.$arr['showUA'].'\'';
	$show_resp = '\''.$arr['show_resp'].'\'';
	if($arr['theme']=='simple-black')
		$theme = 1;
	else if($arr['theme']=='Cozzy-green')
		$theme = 2;
	else if($arr['theme']=='simple-white')
		$theme = 3;
	else if($arr['theme']=='Oxygen')
		$theme = 1;
	else if($arr['theme']=='lor2')
		$theme = 4;
	else
		$theme = 1;
	$gmt = $arr['gmt'];
	if(substr($gmt, 0, 1)!='+' && substr($gmt, 0, 1)!='-' && substr($gmt, 0, 1)!='0')
		$gmt = '+'.$gmt;
	$filters = '\'\'';
	$mark = 1;
	$banned = '\'0\'';
	$sort_to = '\''.$arr['sort_to'].'\'';
	$query = "INSERT INTO users(gid, nick, password, name, lastname, birthday, gender, email, show_email, im, show_im, country, city, photo, register_date, last_visit, captcha, blocks, additional, news_on_page, comments_on_page, threads_on_page, show_avatars, show_ua, show_resp, theme, gmt, filters, mark, banned, sort_to) VALUES($gid, $nick, $password, $name, $lastname, $birthday, $gender, $email, $show_email, $im, $show_im, $country, $city, $photo, $register_date, $last_visit, $captcha, $blocks, $additional, $news_on_page, $comments_on_page, $threads_on_page, $show_avatars, $show_ua, $show_resp, $theme, $gmt, $filters, $mark, $banned, $sort_to);";
	$str = $str.'
	'.$query;
	$i++;
}

file_put_contents('users.sql', $str);*/


/**************************конвертация*тредов*************************************/
echo 'yes';
exit;
$query = 'SELECT MAX(tid) AS mtid FROM comments';
$res = mysql_query($query);
$arr = mysql_fetch_assoc($res);
for($i=0; $i< $arr['mtid']; $i++)
{
	$query = "SELECT * FROM comments WHERE tid = $i ORDER BY cid";
	$res = mysql_query($query);
	$arr = mysql_fetch_assoc($res);
	if(empty($arr))
		echo $i;
	else
		echo $arr['cid'];
}
?>
