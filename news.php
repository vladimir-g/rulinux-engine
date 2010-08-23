<?
define('SUB_PAGE', false);
$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$nid=intval($_GET['nid']);
$cid=$_GET['cid'];
include('incs/db.inc.php');
$content=array('title'=>'Новости');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/news.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/faq.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();

require_once('incs/header.inc.php');
if ($_SESSION['user_admin'] == 1 && isset($_GET['stick'])){
	$sticked = $baseC->eread('news', 'ontop', '', 'id', $_GET['eid']);
	if ($sticked == 1)
		$baseC->erewrite('news', 'ontop', 0, $_GET['eid']);
	else
		$baseC->erewrite('news', 'ontop', 1, $_GET['eid']);
}
$header=$pagesC->get_templates('header');
$footer=$pagesC->get_templates('footer');
if (strpos($header, '[menu]')<0 && strpos($footer, '[menu]')<0)
	$pagesC->get_menu();
$perpage = (int)$_SESSION['user_login'] > 0 ? $baseC->eread('users', 'news_on_page', '', 'id', $_SESSION['user_login']) : 50;

$limit = $perpage*($_GET['page']).', '.$perpage;
if ($_GET['cid'] <= 0){
	$news_ids = $baseC->eread('news', 'id', null, '', '', '`active`=1 AND `deleted`=0 AND `cid` >0 ORDER BY `ontop` DESC, `approve_time` DESC, `timestamp` DESC LIMIT '.$limit);
	$news_count = $baseC->other_query('SELECT COUNT(id) FROM [prefix]news WHERE `active`=1 AND `deleted`=0 AND `cid` >0');
}
else{
	$news_ids = $baseC->eread('news', 'id', null, '', '', '`active`=1 AND `deleted`=0 AND `cid`='.$_GET['cid'].' ORDER BY `ontop` DESC, `approve_time` DESC, `timestamp` DESC LIMIT '.$limit);
	$news_count = $baseC->other_query('SELECT COUNT(id) FROM [prefix]news WHERE `active`=1 AND `deleted`=0 AND `cid`='.$_GET['cid']);
}
$pages = ceil($news_count[0][0]/$perpage);

$cond = $news_ids[0];

foreach($news_ids as $key => $news_id){
	if($key <= 0)
		continue;
	else
		$cond .= ','.$news_id;
}

if($info["show_hid"])
	$add = '';
else
	$add = ' AND news.deleted=0';

