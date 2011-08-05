<form action="admin.php?action=install_filter" method="post" enctype="multipart/form-data">
<a href="admin.php?action=manage_filters_ui"><<<Назад</a>
<fieldset>
<legend>Установка фильтра</legend>
<table align="center">
<tr>
<td>Путь к архиву:</td>
<td><input name="file" type="file"></td>
<input type="hidden" name="set" value="install_filter">
<td><input type="submit" value="Сохранить"></td>
</tr>
</table>
</fieldset>
</form>