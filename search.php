<?
$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$nid=intval($_GET['nid']);
$cid=$_GET['cid'];
include('incs/db.inc.php');
$content=array('title'=>'Поиск');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/search.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();
$searchC = new search();

require_once('incs/header.inc.php');
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
if (strpos($header, '[menu]')<0 && strpos($footer, '[menu]')<0)
	$pagesC->get_menu();
if(!empty($_GET['include']))
{
	if($_GET['include']=='topics')
	{
		$mode='title';
	}
	elseif($_GET['include']=='comments')
	{
		$mode='content';
	}
	elseif($_GET['include']=='all')
	{
		$mode='both';
	}
	else
	{
		echo 'Неверный прарметр include';
		include_once('incs/bottom.inc.php');
		exit();
	}
	
	if($_GET['date']=='3month')
	{
		$time='3month';
	}
	elseif($_GET['date']=='year')
	{
		$time='year';
	}
	elseif($_GET['date']=='all')
	{
		$time='both';
	}
	else
	{
		echo 'Неверный прарметр date';
		include_once('incs/bottom.inc.php');
		exit();
	}
	
	if($_GET['section']=='0')
	{
		$section='all';
	}
	elseif($_GET['section']=='1')
	{
		$section='news';
	}
	elseif($_GET['section']=='2')
	{
		$section='forum';
	}
	elseif($_GET['section']=='3')
	{
		$section='gallery';
	}
	else
	{
		echo 'Неверный прарметр section';
		include_once('incs/bottom.inc.php');
		exit();
	}
	if(empty($_GET['username']))
	{
		$user='all';
	}
	else
	{
		$user=$_GET['username'];
	}
	$found=$searchC->find1($_GET['q'], $mode, $time, $section, $user);
}
echo '<!--content section begin-->';
echo '<br /><h1>Поиск по сайту</h1><br />';
?>

<form method=GET action="search.php">
Искать: <input type="text" name="q" size=50 value="<?=htmlspecialchars($_GET['q'])?>"><p>
  <select name="include">
	<?
	if($_GET['include']=="topics")
	{
		?>
		<option value="topics" selected>только темы</option>
		<option value="comments" >только комментарии</option>
		<option value="all" >темы и комментарии</option>
		<?
	}
	elseif($_GET['include']=="comments")
	{
		?>
		<option value="topics" >только темы</option>
		<option value="comments" selected>только комментарии</option>
		<option value="all" >темы и комментарии</option>
		<?
	}
	else
	{
		?>
		<option value="topics" >только темы</option>
		<option value="comments" >только комментарии</option>
		<option value="all" selected>темы и комментарии</option>
		<?
	}
	?>
  </select>
  За:
  <select name="date">
  <?
	if($_GET['date']=="3month")
	{
		?>
		<option value="3month" selected>три месяца</option>
		<option value="year" >год</option>
		<option value="all" >весь период</option>
		<?
	}
	elseif($_GET['date']=="year")
	{
		?>
		<option value="3month" >три месяца</option>
		<option value="year" selected>год</option>
		<option value="all" >весь период</option>
		<?
	}
	else
	{
		?>
		<option value="3month" >три месяца</option>
		<option value="year" >год</option>
		<option value="all" selected>весь период</option>
		<?
	}
	?>
  </select>
<br>
  Раздел:
  <select name="section">
  <?
	if($_GET['section']=="1")
	{
		?>
		<option value="1" selected>новости</option>
		<option value="2" >форум</option>
		<option value="3" >галерея</option>
		<option value="0" >все</option>
		<?
	}
	elseif($_GET['section']=="2")
	{
		?>
		<option value="1" >новости</option>
		<option value="2" selected>форум</option>
		<option value="3" >галерея</option>
		<option value="0" >все</option>
		<?
	}
	elseif($_GET['section']=="3")
	{
		?>
		<option value="1" >новости</option>
		<option value="2" >форум</option>
		<option value="3" selected>галерея</option>
		<option value="0" >все</option>
		<?
	}
	else
	{
		?>
		<option value="1" >новости</option>
		<option value="2" >форум</option>
		<option value="3" >галерея</option>
		<option value="0" selected>все</option>
		<?
	}
	?>
   </select>
  Пользователь:
  <input type="text" name="username" size=20 value=<?=$_GET['username']?>><p>
  <br>
<input type="submit" value="Искать!"><BR>
</form>

<?
if((int)$_SESSION['user_login'] > 0)
	$perpage = $baseC->eread('users', 'comments_on_page', '', 'id', $_SESSION['user_login']);
else
{
	if(empty($_COOKIE['comments_on_page']))
		$perpage = 50;
	else
		$perpage = $_COOKIE['comments_on_page'];
}
if (intval(sizeof($found))>0)
{
	foreach ($found as $result_str)
	{
		$str_c = $result_str['comment'];
		$str_arr_c = array_unique(explode(" ", $_GET['q']));
		for($i=0;$i<sizeof($str_arr_c);$i++)
		{
			$str_arr_cb[$i] = '<b>'.$str_arr_c[$i].'</b>';
		}
		$str_c = str_replace($str_arr_c, $str_arr_cb, $str_c);
		
		$str_s = $result_str['subject'];
		$str_arr_s = array_unique(explode(" ", $_GET['q']));
		for($i=0;$i<sizeof($str_arr_s);$i++)
		{
			$str_arr_sb[$i] = '<b>'.$str_arr_s[$i].'</b>';
		}
		$str_s = str_replace($str_arr_s, $str_arr_sb, $str_s);
		
		$usr = $baseC->eread('users', 'nick', null, 'id', $result_str['uid']);
		$timestamp = $baseC->timeToSTDate($result_str['timestamp']);
		$count = $baseC->other_query('SELECT count(cid) FROM comments WHERE tid = '.$result_str['tid'].' AND mconf=0 AND cid <= '.$result_str['cid']);
		$pages = floor($count[0][0]/$perpage);
		?>
		<br>
		<div class=msg id=<?=$comment_id?>>
		<table cellspacing="0" cellspadding="0" width=100%><tr><td valign=top><h2><a href="message.php?newsid=<?=$result_str['tid']?>&page=<?=$pages?>#<?=$result_str['cid']?>"><?=$str_s?><!-- <?=$result_str['subject']?> --></a></h2>
		<!-- <?=$result_str['comment']?>-->
		<?=$str_c?>
		<div class=sign><?=$usr?>
		(<a href="profile.php?user=<?=$usr?>">*</a>)
		(<?=$timestamp?>)<br><?=$additional?></div>
		</td></tr></table></div></div></div>
		<?
	}
}
include_once('incs/bottom.inc.php');
?>
