<?if ($inside):?>
<?
require_once('classes/search.class.php');
if (isset($_POST['res_per_page']) && isset($_POST['find_by'])){
	if (intval($_POST['res_per_page'])>0){
		foreach ($_POST as $k=>$v) {
			$r=search::change_settings($k, $v);
			if ($r<=0)
				break;
		}
	}
	else $r=-1;
}
?>
<form action="<?echo $scriptname?>?mod=search" method="post">
<table style="width:100%">
<tr>
<td>Результатов на страницу</td>
<td><input type="text" name="res_per_page" value="<?echo base::eread('search_config', 'value', null, 'name', 'res_per_page')?>" style="width:100%" /></td>
</tr>
<tr>
<td>Поиск по</td>
<td>
<?
$s_by=base::eread('search_config', 'value', null, 'name', 'find_by');
if ($s_by=='1'){
	$v1='1';
	$v2='2';
	$n1='словосочетаниям';
	$n2='отдельным словам запроса (разделенным пробелом)';
}
elseif ($s_by=='2'){
	$v1='2';
	$v2='1';
	$n1='отдельным словам запроса (разделенным пробелом)';
	$n2='словосочетаниям';
}
?>
<select style="width:100%" name="find_by">
<option value="<?echo $v1?>"><?echo $n1?></option>
<option value="<?echo $v2?>"><?echo $n2?></option>
</select>
</td>
</tr>
<tr>
<td><input type="submit" value="Сохранить настройки">&nbsp;<input type="reset" value="Отменить изменения"></td>
<td></td>
</tr>
</table>
</form>
<?
if (isset($_POST['res_per_page']) && isset($_POST['find_by'])){
	if ($r>0) messages::showmsg('Сохранение настроек', 'Настройки успешно ихменены сохранены в базе', 'success');
	else messages::showmsg('Сохранение настроек', 'Настройки изменены не были', 'error');
}
?>
<?endif;?>