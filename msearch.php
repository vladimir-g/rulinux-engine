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
require_once('incs/header.inc.php');

$header=pages::get_templates('header');
$footer=pages::get_templates('footer');
if (strpos($header, '[menu]')>=0 || strpos($footer, '[menu]')>=0)
	pages::get_menu('', 0);
if (!empty($_GET['keys'])){
	$strict = (int)$_GET['strict'] ? true : false;
	if ((int)$_GET['all']>0)
		$found=search::find($_GET['keys'], 'both', $strict);
	else
		$found=search::find($_GET['keys'], 'title', $strict);
}
echo '<!--content section begin-->';
echo '
<div id="a0"></div>
<br />
<span>[ <a href="admin.php?mod=links&eid=add" title="Добавить новый вопрос" class="actions">Добавить</a> ]</span><br><br>';
if (intval(sizeof($found))>0){
	echo '<span>Найдено: '.intval($found['size']).'</span>
	<br /><span>Результаты поиска:</span><ul>';
	$anch_id = 1;
	foreach ($found['content'] as $oneres){
		echo '<li /><a href="#a'.$anch_id.'">'.$oneres['title'].'</a>';
		$anch_id++;
	}
	echo '</ul>';
}
if (intval(sizeof($found))>0){
	if (sizeof($found['content'])<=0)
		echo '<span>По Вашему запросу ничего не найдено</span><br />';
	$a=1;
	foreach ($found['content'] as $f) {
		$th = explode(' ', $_GET['keys']);
		if ($a == 1){
			$linkb = '#';
			$linkf = '#a'.($a+1);
		}
		elseif ($a > 1 && $a<intval($found['size'])){
			$linkb = '#a'.($a-1);
			$linkf = '#a'.($a+1);
		}
		else{
			$linkb = '#a'.($a-1);
			$linkf = '#a'.$a;
		}
		echo '<div id="a'.$a.'" align="right" style="margin-right:5pt">[ <a href="#keys" title="Перейти к форме поиска" class="actions">#</a> ] 
		[ <a href="'.$linkb.'" title="Перейти к предыдущему вопросу" class="actions"><</a> ] 
		[ <a href="'.$linkf.'" title="Перейти к следующему вопросу" class="actions">></a> ] 
		[ <a href="admin.php?mod=links&eid='.$f['id'].'" title="Изменить вопрос/ответ" class="actions">Править</a > ]</div><hr />';
		if ((int)$_GET['mark']>0){
			foreach ($th as $t) {
				$f['title']=str_replace($t, '<span class="highlite">'.$t.'</span>', $f['title']);
			}
		}
		echo '<span class="question">'.$f['title'].'</span><br />';
		if ((int)$_GET['mark']>0 && strlen($_GET['keys'])>=3)
			$f['content']=str_replace($_GET['keys'], '<span class="highlite">'.$_GET['keys'].'</span>', $f['content']);
		echo '<span>'.$f['content'].'</span></a><br />';
		$a++;
	}
	echo '<div align="center">[ <a href="#keys">Наверх</a> ]</div>';
}
include_once('incs/bottom.inc.php');
?>
