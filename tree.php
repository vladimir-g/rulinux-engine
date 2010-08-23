<?
define('GLOBAL_SECTION', 'forum');

$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$nid=intval($_GET['nid']);
$cid=$_GET['cid'];
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/news.class.php');
require_once('classes/forum.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/faq.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();
$forumC = new forumClass();

if (intval($_GET['newsid'])>0)
{
	$newsid = $_GET['newsid'];
	$fid = $baseC->eread('comments', 'fid', null, 'tid', $newsid);
	if(count($fid)>1)
		$fid=$fid[0];
	if($fid == 0 || $fid == -1)
	{
		$get_news=$newsC->get_news_by_id($_GET['newsid']);
		$text = $get_news['text'];
		$text = preg_replace('/\\\\cut\{.*?\}/sim', '', $text);
		$ip = "";
		$useragent = "";
		$title = $get_news['title'];
		$cid = $get_news['cid'];
		if (!empty($get_news['approved']))
		{
			$get_news['approve_time'] = $baseC->timeToSTDate($get_news['approve_time']);
			$approved = '<br> Подтверждено: '.$get_news['approved'].' 
			(<a href="profile.php?user='.$get_news['approved'].'">*</a>) ('.$get_news['approve_time'].')';

		}
		else
		{
			$get_news['approve_time'] = $baseC->timeToSTDate($get_news['approve_time']);
			$approved = '';
		}
		$user = $get_news['by'];
		$get_news['timestamp'] = $baseC->timeToSTDate($get_news['timestamp']);
		$timestamp = $get_news['timestamp'];
		$id = $get_news['id'];
		$fcid = $baseC->other_query("SELECT MIN(cid) FROM comments WHERE tid = $id");
		$fcid = $fcid[0][0];
		$add=' AND cid != '.$fcid.' ';
		$hat = 'Новости';
	}
	else
	{
		if($_SESSION['user_admin'] == 1 && isset($_GET['mconf'])){
			$conf = $baseC->check_setting('last_conf');
			$baseC->erewrite('admin', 'conf', $conf, $_SESSION['user_login'], 'user');
			$readed = $baseC->other_query('SELECT users.nick FROM users, admin WHERE users.id=admin.user AND admin.conf=\''.$conf.'\'', 'assoc_array');
		}
		$get_thread = $forumC->getThread($_GET['newsid']);
		$text = $get_thread[0]["comment"];
		$ip = $get_thread[0]["ip"];
		$useragent = $get_thread[0]["useragent"];
		$id = $_GET['newsid'];
		$deleted = $baseC->eread('threads', 'deleted', null, 'tid', $id);
		$attached = $baseC->eread('threads', 'attached', null, 'tid', $id);
		$closed = $baseC->eread('threads', 'closed', null, 'tid', $id);
		$text = str_replace("\n", "<br>\n", $text);
		$text  = str_replace('[q]', '<i>> ', $text);
		$text  = str_replace('[/q]', '</p>', $text);
		$title = $get_thread[0]["subject"];
		if($baseC->eread('threads', 'deleted', '', 'tid', $id))
		{
			$approved = '<br><font color="grey"><b>Тред удален '.$baseC->eread('users', 'nick', '', 'id', $baseC->eread('threads', 'deleted_by', '', 'tid', $id)).' по причине '.$baseC->eread('threads', 'deleted_for', '', 'tid', $id).'</b></font>';
		}
		else
		{
			$approved = "";
		}
		$user = $baseC->eread('users', 'nick', '', 'id', $get_thread[0]["uid"]);
		$timestamp = $baseC->timeToSTDate($get_thread[0]["timestamp"]);
		$query = 'SELECT cid FROM comments WHERE fid ='.$fid.' AND tid = '.$newsid.' AND timestamp = '.$get_thread[0]["timestamp"];
		$mcid = $baseC->other_query($query);
		$fcid = $mcid[0][0];
		//$add=' AND cid != '.$fcid.' ';
		$hat = 'Форум';
	}
	$user_in = $usersC->get_user_info($_SESSION['user_login']);
	if($user_in["show_filthy_lang"])
	{
		$add = $add.' AND filthy_lang = 0 ';
	}
	//if ($get_news['isVote'])
	//{
	
	//}
}
$content['title'] .= $hat.' - '.$title;
require_once('incs/header.inc.php');
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
if ((int)$_GET['del'] > 0) //Скрытие коментария
{
	$c_uid = $baseC->get_field_by_id('comments', 'uid', $_GET['del'], 'cid');
	$your_nick = $baseC->get_field_by_id('users', 'nick',  $c_uid, 'id');
	$tid = $baseC->eread('comments', 'tid', null, 'cid', $_GET['del']);
	$fid = $baseC->eread('comments', 'fid', null, 'cid', $_GET['del']);
	if ((($your_nick == $_SESSION['user_name']) && $c_uid > 0) || ($_SESSION['user_admin'] == 1))
	{
		if(!isset($_POST['purpose']))
		{
			echo '<form action="" method="POST">';
			echo 'Укажите причину удаления:';
			$rules = $usersC->get_rules();
			echo '<select name="delete_for" onchange="submit()">';
			foreach($rules as $rule)
			{
				echo '<option value="'.$rule[0].'">'.$rule[1].'</option>';
			}
			echo '<option value="-1">Другая</option>';
			echo '</select><br>';
			if(isset($_POST['delete_for']))
			{ 
				echo '<input type="text" name="purpose" value="'.$_POST['delete_for'].'" style="width:55%">';
			}
			echo '<br><input type="submit" value="Скрыть">';
			echo '</form>';
		}
		if(isset($_POST['purpose'])){
			if($usersC->hid_comment($_GET['del'], $_POST['purpose'])){
				echo "<fieldset><p align=center>Коментарий успешно скрыт. <br>Через несколько секунд вы будете перенаправлены в тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid&fid=$fid>сюда</a></fieldset>";
				echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid&fid=$fid' /></header>";
			}
			else
			{
				echo 'Не удалось скрыть коментарий';
			}
		}
		echo '<!--content section end-->';
		include_once('incs/bottom.inc.php');
		exit();
	}
}
if ((int)$_GET['res'] > 0) //Восстановление коментария
{
	$c_uid = $baseC->get_field_by_id('comments', 'uid', $_GET['res'], 'cid');
	$your_nick = $baseC->get_field_by_id('users', 'nick',  $c_uid, 'id');
	$tid = $baseC->eread('comments', 'tid', null, 'cid', $_GET['res']);
	$fid = $baseC->eread('comments', 'fid', null, 'cid', $_GET['res']);
	if ((($your_nick == $_SESSION['user_name']) && $c_uid > 0) || $_SESSION['user_admin'] == 1)
	{
		if($usersC->res_comment($_GET['res']))
		{
			echo "<fieldset><p align=center>Коментарий успешно восстановлен. <br>Через несколько секунд вы будете перенаправлены в тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid&fid=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid&fid=$fid' /></header>";
		}
		else
			{
				echo 'Не удалось восстановить коментарий';
			}
		echo '<!--content section end-->';
		include_once('incs/bottom.inc.php');
		exit();
	}
}
if ((int)$_GET['rem'] > 0) //Удаление коментария
{
	if($_POST['ansver']=='Да')
	{
		if ($_SESSION['user_admin'] == 1)
		{
			$tid = $baseC->eread('comments', 'tid', null, 'cid', $_GET['rem']);
			$fid = $baseC->eread('comments', 'fid', null, 'cid', $_GET['rem']);
			if($usersC->rem_comment($_GET['rem']))
			{
				if (preg_match("/tracker/i", $_POST['referer'])) 
				{
					echo "<fieldset><p align=center>Коментарий успешно удален насовсем. <br>Через несколько секунд вы будете перенаправлены в трекер. <br>Если вы не хотите ждать нажмите <a href=/tracker.php>сюда</a></fieldset>";
					echo "<header><meta http-equiv='Refresh' content='0; url=tracker.php' /></header>";
				}
				else
				{
					echo "<fieldset><p align=center>Коментарий успешно удален насовсем. <br>Через несколько секунд вы будете перенаправлены в тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid&fid=$fid>сюда</a></fieldset>";
					echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid&fid=$fid' /></header>";
				}
			}
			echo '<!--content section end-->';
			include_once('incs/bottom.inc.php');
			exit();
		}
	}
	elseif($_POST['ansver']=='Нет')
	{
		$tid = $baseC->eread('comments', 'tid', null, 'cid', $_GET['rem']);
		$fid = $baseC->eread('comments', 'fid', null, 'cid', $_GET['rem']);
		if (preg_match("/tracker/i", $_POST['referer'])) 
		{
			echo "<fieldset><p align=center>Удаление коментария отменено. <br>Через несколько секунд вы будете перенаправлены в трекер. <br>Если вы не хотите ждать нажмите <a href=/tracker.php>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=tracker.php' /></header>";
		}
		else
		{
			echo "<fieldset><p align=center>Удаление коментария отменено. <br>Через несколько секунд вы будете перенаправлены в тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid&fid=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid&fid=$fid' /></header>";
		}
		echo '<!--content section end-->';
		include_once('incs/bottom.inc.php');
		exit();
	}
	else
	{
		if ($_SESSION['user_admin'] == 1)
		{
			?>
			<form method=POST action=message.php?newsid=<?=$_GET['newsid']?>&rem=<?=$_GET['rem']?>>
			<fieldset><p align=center>
			Вы действительно хотите удалить безвозвратно сообщение?<br><br>
			<input type="submit" name="ansver" value="Да">
			<input type="submit" name="ansver" value="Нет">
			<?
			if(strstr($_SERVER['HTTP_REFERER'], 'tracker.php'))
			{
				echo '<input type="hidden" name="referer" value="tracker.php">';
			}
			?>
			</p></fieldset>
			</form>
			<?
		}
			include_once('incs/bottom.inc.php');
			exit();
	}
}

if (strpos($header, '[menu]')<0 && strpos($footer, '[menu]')<0)
	$pagesC->get_menu();
$order=$baseC->eread('news_config', 'value', null, 'name', 'order');
if ($order=='21')
	$update=$get_news[1]['timestamp'];
else 
	$update=$get_news[sizeof($get_news)-1]['timestamp'];
echo '<!--content section begin-->';
if ($nid<=0)
	$nid=1;
$a=0;
if (intval($_GET['newsid'])>0) //Тело
{
	//if ($get_news['id']>0 && $baseC->eread('news', 'active', 'name', 'id', $get_news['id']) > 0)
	//{
		$show_hid = $info["show_hid"];
		if((int)$_SESSION['user_login'] > 0)
			$perpage = $baseC->eread('users', 'comments_on_page', '', 'id', $_SESSION['user_login']);
		else
		{
			if(empty($_COOKIE['comments_on_page']))
				$perpage = 50;
			else
				$perpage = $_COOKIE['comments_on_page'];
		}
		if($show_hid)
			$comment_count = $baseC->other_query('SELECT COUNT(cid) FROM [prefix]comments WHERE tid='.$id.' AND fid='.$fid.' '.$add);
		else
			$comment_count = $baseC->other_query('SELECT COUNT(cid) FROM [prefix]comments WHERE tid='.$id.' AND fid='.$fid.' '.$add.' AND deleted=0');
		$pages = ceil($comment_count[0][0]/$perpage);
		echo '<form method="GET" action="message.php">
		<input type="hidden" name="newsid" value="'.intval($_GET['newsid']).'">
		<table class="nav">
		<tr>
		<td align=left valign=middle>';
		if($fid == 0)
		{
			echo '<a href="news.php">Новости</a> - <b><a href="news.php?cid='.$cid.'">'.$baseC->get_field_by_id('categories', 'name', $cid).'</a></b>';
			//if ($get_news['cid'] == 0)
			//	echo'<b><a href="gallery.php">Галерея</a></b>';
			//elseif ($get_news['cid'] == -1)
			//	echo'<b><a href="votes.php">Голосования</a></b>';
			//else
			//	echo'<b><a href="news.php?cid='.$get_news['cid'].'">'.$baseC->get_field_by_id('categories', 'name', $get_news['cid']).'</a></b>';
		}
		elseif($fid == -1)
		{
			echo '<a href="news.php">Новости</a> - <b><a href="gallery.php">Галерея</a></b>';
		}
		else
		{
			echo '<a href=view-section.php?id=1>Форум</a> - ';
			$name = $baseC->eread('forums', 'name', '', 'forum_id', $fid);
			$rewrite = $baseC->eread('forums', 'rewrite', '', 'forum_id', $fid);
			echo '<b><a href=forum-'.$rewrite.'>'.$name.'</b>';
		}
		echo '</td>
		<td align=right>
		[<a href="http://www.rulinux.net/view-rss.php?section=forum&newsid='.$_GET['newsid'].'">RSS</a>]
		<!--[<a href="ignore-list.jsp">Фильтр</a>] -->
		<select name="filter" onChange="submit()">';
		switch ($_GET['filter'])
		{
			case 'anonymous':
				echo '<option value="anonymous">без анонимных</option>';
				echo '<option value="show">все комментарии</option>';
				break;
			default:
				echo '<option value="show">все комментарии</option>';
				echo '<option value="anonymous">без анонимных</option>';
				break;
		}
		echo '</select>
		</td>
		</table>
		</form>';
		?>
		<div class=messages><div class="title">
		<a href="message.php?newsid=<?=$id?>&fid=<?=$fid?>&page=<?=$pageN?>#<?=$fcid?>"><img border="0" src="design/<?=$tpl_name ?>/id.png" alt="[#]"></a>
		<?
		if($fid>0)
		{
			$usrname = $baseC->eread('users', 'nick', null, 'id', $baseC->eread('comments', 'uid', null, 'cid', $fcid));
			if($_SESSION['user_admin'])
			{
				?>
				<a href="http://whois.domaintools.com/<?=$ip?>"><img border="0" src="design/<?=$tpl_name ?>/ip.png" alt="<?=$tmp['IP']?>"></a>
				<td align = center>
				<a href="group.php?action=remove&tid=<?=$id?>&fid=<?=$fid?>"><img border="0" src="design/<?=$tpl_name ?>/remove.png" alt="Удалить"></a>
				<a href="group.php?action=move&tid=<?=$id?>"><img border="0" src="design/<?=$tpl_name ?>/move.png" alt="Переместить"></a>
				<?
				if($deleted)
				{
					?>
					<a href="group.php?action=unhide&tid=<?=$id?>&fid=<?=$fid?>"><img border="0" src="design/<?=$tpl_name ?>/unhide.png" alt="Восстановить"></a>
					<?
				}
				else
				{
					?>
					<a href="group.php?action=hide&tid=<?=$id?>&fid=<?=$fid?>"><img border="0" src="design/<?=$tpl_name ?>/hide.png" alt="Скрыть"></a>
					<?
				}
				if($attached)
				{
					?>
					<a href="group.php?action=detach&tid=<?=$id?>&fid=<?=$fid?>"><img border="0" src="design/<?=$tpl_name ?>/detach.png" alt="Открепить"></a>
					<?
				}
				else
				{
					?>
					<a href="group.php?action=attach&tid=<?=$id?>&fid=<?=$fid?>"><img border="0" src="design/<?=$tpl_name ?>/attach.png" alt="Прикрепить"></a>
					<?
				}
				if($closed)
				{
					?>
					<a href="group.php?action=open&tid=<?=$id?>&fid=<?=$fid?>"><img border="0" src="design/<?=$tpl_name ?>/open.png" alt="Открыть"></a>
					<?
				}
				else
				{
					?>
					<a href="group.php?action=close&tid=<?=$id?>&fid=<?=$fid?>"><img border="0" src="design/<?=$tpl_name ?>/close.png" alt="Закрыть"></a>
					<?
				}
				?>
				<a href="edit-message.php?cid=<?print $fcid?>"><img border="0" src="design/<?=$tpl_name ?>/edit.png" alt="[Редактировать]"></a>
				</td>
				<?
			}
			else if ($usrname == $_SESSION['user_name'])
			{
				?>
				<a href="edit-message.php?cid=<?print $fcid?>"><img border="0" src="design/<?=$tpl_name ?>/edit.png" alt="[Редактировать]"></a>
				<?
			}
		}
		?>
		</div>
		<div class="msg" id=<?=$fcid?>><h2 class="nt"><?=$title?></h2>
		<?
		echo $text;
		if($_SESSION['user_admin'] == 1 && isset($_GET['mconf'])){
			if(sizeof($readed) > 0){
				echo '<br><em>Тред прочли: '.$readed[0]['nick'];
				foreach($readed as $key => $reader){
					if($key == 0) continue;
					else echo ', '.$reader['nick'];
				}
				echo '</em>';
			}
		}
		?>
		<p><i><?=$user?>
		(<a href="profile.php?user=<?=$user?>">*</a>) (<?=$timestamp?>)</i>
		<?=$approved?>
		<br><br>
		<i><?=$useragent?></i>
		</p>[<a href="comment.php?answerto=<?=$id?>&cid=<?=$fcid?>&fid=<?=$fid?>&news">Ответить на это сообщение</a>]<br>
		</div></div><br>
		<?
		$nfid = $fid;
		$ptid = $baseC->other_query("SELECT MAX(tid) FROM comments WHERE fid = '$nfid' AND tid < $id");
		$ntid = $baseC->other_query("SELECT MIN(tid) FROM comments WHERE fid = '$nfid' AND tid > $id");
		$prtid = $ptid[0][0];
		$nxtid = $ntid[0][0];
                if($prtid > 0){
                    $pcid = $baseC->other_query("SELECT MIN(cid) FROM comments WHERE tid = $prtid");
                    $psubj = $baseC->eread('comments','subject',null,'cid',$pcid[0][0]);
                }
		if($nxtid > 0){
                    $ncid = $baseC->other_query("SELECT MIN(cid) FROM comments WHERE tid =$nxtid");
                    $nsubj = $baseC->eread('comments','subject',null,'cid',$ncid[0][0]);
                }
		?>
		<table class=nav>
		<tr>
		<td align=left valign=middle width="35%">
		<table>
		<tr valign=middle>
		<td align=left valign=top>
		<?
		if(isset($ptid[0][0]))
		{
			?>
			<a href="message.php?newsid=<?=$ptid[0][0]?>&fid=<?=$nfid?>" rel=prev rev=next>
			<?=$psubj?>
			</a>
		<?
		}
		?>
		</td>
		</tr>
		</table>
		</td>
		<td align=left valign=middle width="35%">
		<table width="100%">
		<tr valign=middle align=right>
		<td>
		<?
		if(isset($ntid[0][0]))
		{
			?>
			<a href="message.php?newsid=<?=$ntid[0][0]?>&fid=<?=$nfid?>" rel=prev rev=next>
			<?=$nsubj?>
			</a>
		<?
		}
		?>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		<?
		$limit = $perpage*($_GET['page']).', '.$perpage;
		if($pages > 1)
		{
			echo '<div class="pageinfo">[страница ';
			for ($p = 0; $p < $pages; $p++)
			{
				if ($p == (int)$_GET['page'])
					if (!isset($_GET['all']))
						echo '<b>'.($p+1).'</b>&nbsp;';
					else
						echo '<a href="message.php?newsid='.$_GET['newsid'].'&fid='.$fid.'&page='.$p.'">'.($p+1).'</a>&nbsp;';
				else
					echo '<a href="message.php?newsid='.$_GET['newsid'].'&fid='.$fid.'&page='.$p.'">'.($p+1).'</a>&nbsp;';
			}
			if (isset($_GET['all']))
				echo ' <b>все</b> ]</div>';
			else
				echo ' <a href="message.php?newsid='.$_GET['newsid'].'&fid='.$fid.'&all">все</a> ]</div>';
		}
                $baseQuery = '
                    SELECT c.subject, c.cid, c.tid, c.uid, c.referer ref, u.status, c.ip IP, c.deleted, u.status active, c.UserAgent UA, c.deleted_for, u.nick, c.timestamp, u.showUA, c.comment, u.health
                    FROM `comments` `c`, `users` `u`
                    WHERE c.uid=u.id
                ';
		if ($show_hid == 1)
		{
			if ($_GET['filter'] == 'anonymous')
			{
				if (isset($_GET['all']))
                                    $comment_ids0 = $baseC->other_query($baseQuery.' AND tid='.$id.' AND fid='.$fid.' AND uid > 0'.$add.' ORDER BY ref, cid ASC', 'assoc_array');
				else
                                    $comment_ids0 = $baseC->other_query($baseQuery.' AND tid='.$id.' AND fid='.$fid.' AND uid > 0'.$add.' ORDER BY ref, cid ASC LIMIT '.$limit, 'assoc_array');
			}
			else
			{
				if (isset($_GET['all']))
                                    $comment_ids0 = $baseC->other_query($baseQuery.$add.' AND tid='.$id.' AND fid='.$fid.' ORDER BY ref, cid ASC', 'assoc_array');
				else
                                    $comment_ids0 = $baseC->other_query($baseQuery.$add.' AND tid='.$id.' AND fid='.$fid.' ORDER BY ref, cid ASC LIMIT '.$limit, 'assoc_array');
			}
		}
		else
		{
			if ($_GET['filter'] == 'anonymous')
			{
				if (isset($_GET['all']))
                                    $comment_ids0 = $baseC->other_query($baseQuery.' AND tid='.$id.' AND `deleted`=0  AND fid='.$fid.' AND uid > 0'.$add.' ORDER BY ref, cid ASC', 'assoc_array');
				else
                                    $comment_ids0 = $baseC->other_query($baseQuery.' AND tid='.$id.' AND `deleted`=0  AND fid='.$fid.' AND uid > 0'.$add.' ORDER BY ref, cid ASC LIMIT '.$limit, 'assoc_array');
			}
			else
			{
				if (isset($_GET['all']))
                                    $comment_ids0 = $baseC->other_query($baseQuery.' AND tid='.$id.' AND fid='.$fid.' AND `deleted`=0'.$add.' ORDER BY ref, cid ASC', 'assoc_array');
				else
                                    $comment_ids0 = $baseC->other_query($baseQuery.' AND tid='.$id.' AND fid='.$fid.' AND `deleted`=0'.$add.' ORDER BY ref, cid ASC LIMIT '.$limit, 'assoc_array');
			}
		}
                function findChild($array, $parent, $key = 'ref', $param = 'cid'){
                    foreach($array as $elm){
                        if($elm[$key] == $parent)
                            return $elm[$param];
                    }
                    return 0;
                }
                
                function buildCommentTree($array, $baseID, $params, $level = 0){
                    $baseC = new base();
                    $usersC = new users();
                    
                    //echo str_repeat("   ", $level).$array[$baseID]['subject'] . "\n";
                   foreach($params as $k => $v)
                        $$k = $v;
                    if($fcid != $baseID){
//------------------------------------
                        $tmp = array();
                        $deleted = '';
                        $tmp = $array[$baseID];
                        if($baseC->get_field_by_id('comments', 'deleted', $baseID, 'cid') == 1)
                        {
                                $tmp['deleted_for'] = '';
                                $deleted = '<span style="font-weight:bolder;">Скрыто '.$tmp['deleted_by'].' по причине "'.$tmp['deleted_for'].'"</span><br>';
                        }
                        else
                                $deleted = '';
                        $tmp['timestamp'] = $baseC->timeToSTDate($tmp['timestamp']);
                        $tmp['showUA'] = $baseC->get_field_by_id('users', 'showUA', $tmp['uid']);
                        $tmp['comment'] = str_replace("\n", "<br>\n", $tmp['comment']);
                        $answtoauthor = $usersC->get_user_info($baseC->get_field_by_id('comments', 'uid', $tmp['ref'], 'cid'));
                        $comment_author = $usersC->get_user_info($baseC->get_field_by_id('comments', 'uid', $baseID, 'cid'));
                        $user = $tmp['uid'] <= 0 ? 'anonymous' : $tmp['nick'];
                        $user_active = $tmp['uid'] <= 0 ? 'anonymous' : $tmp['nick'];
                        $usr = $answtoauthor['nick'] == '' ? 'anonymous' : $answtoauthor['nick'];
                        if ($_SESSION['user_admin'] == 1 || (int)$tmp['showUA'] > 0)
                        {
                                $additional = '<br>('.$tmp['UA'].')';
                        }
                        else
                                $additional = '';
                        $answerto_time = $baseC->timeToSTDate($baseC->get_field_by_id('comments', 'timestamp', $tmp['ref'], 'cid'));
                        $answerto_count = $baseC->other_query('SELECT count(cid) FROM comments WHERE tid = '.$id.' AND cid <= '.$baseC->get_field_by_id('comments', 'cid', $tmp['ref'], 'cid'));
                        $answerto_pageN = floor($answerto_count[0][0]/$perpage);
                        $answerto = $tmp['ref'] <= 0 ? '' : 'Ответ на: <a href="message.php?newsid='.$id.'&fid='.$fid.'&page='.$answerto_pageN.'#'.$baseC->get_field_by_id('comments', 'cid', $tmp['ref'], 'cid').'">'.$baseC->get_field_by_id('comments', 'subject', $tmp['ref'], 'cid').'</a> от '.$usr.' '.' '.$answerto_time;
                        $tmp['comment']  = str_replace('[q]', '<p style="font-style:italic;">> ', $tmp['comment']);
                        $tmp['comment']  = str_replace('[/q]', '</p>', $tmp['comment']);
                        $user_name = $tmp['active'] ? $user : '<s>'.$user.'</s>';
                        if ($user == 'anonymous')
                                $health = 100;
                        else
                                $health = $baseC->eread('users', 'health', '', 'id', $tmp['uid']);
                        switch ($health)
                        {
                                case $health >= 70:
                                        $health_color = '#068200';
                                        break;
                                case $health < 70 && $health >20:
                                        $health_color = '#e8a500';
                                        break;
                                case $health <= 20:
                                        $health_color = '#ff0000';
                                        break;
                        }
                        if (!empty($comment_author['photo']) && $comment_author != '-1' && ($info['show_avatars']))
                        {
                                $avatar = '<td valign=top align=center width="160px"><img src='.$comment_author['photo'].'></td>';
                        }
                        else
                        {
                                $avatar = "";
                        }
                                $count = $baseC->other_query("SELECT count(cid) FROM comments WHERE tid = $id AND cid <= ".$baseID);
                                $pageN = floor($count[0][0]/$perpage);
                                if($level > 0)
                                    $margin = 20 * $level;
                                ?>
                                <div class="messages" style="margin-left: <?=$margin?>px">
                                <div class="comment" id="cmm<?=$baseID?>">
                                <div class=title><?=$reason?>
                                <a href="message.php?newsid=<?=$id?>&fid=<?=$fid?>&page=<?=$pageN?>#<?=$baseID?>"><img border="0" src="design/<?=$tpl_name ?>/id.png" alt="[#]"></a>
                                <?
                                $del = $baseC->get_field_by_id('comments', 'deleted', $baseID, 'cid');
                                $usrname = $baseC->eread('users', 'nick', null, 'id', $baseC->eread('comments', 'uid', null, 'cid', $baseID));
                                if($_SESSION['user_admin'])
                                {
                                        ?>
                                        <a href="http://whois.domaintools.com/<?=$tmp['IP']?>"><img border="0" src="design/<?=$tpl_name ?>/ip.png" alt="<?=$tmp['IP']?>"></a>
                                        <?
                                        if($del!=1)
                                        {
                                                ?>
                                                <a href="message.php?newsid=<?print $id?>&del=<?print $baseID?>"><img border="0" src="design/<?=$tpl_name ?>/hide.png" alt="[Скрыть]"></a>
                                                <?
                                        }
                                        else
                                        {
                                                ?>
                                                <a href="message.php?newsid=<?print $id?>&res=<?print $baseID?>"><img border="0" src="design/<?=$tpl_name ?>/unhide.png" alt="[Восстановить]"></a>
                                                <?
                                        }
                                        ?>
                                        <a href="edit-message.php?cid=<?print $baseID?>"><img border="0" src="design/<?=$tpl_name ?>/edit.png" alt="[Редактировать]"></a>
                                        <a href="message.php?newsid=<?print $id?>&rem=<?print $baseID?>"><img border="0" src="design/<?=$tpl_name ?>/remove.png" alt="[Удалить]"></a>
                                        <?
                                }
                                else if ($usrname == $_SESSION['user_name'])
                                {
                                        if($del!=1)
                                        {
                                                ?>
                                                <a href="message.php?newsid=<?print $id?>&del=<?print $baseID?>"><img border="0" src="design/<?=$tpl_name ?>/hide.png" alt="[Скрыть]"></a>
                                                <?
                                        }
                                        else
                                        {
                                                ?>
                                                <a href="message.php?newsid=<?print $id?>&res=<?print $baseID?>"><img border="0" src="design/<?=$tpl_name ?>/unhide.png" alt="[Восстановить]"></a>
                                                <?
                                        }
                                        ?>
                                        <a href="edit-message.php?cid=<?print $baseID?>"><img border="0" src="design/<?=$tpl_name ?>/edit.png" alt="[Редактировать]"></a>
                                        <?
                                }
                                ?>
                                <?=$answerto ?><br><?=$deleted?>
                                </div>
                                <div class=msg id=<?=$baseID?>>
                                <div style="width:60px; border: 1px solid #000000; height:5px;" title="<?=$health?>%"><div style="width:<?=$health?>%; border: 1px solid <?=$health_color?>; background-color:<?=$health_color?>; height:3px" title="<?=$health?>%"></div></div>
                                <table cellspacing="0" cellspadding="0" width=100%><tr><?=$avatar ?><td valign=top><h2><?=$tmp['subj']?></h2>
                                <?=$tmp['comment']?>
                                <div class=sign><?=$user_name?>
                                (<a href="profile.php?user=<?=$user_name?>">*</a>)
                                (<?=$tmp['timestamp']?>)<br><?=$additional?></div>
                                <div class=reply>[<a href="comment.php?answerto=<?=$id?>&cid=<?=$baseID?>&fid=<?=$fid?>&news">Ответить на это сообщение</a>]
                                </div></td></tr></table></div></div></div>
                                <?
//------------------------------------
                    }
                    foreach ($array as $row){
                        if ($row['ref'] == $baseID)
                            buildCommentTree($array, $row['cid'], $params, $level + 1);
                    }
                    // выводим корневой элемент, об остальных позаботится рекурсия
                }
		if (sizeof($comment_ids0)<1)
		{
		    $comment_ids = array($comment_ids0);
		}
		else{
                    $comments = array();
                    foreach($comment_ids0 as $comm){
                        $comments[$comm['cid']] = $comm;
                    }
                    $params = array(
                        'id' => $id,
                        'perpage' => $perpage,
                        'fcid' => $fcid,
                        'tpl_name' => $tpl_name
                    );
                    $comments = buildCommentTree($comments, $comment_ids0[0]['cid'], $params);
                    $comment_ids = $comment_ids0;
		}
                unset($comment_ids0);
		if($pages > 1)
		{
			echo '<div class="pageinfo">[страница ';
			for ($p = 0; $p < $pages; $p++)
			{
				if ($p == (int)$_GET['page'])
					if (!isset($_GET['all']))
						echo '<b>'.($p+1).'</b>&nbsp;';
					else
						echo '<a href="message.php?newsid='.$_GET['newsid'].'&fid='.$fid.'&page='.$p.'">'.($p+1).'</a>&nbsp;';
				else
					echo '<a href="message.php?newsid='.$_GET['newsid'].'&fid='.$fid.'&page='.$p.'">'.($p+1).'</a>&nbsp;';
			}
			if (isset($_GET['all']))
				echo ' <b>все</b> ]</div>';
			else
				echo ' <a href="message.php?newsid='.$_GET['newsid'].'&fid='.$fid.'&all">все</a> ]</div>';
		}
		?>
		<table class=nav>
		<tr>
		<td align=left valign=middle width="35%">
		<table>
		<tr valign=middle>
		<td align=left valign=top>
		<?
		if(isset($ptid[0][0]))
		{
			?>
			<a href="message.php?newsid=<?=$ptid[0][0]?>&fid=<?=$nfid?>" rel=prev rev=next>
			<?=$psubj?>
			</a>
		<?
		}
		?>
		</td>
		</tr>
		</table>
		</td>
		<td align=left valign=middle width="35%">
		<table width="100%">
		<tr valign=middle align=right>
		<td>
		<?
		if(isset($ntid[0][0]))
		{
			?>
			<a href="message.php?newsid=<?=$ntid[0][0]?>&fid=<?=$nfid?>" rel=prev rev=next>
			<?=$nsubj?>
			</a>
		<?
		}
		?>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		<?
	//}
	//else 
	//	echo '<div class="error" align="center" style="text-align:center; height:100%;vertical-align:middle"><img src="images/helper.png"><br />Ошибка!<br /> 
	//	Запрошенная Вами новость не найдена
	//	</div>';
}
//if (sizeof($get_news)<=0)
//	echo '<h1>Новостей не найдено</h1>';
echo '<!--content section end-->';
include_once('incs/bottom.inc.php');
?>