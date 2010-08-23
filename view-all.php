<?
$content['title'] = 'Просмотр неподтвержденных';
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');
require_once('classes/messages.class.php');
require_once('classes/art.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();

require_once('incs/header.inc.php');

echo '<!--content section begin-->';
?>

<h1><?=$content['title']?></h1>
<hr>
<?
if ($_SESSION['user_admin'] == 1 && (int)$_GET['approve'] > 0)
{
	base::erewrite('news', 'active', 1, $_GET['approve']);
	base::erewrite('news', 'approved', $_SESSION['user_name'], $_GET['approve']);
	preg_match('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', date('d.m.Y H:i:s'), $found);
	$time = mktime($found[4], $found[5], $found[6], $found[2], $found[1], $found[3]);
	base::erewrite('news', 'approve_time', $time, $_GET['approve']);
	$news = news::get_news_by_id($_GET['approve']);
	if (base::eread('news', 'cid', '', 'id', $_GET['approve']) <= 0)
	{
		$imid = base::eread('gallery', 'id', '', 'nid', $_GET['approve']);
		base::erewrite('gallery', 'active', 1, $imid);
		base::erewrite('gallery', 'approved_by', $_SESSION['user_name'], $imid);
		base::erewrite('gallery', 'approve_time', $time, $imid);
	}
}
$unappproved_news = news::get_news('WHERE active = 0');
$unappproved_gallery = news::get_news('WHERE active = 0', 'gallery');
$unapprooved_articles = artClass::getUnapprooved();
$unappproved_news = $unappproved_news + $unapprooved_articles;
foreach($unappproved_news as $key => $news){
   $posttime = getdate($news['timestamp']);
   $news['timestamp'] = base::timeToSTDate($news['timestamp']);
   /*$news['timestamp'] .= (strlen($posttime['mday']) < 2 ? '0'.$posttime['mday'] : $posttime['mday']);
   $news['timestamp'] .= '.'.(strlen($posttime['mon']) < 2 ? '0'.$posttime['mon'] : $posttime['mon']);
   $news['timestamp'] .= '.'.$posttime['year'];
   $news['timestamp'] .= '&nbsp;';
   $news['timestamp'] .= (strlen($posttime['hours']) < 2 ? '0'.$posttime['hours'] : $posttime['hours']);
   $news['timestamp'] .= ':'.(strlen($posttime['minutes']) < 2 ? '0'.$posttime['minutes'] : $posttime['minutes']);
   $news['timestamp'] .= ':'.(strlen($posttime['seconds']) < 2 ? '0'.$posttime['seconds'] : $posttime['seconds']);*/
	if ($news['isVote'] == -1)
	{
		echo '<h2><a href="view-article.php?aid='.$news['id'].'">Статьи - '.base::eread('forums', 'name', null, 'forum_id', base::eread('articles', 'fid', null, 'id', $news['id'])).' - '.$news['title'].'</a></h2>';
		if($_SESSION['user_admin'] == 1)
			echo '<a href="art.php?action=approove&aid='.$news['id'].'">Подтвердить</a> | <a href="admin.php?mod=news&action=edit&eid='.$news['id'].'">Редактировать</a> | <a href="art.php?action=remove&aid='.$news['id'].'">Удалить полностью</a><br>';
	}
	else
	{
		if ($news['isVote'] == 0)
			echo '<h2><a href="message.php?newsid='.$news['id'].'">Новости - '.$news['title'].'</a></h2>';
		else
			echo '<h2><a href="message.php?newsid='.$news['id'].'">Голосования - '.base::get_field_by_id('votes', 'question', $news['isVote']).'</a></h2>';
		if($_SESSION['user_admin'] == 1)
		{
			$rem = base::other_query('SELECT MIN(cid) FROM comments WHERE tid = '.$news['id']);
			echo '<a href="view-all.php?approve='.$news['id'].'">Подтвердить</a> | <a href="admin.php?mod=news&action=edit&eid='.$news['id'].'">Редактировать</a> | <a href="message.php?newsid='.$news['id'].'&rem='.$rem[0][0].'">Удалить полностью</a><br>';
		}
	
	}
	if ($news['isVote'] > 0)
	{
		$answers = base::eread('votes_has_answers', 'desc', '', 'voteId', $news['isVote']);
		$news['text'] = '<ol>';
		foreach ($answers as $answer){
			$news['text'] .= '<li>'.$answer;
		}
		$news['text'] .= '</ol>';
		$news['by'] = base::get_field_by_id('votes', 'by', $news['isVote']);
		$posttime = getdate(base::get_field_by_id('votes', 'timestamp', $news['isVote']));
		$news['timestamp'] = base::timeToSTDate($news['timestamp']);
		/*$news['timestamp'] = '';
		$news['timestamp'] .= (strlen($posttime['mday']) < 2 ? '0'.$posttime['mday'] : $posttime['mday']);
		$news['timestamp'] .= '.'.(strlen($posttime['mon']) < 2 ? '0'.$posttime['mon'] : $posttime['mon']);
		$news['timestamp'] .= '.'.$posttime['year'];
		$news['timestamp'] .= '&nbsp;';
		$news['timestamp'] .= (strlen($posttime['hours']) < 2 ? '0'.$posttime['hours'] : $posttime['hours']);
		$news['timestamp'] .= ':'.(strlen($posttime['minutes']) < 2 ? '0'.$posttime['minutes'] : $posttime['minutes']);
		$news['timestamp'] .= ':'.(strlen($posttime['seconds']) < 2 ? '0'.$posttime['seconds'] : $posttime['seconds']);*/
    }
	$news_text = $news['text'];
	if(preg_match('/\\\\cut\{(.*?)\}/sim', $news_text, $a)){
		$cutTo = strpos($news_text, '\\cut');
		if($cutTo > 0){
			$cutText = strlen(trim($a[1])) > 0 ? '<a href="message.php?newsid='.$get_news['id'].'">Далее &rarr;</a>' : '<a href="message.php?newsid='.$get_news['id'].'">'.$a[1].' &lquo;</a>';
			$news_text = substr($news_text, 0, $cutTo).'<br><br>'.$cutText;
		}
	}
	echo '<ul><p style=" text-align: justify">'.$news_text.'</p>';
	echo '<p style="font-style:italic">'.$news['by'].' (<a href="profile.php?user='.$news['by'].'">*</a>) ('.$news['timestamp'].')</p>';
	echo '</ul><hr>';
}
?>

<?
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>
