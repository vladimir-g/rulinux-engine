<?
if ($inside):
require_once('classes/news.class.php');
if ($_GET['action']!='category' && $_GET['action']!='manage' && $_GET['action']!='edit' && $_GET['action']!='settings')
	$_GET['action']='edit';
?>
<?$action=$_GET['action']?>
<?if ($action=='category'):?>
<?
$categories=base::eread('categories', 'id', 'name', null, null, null);
if (sizeof($categories)>=0 && $categories!=-1):
?>
<form action="<?echo $scriptname?>?mod=news&action=category" method="post">
<select size="5" style="width:100%" id="names" onchange="get_name(this.value)">
<?
foreach ($categories as $categorie=>$id)
	echo '<option value="'.$id.'">'.$categorie.'</option>';
?>
</select><br /><br />
<input type="text" id="parent" style="width:25%">
<input type="button" value="Переименовать" onclick="rename_c(document.getElementById('parent').value, document.getElementById('names').value)">
<input type="button" value="Удалить" onclick="act_c(document.getElementById('names').value, 'del')"><br />
Имя новой категории: <br />
<input type="text" id="newcat" style="width:25%">
<input type="button" value="Создать категорию" onclick="act_c(document.getElementById('newcat').value, 'add')">
</form>
<script src="js/ajax.js">
</script>
<script>
function rename_c(name, id){
	document.getElementById("names").innerHTML=""
	showMsg('Подождите...' , 'msgSpan')
	var q="ajax/news.ajx.php";
	q=q+"?byself_id="+id+"&rename_to="+name;
	sendRequest(q,changeName,null,false);
}
function act_c(id, act){
	switch (act){
		case 'add':
			document.getElementById("names").innerHTML=""
			showMsg('Подождите...' , 'msgSpan')
			var q="ajax/news.ajx.php";
			q=q+"?addc="+id;
			sendRequest(q,addCategory,null,false);
		break;
		case 'del':
			document.getElementById("names").innerHTML=""
			showMsg('Подождите...' , 'msgSpan')
			var q="ajax/news.ajx.php";
			q=q+"?byself_id="+id+"&delete";
			sendRequest(q,delCat,null,false);
		break;
	}
}
function get_name(id){
	showMsg('Подождите...' , 'msgSpan')
	var q="ajax/news.ajx.php";
	q=q+"?byself_id="+id
	sendRequest(q,vChanged,null,false);
}
function addCategory(a) 
{ 
document.getElementById("newcat").value=''
document.getElementById("names").innerHTML=a;
hideMsg('msgSpan')
}
function vChanged(a) 
{ 
document.getElementById("parent").value=a
hideMsg('msgSpan')
}
function changeName(a) 
{
document.getElementById("parent").value=''
document.getElementById("names").innerHTML=a
hideMsg('msgSpan')
}
function delCat(a){
document.getElementById("parent").value=''
document.getElementById("names").innerHTML=a
hideMsg('msgSpan')
}
</script>
<?endif;?>
<?elseif ($action=='manage'):?>
<?
if (intval($_GET['delid'])>0)
	$delnret=base::del_element('news', $_GET['delid']);
?>
<table width="100%" id="allnews">
<th align="left">Заголовок</th>
<th align="left">Краткий текст</th>
<th align="left">Опубликована</th>
<th align="left">Категория</th>
<th align="left">Действие</th>
<?
$news_ids=news::get_news_ids();
foreach ($news_ids as $news_id) {
	echo '<tr>';
	echo '<td>'.news::get_news_by_id_adm($news_id, 'title').'</td>';
	echo '<td>'.htmlspecialchars(news::get_news_by_id_adm($news_id, 'desc')).'</td>';
	echo '<td>'.news::get_news_by_id_adm($news_id, 'timestamp').'</td>';
	echo '<td>'.base::get_field_by_id('categories', 'name', news::get_news_by_id_adm($news_id, 'cid')).'</td>';
	echo '<td><a href="'.$scriptname.'?mod=news&action=edit&eid='.$news_id.'">Редактировать</a>
	<a href="javascript:delete_n('.$news_id.')">Удалить</a></td>';
	echo '</tr>';
}
?>
</table>
<?
if (intval($_GET['delid'])>0)
	if ($delnret>0)
		messages::showmsg('Удаление новости', 'Новость успешна удалена из базы', 'success');
	else
		messages::showmsg('Удаление новости', 'Новость не была удалена из базы', 'error');
