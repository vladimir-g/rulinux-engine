<?
require_once('classes/config.class.php');
class users{
	public static $rules_table = array(
		'pr', 'pa', 'pe', 'pd',
		'ur', 'ua', 'ue', 'ud',
		'nr', 'na', 'ne', 'nd',
		'sr', 'se'
	);
	function get_group($id){
		if ($id == 'all'){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				$ret=array();
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
				$query = '
					SELECT `gid`, `name`
					FROM `'.$GLOBALS['tbl_prefix'].'groups`
					WHERE 1
				';
				if ($groups_res = mysql_query($query)){
					$i = 0;
					while($groups = mysql_fetch_object($groups_res)){
						$ret[$i] = array(
							'id' => $groups->gid,
							'name' => $groups->name,
						);
						$i++;
					}
				}
				else
					return -2;
				return $ret;
			}
			else
				return -1;
		}
		elseif((int)$id > 0){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
				$query = '
					SELECT *
					FROM `'.$GLOBALS['tbl_prefix'].'groups`
					WHERE `gid`='.$id;
				$groups_res = mysql_query($query);
				if (mysql_num_rows($groups_res) > 0){
					while($groups = mysql_fetch_object($groups_res)){
						$ret= array(
							'name' => $groups->name,
							'rules' => $groups->rules,
							'description' => $groups->desc
						);
					}
				}
				else
					return -2;
				return $ret;
			}
			else
				return -1;
		}
	}
	function add_comment($tid, $uid, $referer, $fid, $subj, $comment, $raw_comment, $filthy_lang){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
			$timestamp = date('d.m.Y H:i:s');
			preg_match('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', $timestamp, $found);
			$timestamp = mktime($found[4], $found[5], $found[6], $found[2], $found[1], $found[3]);
			$query = 'SELECT referer, parent FROM comments WHERE cid='.$referer;
			$comm_id_res = mysql_query($query);
			$comm_id = mysql_fetch_object($comm_id_res);
			if ($comm_id->referer == 0)
				$parent = $referer;
			else
				$parent = $comm_id->parent;
			$subj = htmlspecialchars($subj, ENT_QUOTES);
			$subj = mysql_real_escape_string($subj);
			$comment = mysql_real_escape_string($comment);
			$raw_comment = mysql_real_escape_string($raw_comment);
			$ua = htmlspecialchars($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES);
			$ua = mysql_real_escape_string($ua);
			if($fid != 0)
			{
				$tquery = 'UPDATE threads SET chandging_date = \''.$timestamp.'\' WHERE tid = \''.$tid.'\'';
				$thq = mysql_query($tquery);
				if(!thq)
				{
					return -1;
				}
			}
			$query = 'SELECT mconf FROM threads WHERE tid='.$tid;
			$mconf_res = mysql_query($query);
			$mconf = mysql_fetch_object($mconf_res);
			$query = '
				INSERT INTO `'.$GLOBALS['tbl_prefix'].'comments`
				(`tid`,`uid`,`fid`,`referer`,`parent`,`timestamp`,`subject`,`comment`, `IP`, `UserAgent`, `mconf`, `raw_comment`, `filthy_lang`)
				VALUES
				(\''.$tid.'\', \''.$uid.'\', \''.$fid.'\', \''.$referer.'\', \''.$parent.'\', \''.$timestamp.'\', \''.$subj.'\', \''.$comment.'\', \''.base::GetRealIp().'\', \''.$ua.'\', '.((int)$mconf->mconf).', \''.$raw_comment.'\', \''.$filthy_lang.'\'); 
			';
			if (mysql_query($query))
			{
				$cid = mysql_insert_id();
				return $cid;
			}
			else{
				echo mysql_error();
				return -2;
				echo mysql_error();
			}
		}
		else return -1;
	}
	function hid_comment($comment_id, $purpose='', $recurse = false){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$query1 = 'UPDATE comments SET deleted=1, deleted_for=\''.$purpose.'\', deleted_by='.$_SESSION['user_login'].' WHERE cid='.$comment_id;
			//if($recurse)
				//$query2 = 'UPDATE comments SET deleted=1, deleted_for=\'Ответ на некорректное сообщение\', deleted_by='.$_SESSION['user_login'].' WHERE parent='.$comment_id;
			if(mysql_query($query1) /*&& mysql_query($query2)*/) return 1;
			else return 0;
		}
	}
	
		function res_comment($comment_id){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$query1 = 'UPDATE comments SET deleted=0, deleted_for=\'\', deleted_by=\'\' WHERE cid=\''.$comment_id.'\'';
			if(mysql_query($query1)) return 1;
			else return 0;
		}
	}
	
	function rem_comment($comment_id){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$tid = base::other_query("SELECT tid, fid FROM comments WHERE cid = '$comment_id'");
			$min = base::other_query('SELECT MIN(cid) FROM comments WHERE tid = \''.$tid[0][0].'\';');
			if($comment_id == $min[0][0])
			{
				if($tid[0][1] == 0)
				{
					$query1 = 'DELETE FROM  comments WHERE tid=\''.$tid[0][0].'\'';
					if(mysql_query($query1))
					{
						$query2 = 'DELETE FROM  news WHERE id=\''.$tid[0][0].'\'';
						if(mysql_query($query2))
						{
							return 1;
						}
						else
						{
							echo mysql_error();
							return 0;
						}
					}
					else
					{
						echo mysql_error();
						return 0;
					}
				}
				elseif($tid[0][1] == -1)
				{
					$query1 = 'DELETE FROM  comments WHERE tid=\''.$tid[0][0].'\'';
					if(mysql_query($query1))
					{
						$query2 = 'DELETE FROM  news WHERE id=\''.$tid[0][0].'\'';
						if(mysql_query($query2))
						{
							$query3 = 'DELETE FROM  gallery WHERE nid=\''.$tid[0][0].'\'';
							if(mysql_query($query3))
							{
								return 1;
							}
							else
							{
								echo mysql_error();
								return 0;
							}
						}
						else
						{
							echo mysql_error();
							return 0;
						}
					}
					else
					{
						echo mysql_error();
						return 0;
					}
				}
				else
				{
					$query1 = 'DELETE FROM  comments WHERE tid=\''.$tid[0][0].'\'';
					if(mysql_query($query1))
					{
						$query2 = 'DELETE FROM  threads WHERE tid=\''.$tid[0][0].'\'';
						if(mysql_query($query2))
						{
							return 1;
						}
						else
						{
							echo mysql_error();
							return 0;
						}
					}
					else
					{
						echo mysql_error();
						return 0;
					}	
				}
			}
			else
			{
				$query1 = 'DELETE FROM  comments WHERE cid=\''.$comment_id.'\'';
				if(mysql_query($query1)) return 1;
				else return 0;
			}
		}
	}
	function get_comments_by_uid($uid, $order = 'timestamp', $desc = 'DESC', $deleted = false, $offset, $len){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$uid = $uid ? $uid : 'users.id';
			$del = $deleted ? 1 : 0;
			if ($deleted)
			{
				$add = '';
			}
			else
			{
				
			}			
			
			$query = 'SELECT cid, tid, fid, timestamp, subject, deleted_for, deleted_by FROM comments WHERE uid = '.$uid.' AND mconf!=1 AND deleted = '.$del.' ORDER BY `'.$order.'` '.$desc.' LIMIT '.$offset.', '.$len;
			
			$uinfo_res = mysql_query($query);
			if (mysql_numrows($uinfo_res)>0)
			{
				while ($uinfo=mysql_fetch_array($uinfo_res)) 
				{
					if($uinfo[fid]==0)
					{
						$section='Новости';
						$name=base::eread('categories', 'name', null, 'id', base::eread('news', 'cid', null, id, $uinfo['tid']));
					}
					elseif($uinfo[fid]==-1)
					{
						$section='Новости';
						$name='Галерея';
					}					
					else
					{
						$section='Форум';
						$name=base::eread('forums', 'name', null, 'forum_id', $uinfo['fid']);
					}
					$comment = array("section"=>$section, "name" => $name, "subject"=>$uinfo['subject'], "tid"=>$uinfo['tid'], "fid"=>$uinfo['fid'], "cid"=>$uinfo['cid'], "timestamp"=>$uinfo['timestamp'], "deleted_for"=>$uinfo['deleted_for'] == 0 ? 'Причина не указана' : $uinfo['deleted_for'], "deleted_by"=>$uinfo['deleted_by'] == 0 ? 'Неизвестно' : base::eread('users', 'nick', null, 'id', $uinfo['deleted_by']));
					$comments[] = $comment;
				}
			}
			
			if (!function_exists('timesort')){
				function timesort($p, $n){
					if ($p['timestamp'] == $n['timestamp']) {
						 return 0;
					}
					return ($p['timestamp'] > $n['timestamp']) ? -1 : 1;
				}
			}
			if (sizeof($comments) > 0){
				usort($comments, 'timesort');
				$comments = array_slice($comments, 0, $len);
			}
			
			/*
			if ($deleted){
				$query = '
					SELECT comments.cid, comments.tid, categories.name, comments.timestamp, news.title, comments.deleted_for, comments.deleted_by, users.nick del_by
					FROM comments, news, categories, users
					WHERE uid = '.$uid.'
					AND users.id = comments.deleted_by
					AND comments.tid = news.id
					AND news.cid = categories.id
					AND comments.deleted = '.$del.'
					ORDER BY `'.$order.'` '.$desc.'
					LIMIT '.$offset.', '.$len.'
				';
			}
			else{
				$query = '
					SELECT comments.cid, comments.tid, categories.name, comments.timestamp, news.title, comments.deleted_for, comments.deleted_by
					FROM comments, news, categories
					WHERE uid = '.$uid.'
					AND comments.tid = news.id
					AND news.cid = categories.id
					AND comments.deleted = '.$del.'
					ORDER BY `'.$order.'` '.$desc.'
					LIMIT '.$offset.', '.$len.'
				';
			}
			$uinfo_res = mysql_query($query);
			if (mysql_numrows($uinfo_res)>0){
				while ($uinfo=mysql_fetch_array($uinfo_res)) {
					$comments[] = $uinfo;
				}
			}
			$del = $deleted ? 2 : 1;
			$query = '
				SELECT forum_messages.forum_id fid, forum_messages.thread_id tid, forum_messages.message_id, forum_threads.name title, forum_messages.posting_date timestamp, forums.name, users.nick, forum_messages.delete_moder del_by, forum_messages.delete_reason deleted_for
				FROM forum_messages, forum_threads, forums, users
				WHERE users.id = '.$uid.'
				AND forum_messages.user_name = users.nick
				AND forum_threads.thread_id = forum_messages.thread_id
				AND forum_threads.forum_id = forum_messages.forum_id
				AND forums.forum_id = forum_messages.forum_id
				AND forum_messages.stat = '.$del.'
				ORDER BY `'.$order.'` '.$desc.'
				LIMIT '.$offset.', '.$len.'
			';
			$uinfo_res = mysql_query($query);
			if (mysql_numrows($uinfo_res)>0){
				while ($uinfo=mysql_fetch_array($uinfo_res)) {
					$comments[] = $uinfo;
				}
			}
			if (!function_exists('timesort')){
				function timesort($p, $n){
					if ($p['timestamp'] == $n['timestamp']) {
						 return 0;
					}
					return ($p['timestamp'] > $n['timestamp']) ? -1 : 1;
				}
			}
			if (sizeof($comments) > 0){
				usort($comments, 'timesort');
				$comments = array_slice($comments, 0, $len);
			}*/
			return $comments;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	
	function add_group($name, $rules){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
			$query = '
				SELECT `gid`
				FROM `'.$GLOBALS['tbl_prefix'].'groups`
				WHERE `name`=\''.$name.'\'
				';
			$res = mysql_query($query);
			if (mysql_numrows($res) < 1){
				$query = '
					INSERT INTO `'.$GLOBALS['tbl_prefix'].'groups` (`name`, `rules`)
					VALUES (\''.$name.'\', \''.$rules.'\')
				';
				if (mysql_query($query)){
						return 1;
				}
				else return 0;
			}
			else
				return -2;
		}
		else
			return -1;
	}
	function get_users($count){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			$ret=array();
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
			if ($count>0)
				$limit='LIMIT '.$count;
			$query='
					SELECT *
					FROM `'.$GLOBALS['tbl_prefix'].'users`
					'.$limit;
			if ($get_users_res=mysql_query($query)){
				$i=0;
				while ($get_users=mysql_fetch_object($get_users_res)) {
					$ret[$i]=$get_users;
					$ret[$i]=array(
									'id'=>$get_users->id,
									'login'=>$get_users->nick,
									'name'=>$get_users->name,
									'group'=>$get_users->gid
									);
					$i++;
				}
				
				return $ret;
			}
			else return -1;
		}
		else return -1;
	}
	function get_user_info($uid){
		if($uid>0)
		{
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) 
			{
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if ($uid > 0)
				{
					$uinfo_res=mysql_query('SELECT * FROM `'.$GLOBALS['tbl_prefix'].'users` WHERE `id`='.$uid );
					if (mysql_numrows($uinfo_res)>0)
					while ($uinfo=mysql_fetch_array($uinfo_res)) 
					{
						return $uinfo;
					}
				}
				else {echo mysql_error();return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else
		{
			$user = array();
			$user['id'] = '0';
			$user['nick'] = 'anonymous';
			$user['status'] = 1;
			$user['email'] = 'null';
			$user['gid'] = 1;
			$user['registered'] = '2009-02-12 13:42:51';
			$user['show_avatars'] = isset($_COOKIE['show_avatars']) ? $_COOKIE['show_avatars'] : 0;
			$user['showUA'] = isset($_COOKIE['showUA']) ? $_COOKIE['showUA'] : 0;
			$user['show_hid'] = isset($_COOKIE['show_hid']) ? $_COOKIE['show_hid'] : 0;
			$user['show_resp'] = isset($_COOKIE['show_resp']) ? $_COOKIE['show_resp'] : 0;
			$user['show_filthy_lang'] = isset($_COOKIE['show_filthy_lang']) ? $_COOKIE['show_filthy_lang'] : 0;
			$user['sort_to'] = isset($_COOKIE['sort_to']) ? $_COOKIE['sort_to'] : 0;
			$user['threads_on_page'] = isset($_COOKIE['threads_on_page']) ? $_COOKIE['threads_on_page'] : 30;
			$user['comments_on_page'] = isset($_COOKIE['comments_on_page']) ? $_COOKIE['comments_on_page'] : 50;
			$user['news_on_page'] = isset($_COOKIE['news_on_page']) ? $_COOKIE['news_on_page'] : 20;
			$user['left_block'] = isset($_COOKIE['left_block']) ? $_COOKIE['left_block'] : 'auth:1,links:3,gall:2,faq:4';
			$user['right_block'] = isset($_COOKIE['right_block']) ? $_COOKIE['right_block'] : '';
			return $user;
		}
		
	}
	function get_users_count(){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			$ret=array();
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$c_res=mysql_query('
								SELECT `id`
								FROM `'.$GLOBALS['tbl_prefix'].'users`
								');
			return mysql_numrows($c_res);
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_rules(){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
				$ret = array();
				$query = 'SELECT id, name FROM `'.$GLOBALS['tbl_prefix'].'rules`';
				$rules_res = mysql_query($query);
				while ($rules = mysql_fetch_object($rules_res))
					$ret[] = array($rules->id, $rules->name);
				return $ret;
			}
	}
	function add_user($nick, $pass, $name, $email, $country, $city, $birth, $status, $gender){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
				$user_res=mysql_query('
										SELECT `id`
										FROM `'.$GLOBALS['tbl_prefix'].'users`
										WHERE nick = \''.$nick.'\'
										');
				if (mysql_numrows($user_res)>0){
					
					return -2;
				}
				else {
					if (mysql_query('
									INSERT INTO `'.$GLOBALS['tbl_prefix'].'users` (`gid` , `nick` , `pass` , `name` , `birthday` , `gender` , `email` , `country` , `city` , `registered`, `status`)
									VALUES (1, \''.$nick.'\', \''.md5($pass).'\', \''.$name.'\', \''.$birth.'\', \''.$gender.'\', \''.$email.'\', \''.$country.'\', \''.$city.'\', \''.date('Y-m-d').'\', \''.$status.'\');
									')){
						
						return 1;
									}
					else {
						echo mysql_error();
						
						return -3;
					}
				}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function modify_user_info($field, $value, $uid){
			if ($field != '' && $value != ''){
				if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
					mysql_selectdb($GLOBALS['db_name']);
					$ex_res = mysql_query('
												SELECT `nick`
												FROM `'.$GLOBALS['tbl_prefix'].'users`
												WHERE `id`='.$uid
												);
					if (mysql_num_rows($ex_res)>0){
						mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
						$res = mysql_query('
											UPDATE `'.$GLOBALS['tbl_prefix'].'users`
											SET `'.$field.'`=\''.$value.'\'
											WHERE `id`='.$uid
											);
						if ($res)
							return 1;
						else 
							return -3;
					}
					else {
						return -4;
					}
				}
			}
			else 
				return -2;
	}
	function add_vote($question, $params, $by, $multichoice = 0, $active = 0){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
			$time = date('d.m.Y H:i:s');
			preg_match('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', $time, $found);
			$time = mktime($found[4], $found[5], $found[6], $found[2], $found[1], $found[3]);
			$query = 'INSERT INTO `'.$GLOBALS['tbl_prefix'].'votes` (`question`, `timestamp`, `by`, `active`, `multichoice`) VALUES (\''.$question.'\', \''.$time.'\', \''.$by.'\', \''.$active.'\', \''.$multichoice.'\')';
			if (mysql_query($query)){
				$query = '';
				$voteId = mysql_insert_id();
				foreach($params as $param){
					$query = 'INSERT INTO `'.$GLOBALS['tbl_prefix'].'votes_has_answers` (`voteId`, `desc`) VALUES (\''.$voteId.'\', \''.$param.'\')';
					mysql_query($query);
				}
				return $voteId;
			}
			else return -2;
		}
		else return -3;
	}
	function filter_users($filter, $value){
		if ($_SESSION['user_admin']==1){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				$i = 0;
				$ret=array();
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$users_res = mysql_query('
					SELECT *
					FROM `'.$GLOBALS['tbl_prefix'].'users`
					WHERE `'.$filter.'`=\''.$value.'\'
				');
				while ($users = mysql_fetch_object($users_res)){
					$ret[$i] = array(
						'id' => $users->id,
						'gid' => $users->gid,
						'nick' => $users->nick,
						'name' => $users->name,
						'bd' => $users->birthday,
						'gen' => $users->gender,
						'mail' => $users->email,
						'cou' => $users->country,
						'city' => $users->city,
						'reg' => $users->registered,
						'lv' => $users->last_visit,
					);
					$i++;
				}
				return $ret;
			}
			else return 0;
		}
		else return -1;
	}
	function make_admin($uid, $nick = ''){
		if ($_SESSION['user_admin']==1 || $GLOBALS['install']=='yes'){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				$ret=array();
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if($nick!=''){
					$user_res=mysql_query('
											SELECT `id`
											FROM `'.$GLOBALS['tbl_prefix'].'users`
											WHERE `nick`=\''.$nick.'\'
											');
					while ($user=mysql_fetch_object($user_res))
						$id=$user->id;
				}
				$user=mysql_query('
									SELECT `id`
									FROM `'.$GLOBALS['tbl_prefix'].'admin`
									WHERE `user`=\''.$uid.'\'
									');
				if (mysql_numrows($user)>0){
					
					return 1;
				}
				else {
					if (mysql_query('
									INSERT INTO `'.$GLOBALS['tbl_prefix'].'admin`(`user`, `level`)
									VALUES ('.$uid.', 1)
									')){
										
										return 1;
									}
					else{
						return -1;
					}
				}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else return -1;
	}
}
?>
