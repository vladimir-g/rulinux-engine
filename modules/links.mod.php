<?if ($inside):?>
<?
require_once('classes/pages.class.php');
switch($_GET['action']){
	case 'header': $_GET['eid'] = 'header11'; break;
	case 'footer': $_GET['eid'] = 'footer11'; break;
	case 'menu': $_GET['eid'] = 'menu'; break;
	case 'menu_active': $_GET['eid'] = 'menu_active'; break;
	case 'add': if($_GET['manage'] != 'menu') $_GET['eid'] = 'add'; else $_GET['eid'] = 'mmenu'; break;
	case 'mmenu': $_GET['manage'] = 'menu'; break;
	case 'mlink': $_GET['manage'] = 'link'; break;
	case 'mmeta': $_GET['manage'] = 'meta'; break;
}
$eid=$_GET['eid'];
if (empty($eid))
	$action=$_GET['action'];
?>
<style>
.active{
	border-left:solid #6a6c9b 1px;
	border-top:solid #6a6c9b 1px;
	border-right:solid #6a6c9b 1px;
	border-bottom:none;
	background-color:#ffffff;
}
.inactive{
	border: solid #6a6c9b 1px;
	background-color:#ffffff;
}
</style>
<?
if (!isset($eid) && !isset($_GET['manage']))
	$_GET['manage']='link';
