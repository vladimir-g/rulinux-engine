<?
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
require_once('incs/header.inc.php');
$header=pages::get_templates('header');
$footer=pages::get_templates('footer');
if (strpos($header, '[menu]')<0 && strpos($footer, '[menu]')<0)
	pages::get_menu();
if (intval($_GET['newsid'])>0)	$get_news=news::get_news_by_id($_GET['newsid']);
else $get_news=news::get_news('WHERE `active` = 1 AND `type` = 2');
$order=base::eread('news_config', 'value', null, 'name', 'order');
if ($order=='21') $update=$get_news[1]['timestamp'];
else $update=$get_news[sizeof($get_news)-1]['timestamp'];
echo '<!--content section begin-->';
echo '<a href="news.php"><img src="images/narchive.png" border="0"></a><br><br>';
if ($_GET['newsid']<=0 && sizeof($get_news)>0){
echo '<table>';
echo '<tr>
        <td align="left" valign="middle"><span class="news_o_1">Дата добавления последней статьи '.$update.'</span><br><br><br><br>
          </td>
        </tr>';
echo '</table>';
}
if ($nid<=0)
	$nid=1;
$a=0;
if (intval($_GET['newsid'])>0){
	if ($get_news['id']>0 && base::eread('news', 'active', 'name', 'id', $get_news['id']) > 0){
		echo '<h2 class="nt">'.$get_news['title'].'</h2>';
		echo '<p>'.$get_news['text'].'</p>';
		echo '<p style="color:gray; font-weight:bold">Опубликована: '.$get_news['by'].' ['.$get_news['timestamp'].']</p>';
	}
	else 
		echo '<div class="error" align="center" style="text-align:center; height:100%;vertical-align:middle"><img src="images/helper.png"><br />Ошибка!<br /> 
Запрошенная Вами статья не найдена
</div>';
}
else
for ($i=$nid; $i<=sizeof($get_news); $i++){
	if ($a>=base::eread('news_config', 'value', null, 'name', 'all'))
		break;
	if (!empty($cid))
		if ($get_news[$i]['cid']!=$cid)
			continue;
	echo '<a href="'.$scriptname.'?cid='.$get_news[$i]['cid'].'" class="nt" style="text-decoration:none">['.$get_news[$i]['cid'].']</a> &nbsp;<a href="'.$scriptname.'?newsid='.$get_news[$i]['id'].'" class="nt" style="text-decoration:none">'.$get_news[$i]['title'].'</a>';
	if ($_SESSION['user_admin']>=1) {
		echo '<div class="news_dob"><a href="admin.php?mod=news&action=edit" target="_blank" class="newslinks">Добавить новость</a> | 
		<a href="admin.php?mod=news&action=edit&eid='.$get_news[$i]['id'].'" target="_blank" class="newslinks">Редактировать</a> | 
		<a href="admin.php?mod=news&action=manage&delid='.$get_news[$i]['id'].'" target="_blank" class="newslinks">Удалить</a></div>';
		$a++;
	}
	echo '<br><p>'.$get_news[$i]['desc'].'</p>';
	echo '<p style="color:gray; font-weight:bold">Опубликована: '.$get_news[$i]['by'].' ['.$get_news[$i]['timestamp'].']</p>';
	echo '<br><a href="'.$scriptname.'?newsid='.$get_news[$i]['id'].'" id="more">Подробнее >></a><br><br><br>';
}
if (sizeof($get_news)>=base::eread('news_config', 'value', null, 'name', 'all')){
	if (base::eread('news_config', 'value', null, 'name', 'all')==0)
		$number_of_pages=0;
	else
		$number_of_pages=sizeof($get_news)/base::eread('news_config', 'value', null, 'name', 'all');
			if (strpos($number_of_pages, '.')>0);
				if ($number_of_pages > 1)
					$number_of_pages = (int)$number_of_pages+1;
			if ($number_of_pages>=2){
			echo '<div class="news_pages" align="center">';
			$p = 1;
			for ($b=0; $b<=$number_of_pages; $b += base::eread('news_config', 'value', null, 'name', 'all')){
				if ($get_news[$b+1]=='') break;
				echo '[<a href="news.php?nid='.($b+1).'">'.$p.'</a>]&nbsp;';
				$p++;
			}
			echo '</div>';
			}
		}
		if (sizeof($get_news)<=0)
			echo '<div class="error" align="center" style="text-align:center; height:100%;vertical-align:middle"><img src="images/helper.png"><br /> 
Ни одной статьи не найдено
</div>';
echo '<!--content section end-->';
include_once('incs/bottom.inc.php');
?>