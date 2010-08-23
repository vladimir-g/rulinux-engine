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
if (strpos($header, '[menu]')<0 && strpos($footer, '[menu]')<0)
	pages::get_menu();
if (!empty($_GET['keys']))
	$found=search::find($_GET['keys'], 'both');
echo '<!--content section begin-->';
//echo '<span class="pagetitle">'.$content['title'].'</span><br>';
echo '<table>';
echo '<tr>
	  <td class="searchicn"><img src="images/helper.png"></td>
        <td align="left" valign="middle"><span class="news_o_1">найдено: '.intval(sizeof($found)).'</span><br />
          <span class="news_o_2">Поиск</span><br />
          <span class="news_o_1">Результаты поиска</span>
          </td>
        </tr>';
echo '</table>';
?>
<form action="<?echo $scriptname?>" method="get">
<?if (empty($_GET['keys'])):?>
<input type="text" value="Поиск..." id="keys" name="keys" style="width:50%;" onfocus="clear_field()" onchange="validate_field()">&nbsp;<input type="submit" value="Искать">
<?else:?>
<input type="text" value="<?echo $_GET['keys']?>" id="keys" name="keys" style="width:50%;" onfocus="clear_field()" onchange="validate_field()">&nbsp;<input type="submit" value="Искать">
<?endif;?>
</form>
<script>
function clear_field(){
	if (document.getElementById('keys').value=='Поиск...')
		document.getElementById('keys').value='';
}
function validate_field(){
	if (document.getElementById('keys').value=='')
		document.getElementById('keys').value='Поиск...';
}
</script>
<?
if (intval(sizeof($found))>0){
	echo '<br /><span class="news_o_1">В страницах: </span><br />';
	if (sizeof($found['content'])<=0)
		echo '<span class="news_o_1">По Вашему запросу ничего не найдено</span><br />';
	foreach ($found['content'] as $f) {
		echo '<a href="page.php?id='.$f['id'].'&mark='.$_GET['keys'].'"><span class="news_o_2">'.$f['title'].'</span></a><br />';
		echo '<span class="news_text">'.substr($f['content'], 0, 500).'...</span></a><br />';
	}
	echo '<br /><span class="news_o_1">В новостях: </span><br />';
	if (sizeof($found['news'])<=0)
		echo '<span class="news_o_1">По Вашему запросу ничего не найдено</span><br />';
   if (sizeof($found['news'])>0){
	foreach ($found['news'] as $f) {
		echo '<a href="page.php?id='.$f['id'].'&mark='.$_GET['keys'].'"><span class="news_o_2">'.$f['title'].'</span></a><br />';
		echo '<span class="news_text">'.substr($f['text'], 0, 500).'...</span></a><br />';
	}
}
}
include_once('incs/bottom.inc.php');
?>
