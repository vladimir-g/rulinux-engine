<?
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
	function add_comment($tid, $uid, $referer, $subj, $comment){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			function GetRealIp(){
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip=$_SERVER['HTTP_CLIENT_IP'];
				}
				elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
					$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				else{
					$ip=$_SERVER['REMOTE_ADDR'];
				}
				return $ip;
			}
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
			$query = '
				INSERT INTO `'.$GLOBALS['tbl_prefix'].'comments`
				(`tid`,`uid`,`referer`,`parent`,`timestamp`,`subject`,`comment`, `IP`, `UserAgent`)
				VALUES
				(\''.$tid.'\', \''.$uid.'\', \''.$referer.'\', \''.$parent.'\', \''.$timestamp.'\', \''.$subj.'\', \''.$comment.'\', \''.GetRealIp().'\', \''.$_SERVER['HTTP_USER_AGENT'].'\');
			';
			if (mysql_query($query)){
				$query = '
					SELECT `cid`
					FROM `'.$GLOBALS['tbl_prefix'].'comments`
					WHERE `uid`=\''.$uid.'\' AND `timestamp` = \''.$timestamp.'\'
				';
				$query_res = mysql_query($query);;
				while (@$res = mysql_fetch_object($query))
					$cid = $res->cid;
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
	function rm_comment($comment_id, $purpose='', $recurse = true){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$query1 = 'UPDATE comments SET deleted=1, deleted_for=\''.$purpose.'\', deleted_by='.$_SESSION['user_login'].' WHERE cid='.$comment_id;
			if($recurse)
				$query2 = 'UPDATE comments SET deleted=1, deleted_for=\'Ответ на некорректное сообщение\', deleted_by='.$_SESSION['user_login'].' WHERE parent='.$comment_id;
			if(mysql_query($query1) && mysql_query($query2)) return 1;
			else return 0;
		}
	}
	function get_comments_by_uid($uid, $order = 'date', $desc = 'DESC'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$query = '
				SELECT *
				FROM `'.$GLOBALS['tbl_prefix'].'comments`
				WHERE `uid`='.$uid.'
				ORDER BY `'.$order.'` '.$desc.'
				';
			$uinfo_res = mysql_query($query);
			if (mysql_numrows($uinfo_res)>0)
				while ($uinfo=mysql_fetch_array($uinfo_res)) {
					return $uinfo;
				}
			else {echo mysql_error();return -1;}
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
				mysql_close();
				return $ret;
			}
			else return -1;
		}
		else return -1;
	}
	function get_user_info($uid){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if ($uid > 0){
			$uinfo_res=mysql_query('
									SELECT *
									FROM `'.$GLOBALS['tbl_prefix'].'users`
									WHERE `id`='.$uid
									);
			if (mysql_numrows($uinfo_res)>0)
				while ($uinfo=mysql_fetch_array($uinfo_res)) {
					return $uinfo;
				}
			}
			else {echo mysql_error();return -1;}
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
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
					mysql_close();
					return -2;
				}
				else {
					if (mysql_query('
									INSERT INTO `'.$GLOBALS['tbl_prefix'].'users` (`gid` , `nick` , `pass` , `name` , `birthday` , `gender` , `email` , `country` , `city` , `registered`, `status`)
									VALUES (1, \''.$nick.'\', \''.md5($pass).'\', \''.$name.'\', \''.$birth.'\', \''.$gender.'\', \''.$email.'\', \''.$country.'\', \''.$city.'\', \''.date('Y-m-d').'\', \''.$status.'\');
									')){
						mysql_close();
						return 1;
									}
					else {
						echo mysql_error();
						mysql_close();
						return -3;
					}
				}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function modify_user_info($field, $value, $uid){
		if ($_SESSION['user_admin']==1 || $GLOBALS['install']=='yes'){
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
		else return -1;
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
	function make_admin($nick, $uid){
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
					mysql_close();
					return 1;
				}
				else {
					if (mysql_query('
									INSERT INTO `'.$GLOBALS['tbl_prefix'].'admin`(`user`, `level`)
									VALUES ('.$id.', 1)
									')){
										mysql_close();
										return 1;
									}
					else{
						mysql_close();
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
