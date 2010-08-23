<?
class search{
	function change_settings($setting, $value){
		if ($_SESSION['user_admin']!=2){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$query='
						UPDATE `'.$GLOBALS['tbl_prefix'].'search_config`
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
	function highlite($string){
		$keys = array();
		$a=0;
		for ($i = 0; $i < strlen($k); $i++){
			echo $buff = substr($k, $i, 1);
			if ($buff != ' '){
				$buff = substr($k, $i, 1);
				$keys[$a] = $keys[$a].$buff;
				$buff = '';
			}
			elseif ($buff == ' '){
				$temp = '';
				$buff = '';
				$a = sizeof($keys);
				continue;
			}
		}
		return $keys;
	}
	
	function find1($str, $mode, $time, $section, $user)
	{
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			return 0;
		}
		mysql_selectdb($GLOBALS['db_name']);
		mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
		
		if(empty($str))
		{
			return 0;
		}
		
		if($user!='all')
		{
			$add.='WHERE uid = '.base::eread('users', 'id', null, 'nick', $user).' AND ';
		}
		else
		{
			$add.='WHERE ';
		}
		
		if($time!='both')
		{
			$tm = mktime(date('H')-$h_date, date('i'), 0, date('m'), date('d'), date('Y'));
			//$m3 = $timestamp = mktime(date('H')-$h_date, date('i'), 0, 7, date('d'), date('Y'));
			//$y = mktime(date('H')-$h_date, date('i'), 0, date('m'), date('d'), 2008);
			//$result = $tm - $y;
			//echo $tm.'-'.$y.'='.$result.'<br />';
			if($time=='3month')
			{
				$times = $tm - 794800;
				$add.='timestamp > '.$times.' AND ';
			}
			elseif($time=='year')
			{
				$times = $tm - 31536000;
				$add.='timestamp > '.$times.' AND ' ;
			}
		}
		
		if($section!='all')
		{
			if($section=='news')
			{
				$add.='fid=0 AND ';
			}
			elseif($section=='gallery')
			{
				$add.='fid=-1 AND ';
			}
			elseif($section=='forum')
			{
				$add.='fid!=-1 AND fid!=0 AND ';
			}
		}
		
		$str_arr = array_unique(explode(" ",$str));
		$num = sizeof($str_arr);
		if($mode=='title')
		{
			for($i=0; $i<$num;$i++)
			{
				$add.='subject LIKE \'%'.$str_arr[$i].'%\' AND ';
			}
		}
		elseif($mode=='content')
		{
			for($i=0; $i<$num;$i++)
			{
				$add.='comment LIKE \'%'.$str_arr[$i].'%\' AND ';
			}
		}
		elseif($mode=='both')
		{
			$add.='(';
			for($i=0; $i<$num;$i++)
			{
				//echo $i.' '.$str_arr[$i].'<br>';
				$add.='subject LIKE \'%'.$str_arr[$i].'%\' AND ';
			}
			$add = substr_replace($add, '', -4);
			$add.='OR ';
			for($i=0; $i<$num;$i++)
			{
				//echo $i.' '.$str_arr[$i].'<br>';
				$add.='comment LIKE \'%'.$str_arr[$i].'%\' AND ';
			}
			$add = substr_replace($add, '', -4);
			$add.=') AND ';
		}
		
		$query='SELECT subject, comment, tid, uid, useragent, timestamp, cid FROM comments '.$add.' mconf!=1 ORDER BY timestamp DESC';
		
		//echo $str.', '.$mode.', '.$time.', '.$section.', '.$user.'<br>';
		//echo $query;
		$ret = array();
		$srch_q = mysql_query($query);
		if(srch_q)
		{
			while($srch = mysql_fetch_array($srch_q))
			{	
				//echo '<b>'.$srch['subject'].'</b><br />'.$srch['comment'].'<br /><br />';
				$ret_str = array("subject"=>$srch['subject'], "comment"=>$srch['comment'], "timestamp"=>$srch['timestamp'], "tid"=>$srch['tid'], "cid"=>$srch['cid'], "uid"=>$srch['uid'], "useragent"=>$srch['useragent']);
				$ret[] = $ret_str;
			}
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function find($str, $mode, $strict = false){
		if (!empty($str)){
			//$str = preg_replace('/[\.\,\;\:\!\?]/', '', $str);
			function comp_keys($a, $b){
				$aVal = is_array($a) ? $a['last_name'] : $a;
				$bVal = is_array($b) ? $b['last_name'] : $b;
				return strcasecmp($aVal, $bVal);
			}
			$stop_words = array(
				'и', 'да', 'но', 'даже', 'тоже',
				'а', 'в', 'также', 'же', 'так',
				'потом', 'с', 'у', 'уже', 'к',
				'за', 'из-за', 'из-под', 'на',
				'под', 'из', 'не'
			);
			$key_array = explode(' ', $str);
			$valid_keys = array_udiff($key_array, $stop_words, 'comp_keys');
			$concat = $strict ? 'AND' : 'OR';
			switch ($mode) {
				case 'title':
					foreach ($valid_keys as $vkey){
						if (strlen($condition1) <= 0)
							$condition1 = 'title LIKE \'%'.$vkey.'%\'';
						else
							$condition1 .= ' '.$concat.' title LIKE \'%'.$vkey.'%\'';
					}
					break;
				case 'content':
					foreach ($valid_keys as $vkey){
						if (strlen($condition1) <= 0)
							$condition1 = 'content LIKE \'%'.$vkey.'%\'';
						else
							$condition1 .= ' '.$concat.' content LIKE \'%'.$vkey.'%\'';
					}
					break;
				case 'both':
					foreach ($valid_keys as $vkey){
						if (strlen($condition1) <= 0)
							$condition1 = 'title LIKE \'%'.$vkey.'%\' OR content LIKE \'%'.$vkey.'%\'';
						else
							$condition1 .= ' '.$concat.' title LIKE \'%'.$vkey.'%\' OR content LIKE \'%'.$vkey.'%\'';
					}
					break;
				default: return 0;
			}
			$condition2 = str_replace('content', 'text', $condition1);
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$query1='
						SELECT *
						FROM `'.$GLOBALS['tbl_prefix'].'pages`
						WHERE '.$condition1.'
						GROUP BY title';
				$query2='
						SELECT `id`, `title`, `text`
						FROM `'.$GLOBALS['tbl_prefix'].'news`
						WHERE '.$condition2.'
						GROUP BY title';
				if ($found_res=mysql_query($query1)){
					$a=1;
					while ($found=mysql_fetch_object($found_res)) {
						$ret['content'][$a]=array(
													'id'=>$found->id,
													'title'=>$found->title,
													'content'=>$found->content
													);
						$ret['size'] = $a;
						$a++;
					}
				}
				if ($found_res=mysql_query($query2)){
					$a=1;
					while ($found=mysql_fetch_object($found_res)) {
						$ret['news'][$a]=array(
												'id'=>$found->id,
												'title'=>$found->title,
												'text'=>$found->text
												);
						$a++;
					}
				}
				
				return $ret;
			}
			else return -1;
		}
		else return 0;
	}
}
?>
