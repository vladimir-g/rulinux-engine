<form action="admin.php?action=edit_settings" method="post">
<a href="admin.php?action=edit_settings_ui"><<<Назад</a>
<fieldset>
<legend>Изменение регистрационного письма</legend>
<table width="100%">
<tr>
<td style="width: 10%">Тема письма:</td>
<td><input type="text" name="subject" value="<?=$subj?>" style="width:100%"></td>
</tr>
<tr valign="top">
<td>Текст сообщения:</td>
<td><textarea name="text" style="height:100%;width:100%;" rows="25"><?=$text?></textarea></td>
</tr>
<tr>
<td><input type="hidden" name="set" value="change_letter"></td>
<td><input type="submit" value="Сохранить"></td>
</table>
</fieldset>
</form>