if (empty($eid)):
$act=$_GET['action'];
if ($act=='madd' && isset($_POST['link'])) {
	if (!empty($_POST['link']) && !empty($_POST['name']))
		$addres=pages::add_menu_link($_POST['link'], $_POST['name']);
	else $addres=-1;
}
elseif ($act=='edit' && isset($_POST['id_0'])) {
	for ($cnt=0; $cnt<intval($_POST['idc'])+1; $cnt++){
		$editres=pages::set_link(
								$_POST['link_'.$_POST['id_'.$cnt]],
								$_POST['name_'.$_POST['id_'.$cnt]],
								$_POST['id_'.$cnt], $_POST['parent_'.$_POST['id_'.$cnt]]
								);
	}
}
elseif ($act=='del') {
	if (intval($_GET['did'])>0)
		$delres=base::del_element('menu', $_GET['did']);
	else $delres=-1;
}
?>
<? if($_GET['manage']=='menu'): ?>
<span>Ссылки меню</span><br />
<form action="admin.php?mod=links&action=edit&manage=menu" method="post">
<table style="width:100%">
<tr>
<th>Ссылка</th>
<th>Название</th>
<th>Родительский элемент</th>
<th>Действия</th>
</tr>
<?
$lnks=pages::get_links('name');
if ($lnks!=-1) {
	$names=array_flip($lnks);
	$idc=0;
	foreach ($names as $name) {
		$eid = $lnks[$name];
		echo '<tr>
		<input type="hidden" name="id_'.$idc.'" value="'.$lnks[$name].'" id="i'.$idc.'">
		<td><input type="text" name="link_'.$eid.'" value="'.base::get_field_by_id('menu', 'link', $lnks[$name]).'" style="width:100%" id="l'.$idc.'"></td>';
		echo '
		<td><input type="text" name="name_'.$eid.'" value="'.$name.'" style="width:100%" id="n'.$idc.'"></td>';
		echo '<td style="text-align:center">';
		echo '<select name="parent_'.$eid.'" id="p'.$idc.'">';
		if ((int)pages::get_parent($eid) == 0){
			echo '<option value="0">Нет</option>';
			foreach ($names as $lnk) {
				$lid = $lnks[$lnk];
				echo '<option value="'.$lid.'">'.base::get_field_by_id('menu', 'name', $lid).'</option>';
			}
		}
		else{
			echo '<option value="'.pages::get_parent($eid).'">'.base::get_field_by_id('menu', 'name', pages::get_parent($eid)).'</option>';
			foreach ($names as $lnk) {
				$lid = base::get_field_id('menu', 'link', $lnks[$lnk]);
				if ($lid != $eid)
					echo '<option value="'.$lid.'">'.base::get_field_by_id('menu', 'name', $lid).'</option>';
			}
			echo '<option value="0">Нет</option>';
		}
		
		echo '</select>';
		echo '</td>';
		echo '
		<td align="center"><a href="'.$scriptname.'?mod=links&action=del&did='.$eid.'&manage=menu">Удалить</a>
		<a href="javascript:e_up('.$idc.')" class="linkspan" title="Пункт наверх">&uarr;</a>
		<a href="javascript:e_dn('.$idc.')" class="linkspan" title="Пункт вниз">&darr;</a>
		</td>
		</tr>';
		$idc++;
	}
	echo '<input type="hidden" name="idc" value="'.$idc.'">';
}
?>
<script>
var cnt=<?echo $idc?>;
function e_up(num){
	if(document.getElementById('i'+(num-1))!=null){
		temp=document.getElementById('l'+(num-1)).value;
		document.getElementById('l'+(num-1)).value=document.getElementById('l'+num).value;
		document.getElementById('l'+num).value=temp
		temp=document.getElementById('n'+(num-1)).value;
		document.getElementById('n'+(num-1)).value=document.getElementById('n'+num).value;
		document.getElementById('n'+num).value=temp
		temp=document.getElementById('p'+(num-1)).innerHTML;
		document.getElementById('p'+(num-1)).innerHTML=document.getElementById('p'+num).innerHTML;
		document.getElementById('p'+num).innerHTML=temp
	}
}
function e_dn(num){
	if(document.getElementById('i'+(num+1))!=null){
		temp=document.getElementById('i'+(num+1)).value;
		document.getElementById('i'+(num+1)).value=document.getElementById('i'+num).value;
		document.getElementById('i'+num).value=temp
		temp=document.getElementById('l'+(num+1)).value;
		document.getElementById('l'+(num+1)).value=document.getElementById('l'+num).value;
		document.getElementById('l'+num).value=temp
		temp=document.getElementById('n'+(num+1)).value;
		document.getElementById('n'+(num+1)).value=document.getElementById('n'+num).value;
		document.getElementById('n'+num).value=temp
		temp=document.getElementById('p'+(num+1)).innerHTML;
		document.getElementById('p'+(num+1)).innerHTML=document.getElementById('p'+num).innerHTML;
		document.getElementById('p'+num).innerHTML=temp
	}
}
</script>
<tr>
<td><input type="submit" value="Сохранить">&nbsp;<input type="reset" value="Отменить изменения"></td>
<td></td>
</tr>
</table>
</form>
<form action="admin.php?mod=links&action=madd&manage=menu" method="post">
<span>Создать новый пункт меню</span><br />
<table style="width:100%">
<tr>
<td>Ссылка</td>
<td><input type="text" name="link" value="" style="width:100%"></td>
</tr><tr>
<td>Название</td>
<td><input type="text" name="name" value="" style="width:100%"></td>
</tr>
<tr>
<td><input type="submit" value="Создать"></td>
<td></td>
</tr>
</table>
</form>
<?elseif ($_GET['manage']=='link'):?>
<script>
function check_all(par, num){
	switch (par) {
		case 'caab':
			for (i=1; i<=num; i++){
				id='b'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=true;
				}
			}
			check_all('uadb', num);
		break;
		case 'cadb':
			for (i=1; i<=num; i++){
				id='d'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=true;
				}
			}
			check_all('uaab', num);
		break;
		case 'ca':
			for (i=1; i<=num; i++){
				id='b'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=true;
				}
				id='d'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=true;
				}
			}
		break;
		case 'uaab':
			for (i=1; i<=num; i++){
				id='b'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=false;
				}
			}		
		break;
		case 'uadb':
			for (i=1; i<=num; i++){
				id='d'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=false;
				}
			}
		break;
		case 'ua':
			for (i=1; i<=num; i++){
				id='b'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=false;
				}
				id='d'+i
				if (document.getElementById(id)!=null){
					document.getElementById(id).checked=false;
				}
			}
		break;
	}
}
</script>
<form action="<? echo $scriptname?>?mod=links&manage=link" method="post">
<table style="width:100%" cellspadding="0" sellspacing="0" id="toc">
<? if($_GET['filter'] == 'id'){$clr = '#0000ff'; if(!isset($_GET['desc'])){$desc='&desc'; $arr='&dArr;';} else $arr=' &uArr;';} else {$clr = '#000000'; $desc=''; $arr='';} ?>
<th align="left"><a href="?mod=links&filter=id<?=$desc;?>" style="color:<?=$clr;?>; border-bottom:dashed 1px <?=$clr;?>"># <?=$arr;?></a></th>

<? if($_GET['filter'] == 'title'){$clr = '#0000ff'; if(!isset($_GET['desc'])){$desc='&desc'; $arr='&dArr;';} else $arr=' &uArr;';} else {$clr = '#000000'; $desc=''; $arr='';} ?>
<th align="left"><a href="?mod=links&filter=title<?=$desc;?>" style="color:<?=$clr;?>; border-bottom:dashed 1px <?=$clr;?>">Страница <?=$arr;?></a></th>

