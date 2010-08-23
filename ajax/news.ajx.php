<?
include('../incs/db.inc.php');
require_once('../classes/auth.class.php');
require_once('../classes/config.class.php');
require_once('../classes/news.class.php');
include('../classes/articles.class.php');
header('Content-Type: text/html; charset='.$db_charset);
?>
<?if ($_SESSION['user_admin']>=1):?>
<?
if (!empty($_GET['act'])){
	if ($_GET['act']=='manage'){
		if ($_GET['del']>0){
			news::del_news($_GET['del']);
			$news_ids=news::get_news_ids();?>
			<th align="left">Заголовок</th>
<th align="left">Краткий текст</th>
<th align="left">Опубликована</th>
<th align="left">Категория</th>
<th align="left">Действие</th>
			<?
foreach ($news_ids as $news_id) {
	echo '<tr>';
	$n=news::get_news_by_id($news_id);
	echo '<td>'.$n['title'].'</td>';
	echo '<td>'.substr($n['text'], 0, 20).'...</td>';
	echo '<td>'.$n['timestamp'].'</td>';
	echo '<td>'.$n['cid'].'</td>';
	echo '<td><a href="'.$scriptname.'?mod=news&action=edit&eid='.$news_id.'">Редактировать</a>
	<a href="javascript:delete_n('.$news_id.')">Удалить</a></td>';
	echo '</tr>';
}
		}
	}
}
if (!empty($_GET['byself_id']) && empty($_GET['rename_to']) && !isset($_GET['delete'])){
	echo base::get_field_by_id('categories', 'name', $_GET['byself_id']);
}
elseif (!empty($_GET['byself_id']) && !empty($_GET['rename_to']) && !isset($_GET['delete']/* && base::nomore($_GET, 'byself_id:rename_to')*/)){
	$res=base::erewrite('categories', 'name', $_GET['rename_to'], $_GET['byself_id']);
	if ($res>-1){
		$cats=base::eread('categories', 'id', 'name');
		foreach ($cats as $cat=>$id) {
			echo '<option value="'.$id.'">'.$cat.'</option>';
		}
	}
}
if (!empty($_GET['byself_id']) && isset($_GET['delete'])) {
	$res=base::del_element('categories', $_GET['byself_id']);
	if ($res>-1){
		$cats=base::eread('categories', 'id', 'name');
		foreach ($cats as $cat=>$id) {
			echo '<option value="'.$id.'">'.$cat.'</option>';
		}
	}
}
if (!empty($_GET['addc'])) {
	$res=categories::addcat($_GET['addc'], "1");
	if ($res>-1){
		$cats=base::eread('categories', 'id', 'name');
		foreach ($cats as $cat=>$id) {
			echo '<option value="'.$id.'">'.$cat.'</option>';
		}
	}
}
?>
<?endif;?>