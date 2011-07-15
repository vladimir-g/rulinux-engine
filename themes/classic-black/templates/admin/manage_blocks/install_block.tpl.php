<form action="admin.php?action=install_block" method="post" enctype="multipart/form-data">
<a href="admin.php?action=manage_blocks_ui"><<<Назад</a>
<fieldset>
<legend>Установка блока</legend>
<table align="center">
<tr>
<td>Путь к блоку:</td>
<td><input name="file" type="file"></td>
<input type="hidden" name="set" value="install_block">
<td><input type="submit" value="Сохранить"></td>
</tr>
</table>
</fieldset>
</form>