<th align="left">Ссылка</a></th>
<th align="left">Связь</th>
<th align="left">Действие со страницей</th>
<?
$to_add=$_POST['toadd'];
$to_del=$_POST['todel'];
if ($to_add>0 || $to_del>0){
	for ($a=1; $a<=$to_add; $a++){
		$pg=pages::get_page($_POST['lp'.$a]);
		if ($pg!=-1)
			pages::add_menu_link('page.php?id='.$a, $pg['title']);
	}
	for ($a=1; $a<=$to_del; $a++){
		$pg=pages::get_page($_POST['dp'.$a]);
		if ($pg!=-1)
			pages::rm_page_link('page.php?id='.$a);
	}
}
if (isset($_GET['did']) && intval($_GET['did'])>0){
	$delret1=base::del_element('pages', $_GET['did']);
	$delret2=pages::rm_page_link('page.php?id='.$_GET['did']);
}
$d=0;
$b=0;
$getlnks=pages::get_links();
if(sizeof($getlnks)>1)
	$getlnames = array_flip($getlnks);
else
	$getlnames = $getlnks;
$pages=pages::get_pages($_GET['filter'], isset($_GET['desc']) ? 'DESC' : 'ASC');
@$ids=array_flip($pages);
if ((int)$pages>-1){
foreach ($pages as $p) {
	echo '<tr class="highlite">';
	echo '<td><strong>'.$ids[$p].'</strong></td>';
	$curr=pages::get_page($ids[$p]);
	if ($curr['text'] == '')
		$addin='<br /><span style="color:#008200">Страница пуста</span>';
	echo '<td><a href="page.php?id='.$ids[$p].'">'.$p.$addin.'</a></td>';
	$addin = '';
	echo '<td><a href="'.$getlnks[$p].'" target="_blank">'.$getlnames[$getlnks[$p]].'</a></td>';
	if (empty($getlnks[$p])){
		$b++;
		echo '<td><input type="checkbox" id="b'.$b.'" name=lp'.$ids[$p].' value="'.$ids[$p].'">&nbsp;Связать</td>';
		$toadd=$ids[$p];
	}
	else{
		$d++;
		echo '<td><input type="checkbox" id="d'.$d.'" name=dp'.$ids[$p].' value="'.$ids[$p].'">&nbsp;Удалить связь</td>';
		$todel=$ids[$p];
	}
	echo '<td><a href="'.$scriptname.'?mod=links&eid='.$ids[$p].'" target="_blank">Редактировать</a> 
	<a href="'.$scriptname.'?mod=links&manage=link&did='.$ids[$p].'">Удалить</a></td>';
	echo '</tr>';
}
}
?>
</table>
<input type="hidden" name="toadd" value="<?echo $toadd?>">
<input type="hidden" name="todel" value="<?echo $todel?>">
<input type="submit" value="Сохранить связи">
</form>
<a href="javascript: check_all('caab', '<?echo $b+$d?>')">Отметить все для создания связи</a><br />
<a href="javascript: check_all('cadb', '<?echo $b+$d?>')">Отметить все для удаления связи</a><br />
<a href="javascript: check_all('ca', '<?echo $b+$d?>')">Отметить все</a><br />
<a href="javascript: check_all('uaab', '<?echo $$b+$d?>')">Снять отметку со всех для создания связи</a><br />
<a href="javascript: check_all('uadb', '<?echo $b+$d?>')">Снять отметку со всех для удаления связи</a><br />
<a href="javascript: check_all('ua', '<?echo $b+$d?>')">Снять отметку со всех</a><br /><br />
<?
if (isset($_GET['did'])){
	if (base::get_field_id('menu', 'link', 'page.php?id='.$_GET['did'])>0){
		if ($delret1>-1 && $delret2>-1)
			$delret=true;
		else $delret=false;
	}
	else{
		if ($delret1>-1)
			$delret=true;
		else $delret=false;
	}
	if ($delret)
		messages::showmsg('Удаление страницы', 'Страница успешно удалена из базы данных', 'success');
	else
		messages::showmsg('Удаление страницы', 'Заданная страница не была найдена в базе данных', 'error');
}
?>
<?elseif ($_GET['manage']=='meta'):?>
<?
$chmeta=1;
if (isset($_POST['metachange']) && $_POST['metachange']>0 && !isset($_POST['metatag'])){
	for ($i=0; $i<$_POST['metachange']; $i++){
		if ($_POST['http_equiv'.$i]!=''){
			if (pages::set_meta_data($_POST['http_equiv'.$i], $_POST['content'.$i], $_POST['meta'.$i], false)==-1){
				$chmeta=-1;
				break;
			}
		}
		else{
			$chmeta=-1;
			break;
		}
	}
}
elseif (isset($_POST['metatag'])){
	if ($_POST['http_equiv']!=''){
		$addmeta=pages::set_meta_data($_POST['http_equiv'], $_POST['content'], null, true);
	}
	else $addmeta=-1;
}
$mdelres=0;
if (isset($_GET['mdel']))
	if ($_GET['mdel']>0)
		$mdelres=base::del_element('meta', $_GET['mdel']);
	else $mdelres=-1;
