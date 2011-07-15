<form action="admin.php?action=edit_settings" method="post">
<a href="admin.php?action=edit_settings_ui"><<<Назад</a>
<fieldset>
<legend>Изменение парольной фразы</legend>
<table align="center">
<tr>
<td>Парольная фраза:</td>
<td><input type="text" name="password" value="<?=$pass?>" style="width:100%"></td>
<input type="hidden" name="set" value="change_pass">
<td><input type="submit" value="Сохранить"></td>
</table>
</fieldset>
</form>