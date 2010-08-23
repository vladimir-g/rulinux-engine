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
require_once('incs/header.inc.php');
$header=pages::get_templates('header');
$footer=pages::get_templates('footer');
if (strpos($header, '[menu]')<0 && strpos($footer, '[menu]')<0)
	pages::get_menu();
if (intval($_GET['newsid'])>0)	$get_news=news::get_news_by_id($_GET['newsid']);
else $get_news=news::get_news('WHERE `active` = 1 AND `type` = 1');
$order=base::eread('news_config', 'value', null, 'name', 'order');
if ($order=='21') $update=$get_news[1]['timestamp'];
else $update=$get_news[sizeof($get_news)-1]['timestamp'];
echo '<!--content section begin-->';
echo '<div align="right">[<a href="add-content?type=1">Добавить новость</a>]</div>';
if ($nid<=0)
	$nid=1;
$a=0;
for ($i=$nid; $i<=sizeof($get_news); $i++){
	if ($a>=base::eread('news_config', 'value', null, 'name', 'all'))
		break;
	if (!empty($cid))
		if ($get_news[$i]['cid']!=$cid)
			continue;
	echo '<h2><a href="message.php?newsid='.$get_news[$i]['id'].'" id="newsheader" style="text-decoration:none">'.$get_news[$i]['title'].'</a></h2>';
	if ($_SESSION['user_admin']>=1) {
		echo '<div>
		<a href="admin.php?mod=news&action=edit" target="_blank" id="otherlinks">Добавить новость</a> | 
		<a href="admin.php?mod=news&action=edit&eid='.$get_news[$i]['id'].'" target="_blank" id="otherlinks">Редактировать</a> | 
		<a href="admin.php?mod=news&action=manage&delid='.$get_news[$i]['id'].'" target="_blank" id="otherlinks">Удалить</a></div>';
		$a++;
	}
	echo '<table cellspadding="0" cellspacing="0" border="0">';
	echo '<tr>';
	echo '<td style="vertical-align:top">';
	echo '<img src="design/'.base::check_setting('template').'/icons/'.$get_news[$i]['cid'].'.gif">';
	echo '</td>';
	echo '<td style="vertical-align:top">';
	echo $get_news[$i]['text'];
	//echo '<strong>Категория: <a href="'.$scriptname.'?cid='.$get_news[$i]['cid'].'" id="otherlinks">'.$get_news[$i]['cid'].'</a></strong>';
	echo '<p style="font-style:italic">'.$get_news[$i]['by'].' (<a href="profile.php?user='.$get_news[$i]['by'].'">*</a>) ('.$get_news[$i]['timestamp'].')</p>';
	$perpage = (int)$_SESSION['user_login'] > 0 ? base::eread('users', 'comments_on_page', '', 'id', $_SESSION['user_login']) : 50;
	$comment_count = base::other_query('SELECT COUNT(cid) FROM [prefix]comments WHERE tid='.$get_news[$i]['id'].' AND deleted=0');
	$pages = ceil($comment_count[0][0]/$perpage);
	$pages_str = '';
		if($pages > 1){
		$pages_str .= ' (стр. ';
		for ($p = 0; $p < $pages; $p++){
				$pages_str .= '<a href="message.php?newsid='.$get_news[$i]['id'].'&page='.$p.'">'.($p+1).'</a>&nbsp;';
		}
			$pages_str .= '<a href="message.php?newsid='.$get_news[$i]['id'].'&all">все</a>)';
		}
		if(sizeof(base::eread('comments', 'cid', null, 'tid', $get_news[$i]['id'], 'AND `deleted`=0')) > 0){
			echo '[<a href="message.php?newsid='.$get_news[$i]['id'].'" id="more-1">';
			echo ''.base::declOfNum(sizeof(base::eread('comments', 'cid', null, 'tid', $get_news[$i]['id'], 'AND `deleted`=0')), array('комментарий', 'комментария', 'комментариев')).'</a>'.$pages_str.']&nbsp;';
		}
	echo '[<a href="comment.php?answerto='.$get_news[$i]['id'].'&cid=0&news" id="more-1">Добавить комментарий</a>]<br><br>';
	echo '</td>';
	echo '</tr>';
	echo '</table><hr>';
}

if (sizeof($get_news)>=base::eread('news_config', 'value', null, 'name', 'all')){
	if (base::eread('news_config', 'value', null, 'name', 'all')==0)
		$number_of_pages=0;
	else{
			$number_of_pages=sizeof($get_news)/base::eread('news_config', 'value', null, 'name', 'all');
			if (strpos($number_of_pages, '.')>0){
				if ($number_of_pages > 1){
					$number_of_pages = (int)$number_of_pages+1;
				}
			}
			if ($number_of_pages>=2){
         	echo '<div class="news_pages" align="center">';
				echo '[<a href="news.php">1</a>]&nbsp;';
         	$p = 2;
         	for ($b=0; $b<$number_of_pages-1; $b++){
         		if ($get_news[$b+1]=='') break;
					$c=1+base::eread('news_config', 'value', null, 'name', 'all')*$p-base::eread('news_config', 'value', null, 'name', 'all');
         		echo '[<a href="news.php?nid='.$c.'">'.$p.'</a>]&nbsp;';
         		$p++;
         	}
         	echo '</div>';
         }
	}
}
if (sizeof($get_news)<=0)
			echo '<h1>Новостей не найдено</h1>';
echo '<!--content section end-->';
include_once('incs/bottom.inc.php');
?>