?>
<form action="<?echo $scriptname?>?mod=links&manage=meta" method="post">
<table style="width:100%">
<th>Имя</th>
<th>Значение</th>
<th>Действие</th>
<?
$c=0;
$meta=pages::get_meta_data(true);
$mnames=array_keys($meta);
foreach ($mnames as $m) {
	$metaid=base::get_field_id('meta', 'http_equiv', $m);
	echo '<tr>
	<td><input type="text" name="http_equiv'.$c.'" value="'.$m.'" style="width:100%"></td>
	<input type="hidden" name="meta'.$c.'" value="'.$metaid.'">
	<td><input type="text" name="content'.$c.'" value="'.$meta[$m].'" style="width:100%"></td>
	<td align="center"><a href="'.$scriptname.'?mod=links&manage=meta&mdel='.$metaid.'">Удалить</td>
	</tr>';
	$c++;
}
?>
</table>
<input type="hidden" name="metachange" value="<?echo $c?>">
<input type="submit" value="Сохранить изменения">&nbsp;<input type="reset" value="Отменить все изменения">
</form>
<form action="<?echo $scriptname?>?mod=links&manage=meta" method="post">
<input type="hidden" name="metatag">
<table style="width:100%">
<tr>
<td>http-equiv</td>
<td><input type="text" name="http_equiv" value="" style="width:100%"></td>
</tr>
<tr>
<td>content</td>
<td><input type="text" name="content" value="" style="width:100%"></td>
</tr>
</table>
<input type="submit" value="Добавить мета-тег">
</form>
<?
if (isset($_POST['metachange']))
	if ($chmeta>-1)
		messages::showmsg('Изменение мета-данных', 'Мета-данные успешно изменены', 'success');
	else
		messages::showmsg('Изменение мета-данных', 'Мета-данные не были изменены из-за ошибки', 'error');
if (isset($_POST['metatag']))
	if ($addmeta>0)
		messages::showmsg('Добавление мета-данных', 'Мета-данные успешно добавлены', 'success');
	else
		messages::showmsg('Добавление мета-данных', 'Мета-данные не были добавлены из-за ошибки', 'error');
if (isset($_GET['mdel']))
	if ($mdelres>0)
		messages::showmsg('Удаление мета-данных', 'Мета-данные успешно удалены', 'success');
	else
		messages::showmsg('Удаление мета-данных', 'Мета-данные не были удалены из-за ошибки', 'error');
?>
<?endif;?>
<?
if($editres>-1)
	messages::showmsg('Обновление ссылок', 'Ссылки меню успешно изменены и сохранены в базе!', 'success');
elseif($editres==-1) messages::showmsg('Обновление ссылок', 'Изменение ссылок завершилось ошибкой, пожалуста, обратитесь в техподдержку', 'error');
if($addres>-1)
	messages::showmsg('Создание ссылки', 'Ссылка меню успешно сохранена в базе!', 'success');
elseif($addres==-1) messages::showmsg('Создание ссылок', 'Создание ссылки завершилось ошибкой, пожалуста, проверьте параметры', 'error');
if($delres>-1)
	messages::showmsg('Удаление ссылки', 'Ссылка меню успешно удалена из базы!', 'success');
elseif($delres==-1) messages::showmsg('Удаление ссылки', 'Удаление ссылки завершилось ошибкой, пожалуста, проверьте параметры или обратитесь в службу техподдержки', 'error');
?>
<?else:?>
<?
$v=0;
if (isset($_POST['title']) || isset($_POST['content']))
	$editres=pages::edit_page($eid, $_POST['title'], $_POST['content'], base::check_setting('template'), $_SESSION['user_login']);
