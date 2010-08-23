<?
include('incs/db.inc.php');
require_once('classes/forum.class.php');
require_once('classes/art.class.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');

$baseC = new base();
$pagesC = new pages();
$usersC = new users();
$forumC = new forumClass();
$artC = new artClass();

if(isset($_GET['id']))
{
	$id=$_GET['id'];
	if($id==1)
	{
		$content['title'] .= 'Форум';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$fn = $forumC->getForums();
		?>
		<table class="nav">
		<tbody><tr>
		<td align="left" valign="middle">
        <strong>Форум</strong>
		</td>
		<td align="right" valign="middle">
        [<a href="/add-section.php">Добавить сообщение</a>]
        [<a href="/tracker.php">Последние сообщения</a>]
        [<a href="/page.php?id=1">Правила форума</a>]
		[<a href="/faq.php">FAQ</a>]
		</td>
		</tr>
		</tbody></table>
		<h1>Форум</h1>
		Группы:
		<ul>
		<?
		foreach($fn as $array)
		{
			?>
			<li>
			<a href="/group.php?group=<? print $array["fid"]?>"><?=$array["name"]?></a>
			(<?=$array["in_sum"]?>/<?=$array["in_day"]?>/<?=$array["in_hour"]?>) - <em><?=$array["description"]?></em>
			</li>
			<?
		}
	}
	else if($id==2)
	{
		$content['title'] .= 'Статьи';
		require_once('incs/header.inc.php');
		$header=$pagesC->get_templates('header');
		$footer=$pagesC->get_templates('footer');
		$fn = $artC->getSections();
		?>
		<table class="nav">
		<tbody><tr>
		<td align="left" valign="middle">
        <strong>Статьи</strong>
		</td>
		<td align="right" valign="middle">
        [<a href="/add-article.php">Добавить статью</a>]
		[<a href="/faq.php">FAQ</a>]
		</td>
		</tr>
		</tbody></table>
		<h1>Статьи</h1>
		Группы:
		<ul>
		<?
		foreach($fn as $array)
		{
			?>
			<li>
			<a href="art.php?group=<? print $array["fid"]?>"><?=$array["name"]?></a>
			(<?=$array["in_sum"]?>/<?=$array["in_day"]?>/<?=$array["in_hour"]?>) - <em><?=$array["description"]?></em>
			</li>
			<?
		}
	}
	else
	{
		echo 'Неизвестный раздел';
	}
}
else
{
	echo 'Неизвестный раздел';
}
?>
<p>
<h1>Настройки</h1>
<? 
if ($_SESSION['user_login'] == ''):
{
	?>
	Если вы еще не зарегистрировались - вам <a href="/register.php">сюда</a>.
	<?
}
elseif ($_SESSION['user_login'] != ''):
{
?>
<ul>
<li><a href="/profile.php?user=<?print $_SESSION['user_name'];?>">Персональные настройки сайта</a>
</li></ul>
<?
}
endif;

include_once('incs/bottom.inc.php');

?>