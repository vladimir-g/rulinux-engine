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

$content['title'] .= 'Пользователи зарегистрированные на форуме';
require_once('incs/header.inc.php');
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
$dbcnx = @mysql_connect($db_host,$db_user,$db_pass); 
if (!$dbcnx) 
{
	echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
	exit();
}
if (!@mysql_select_db($db_name, $dbcnx)) 
{
	echo( "<P>В настоящий момент база данных не доступна, поэтому корректное отображение страницы невозможно.</P>" );
	exit();
}
mysql_query("SET NAMES utf8");
$query = "SELECT * FROM users ORDER BY nick";
$usr = $baseC->other_query($query);
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
<!-- [<a href="http://www.linux.org.ru/section-rss.jsp?section=2">RSS</a>] -->
</td>
</tr>
</tbody>
</table>
<h1>Список зарегистрированных пользователей</h1>
<br>
<table width="100%" class="message-table">
<thead>
<b>Список пользователей</b>
<tr>
<th></th>
</tr>
</thead>
<tfoot>
</tfoot>
<tbody>
<?
$i=0;
foreach($usr as $user)
{
	$i++;
	$text = '';
	if($user[13]!='')
	{
		$text .= '<img src='.$user[13].'  height="100" align="right" border = "2" vspace="3" hspace="3">';
	}
	else
	{
		$text .= '<img src="/avatars/no_avatar.gif"  height="100" align="right" border = "2" vspace="3" hspace="3">';
	}
	$text .= '<b>Ник:</b> '.$user[2].'<br>';
	if($user[1]==2)
	{
		$text .= '<b>Статус: <font color="blue">Модератор</font></b><br>';
	}
	else
	{
		$text .= '<b>Статус:</b> Пользователь<br>';
	}
	$text .= '<b>Имя:</b> '.$user[4].'<br>
	<b>Город:</b> '.$user[12].'<br>
	<b>Страна:</b> '.$user[11].'<br>';
	if($user[8]==1)
	{
		$text .= '<b>e-mail:</b> '.$user[7].'<br>';
	}
	else
	{
		$text .= '<b>e-mail:</b> скрыт<br>';
	}
	if($user[10]==1)
	{
		$text .= '<b>IM:</b> '.$user[9].'<br>';
	}
	else
	{
		$text .= '<b>IM:</b> скрыт<br>';
	}
	?>
	<tr>
	<td>
	<?=$text?>
	<td>
	</tr>
	<?
}
?>

</tbody>
</table>
<?
include_once('incs/bottom.inc.php');
?>