$comment_count = $baseC->other_query('
	SELECT COUNT(comments.cid) cid, news.id
	FROM comments, news
	WHERE news.id=comments.tid
	AND news.id in('.$cond.')
	'.$add.'
	GROUP BY tid
	ORDER BY news.ontop DESC, news.approve_time DESC, news.timestamp DESC
');
foreach ($news_ids as $key => $news_id){
     $get_news = $newsC->get_news_by_id($news_id);
     echo '<h2><a href="message.php?newsid='.$get_news['id'].'" id="newsheader" style="text-decoration:none">'.$get_news['title'].'</a></h2>';
     if ($_SESSION['user_admin']>=1) {
		$sticked = $baseC->eread('news', 'ontop', '', 'id', $get_news['id']) == 0 ? 'Прикрепить' : 'Открепить';
          echo '<div>
               <a href="admin.php?mod=news&action=edit" target="_blank" id="otherlinks">Добавить новость</a> | 
               <a href="javascript:edit_news('.$get_news['id'].')" id="edit'.$get_news['id'].'">Редактировать</a> |
	       <a href="admin.php?mod=news&action=edit&eid='.$get_news['id'].'" target="_blank" id="otherlinks">Редактировать</a> |
					<a href="news.php?stick&eid='.$get_news['id'].'" id="otherlinks">'.$sticked.'</a> | 
               <a href="admin.php?mod=news&action=manage&delid='.$get_news['id'].'" target="_blank" id="otherlinks">Удалить</a></div>';
          $a++;
	  $editadmin = ' ondblclick="edit_news('.$get_news['id'].')"';
	}
	echo '<table cellspadding="0" cellspacing="0" border="0" style="width: 100%">';
	echo '<tr>';
	echo '<td style="vertical-align:top; padding-right: 10px;">';
	if(file_exists('design/'.$tpl_name.'/icons/'.$get_news['cid'].'.gif')) $ext = 'gif';
	if(file_exists('design/'.$tpl_name.'/icons/'.$get_news['cid'].'.png')) $ext = 'png';
	if(file_exists('design/'.$tpl_name.'/icons/'.$get_news['cid'].'.jpg')) $ext = 'jpg';
	echo '<img src="design/'.$tpl_name.'/icons/'.$get_news['cid'].'.'.$ext.'">';
	echo '</td>';
	echo '<td style="vertical-align:top" style="width: 100%">';
	$news_text = $get_news['text'];
	if(preg_match('/\\\\cut\{(.*?)\}/sim', $news_text, $a)){
		$cutTo = strpos($news_text, '\\cut');
		if($cutTo > 0){
			$cutText = strlen(trim($a[1])) > 0 ? '<a href="message.php?newsid='.$get_news['id'].'">'.(strlen($a[1]) < 1 ? 'Далее ' : $a[1]).' &rarr;</a>' : '<a href="message.php?newsid='.$get_news['id'].'">'.$a[1].' &lquo;</a>';
			$news_text = substr($news_text, 0, $cutTo).$cutText;
		}
	}
	echo '<div id="n'.$get_news['id'].'"'.$editadmin.'>'.$news_text.'</div><div style="margin-top:2px;" id="f'.$get_news['id'].'"></div>';
	//echo '<strong>Категория: <a href="'.$scriptname.'?cid='.$get_news['cid'].'" id="otherlinks">'.$get_news['cid'].'</a></strong>';
   /*$time = getdate($get_news['timestamp']);
   $get_news['timestamp'] = '';
   $get_news['timestamp'] .= (strlen($time['mday']) < 2 ? '0'.$time['mday'] : $time['mday']);
   $get_news['timestamp'] .= '.'.(strlen($time['mon']) < 2 ? '0'.$time['mon'] : $time['mon']);
   $get_news['timestamp'] .= '.'.$time['year'];
   $get_news['timestamp'] .= '&nbsp;';
   $get_news['timestamp'] .= (strlen($time['hours']) < 2 ? '0'.$time['hours'] : $time['hours']);
   $get_news['timestamp'] .= ':'.(strlen($time['minutes']) < 2 ? '0'.$time['minutes'] : $time['minutes']);
   $get_news['timestamp'] .= ':'.(strlen($time['seconds']) < 2 ? '0'.$time['seconds'] : $time['seconds']);*/
	$get_news['timestamp'] = $baseC->timeToSTDate($get_news['timestamp']);
	echo '<p style="font-style:italic">'.$get_news['by'].' (<a href="profile.php?user='.$get_news['by'].'">*</a>) ('.$get_news['timestamp'].')</p>';
	
	$comment_pages = ceil($comment_count[0][0]/$perpage);
	$pages_str = '';
		if($comment_pages > 1){
		$pages_str .= ' (стр. ';
		for ($p = 1; $p < $comment_pages; $p++){
				$pages_str .= '<a href="message.php?newsid='.$get_news['id'].'&page='.$p.'">'.($p+1).'</a>&nbsp;';
		}
			$pages_str .= '<a href="message.php?newsid='.$get_news['id'].'&all">все</a>)';
		}
		if($comment_count[$key]['cid']-1 > 0){
			echo '[<a href="message.php?newsid='.$get_news['id'].'" id="more-1">';
			echo ''.$baseC->declOfNum(($comment_count[$key]['cid']-1), array('комментарий', 'комментария', 'комментариев')).'</a>'.$pages_str.']&nbsp;';
		}
	echo '[<a href="comment.php?answerto='.$get_news['id'].'&cid=0&fid=0&news" id="more-1">Добавить комментарий</a>]<br><br>';
	echo '</td>';
	echo '</tr>';
	echo '</table><hr>';
}
for ($p = 0; $p < $pages; $p++){
     $break = '';
     if (!(($p+1) % 10)) $break = '<br>';
     if ($p == (int)$_GET['page'])
          if (!isset($_GET['all']))
              echo '<strong>'.($p+1).'</strong>&nbsp;'.$break;
          else
              echo '<a href="news.php?page='.$p.'">'.($p+1).'</a>&nbsp;'.$break;
     else
          echo '<a href="news.php?page='.$p.'">'.($p+1).'</a>&nbsp;'.$break;
}

if (sizeof($news_ids) < 1)
			echo '<h1>Новостей не найдено</h1>';
echo '<!--content section end-->';
include_once('incs/bottom.inc.php');
?>