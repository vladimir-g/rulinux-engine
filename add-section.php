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

$content['title'] .= 'Форум - Добавить тред';
require_once('incs/header.inc.php');
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
$forum = new forumClass();
$fn = $forum->getForums();
 ?>
  <h1>Добавить тему в форум</h1>
Просьба ко всем, добавляющим темы в форум:
<ul>
<li><b>Прочитайте <a href="/wiki/en/lor-faq">FAQ</a></b>! Возможно, ваш вопрос уже содержится в нашем сборнике ответов на часто задаваемые вопросы.
<li><b>Пишите в правильный форум!</b> Выберете подходящий по теме вашего вопроса раздел форума, например
вопросы по администрированию системы нужно задавать в Admin, а
не в General и т.п.
<li><b>Пишите осмысленный заголовок</b>. Придумайте осмысленный заголовок теме. Сообщения с бессмысленными загловками ("Помогите!", "Вопрос", ...), как правило, остаются без ответа.
<!-- <li>Не включайте без нужды режим преформатированного текста,
это сбивает форматирование сайта. -->
</ul>
  <h2>Выберите группу</h2>
Доступные группы:
<?$dbcnx = @mysql_connect($db_host,$db_user,$db_pass); 
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
$ath = mysql_query("SELECT forum_id, name, description FROM forums;");
if($ath)
{
	while($author = mysql_fetch_array($ath))
	{
		?>
		<li>
		<a href="<?=$path?>add-message.php?fid=<? print $author['forum_id']?>"><? print $author['name']?></a>
		(<a href="<?=$path?>group.php?group=<? print $author['forum_id']?>">Просмотр...</a>) - <em><? print $author['description']?></em>
		</li>
		<?
	}
}
else
{
  echo "<p><b>Error: ".mysql_error()."</b></p>";
  exit();
}
if(!mysql_close($dbcnx))
{
  echo("Не удалось завершить соединение");
}
include_once('incs/bottom.inc.php');

?>