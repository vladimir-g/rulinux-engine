<?
define('GLOBAL_SECTION', 'articles');

include('incs/db.inc.php');
require_once('classes/art.class.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');

$baseC = new base();
$pagesC = new pages();
$usersC = new users();
$artC = new artClass();

$group = (int)$_GET['group'];
$art = $baseC->other_query("SELECT name, description FROM forums WHERE forum_id = '$group'");
$content['title'] .= 'Статьи раздела '.$art[0][0];
require_once('incs/header.inc.php');
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
if($_GET['action']==approove)
{
	if($_SESSION['user_admin'])
	{
		$uid = $_SESSION['user_login'];
		$aid = $_GET['aid'];
		$artC->approoveArticle($aid, $uid);
		echo "<fieldset><p align=center>Статья успешно подтверждена. <br>Через несколько секунд вы будете перенаправлены в список неподтвержденного. <br>Если вы не хотите ждать нажмите <a href=/view-all.php>сюда</a></fieldset>";
		echo "<header><meta http-equiv='Refresh' content='0; url=view-all.php' /></header>";
	}
}
elseif($_GET['action']==move)
{
	if($_SESSION['user_admin'])
	{
		$aid = $_GET['aid'];
		if(isset($_POST['newfid']))
		{
			$new_fid = $_POST['newfid'];
			if($artC->moveArticle($aid, $new_fid))
			{
				echo "<fieldset><p align=center>Статья успешно перемещена. <br>Через несколько секунд вы будете перенаправлены в список статей. <br>Если вы не хотите ждать нажмите <a href=/art.php?group=$new_fid>сюда</a></fieldset>";
				echo "<header><meta http-equiv='Refresh' content='0; url=art.php?group=$new_fid' /></header>";
			}
			else
			{
				echo 'Не удалось переместить статью.';
			}
		}
		else
		{
			?>
			<form action="/art.php?action=move&aid=<?=$_GET['aid']?>" method="POST">
			Куда перемещать будем?:
			<?
			$forums = $artC->getSections();
			echo '<select name="newfid"">';
			foreach($forums as $forum)
			{
				echo '<option value="'.$forum["fid"].'">'.$forum["name"].'</option>';
			}
			echo '</select><br>';
			?>
			<br><input type="submit" value="Переместить">
			</form>
			<?
		}
	}
}
elseif($_GET['action']==remove)
{
	if($_SESSION['user_admin'])
	{
		$uid = $_SESSION['user_login'];
		$aid = $_GET['aid'];
		$fid = $baseC->eread('articles', 'fid', null, 'id', $aid);
		$artC->removeArticle($aid);
		if(strstr($_SERVER['HTTP_REFERER'], 'view-all.php')=='view-all.php')
		{
			echo "<fieldset><p align=center>Статья успешно удалена. <br>Через несколько секунд вы будете перенаправлены в список неподтвержденного. <br>Если вы не хотите ждать нажмите <a href=/view-all.php>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=view-all.php' /></header>";
		}
		else
		{
			echo "<fieldset><p align=center>Статья успешно удалена. <br>Через несколько секунд вы будете перенаправлены в список статей. <br>Если вы не хотите ждать нажмите <a href=/art.php?group=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=art.php?group=$fid' /></header>";
		}
	}
}
else
{
	if (isset($_GET['group'])=='') 
	{
		echo "Неизвестные параметры";
	}
	else if ((int)$_GET['group'] == 0)
		$_GET['group'] = $baseC->eread('forums', 'forum_id', '', 'rewrite', $_GET['group']);
	if (ereg("^[0-9]*$", $_GET['group'])) 
	{
		?>
		<form action="art.php">
		<table class=nav>
		<tr>
		<td align=left valign=middle>
		<a href="/view-section.php?id=2">Статьи</a> - <b><? print $art[0][0]?></b>
		</td>
		<td align=right valign=middle>
		[<a href="/add-article.php?group=<?=$_GET['group']?>">Добавить статью</a>]
		[<a href="/faq.php">FAQ</a>]
		<select name=group onChange="submit()" title="Быстрый переход">
		<?
		$sel = $baseC->other_query("SELECT forum_id, name FROM forums ORDER BY sort");
		foreach($sel as $val)
		{
			if($val['forum_id']==$_GET['group'])
			{
				?>
				<option value=<? print $val['forum_id']?> selected><? print $val['name']?></option>
				<?
			}
			else
			{
				?>
				<option value=<? print $val['forum_id']?>><? print $val['name']?></option>
				<?
			}
		}
		?>
		</select>
		</td>
		</tr>
		</table>
		</form>
		<h1>Статьи раздела <?=$art[0][0]?></h1><p style="margin-top: 0px">
		
		<div class=forum>
		<?
		if((int)$_GET['page'] > 1)
		{
			$page = $_GET['page'];
		}
		else
		{
			$page = 1;
		}
		?>
		<table width="100%" class="message-table">
		<thead>
		<tr>
		<?
		if($_SESSION['user_admin'])
		{
			?>
			<th width = "5%">Управление</th>
			<?
		}
		?>
		<th width = "75%">Заголовок</th>
		<th>Дата написания</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
		<td colspan=2>
		<p>
		<div style="float: left"></div>
		<div style="float: right"></div>
		</td>
		</tr>
		</tfoot>
		<tbody>
		<?
		$fn = $artC->getArticles($_GET['group']);
		
		if($fn!=0)
		{
			foreach($fn as $array)
			{
				$uid = $array["uid"];
				$id = $array["id"];
				$title = $array["title"];
				$user = $baseC->eread('users', 'nick', '', 'id', $uid);
				$timestamp = $array["timestamp"];
				$timestamp = $baseC->timeToSTDate($timestamp);
				?>
				<tr>
				<?
				if($_SESSION['user_admin'])
				{
					?>
					<td>
					<a href="art.php?action=remove&aid=<?=$id?>"><img border="0" src="design/<?=$tpl_name ?>/remove.png" alt="Удалить"></a>
					<a href="art.php?action=move&aid=<?=$id?>"><img border="0" src="design/<?=$tpl_name ?>/move.png" alt="Переместить"></a>
					<a href="#"><img border="0" src="design/<?=$tpl_name ?>/edit.png" alt="Редактировать"></a>
					</td>
					<?
				}
				?>
				<td><a href="/view-article.php?aid=<?=$id?>"><?=$title?></a> (<?=$user?>)</td>
				<td align="center"><?=$timestamp?></td>
				</tr>
				<?
			}
		}
		else
		{
			echo 'Error';
		}
		?>
		</tbody>
		</table>
		</div>
		<div align=center><p>
		<?
		if($pages > 1){
		echo '[страница ';
		for ($p = 1; $p <= $pages; $p++)
		{
			if ($p == (int)$_GET['page'])
			{
				echo '<b>'.$p.'</b> ';
			}
			else
			{
				echo '<a href=/forum-'.$baseC->eread('forums', 'rewrite', '', 'forum_id', $_GET['group']).'_'.$p.'>'.$p.'</a>&nbsp;';
			}
		}
		echo ']';
		}
		?>
		<p>
		<hr>
		<hr>
		</div>
		<?
	}
}
include_once('incs/bottom.inc.php');

?>