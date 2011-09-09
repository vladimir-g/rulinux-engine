<?php
ini_set('register_globals','Off');
$db_name='lorng';
$db_user='rluser';
$db_host='localhost';
$db_pass='rluser';
$db_charset='utf8';
$tbl_prefix='';
$connection = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect to database');;
mysql_select_db($db_name);
mysql_query('SET CHARACTER SET \''.$db_charset.'\'');

/**************************конвертация*таблички*users*************************************/

$query = 'SELECT * FROM users ORDER BY id';
$res = mysql_query($query);
while ($arr = mysql_fetch_assoc($res))
{
	if($arr['gid']==2)
		$gid = 3;
	else
		$gid = $arr['gid'];
	$nick = '\''.$arr['nick'].'\'';
	if($arr['nick'] == 'anonymous')
		continue;
	$password = '\''.$arr['pass'].'\'';
	$name = '\''.pg_escape_string($arr['name']).'\'';
	$lastname = '\'\'';
	if($arr['birthday'] != '0000-00-00')
		$birthday = '\''.pg_escape_string($arr['birthday']).' 00:00:00'.'\'';
	else
		$birthday = '\'2000-01-01 00:00:00\'';
	if($arr['gender'] == '1' || $arr['gender']=='m' || empty($arr['gender']))
		$gender = '\'1\'';
	else
		$gender = '\'0\'';
	if(!empty($arr['email']))
		$email = '\''.pg_escape_string($arr['email']).'\'';
	else
		$email = '\'\'';
	$show_email = '\''.pg_escape_string($arr['show_email']).'\'';
	if(!empty($arr['im']))
		$im = '\''.pg_escape_string($arr['im']).'\'';
	else
		$im = '\'\'';
	$show_im = '\''.pg_escape_string($arr['show_im']).'\'';
	if(!empty($arr['country']))
		$country = '\''.pg_escape_string($arr['country']).'\'';
	else
		$country = '\'\'';
	if(!empty($arr['city']))
		$city = '\''.pg_escape_string($arr['city']).'\'';
	else
		$city = '\'\'';
	if(!empty($arr['photo']))
		$photo = '\''.pg_escape_string($arr['photo']).'\'';
	else
		$photo = '\'\'';
	$register_date = '\''.pg_escape_string($arr['registered']).' 00:00:00'.'\'';
	if($arr['last_visit'] != '0000-00-00')
		$last_visit = '\''.pg_escape_string($arr['last_visit']).' 00:00:00'.'\'';
	else
		$last_visit = '\'2000-01-01 00:00:00\'';
	$captcha = '\''.pg_escape_string($arr['captcha']).'\'';
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
	$additional = '\''.pg_escape_string($arr['additional']).'\'';
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

file_put_contents('users.sql', $str);

/**************************конвертация*тредов*************************************/

$query = 'SELECT MAX(tid) AS mtid, MAX(cid) AS mcid FROM comments';
$res = mysql_query($query);
$marr = mysql_fetch_assoc($res);
$max = $marr['mtid'];
//$max = 1000;
$articles_str = '';
$filename = 'threads.sql';
unlink($filename);
$thrfile = fopen($filename, 'a') or die("Can't open file!");
$str = "CREATE TABLE tmp(id SERIAL, old INTEGER NOT NULL, new INTEGER NOT NULL, PRIMARY KEY(id), UNIQUE(old), UNIQUE(new));\r\n";
if(!fwrite($thrfile, $str))
{
	echo "Не могу произвести запись строки str в файл ($filename)<br>";
}

for($i=1; $i<= $max; $i++)
{
	$tid=0;
	$query = "SELECT * FROM comments WHERE tid = $i ORDER BY cid";
	$res = mysql_query($query);
	$arr = mysql_fetch_assoc($res);
 	if(!empty($arr))
	{
		$gquery = "SELECT * FROM gallery WHERE nid = $i";
		$gres = mysql_query($gquery);
		$garr = mysql_fetch_assoc($gres);
		if(empty($garr))
		{
			$nquery = "SELECT * FROM news WHERE id = $i AND cid !=0";
			$nres = mysql_query($nquery);
			$narr = mysql_fetch_assoc($nres);
			if(!empty($narr))
			{
				$tid = $narr['id'];
				$uid = '(SELECT id FROM users WHERE nick = \''.$narr['by'].'\')';
				$referer = '0';
				$timest = '\''.gmdate('Y-m-d H:i:s',$narr['timestamp']).'\'';
				$subject = '\''.pg_escape_string($narr['title']).'\'';
				$comment = '\''.pg_escape_string($narr['text']).'\'';
				$raw_comment = '\'\'';
				$useragent = '\'\'';
				$changing_timest = $timest;
				$changed_by = '0';
				$changed_for = '\'\'';
				$filters = '\'1:0;2:0;3:0;4:0;5:0;6:0;7:0;8:0;\'';
				$show_ua = '\'1\'';
				$md5 = '\''.md5(rand().$timest).'\'';
				$session_id = '\'\'';
				$cid = '(SELECT MIN(id) FROM comments WHERE tid = \''.$i.'\')';
				$section = 1;
				if($narr['cid']<27)
					$subsection = $narr['cid'];
				else
					$subsection = $narr['cid']-1;
				$attached = '\'0\'';
				$approved = '\'1\'';
				if(!empty($narr['approved']))
					$approved_by = '(SELECT id FROM users WHERE nick = \''.$narr['approved'].'\')';
				else
					$approved_by = 3;
				$approve_timest = '\''.gmdate('Y-m-d H:i:s',$narr['approve_time']).'\'';
				$file = '\'\'';
				$file_size = 0;
				$image_size = '\'\'';
				$extension = '\'\'';
				$prooflink = '\'\'';
				$old = $arr['cid'];
				$new = $cid;
			}
			else
			{
				$tquery = 'SELECT * FROM `threads` WHERE tid = '.$i;
				$tres = mysql_query($tquery);
				$tarr = mysql_fetch_assoc($tres);
				if(!empty($tarr))
				{
					$tid = $tarr['tid'];
					$uquery = 'SELECT nick FROM users WHERE id = '.$tarr['uid'];
					$ures = mysql_query($uquery);
					$uarr = mysql_fetch_assoc($ures);
					if(!empty($uarr))
					{
						$uid = '(SELECT id FROM users WHERE nick = \''.$uarr['nick'].'\')';
					}
					else
						$uid = 1;
					$referer = '0';
					$timest = '\''.gmdate('Y-m-d H:i:s',$tarr['posting_date']).'\'';
					$subject = '\''.pg_escape_string($arr['subject']).'\'';
					$comment = '\''.pg_escape_string($arr['comment']).'\'';
					$raw_comment = '\''.pg_escape_string($arr['raw_comment']).'\'';
					$useragent = '\''.$arr['UserAgent'].'\'';
					$changing_timest = '\''.gmdate('Y-m-d H:i:s',$tarr['chandging_date']).'\'';
					$changed_by = '0';
					$changed_for = '\'\'';
					$filters = '\'1:0;2:0;3:0;4:0;5:0;6:0;7:0;8:0;\'';
					$show_ua = '\'1\'';
					$md5 = '\''.md5(rand().$timest).'\'';
					$session_id = '\'\'';
					$cid = '(SELECT MIN(id) FROM comments WHERE tid = \''.$i.'\')';
					$section = 4;
					if($tarr['fid']<4)
						$subsection = $tarr['fid'];
					else if($tarr['fid']==13)
						$subsection = 4;
					else if($tarr['fid']>13)
						$subsection = 10;
					else
						$subsection = $tarr['fid']+1;
					$attached = '\'0\'';
					$approved = '\'0\'';
					$approved_by = 0;
					$approve_timest = $timest;
					$file = '\'\'';
					$file_size = 0;
					$image_size = '\'\'';
					$extension = '\'\'';
					$prooflink = '\'\'';
					$old = $arr['cid'];
					$new = $cid;
				}
				else
					continue;
			}
		}
		else
		{
			$nquery = "SELECT * FROM news WHERE id = $i";
			$nres = mysql_query($nquery);
			$narr = mysql_fetch_assoc($nres);
			if(!empty($narr))
			{
				$tid = $garr['nid'];
				$uid = '(SELECT id FROM users WHERE nick = \''.$garr['by'].'\')';
				$referer = '0';
				$timest = '\''.gmdate('Y-m-d H:i:s',$garr['timestamp']).'\'';
				$subject = '\''.pg_escape_string($narr['title']).'\'';
				$comment = '\''.pg_escape_string($narr['text']).'\'';
				$raw_comment = '\'\'';
				$useragent = '\'\'';
				$changing_timest = $timest;
				$changed_by = '0';
				$changed_for = '\'\'';
				$filters = '\'1:0;2:0;3:0;4:0;5:0;6:0;7:0;8:0;\'';
				$show_ua = '\'1\'';
				$md5 = '\''.md5(rand().$timest).'\'';
				$session_id = '\'\'';
				$cid = '(SELECT MIN(id) FROM comments WHERE tid = \''.$i.'\')';
				$section = 3;
				$subsection = 1;
				$attached = '\'0\'';
				$approved = '\'1\'';
				if(!empty($garr['approved_by']))
					$approved_by = '(SELECT id FROM users WHERE nick = \''.$garr['approved_by'].'\')';
				else if(!empty($narr['approved_by']))
					$approved_by = '(SELECT id FROM users WHERE nick = \''.$narr['approved_by'].'\')';
				else
					$approoved_by = '7';
				$approve_timest = '\''.gmdate('Y-m-d H:i:s',$narr['approve_time']).'\'';
				$file = '\''.$garr['file'].'\'';
				$file_size = $garr['file_size'];
				$image_size = '\''.$garr['image_size'].'\'';
				$extension = '\''.$garr['extension'].'\'';
				$prooflink = '\'\'';
				$old = $arr['cid'];
				$new = $cid;
			}
			else
				continue;
		}
		$str = "INSERT INTO comments(tid, uid, referer, timest, subject, comment, raw_comment, useragent, changing_timest, changed_by, changed_for, filters, show_ua, md5, session_id) VALUES($tid, $uid, $referer, $timest, $subject, $comment, $raw_comment, $useragent, $changing_timest, $changed_by, $changed_for, $filters, $show_ua, $md5, $session_id);\r\n";
		$tstr = "INSERT INTO threads(id, cid, section, subsection, attached, approved, approved_by, approve_timest, file, file_size, image_size, extension, md5, prooflink) VALUES(($i), $cid, $section, $subsection, $attached, $approved, $approved_by, $approve_timest, $file, $file_size, $image_size, $extension, $md5, $prooflink);\r\n";
		if(!fwrite($thrfile, $str))
		{
			echo "Не могу произвести запись строки str в файл ($filename)<br>";
		}
		if(!fwrite($thrfile, $tstr))
		{
			echo "Не могу произвести запись строки tstr в файл ($filename)<br>";
		}
		$cstr = "INSERT INTO tmp(old, new) VALUES($old, $new);\r\n";
		if(!fwrite($thrfile, $cstr))
		{
			echo "Не могу произвести запись строки str в файл ($filename)<br>";
		}
	}
	else
		continue;
	while($arr = mysql_fetch_assoc($res))
	{
		$uquery = 'SELECT nick FROM users WHERE id = '.$arr['uid'];
		$ures = mysql_query($uquery);
		$uarr = mysql_fetch_assoc($ures);
		if(!empty($uarr))
		{
			$uid = '(SELECT id FROM users WHERE nick = \''.$uarr['nick'].'\')';
		}
		else
			$uid = 1;
		if($arr['referer']!=0)
		{
			if($tid < 3224)
				$referer = '(SELECT MIN(id) FROM comments WHERE tid = '.$tid.')';
			else
				$referer = '(SELECT new FROM tmp WHERE old = '.$arr['referer'].')';
		}
		else
			$referer = '(SELECT MIN(id) FROM comments WHERE tid = '.$tid.')';
		$timest = '\''.gmdate('Y-m-d H:i:s',$arr['timestamp']).'\'';
		$subject = '\''.pg_escape_string($arr['subject']).'\'';
		$comment = '\''.pg_escape_string($arr['comment']).'\'';
		$raw_comment = '\''.pg_escape_string($arr['raw_comment']).'\'';
		$useragent = '\''.$arr['UserAgent'].'\'';
		$changing_timest = $timest;
		$changed_by = '0';
		$changed_for = '\'\'';
		$filters = '\'1:0;2:0;3:0;4:0;5:0;6:0;7:0;8:0;\'';
		$show_ua = '\'1\'';
		$md5 = '\''.md5(rand().$timest).'\'';
		$session_id = '\'\'';
		$str = "INSERT INTO comments(tid, uid, referer, timest, subject, comment, raw_comment, useragent, changing_timest, changed_by, changed_for, filters, show_ua, md5, session_id) VALUES($tid, $uid, $referer, $timest, $subject, $comment, $raw_comment, $useragent, $changing_timest, $changed_by, $changed_for, $filters, $show_ua, $md5, $session_id);\r\n";
		if(!fwrite($thrfile, $str))
		{
			echo "Не могу произвести запись строки str в файл ($filename)<br>";
		}
		$old = $arr['cid'];
		$new = '(SELECT MAX(id) FROM comments)';
		$cstr = "INSERT INTO tmp(old, new) VALUES($old, $new);\r\n";
		if(!fwrite($thrfile, $cstr))
		{
			echo "Не могу произвести запись строки str в файл ($filename)<br>";
		}
	}
}
$str = "DROP TABLE tmp;";
if(!fwrite($thrfile, $str))
{
	echo "Не могу произвести запись строки str в файл ($filename)<br>";
}
fclose($thrfile);

/**************************конвертация*статей*************************************/

$query = 'SELECT * FROM articles ORDER BY id';
$res = mysql_query($query);
$i=0;
while ($arr = mysql_fetch_assoc($res))
{
	$tid = '(SELECT MAX(id) FROM threads)+1';
	if($arr['uid']==-1)
		$uid = '1';
	else
		$uid = $arr['uid']+2;
	$referer = '0';
	$timest = '\''.gmdate('Y-m-d H:i:s',$arr['timestamp']).'\'';
	$subject = '\''.pg_escape_string($arr['title']).'\'';
	$comment = '\''.pg_escape_string($arr['body']).'\'';
	$raw_comment = '\'\'';
	$useragent = '\'\'';
	$changing_timest = $timest;
	$changed_by = '0';
	$changed_for = '\'\'';
	$filters = '\'1:0;2:0;3:0;4:0;5:0;6:0;7:0;8:0;\'';
	$show_ua = '\'1\'';
	$md5 = '\''.md5(rand().$timest).'\'';
	$session_id = '\'\'';
	$str = "INSERT INTO comments(tid, uid, referer, timest, subject, comment, raw_comment, useragent, changing_timest, changed_by, changed_for, filters, show_ua, md5, session_id) VALUES($tid, $uid, $referer, $timest, $subject, $comment, $raw_comment, $useragent, $changing_timest, $changed_by, $changed_for, $filters, $show_ua, $md5, $session_id);";
	$cid = '(SELECT MAX(id) FROM comments)';
	$section = 2;
	switch($arr['fid'])
	{
		case 1:
		case 2:
		case 3:
		case 4:
		case 7:
		case 8:
			$subsection = $arr['fid'];
			break;
		case 5:
			$subsection = $arr['fid']+1;
			break;
		case 6:
		case 9:
		case 10:
			$subsection = 1;
			break;
		case 11:
		case 12:
			$subsection = $arr['fid']-2;
			break;
		case 13:
			$subsection = 5;
			break;
		default:
			break;
	};
	$attached = '\'0\'';
	$approved = '\'1\'';
	$approved_by = $arr['approoved']+2;
	$approve_timest = $timest;
	$file = '\'\'';
	$file_size = 0;
	$image_size = '\'\'';
	$extension = '\'\'';
	$prooflink = '\'\'';
	$tstr = "INSERT INTO threads(cid, section, subsection, attached, approved, approved_by, approve_timest, file, file_size, image_size, extension, md5, prooflink) VALUES($cid, $section, $subsection, $attached, $approved, $approved_by, $approve_timest, $file, $file_size, $image_size, $extension, $md5, $prooflink);";
	$articles_str = $articles_str.'
	'.$str.'
	'.$tstr;
	$i++;
}
file_put_contents('articles.sql', $articles_str);
?>