if ((isset($_POST['title']) || isset($_POST['content'])) && $eid=='add')
	$editres=pages::add_page($_POST['title'], $_POST['content'], $_POST['menulink'], $_POST['menuname'], $_POST['menuid'], $_SESSION['user_login']);
?>
<script src="js/wins.js">
</script>
<span>Редактирование элемента</span><br />
<?
if ($_GET['eid'] >0){
	echo '<a href="javascript:show_history()">История редактирования страницы</a><br />';
}
?>
<form action="<?echo $scriptname?>?mod=links&eid=<?echo $eid?>" method="post">
<?if ((intval($eid)!=0 && !empty($eid)) || $eid=='add'):?>
<?if ($eid=='add'): $titleval='Без Имени'?>
Добавить страницу как элемент меню<br />
<?$links_count=base::get_last_index('pages', 'id');?>
<input type="hidden" value="<?echo intval($links_count)+1?>" name="menuid" style="width:100%">
<?endif;?>
<?if ($eid=='add' && $editres==-1): $titleval=$_POST['title']?>
<?endif;?>
<?if ($eid!='add') $page=pages::get_page($eid);?>
<input type="text" value="<?echo $page['title']; echo $titleval?>" name="title" style="width:100%">
<?elseif ($eid!='add'): $page=pages::get_templates($eid, base::check_setting('template'));?>
<?endif;?>
<?
if($eid!='header' && $eid!='footer' && $eid!='menu' && $eid!='menu_active'):
$plain = false;
?>
<?
else:
$plain = true;
messages::showmsg('Внимание!', 'Редактирование данного элемента может повлиять на отображение страницы вцелом!', 'warning')
?>
<?endif;?>
<?if ($eid!='add'):?>
<?if (intval($eid)!=0 && !empty($eid)) $textarea = $page['text'];?>
<?if (intval($eid)<=0 && !empty($eid)) $textarea = $page;?>
<?endif;?>
<?if ($eid='add' && $editres<0):?>
<?$textarea = $_POST['content']?>
<?endif;?>
<?

if ($plain)
	echo '<textarea name="content" style="width:100%;" rows="20">'.$textarea.'</textarea>';
else{
	include('spaw2/spaw.inc.php');
	$spaw = new SpawEditor('content', $textarea);  
	$spaw->show();
	
	$author_info = users::get_user_info($page['author']);
	$author_info = '<a href="admin.php?mod=users&act=users&uid='.$page['author'].'">'.$author_info['nick'].'</a>';
	$correctors = '';
	$corrs = array_unique($page['edited_by']);
	foreach ($corrs as $corrector_id){
		$e = 0;
		foreach ($page['edited_by'] as $c){
			if ($c == $corrector_id)
				$e++;
		}
		$corrector_info = users::get_user_info($corrector_id);
		$correctors .= '<a href="admin.php?mod=users&act=users&uid='.$corrector_id.'">'.$corrector_info['nick'].'</a> ('.$e.');<br>';
	}
}
?>
<script>

function show_history(){
	var content = '';
			content += '<table width="100%" border="0">';
			content += '<tr style="border:dashed 1px gray">';
			content += '<td style="vertical-align:top; width:100px">';
			content += '<strong>Автор:</strong><hr>';
			content += '</td>';
			content += '<td style="vertical-align:top">';
			content += '<?=$author_info?><hr>';
			content += '</td>';
			content += '</tr>';
			content += '<tr style="border:dashed 1px gray">';
			content += '<td style="vertical-align:top">';
			content += '<strong>Корректоры:</strong>';
			content += '</td>';
			content += '<td style="vertical-align:top">';
			content += '<?=$correctors == '' ? 'Эта страница еще никем не редактировалась' : $correctors?>';
			content += '</td>';
			content += '</tr>';
	addWin('center', 'center', 400, 500, 'История редактирования', content, '#ffffff', '<?=$GLOBALS['icon']?>', true);
}
</script>
<input type="submit" value="Сохранить">
<input type="reset" value="Отменить изменения">
</form>
<?
if((int)$editres<0){
	messages::showmsg('Редактирование страницы', 'Изменение контента завершилось ошибкой, пожалуста, обратитесь в техподдержку'.mysql_error(), 'error');
}
if($page==-1){
	messages::showmsg('Редактирование страницы', 'Не удалось загрузить страницу: страница не найдена', 'error');	
}
if($editres>-1){
	messages::showmsg('Редактирование страницы', 'Изменения были успешно внесены и сохранены в базе!', 'success');
}
?>
<?endif;?>
<?else: header('location: index.php');?>
<?endif;?>
