<?

define('GLOBAL_SECTION', 'forum');

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

if($_GET['action']==remove)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - Удаление треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
		$fid = $_GET['fid'];
		if($forumC->deleteThread($tid))
		{
			echo "<fieldset><p align=center>Тред успешно удален. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$fid' /></header>";
		}	
		else
		{
			echo 'Не удалось удалить тред.';
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для удаления треда';
	}
}
else if($_GET['action']==move)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - Перемещение треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
		if(isset($_POST['newfid']))
		{
			$newfid= $_POST['newfid'];
			if($forumC->moveThread($tid, $newfid))
			{
				//echo 'referer'.$_POST['referer'];
				//exit();
				if (!empty($_POST['referer'])) 
				{
					echo "<fieldset><p align=center>Тред успешно перемещен. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$newfid>сюда</a></fieldset>";
					echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$newfid' /></header>";
				}
				else
				{
					echo "<fieldset><p align=center>Тред успешно перемещен. <br>Через несколько секунд вы будете перенаправлены в тред. <br>Если вы не хотите ждать нажмите <a href=/message.php?newsid=$tid>сюда</a></fieldset>";
					echo "<header><meta http-equiv='Refresh' content='0; url=message.php?newsid=$tid' /></header>";
				}
			}	
			else
			{
				echo 'Не удалось переместить тред.';
			}
		}
		else
		{
			?>
			<form action="/group.php?action=move&tid=<?=$tid?>" method="POST">
			Куда перемещать будем?:
			<?
			$forums = $forumC->getForums();
			echo '<select name="newfid"">';
			foreach($forums as $forum)
			{
				echo '<option value="'.$forum["fid"].'">'.$forum["name"].'</option>';
			}
			echo '</select><br>';
			if(stristr($_SERVER['HTTP_REFERER'], "group.php"))
			{
				echo '<input type="hidden" name="referer" value="group.php">';
			}
			?>
			<br><input type="submit" value="Переместить">
			</form>
			<?
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для перемещения треда';
	}
}
else if($_GET['action']==hide)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - Скрытие треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
			$fid = $_GET['fid'];
		if(isset($_POST['purpose']))
		{
			$uid = $baseC->eread('users', 'id', '', 'nick', $_SESSION['user_name']);
			$reason = $_POST['purpose'];
			if($forumC->hideThread($tid, $uid, $reason))
			{
				echo "<fieldset><p align=center>Тред успешно скрыт. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
				echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$fid' /></header>";
			}	
			else
			{
				echo 'Не удалось скрыть тред.';
			}
		}
		else
		{
			?>
			<form action="/group.php?action=hide&tid=<?=$tid?>&fid=<?=$fid?>" method="POST">
			Укажите причину удаления:
			<?
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
			?>
			<br><input type="submit" value="Скрыть">
			</form>
			<?
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для скрытия треда';
	}
}
else if($_GET['action']==attach)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - Прикрепление треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
		$fid = $_GET['fid'];
		if($forumC->attachThread($tid))
		{
			echo "<fieldset><p align=center>Тред успешно прикреплен. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$fid' /></header>";
		}	
		else
		{
			echo 'Не удалось прикрепить тред.';
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для прикрепления треда';
	}
}
else if($_GET['action']==close)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - Закрытие треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
		$fid = $_GET['fid'];
		if($forumC->closeThread($tid))
		{
			echo "<fieldset><p align=center>Тред успешно закрыт. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$fid' /></header>";
		}	
		else
		{
			echo 'Не удалось закрыть тред.';
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для закрытия треда';
	}
}
else if($_GET['action']==open)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - открытие треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
		$fid = $_GET['fid'];
		if($forumC->openThread($tid))
		{
			echo "<fieldset><p align=center>Тред успешно открыт. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$fid' /></header>";
		}	
		else
		{
			echo 'Не удалось открыть тред.';
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для открытия треда';
	}
}
else if($_GET['action']==detach)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - Открепление треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
		$fid = $_GET['fid'];
		if($forumC->detachThread($tid))
		{
			echo "<fieldset><p align=center>Тред успешно откреплен. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$fid' /></header>";
		}	
		else
		{
			echo 'Не удалось открепить тред.';
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для открепления треда';
	}
}
else if($_GET['action']==unhide)
{
	if($_SESSION['user_admin'])
	{
		$content['title'] .= 'Форум - Восстановление треда';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$tid = $_GET['tid'];
		$fid = $_GET['fid'];
		if($forumC->unhideThread($tid))
		{
			echo "<fieldset><p align=center>Тред успешно восстановлен. <br>Через несколько секунд вы будете перенаправлены в список тредов. <br>Если вы не хотите ждать нажмите <a href=/group.php?group=$fid>сюда</a></fieldset>";
			echo "<header><meta http-equiv='Refresh' content='0; url=group.php?group=$fid' /></header>";
		}	
		else
		{
			echo 'Не удалось восстановить тред.';
		}
	}
	else
	{
		echo 'У вас недостаточно полномочий для восстановления треда';
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
		$forumid = (int)$_GET['group'];
		$forum = $baseC->other_query("SELECT name, description FROM forums WHERE forum_id = '$forumid'");
		$content['title'] .= 'Форум - '.$forum[0][0];
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		?>
		<form action="group.php">
		<table class=nav>
		<tr>
		<td align=left valign=middle>
		<a href="/view-section.php?id=1">Форум</a> - <b><? print $forum[0][0]?></b>
		</td>
		<td align=right valign=middle>
		[<a href="/add-message.php?fid=<?print $_GET['group']?>">Добавить сообщение</a>]
		[<a href="/tracker.php">Последние сообщения</a>]
		[<a href="/page.php?id=1">Правила форума</a>]
		[<a href="/faq.php">FAQ</a>]
		<select name=group onChange="submit()" title="Быстрый переход">
		<?
		$sel = $baseC->other_query("SELECT forum_id, name, rewrite FROM forums ORDER BY sort");
		foreach($sel as $val)
		{
			if($val['forum_id']==$_GET['group'])
			{
				?>
				<option value=<? print $val['rewrite']?> selected><? print $val['name']?></option>
				<?
			}
			else
			{
				?>
				<option value=<? print $val['rewrite']?>><? print $val['name']?></option>
				<?
			}
		}
		?>
		</select>
		</td>
		</tr>
		</table>
		</form>
		<h1>Форум <?=$forum[0][0]?></h1><p style="margin-top: 0px">
		<em><?=$forum[0][1]?></em><br><br>
		
		<!--<script type="text/javascript" src="/js/jquery.js"></script>-->
		<script type="text/javascript">
		var mins = 2;
		$(function()
		{		
			$("#trigger").click(function(event) {
			event.preventDefault();
			$("#box").slideToggle();
			});
			
			$("#box a").click(function(event) {
			event.preventDefault();
			$("#box").slideUp();
			});
			timer();		
		});
		</script>
		<?
			$text = $baseC->other_query("SELECT shortfaq FROM forums WHERE forum_id  = '$forumid'");
		?>
		<style>
		#trigger {display:block;padding-top:1px;}
		#box {display:none}
		#box {color:black;border:1px solid #999;padding:5px}
		</style>
		<div id="jq-wrapper">
		<div id="bodyContent" style="padding-bottom:10px">
		<a href="#" id="trigger">Рекомендации по разделу</a>
		<div id="box">
		<?=$text[0][0] ?>
		</div>
		</div>
		</div>
		
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
			<th>Управление</th>
			<?
		}
		?>
		<th width = "70%">Заголовок</th>
		<th>Число ответов
		<br>всего/день/час</th>
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
		$forum = new forumClass();
		$perpage = (int)$_SESSION['user_login'] > 0 ? $baseC->eread('users', 'threads_on_page', '', 'id', $_SESSION['user_login']) : 30;
		$show_hid = $info["show_hid"];
		$sort_to = (int)$_SESSION['user_login'] > 0 ? $baseC->eread('users', 'sort_to', '', 'id', $_SESSION['user_login']) : 0;
		if($show_hid == 1)
		{
			$threads_count = $baseC->other_query('SELECT COUNT(tid) FROM threads WHERE fid = '.$forumid);
		}
		else
		{
			$threads_count = $baseC->other_query('SELECT COUNT(tid) FROM threads WHERE fid = '.$forumid.' AND deleted != 1');
		}
		$fn = $forum->getThreads($forumid, $page, $perpage, $sort_to, $show_hid);
		$pages = ceil($threads_count[0][0]/$perpage);
		foreach($fn as $array)
		{
			$user = $baseC->eread('users', 'nick', '', 'id', $array["uid"]);
			?>
			<tr>
			<?
			if($_SESSION['user_admin'])
			{
				?>
				<td align = center>
				<a href="group.php?action=remove&tid=<?=$array["tid"]?>&fid=<?=$forumid?>"><img border="0" src="design/<?=$tpl_name ?>/remove.png" alt="Удалить"></a>
				<a href="group.php?action=move&tid=<?=$array["tid"]?>"><img border="0" src="design/<?=$tpl_name ?>/move.png" alt="Переместить"></a>
				
				<?
				if($array["deleted"])
				{
					?>
					<a href="group.php?action=unhide&tid=<?=$array["tid"]?>&fid=<?=$forumid?>"><img border="0" src="design/<?=$tpl_name ?>/unhide.png" alt="Восстановить"></a>
					<?
				}
				else
				{
					?>
					<a href="group.php?action=hide&tid=<?=$array["tid"]?>&fid=<?=$forumid?>"><img border="0" src="design/<?=$tpl_name ?>/hide.png" alt="Скрыть"></a>
					<?
				}
				if($array["attached"])
				{
					?>
					<a href="group.php?action=detach&tid=<?=$array["tid"]?>&fid=<?=$forumid?>"><img border="0" src="design/<?=$tpl_name ?>/detach.png" alt="Открепить"></a>
					<?
				}
				else
				{
					?>
					<a href="group.php?action=attach&tid=<?=$array["tid"]?>&fid=<?=$forumid?>"><img border="0" src="design/<?=$tpl_name ?>/attach.png" alt="Прикрепить"></a>
					<?
				}
				if($array["closed"])
				{
					?>
					<a href="group.php?action=open&tid=<?=$array["tid"]?>&fid=<?=$forumid?>"><img border="0" src="design/<?=$tpl_name ?>/open.png" alt="Открыть"></a>
					<?
				}
				else
				{
					?>
					<a href="group.php?action=close&tid=<?=$array["tid"]?>&fid=<?=$forumid?>"><img border="0" src="design/<?=$tpl_name ?>/close.png" alt="Закрыть"></a>
					
					
					<?
				}
				?>
				</td>
				<?
			}
			?>
			<td><? if($array["attached"]==1){ echo '<img border="0" src="design/'.$tpl_name.'/paper_clip.gif">&nbsp;';}?><a href="/message.php?newsid=<?print $array["tid"]?>"><? if($array["deleted"]==0) print $array["subject"]; else print '<b>[X]</b> '.$array["subject"];?></a> (<?=$user?>)</td>
			<td align=center><b><?=$array["in_sum"]?></b>/<b><?=$array["in_day"]?></b>/<b><?=$array["in_hour"]?></b></td>
			</tr>
			<?
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