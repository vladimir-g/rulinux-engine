<?
include('incs/db.inc.php');
require_once('classes/art.class.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
$baseC = new base();
$pagesC = new pages();
$usersC = new users();
if (intval((int)$_GET['aid'])>0)
{
	$article = artClass::getArticle($_GET['aid']);
	$title = $article[0]["title"];
	$sect = base::eread('forums', 'name', null, 'forum_id', $article[0]["fid"]);
	$body = $article[0]["body"];
	$timestamp = $article[0]["timestamp"];
	$user = base::eread('users', 'nick', '', 'id', $article[0]["uid"]);
	$approoved = base::eread('users', 'nick', '', 'id', $article[0]["approoved"]);
	$content['title'] .= 'Статьи - '.$title;
	require_once('incs/header.inc.php');
	$header=pages::get_templates('header');
	$footer=pages::get_templates('footer');
	?>
	<table class=nav>
	<tr>
	<td align=left valign=middle>
	<a href="/view-section.php?id=2">Статьи</a> - <b><a href="/art.php?group=<?=$article[0]["fid"]?>"><?=$sect?></b>
	</td>
	<td align=right valign=middle>
	[<a href="/view-rss.php">RSS</a>]
	</td>
	</tr>
	</table>
	
	<div class=messages><div align="center" class="title"><h2 class="nt"><?=$title?></h2></div>
	<div class="msg">
	<?=$body?>
	<p><i><?=$user?>
	(<a href="profile.php?user=<?=$user?>">*</a>) (<?=$timestamp?>)</i>
	<br />
	<font color="grey"><b>Подтверждено: <?=$approoved?></b></font>
	</p>
	</div></div><br>
	<?
	
	
	
	//echo $title.'<br>'.$body.'<br>'.$user.' '.$timestamp.'<br><i>'.$approoved.'</i>';
	
}
else
{
	$content['title'] .= 'Статьи - '.$title;
	require_once('incs/header.inc.php');
	$header=pages::get_templates('header');
	$footer=pages::get_templates('footer');
	echo "Неизвестные параметры";
}
include_once('incs/bottom.inc.php');
?>