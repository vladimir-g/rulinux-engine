<form action="add_subsection_act" method="post" enctype="multipart/form-data">
<a href="add_subsection"><<<Назад</a>
<fieldset>
<legend>Добавление подраздела</legend>
<table align="center" width="100%">
<thead>
</thead>
<tfoot>
</tfoot>
<tbody>
<tr>
<td valign="top" width="15%">Название подраздела: </td>
<td><input name="name" type="text" style="width:100%"></td>
</tr>
<tr>
<td valign="top">Описание подраздела: </td>
<td><textarea name="description" style="height:100%;width:100%;" rows="15"></textarea></td>
</tr>
<tr>
<td valign="top">Rewrite: </td>
<td><input name="rewrite" type="text" style="width:100%"></td>
</tr>
<tr>
<td valign="top">Иконка: </td>
<td><input name="icon" type="file" style="width:100%"></td>
</tr>
<tr>
<td><input type="hidden" value="1" name="section"></td>
<td><input type="submit" value="Сохранить"></td>
</tr>
</tbody>
</table>
</fieldset>
</form>