?>
<script>
function delete_n(id){
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Браузер не поддерживает запросы HTTP")
			return
		}
		showMsg('Подождите...' , 'msgSpan')
		var url="ajax/news.ajx.php";
		url=url+"?act=manage&del="+id;
		xmlHttp.onreadystatechange=delNews
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
}
function delNews(){
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
{
document.getElementById("allnews").innerHTML=xmlHttp.responseText
hideMsg('msgSpan')
} 
}
function GetXmlHttpObject(handler)
{ 
var objXMLHttp=null
if (window.XMLHttpRequest)
{
objXMLHttp=new XMLHttpRequest()
}
else if (window.ActiveXObject)
{
objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
}
return objXMLHttp
}
</script>
<?elseif ($action=='edit'):?>
<?
if (sizeof($_POST)>0){
	if (!empty($_POST['eid'])){
		$_POST['ntitle'] = strip_tags($_POST['ntitle']);
		$_POST['ndesc'] = strip_tags($_POST['ndesc']);
		$rn1=base::erewrite('news', 'cid', $_POST['cid'], $_POST['eid']);
		$rn2=base::erewrite('news', 'title', $_POST['ntitle'], $_POST['eid']);
		$rn4=base::erewrite('news', 'text', $_POST['ntext'], $_POST['eid']);
		$rn5=base::erewrite('news', 'desc', $_POST['ndesc'], $_POST['eid']);
		$rn6=base::erewrite('news', 'active', $_POST['nactive'], $_POST['eid']);
		$rn7=base::erewrite('news', 'type', $_POST['ntype'], $_POST['eid']);
		if ($rn1>0 && $rn2>0 && $rn4>0 && $rn5>0 && $rn6>0 && $rn7>0)
			$editnewsret=1;
		else $editnewsret=-1;
	}
	else {
		if (!empty($_POST['cid']) && !empty($_POST['ntitle'])){
			$_POST['ntitle'] = strip_tags($_POST['ntitle']);
			$_POST['ndesc'] = strip_tags($_POST['ndesc']);
			$an1=news::add_news($_POST['cid'], $_POST['ntitle'], $_POST['ntext'], date('d.m.Y H:i:s'), $_POST['ndesc'], $_POST['ntype'], $_POST['nactive'], $_SESSION['user_name']);
		}
	}
}
if (!empty($_GET['eid']) && $_GET['eid']>0)
	$cid=news::get_news_by_id_adm($_GET['eid'], 'cid');
$formget='?mod=news&action=edit';
if (!empty($_GET['eid']))
	$formget=$formget.'&eid='.$_GET['eid'];
?>
<form action="<?echo $scriptname.$formget?>" id="formd1" method="post">
<? if (!empty($_GET['eid'])):?>
<input type="hidden" name="eid" value="<?echo $_GET['eid']?>">
<?endif;?>
<table style="width:100%">
<tr>
<td valign="top">Показывать</td>
<td>
<?
$n_active = news::get_news_by_id_adm($_GET['eid'], 'active');
$n_type = news::get_news_by_id_adm($_GET['eid'], 'type');
?>
<select name="nactive" style="width:100%">
<?
switch($n_active){
	case 1:
		echo '<option value="1">Да - пользователи могут читать эту новость</option>
		<option value="0">Нет - пользователи НЕ могут читать эту новость</option>';
	break;
	case 0:
		echo '<option value="0">Нет - пользователи НЕ могут читать эту новость</option>
		<option value="1">Да - пользователи могут читать эту новость</option>';
	break;
	default:
		echo '<option value="1">Да - пользователи могут читать эту новость</option>
		<option value="0">Нет - пользователи НЕ могут читать эту новость</option>';
}
?>
</select>
</td>
</tr>
<tr>
<td valign="top">Тип</td>
<td>
<select name="ntype" style="width:100%">
<?
switch($n_type){
	case 1:
		echo '<option value="1">Новость</option>
		<option value="2">Статья</option>';
	break;
	case 2:
		echo '<option value="2">Статья</option>
		<option value="1">Новость</option>';
	break;
	default:
		echo '<option value="1">Новость</option>
		<option value="2">Статья</option>';
}
?>
</select>
</td>
</tr>
<tr>
<td valign="top">Категория</td>
<td><select style="width:100%" name="cid">
<?
if ($cid!=-1)
	if ($cid>0)
		echo '<option value="'.$cid.'">'.base::get_field_by_id('categories', 'name', $cid, 'id').'</option>';
$categories=base::eread('categories', 'id', 'name', null, null, null);
if (sizeof($categories)>=0 && $categories!=-1)
	foreach ($categories as $categorie=>$id)
		echo '<option value="'.$id.'">'.$categorie.'</option>';
