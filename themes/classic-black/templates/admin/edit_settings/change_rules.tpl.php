<form action="edit_settings_act" method="post">
<a href="edit_settings"><<<Назад</a>
<fieldset>
<legend>Изменение правил сайта</legend>
<table width="100%">
<tr valign="top">
<td style="width: 10%">Правила сайта:</td>
<td><textarea name="rules" style="height:100%;width:100%;" rows="25"><?=$rules?></textarea></td>
</tr>
<tr>
<td><input type="hidden" name="set" value="change_rules"></td>
<td><input type="submit" value="Сохранить"></td>
</table>
</fieldset>
</form>