<?
class news{
	function get_news_ids($table='news'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$ids_res=mysql_query('
								SELECT `id`
								FROM `'.$GLOBALS['tbl_prefix'].$table.'`
								');
			$i=0;
			while ($ids=mysql_fetch_object($ids_res)) {
				$ret[$i]=$ids->id;
				$i++;
			}
			
			return $ret;
		}
		else return -1;
	}
		
	function add_news($category, $title, $text, $date, $desc, $type, $active, $by, $fid)
	{
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				preg_match('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', $date, $found);
				$date = mktime($found[4], $found[5], $found[6], $found[2], $found[1], $found[3]);
				$uid = base::eread('users', 'id',null, 'nick', $by);
				$ip = getenv ("REMOTE_ADDR");;
				$useragent = $_SERVER['HTTP_USER_AGENT'];
				$tid = base::other_query("SELECT MAX(tid) FROM comments");
				$tid = $tid[0][0]+1;
				$title = strip_tags($title, ENT_QUOTES);
				$title = mysql_real_escape_string($title);
				$useragent = strip_tags($useragent, ENT_QUOTES);
				$useragent = mysql_real_escape_string($useragent);
				$text = mysql_real_escape_string($text);
				$query='
					INSERT INTO `'.$GLOBALS['tbl_prefix'].'comments`
					(`tid`, `uid`, `fid`, `referer`, `parent`, `deleted`, `deleted_for`, `deleted_by`, `timestamp`, `subject`, `comment`, `ip`, `useragent`)
					VALUES
					(\''.$tid.'\', \''.$uid.'\', \''.$fid.'\', \'0\', \'0\', \'0\', \'\', \'0\', \''.$date.'\', \''.$title.'\', \''.$text.'\', \''.$ip.'\', \''.$useragent.'\')';
				if (mysql_query($query))
				{
					//$nid = base::other_query(" SELECT tid FROM `comments` WHERE uid = $uid AND fid = 0 AND title = $title AND timestamp = $date AND ip = $ip AND useragent = $useragent;");
					//$id = $nid[0][0];
					//(SELECT tid FROM `comments` WHERE uid = \''.$uid.'\' AND fid = \'0\' AND title = \''.$title.'\' AND timestamp = \''.$date.'\' AND ip = \''.$ip.'\' AND useragent = \''.$useragent.'\')
					$query1='
						INSERT INTO `'.$GLOBALS['tbl_prefix'].'news`
						(`id`, `cid`, `title`, `text`, `timestamp`, `desc`, `type`, `active`, `by`)
						VALUES
						(\''.$tid.'\', \''.$category.'\', \''.$title.'\', \''.$text.'\', \''.$date.'\', \''.$desc.'\', \''.$type.'\', \''.$active.'\', \''.$by.'\')
						';
					if (mysql_query($query1))
						return mysql_insert_id();
					else return -1;
				}
				else 
				{
					echo mysql_error();
					return -1;
				}
			}
			else return -1;
	}
	/*function add_news($category, $title, $text, $date, $desc, $type, $active, $by){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				 preg_match('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', $date, $found);
				$date = mktime($found[4], $found[5], $found[6], $found[2], $found[1], $found[3]);
				$query='
						INSERT INTO `'.$GLOBALS['tbl_prefix'].'news`
						(`cid`, `title`, `text`, `timestamp`, `desc`, `type`, `active`, `by`)
						VALUES
						(\''.$category.'\', \''.$title.'\', \''.$text.'\', \''.$date.'\', \''.$desc.'\', \''.$type.'\', \''.$active.'\', \''.$by.'\')
						';
				if (mysql_query($query))
					return mysql_insert_id();
				else return -1;
			}
			else return -1;
	}*/
	function add_scrot($title, $desc, $file, $extension, $file_size, $image_size, $date, $by, $active, $nid){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				 preg_match('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', $date, $found);
				$date = mktime($found[4], $found[5], $found[6], $found[2], $found[1], $found[3]);
				$query='
						INSERT INTO `'.$GLOBALS['tbl_prefix'].'gallery`
						(`title`, `desc`, `file`, `extension`, `file_size`, `image_size`, `by`, `timestamp`, `active`, `nid`)
						VALUES
						(\''.$title.'\', \''.$desc.'\', \''.$file.'\', \''.$extension.'\', \''.$file_size.'\', \''.$image_size.'\', \''.$by.'\', \''.$date.'\', \''.$active.'\', '.$nid.')
						';
				if (mysql_query($query))
					return 1;
				else return -1;
			}
			else return -1;
	}
	function get_news_by_id_adm($id, $part){
		if (!empty($id) && !empty($part)){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$news_res=mysql_query('
										SELECT `'.$part.'`
										FROM `'.$GLOBALS['tbl_prefix'].'news`
										WHERE `id`='.$id
										);
				while ($news=mysql_fetch_object($news_res)){
					$ret=$news->$part;
					if ($part == 'text'){
						$ret = preg_replace('/^\*/', '<li>', $ret);
						$ret = str_replace("\n", "<br>\n", $ret);
					}
				}
				
				return $ret;
			}
			else return -1;
		}
		else return -1;
	}
	function del_news($id, $table = 'news'){
		if ($_SESSION['user_admin']!=2){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$query='
						DELETE
						FROM `'.$GLOBALS['tbl_prefix'].$table.'`
						WHERE `id`='.$id
						;
				if (mysql_query($query)){
					return 1;
					
				}
				else {
					
					return -1;
				}
			}
			else return -1;
		}
		else return -1;
	}
	function change_settings($setting, $value){
		if ($_SESSION['user_admin']!=2){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$query='
						UPDATE `'.$GLOBALS['tbl_prefix'].'news_config`
						SET `value`=\''.$value.'\'
						WHERE `name`=\''.$setting.'\'
						';
				if (mysql_query($query)){
					
					return 1;
				}
				else{
					
					return -1;
				}
			}
			else return -1;
		}
		else return -1;
	}
	function get_news($condition = '', $table = 'news'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$order=base::eread('news_config', 'value', null, 'name', 'order');
				if ($order=='21') $by='`ontop` DESC, `approve_time` DESC, `timestamp` DESC';
				else $by='`timestamp`';
				$query='
						SELECT *
						FROM `'.$GLOBALS['tbl_prefix'].$table.'`
						'.$condition.'
						ORDER BY '.$by;
				if ($get_news_res=mysql_query($query)){
					$ret=array();
					$i=1;
					while ($get_news=mysql_fetch_array($get_news_res)) {
						$n=$get_news;
						$n['text'] = preg_replace('/^\*/', '<li>', $n['text']);
						//$n['text'] = str_replace("\n", "<br>\n", $n['text']);
						$ret[$i]=$n;
						$i++;
					}
					return $ret;
					
				}
				else { return -1;}
		}
		else return -1;
	}
	
	function get_threads_by_id($id, $table = 'news'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$query='
						SELECT *
						FROM `'.$GLOBALS['tbl_prefix'].$table.'`
						WHERE `id`='.$id;
				if ($get_news_res=mysql_query($query)){
					while ($get_news=mysql_fetch_array($get_news_res)) {
						$ret=$get_news;
						//$ret['text'] = str_replace("\n", "<br>\n", $ret['text']);
					}
					return $ret;
					
				}
				else { return -1;}
		}
		else return -1;
	}
	
	function get_news_by_id($id, $table = 'news'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$query='
						SELECT *
						FROM `'.$GLOBALS['tbl_prefix'].$table.'`
						WHERE `id`='.$id;
				if ($get_news_res=mysql_query($query)){
					while ($get_news=mysql_fetch_array($get_news_res)) {
						$ret=$get_news;
						//$ret['text'] = str_replace("\n", "<br>\n", $ret['text']);
					}
					return $ret;
					
				}
				else { return -1;}
		}
		else return -1;
	}
}
?>