<form action="admin.php?action=edit_settings" method="post">
<a href="admin.php?action=edit_settings_ui"><<<Назад</a>
<fieldset>
<legend>Изменение заголовка сайта</legend>
<table align="center" width="100%">
<tr>
<td width="10%">Заголовок сайта:</td>
<td><input type="text" name="title" value="<?=$title?>" style="width:100%"></td>
<input type="hidden" name="set" value="change_title">
<td width="5%"><input type="submit" value="Сохранить"></td>
</table>
</fieldset>
</form>