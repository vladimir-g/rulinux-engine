<?
include('incs/db.inc.php');
require_once('classes/forum.class.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');

$baseC = new base();
$pagesC = new pages();
$usersC = new users();
$forumC = new forumClass();

if (isset($_GET['h'])=='')
{
	$h_date = 3;
}
else
{
	$h_date = $_GET['h'];
}
//$show_hid = $info["show_hid"];
$p_date = mktime(date('H')-$h_date, date('i'), 0, date('m'), date('d'), date('Y'));
$chisl = $baseC->declOfNum($h_date, array('час', 'часа', 'часов'));
if((int)$_SESSION['user_login'] > 0)
	$perpage = $baseC->eread('users', 'comments_on_page', '', 'id', $_SESSION['user_login']);
else
{
	if(empty($_COOKIE['comments_on_page']))
		$perpage = 50;
	else
		$perpage = $_COOKIE['comments_on_page'];
}
if($info["show_hid"])
{
	$comment_count = $baseC->other_query('SELECT COUNT(cid) FROM [prefix]comments WHERE tid='.$id);
}
else
{
	if($id > 0)
		$comment_count = $baseC->other_query('SELECT COUNT(cid) FROM [prefix]comments WHERE tid='.$id.' AND deleted=0');
}
$content['title'] .= 'Последние сообщения за '.$chisl;
require_once('incs/header.inc.php');
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
?>
<!-- <img src="black/counter_002.gif" alt="" height="1" width="1"> -->
<form action="tracker.php">
<table class=nav><tr>
<td align=left valign=middle>
Последние сообщения за <?=$chisl?>
</td>
<td align=right valign=middle>
за последние
<input name="h" onChange="submit();" value="<?print $h_date?>">
часа
<input type="submit" value="показать">
</td>
</tr>
</table>
</form>
<h1>Последние сообщения за <?=$chisl?></h1>
<div class=forum>
<?
if($info["show_hid"])
{
	$comments = $baseC->other_query("SELECT * FROM comments WHERE timestamp > '$p_date' AND mconf=0 ORDER BY timestamp DESC;");
}
else
{
	$comments = $baseC->other_query("SELECT * FROM comments WHERE timestamp > '$p_date' AND deleted !=1 AND mconf=0 ORDER BY timestamp DESC;");
}
if ($comments[0] != '')
{
	?>
	<table width="100%" class="message-table">
	<thead>
	<tr>
	<?
	if($_SESSION['user_admin'])
	{
		?>
		<th  width = "5%">Управление</th>
		<?
	}
	?>
	<th  width = "12%">Форум</th>
	<th>Заголовок</th>
	<th width = "15%">Время постинга</th>
	</tr>
	</thead>
	<tfoot>
	<?
	if($info["show_hid"])
		$all = $baseC->other_query("SELECT COUNT(tid) FROM comments WHERE timestamp > '$p_date' AND mconf=0;");
	else
		$all = $baseC->other_query("SELECT COUNT(tid) FROM comments WHERE timestamp > '$p_date' AND mconf=0 AND deleted !=1;");
	?>
	<tr><td colspan='4' align='right'>всего: <?print $all[0][0];?> &nbsp</td></tr>
	</tfoot>
	<tbody>
	<?
	foreach ($comments as $comment)
	{
		?>
		<tr>
		<?
		$tid = $comment["tid"];
		$cid = $comment["cid"];
		$count = $baseC->other_query("SELECT count(cid) FROM comments WHERE tid = $tid AND mconf=0 AND cid <= $cid");
		if($count[0][0]==1)
		{
			$pages = floor(($count[0][0])/$perpage);
		}
		else
		{
			$pages = floor(($count[0][0]-2)/$perpage);
		}
		//echo 'count: '.$count[0][0];
		
		if($_SESSION['user_admin'])
		{
			?>
			<td align="center">
			<a href="message.php?newsid=<?=$tid?>&rem=<?=$cid?>"><img border=0 src="design/<?=$tpl_name ?>/remove.png" alt="Удалить"></a>
			<?
			$deleted = $baseC->eread('comments', 'deleted', '', 'cid', $cid);
			if($deleted!=1)
			{
				?>
				<a href="message.php?newsid=<?=$tid?>&del=<?=$cid?>"><img border=0 src="design/<?=$tpl_name ?>/hide.png" alt="Скрыть"></a>
				<?
			}
			else
			{
				?>
				<a href="message.php?newsid=<?=$tid?>&res=<?=$cid?>"><img border=0 src="design/<?=$tpl_name ?>/unhide.png" alt="Восстановить"></a>
				<?
			}
			?>
			</td>
			<?	
		}	
		?>
		<td>
		<?
		$fid = $comment["fid"];
		if($fid == 0)
		{
			$name = "Новости";
			?>
			<a href="/news.php">
			<?
		}
		elseif($fid==-1)
		{
			$name = "Галерея";
			?>
			<a href="/gallery.php">
			<?
		}
		else
		{
			$name = $baseC->eread('forums', 'name', '', 'forum_id', $fid);
			?>
			<a href="/group.php?group=<?=$fid?>">
			<?
		}
		print $name;
		?>
		</a>
		</td>
		<td>
		<a href="/message.php?newsid=<?=$tid?>&fid=<?=$fid?>&page=<?=$pages?>#<?=$cid?>">
		<?print $comment["subject"]?>
		<?
		$ref_user = '';
		if($info["show_resp"])
		{
			if($comment["referer"]!=0)
			{
				$ref_user = ' &#8594; '.$baseC->eread('users', 'nick', '', 'id', $baseC->eread('comments', 'uid', '', 'cid', $comment["referer"]));
			}
		}
		?>
		</a>(<?=$baseC->eread('users', 'nick', '', 'id', $comment["uid"])?><?=$ref_user?>)
		</td>
		<td align='center'>
		<?
		$comment["timestamp"] = $baseC->timeToSTDate($comment["timestamp"]);
		echo $comment["timestamp"];
		?>
		</tr>
		<?
	}
	?>
	</tbody>
	</table>
	<?
}
else
{
	echo 'Сообщений за '.$chisl.' не найдено';
}
?>
</div>
<?
include_once('incs/bottom.inc.php');
?>