?>
</select></td>
</tr>
<tr>
<input type="hidden" id="date" name="date" value="<?echo $timestamp?>">
<td valign="top">Заголовок</td>
<? if (!empty($_GET['eid']) && $_GET['eid']>0)
$newstitle=news::get_news_by_id_adm($_GET['eid'], 'title')?>
<td><input type="text" name="ntitle" style="width:100%" value="<? echo $newstitle ?>"></td>
</tr>
<tr>
<td valign="top">Краткое описание</td>
<td>
<textarea style="width:100%; height:50px;" name="ndesc"><?
if(news::get_news_by_id_adm($_GET['eid'], 'desc')>=0) echo news::get_news_by_id_adm($_GET['eid'], 'desc');
?></textarea>
</td>
</tr>
<tr>
<td valign="top">Текст новости</td>
<td>
<? if (!empty($_GET['eid']) && $_GET['eid']>0)
$newstext=news::get_news_by_id_adm($_GET['eid'], 'text')?>
<?
include('spaw2/spaw.inc.php');
$spaw = new SpawEditor('ntext', $newstext);  
$spaw->show();
?>
</td>
</tr>
<tr>
<td valign="top"></td>
<td>
<input type="submit" value="Сохранить новость" />
<input type="reset" value="Отменить изменения" />
</td>
</tr>
</table>
</form>
<?
if (sizeof($_POST)>0){
	if (empty($_POST['eid'])){
		/*if (!empty($_POST['cid']) && !empty($_POST['ntitle'])){
			$d=base::date_parse($_POST['date'], 'yyyy-mm-dd');
			if (!checkdate($d['month'], $d['day'], $d['year']))
				messages::showmsg('Ошибка сохранения статьи', 'Неверно указана дата', 'error');
		}
	else messages::showmsg('Ошибка сохранения статьи', 'Во время сохранения статьи произошли внутренние ошибки. Пожалуйста, обратитесь в техническую поддержку', 'error');*/
	}
	elseif ($editnewsret==1 && isset($_POST['eid'])) messages::showmsg('Сохранение статьи', 'Новость успешно сохранена в базе данных', 'success');
	elseif ($editnewsret<=-1 && isset($_POST['eid'])) messages::showmsg('Ошибка сохранения статьи', 'Во время сохранения статьи произошли внутренние ошибки. Пожалуйста, обратитесь в техническую поддержку', 'error');
if (empty($_POST['eid']))
if ($an1>-1)
	messages::showmsg('Сохранение статьи', 'Новость успешно сохранена в базе данных', 'success');
	else messages::showmsg('Ошибка сохранения статьи', 'Во время сохранения статьи произошли внутренние ошибки. Пожалуйста, обратитесь в техническую поддержку', 'error');
}
?>
<script language="JavaScript" src="js/calendar.js">
</script>
<script language="JavaScript" src="js/cal_conf2.js">
</script>
<?elseif ($action=='settings'):?>
<?
if (!empty($_POST['all_perpage']) && !empty($_POST['small_perpage']) && !empty($_POST['order'])){
	$r1=news::change_settings('all', $_POST['all_perpage']);
	$r2=news::change_settings('small', $_POST['small_perpage']);
	$r3=news::change_settings('order', $_POST['order']);
	$r4=news::change_settings('rss', $_POST['rss']);
	$_POST['premod'] = $_POST['premod'] ? 1 : 0;
	$r4=news::change_settings('premod', $_POST['premod']);
}
else $chret=-1;
if ($r1>0 && $r2>0 && $r3>0 && $r4>0)
	$chret=1;
else $chret=-1;
?>
<form action="<?echo $scriptname?>?mod=news&action=settings" method="post">
<table>
<tr>
<td>Количество новостей на страницу</td>
<td><input type="text" name="all_perpage" value="<?echo base::eread('news_config', 'value', null, 'name', 'all')?>" style="width:100%" /></td>
</tr>
<tr>
<td>Количество новостей в пред-просмотре</td>
<td><input type="text" name="small_perpage" value="<?echo base::eread('news_config', 'value', null, 'name', 'small')?>" style="width:100%" /></td>
</tr>
<tr>
<td>Порядок вывода новостей</td>
<td>
<?
$order1=base::eread('news_config', 'value', null, 'name', 'order');
echo mysql_error();
if ($order1=='12'){
	$ord_str1='По возрастанию дат';
	$ord_str2='По убыванию дат';
	$order2='21';
}
if ($order1=='21'){
	$ord_str1='По убыванию дат';
	$ord_str2='По возрастанию дат';
	$order2='12';
}
?>
<select name="order" style="width:100%">
<option value="<?echo $order1?>"><?echo $ord_str1?></option>
<option value="<?echo $order2?>"><?echo $ord_str2?></option>
</select>
</td>
</tr>
<tr>
<td>Премодерация</td>
<?
$checked = base::eread('news_config', 'value', null, 'name', 'premod');
$checked = $checked ? 'checked' : '';
?>
<td><input type="checkbox" name="premod" value="1" <?=$checked?>/></td>
</tr>
<!--
<tr>
<td>Добавить RSS (требуется модуль)</td>
<td><input type="checkbox" name="rss" value="<?//echo base::eread('news_config', 'value', null, 'name', 'rss')?>" /></td>
</tr>
-->
<tr>
<td>
<input type="submit" value="Сохранить настройки" />
<input type="reset" value="Отменить изменения" />
</td>
<td></td>
</tr>
</table>
</form>
<?
if (sizeof($_POST)){
	if ($chret>0)
		messages::showmsg('Сохранение настроек', 'Настройки успешно изменены и сохранены в базе', 'success');
	elseif ($chret<=0)
		messages::showmsg('Сохранение настроек', 'Настройки не были изменены проверьте все поля, если данная ошибка повторяется без ошибок с вашей стороны, обратитесь в техническую поддержку', 'error');
}
?>
<?endif;?>
<?endif;?>
