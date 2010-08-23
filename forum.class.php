<?
require_once('classes/config.class.php');
class forumClass
{
	function getThreads($fid, $page, $thr_in_pg, $sorting, $showhidden)//номер форума, номер страницы, к-во тредов на странице, сортировка, показывать скрытые
	{
		$ret = array();
		if($page == 1)
		{
			$limit = 0;
		}
		else
		{
			$limit = ($page-1)*$thr_in_pg;
		}
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		mysql_query("START TRANSACTION; ");
		if($sorting == 0)
		{
			$sort = 'posting_date';
		}
		else if($sorting == 1)
		{
			$sort = 'chandging_date';
		}
		
		if($showhidden == 0)
		{
			$add = ' AND threads.deleted = 0';
			if($_SESSION['user_admin'])
				$add .= '';
			else
				$add .= ' AND threads.mconf=0';
			$thr_q = mysql_query("SELECT * FROM threads, comments WHERE threads.fid = '$fid' AND comments.timestamp = threads.posting_date$add AND comments.timestamp = threads.posting_date ORDER BY attached<>1 ASC, $sort DESC LIMIT $limit, $thr_in_pg;");
		}
		else if($showhidden == 1)
		{
			if($_SESSION['user_admin'])
				$add = '';
			else
				$add = ' AND threads.mconf=0';
			$thr_q = mysql_query("SELECT * FROM threads, comments WHERE threads.fid = '$fid'$add AND comments.timestamp = threads.posting_date ORDER BY attached<>1 ASC, $sort DESC LIMIT $limit, $thr_in_pg");
		}
			$thr_ids = base::eread('threads', 'tid', null, '', '', 'fid='.$fid.' ORDER BY attached<>1 ASC, '.$sort.' DESC LIMIT '.$limit.', '.$thr_in_pg);
			$cond = $thr_ids[0];
			
			foreach($thr_ids as $key => $thr_id){
				if($key <= 0)
					continue;
				else
					$cond .= ','.$thr_id;
			}
			$from_time_day = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$to_time_day = mktime(23, 59, 59, date('m'), date('d'), date('Y'));	
			$from_time_hour = mktime(date('H'), 0, 0, date('m'), date('d'), date('Y'));
			$to_time_hour = mktime(date('H'), 0, 0, date('m'), date('d'), date('Y')) +3599;
			$all_q = base::other_query('
				SELECT COUNT(comments.cid) cid, threads.tid
				FROM comments, threads
				WHERE threads.tid=comments.tid
				AND threads.tid in('.$cond.')
				'.$add.'
				GROUP BY tid
				ORDER BY attached<>1 ASC, posting_date DESC
			');
			$day_q = base::other_query('
				SELECT COUNT(comments.cid) cid, threads.tid
				FROM comments, threads
				WHERE threads.tid=comments.tid
				AND threads.tid in('.$cond.')
				AND ('.$from_time_day.' - timestamp) <= ('.$to_time_day.' - '.$from_time_day.')
				'.$add.'
				GROUP BY tid
				ORDER BY attached<>1 ASC, posting_date DESC
			');
			$hour_q = base::other_query('
				SELECT COUNT(comments.cid) cid, threads.tid
				FROM comments, threads
				WHERE threads.tid=comments.tid
				AND threads.tid in('.$cond.')
				AND ('.$to_time_hour.' - timestamp) <= 3599
				'.$add.'
				GROUP BY tid
				ORDER BY attached<>1 ASC, posting_date DESC
			');
		$thr_i = 0;
		if(thr_q)
		{
			while($thr = mysql_fetch_array($thr_q))
			{		
				
				$tid = $thr['tid'];
				$timestamp = $thr['posting_date']; 
				//$sum_q = mysql_query("SELECT COUNT(cid) FROM comments WHERE deleted =0 AND tid= '$tid'");
				if($all_q[$thr_i]['tid'] == $tid)
					$all = $all_q[$thr_i]['cid'] == 0 ? '-' : $all_q[$thr_i]['cid'];
				
				
				//$day_q = mysql_query("SELECT COUNT(cid) FROM comments WHERE deleted =0 AND tid= '$tid' AND ('$from_time_day' - timestamp) <= ('$to_time_day' - '$from_time_day')");
				$day_f = base::findInMultiArray($day_q, $tid);
				if($day_f >= 0){
					$day = $day_q[$day_f]['cid'] > 0 ? $day_q[$day_f]['cid'] : '-';
				}
				else{
					$day = '-';
				}	
				
				//$hour_q = mysql_query("SELECT COUNT(cid) FROM comments WHERE deleted =0 AND tid= '$tid' AND ('$to_time_hour' - timestamp) <= 3599");
				if(sizeof($hour_q) > 0)
					$hour_f = base::findInMultiArray($hour_q, $tid);
				else
					$hour_f = 0;
				if($hour_f >= 0){
					$hour = $hour_q[$hour_f]['cid'] > 0 ? $hour_q[$hour_f]['cid'] : '-';
				}
				else{
					$hour = '-';
				}
				
//				if ($min_q = mysql_query("SELECT timestamp, tid, uid, deleted, subject FROM comments WHERE fid ='$fid' AND tid = '$tid' AND timestamp = '$timestamp'"))
//				{
//					if($min = mysql_fetch_array($min_q))
//					{
						$str=array("tid"=>$thr['tid'], "uid"=>$thr['uid'], "deleted"=>$thr['deleted'], "closed"=>$thr['closed'],"attached"=>$thr['attached'], "subject"=>$thr['subject'], "timestamp"=>$thr['timestamp '], "in_sum"=>$all, "in_day"=>$day, "in_hour"=>$hour);
						$ret[] = $str;
//					}
//				}	
//				else
//				{
//					echo "<p><b>Error: ".mysql_error()."</b></p>";
//					$ret = 0;
//				}
				$thr_i++;	
			}
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		mysql_query("COMMIT;");
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function getForums()//Получить список форумов
	{
		$ret = array();
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		
		$ath = mysql_query("SELECT forum_id, name, description, rewrite FROM forums ORDER BY sort;");
		if($ath)
		{
			while($forums = mysql_fetch_array($ath))
			{
				$fid = $forums['forum_id'];
				$sum_q = mysql_query("SELECT COUNT(tid) FROM threads WHERE fid= '$fid'");
				if($sum_q)
				{
					if($in_sum = mysql_fetch_array($sum_q))
					{
						$all = $in_sum[0] > 0 ? $in_sum[0] : '-';
					}
					else
					{
						$all = '-';
					}
				}
				else
				{
					echo "<p><b>Error: ".mysql_error()."</b></p>";
					$ret = 0;
				}	
				
				
				$from_time_day = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				$to_time_day = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
				$day_q = mysql_query("SELECT COUNT(tid) FROM threads WHERE fid= '$fid' AND ('$from_time_day' - posting_date) <= ('$to_time_day' - '$from_time_day')");
				if($day_q)
				{
					if($in_day = mysql_fetch_array($day_q))
					{
						$day = $in_day[0] > 0 ? $in_day[0] : '-';
					}
					else
					{
						$day = '-';
					}
				}
				else
				{
					echo "<p><b>Error: ".mysql_error()."</b></p>";
					$ret = 0;
				}	
				
				$from_time_hour = mktime(date('H'), 0, 0, date('m'), date('d'), date('Y'));
				$to_time_hour = mktime(date('H'), 0, 0, date('m'), date('d'), date('Y')) +3599;
				$hour_q = mysql_query("SELECT COUNT(tid) FROM threads WHERE fid= '$fid' AND posting_date >= '$from_time_hour' AND ('$from_time_hour' - posting_date) <= 3599");
				if($hour_q)
				{
					if($in_hour = mysql_fetch_array($hour_q))
					{
						$hour = $in_hour[0] > 0 ? $in_hour[0] : '-';
					}
					else
					{
						$hour = '-';
					}
				}
				else
				{
					echo "<p><b>Error: ".mysql_error()."</b></p>";
					$ret = 0;
				}
				$str = array("fid"=>$forums['forum_id'], "name"=>$forums['name'], "description"=>$forums['description'], "rewrite"=>$forums['rewrite'], "in_sum"=>$all, "in_day"=>$day, "in_hour"=>$hour);
				$ret[] = $str;
			}
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}

	function attachThread($tid)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$ath = mysql_query("UPDATE threads SET attached = 1 WHERE tid = $tid;");
		if($ath)
		{
			$ret = 1;
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function detachThread($tid)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$ath = mysql_query("UPDATE threads SET attached = 0 WHERE tid = $tid;");
		if($ath)
		{
			$ret = 1;
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function closeThread($tid)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$ath = mysql_query("UPDATE threads SET closed = 1 WHERE tid = $tid;");
		if($ath)
		{
			$ret = 1;
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function openThread($tid)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$ath = mysql_query("UPDATE threads SET closed = 0 WHERE tid = $tid;");
		if($ath)
		{
			$ret = 1;
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function deleteThread($tid)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$ath = mysql_query("DELETE FROM `threads` WHERE `tid` = $tid;");
		if($ath)
		{
			$att = mysql_query("DELETE FROM `comments` WHERE `tid` = $tid;");
			if($att)
			{
				$ret = 1;
			}
			else
			{
				echo "<p><b>Error: ".mysql_error()."</b></p>";
				$ret = 0;
			}
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}

	function hideThread($tid, $uid, $reason)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$query = "UPDATE threads SET deleted = 1, deleted_by = '$uid', deleted_for = '$reason'  WHERE tid = $tid;";
		$ath = mysql_query($query);
		if($ath)
		{
			$ret = 1;
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function moveThread($tid, $fid)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$query = "UPDATE threads SET fid = '$fid'  WHERE tid = $tid;";
		$ath = mysql_query($query);
		if($ath)
		{
			$query1 = "UPDATE comments SET fid = '$fid'  WHERE tid = $tid;";
			$att = mysql_query($query1);
			if($att)
			{
				$ret = 1;
			}
			else
			{
				echo "<p><b>Error: ".mysql_error()."</b></p>";
				$ret = 0;
			}
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function unhideThread($tid)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$ath = mysql_query("UPDATE threads SET deleted = 0, deleted_by = '', deleted_for = ''  WHERE tid = $tid;");
		if($ath)
		{
			$ret = 1;
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	
	function getThread($tid)
	{
		$ret = array();
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		
		$ath = mysql_query("SELECT * FROM comments WHERE tid = '$tid' ORDER BY timestamp;");
		if($ath)
		{
			while($comments = mysql_fetch_array($ath))
			{
				//echo $comments['comment'].'<br>';
				$str=array("cid"=>$comments['cid'], "uid"=>$comments['uid'], "timestamp"=>$comments['timestamp'], "subject"=>$comments['subject'], "comment"=>$comments['comment'], "ip"=>$comments['IP'], "useragent"=>$comments['UserAgent']);
				$ret[] = $str;
			}
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
		}
		return $ret;
	}
	function addThread($fid, $uid, $timestamp, $subj, $message, $ip, $useragent, $raw_comment, $mconf = 0, $filthy_lang)
	{
		//echo $uid;
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$subj = htmlspecialchars($subj, ENT_QUOTES);
		$subj = mysql_real_escape_string($subj);
		$message = mysql_real_escape_string($message);
		$useragent = htmlspecialchars($useragent, ENT_QUOTES);
		$useragent = mysql_real_escape_string($useragent);
		$raw_comment = mysql_real_escape_string($raw_comment);
		$tid = base::other_query("SELECT MAX(tid) FROM comments");
		$tid = $tid[0][0]+1;
		$query = "INSERT INTO threads(tid, fid, uid, ip, posting_date, chandging_date, attached, closed, deleted, mconf) VALUES('$tid', '$fid', '$uid', '$ip', '$timestamp', '$timestamp', '0', '0', '0', $mconf)";
		$ath = mysql_query($query);
		//echo $query;
		if($ath)
		{
			$atq = mysql_query("INSERT INTO comments(tid, uid, fid, referer, parent, deleted, deleted_for, deleted_by, timestamp, subject, comment, ip, useragent, raw_comment, mconf, filthy_lang) VALUES((SELECT tid FROM threads WHERE fid='$fid' AND uid='$uid' AND ip='$ip' AND posting_date = '$timestamp'), '$uid', '$fid', '0', '0', '0', '', '0', '$timestamp', '$subj', '$message', '$ip', '$useragent', '$raw_comment', $mconf, $filthy_lang);");
			if($atq)
			{
				$ret = 1;
			}
			else
			{
				echo "<p><b>Error: ".mysql_error()."</b></p>";
				$ret = 0;
			}
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
			$ret = 0;
		}
		return $ret;
	}
	function editMessage($cid, $subj, $message, $raw_message)
	{
		$ret;
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$message = mysql_real_escape_string($message);
		$raw_message = mysql_real_escape_string($raw_message);
		$query = "UPDATE comments SET subject = '$subj', comment='$message', raw_comment = '$raw_message' WHERE cid = '$cid'";
		$ath = mysql_query($query);
		//echo $query;
		if($ath)
		{
			
			$ret = 1;
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			$ret = 0;
		}
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
			$ret = 0;
		}
		return $ret;
	}
}
?>