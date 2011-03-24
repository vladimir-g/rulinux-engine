<form action="profile.php?user=<?=$user?>&edit" method="post">
<fieldset>
<legend>Конструктор главной страницы</legend>
<table width="100%">
<tr>
<th>Блок</th>
<th>Позиция</th>
<th>Сортировка</th>
</tr>
<tr>
<td style="text-align:center">Авторизация</td>
<td style="text-align:center">
<select name="auth_pos">
<option value="1">Слева</option>
<option value="2">Справа</option>
<option value="0">Не используется</option>
</select>
</td>
<td style="text-align:center">
<select name="auth_sort">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select>
</td>
</tr>
<tr>
<td style="text-align:center">Ссылки</td>
<td style="text-align:center">
<select name="links_pos">
<option value="1">Слева</option>
<option value="2">Справа</option>
<option value="0">Не используется</option>
</select>
</td>
<td style="text-align:center">
<select name="links_sort">
<option value="2">2</option>
<option value="1">1</option>
<option value="3">3</option>
<option value="4">4</option>
</select>
</td>
</tr>
<tr>
<td style="text-align:center">Галерея</td>
<td style="text-align:center">
<select name="gall_pos">
<option value="1">Слева</option>
<option value="2">Справа</option>
<option value="0">Не используется</option>
</select>
</td>
<td style="text-align:center">
<select name="gall_sort">
<option value="3">3</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="4">4</option>
</select>
</td>
</tr>
<tr>
<td style="text-align:center">Последние 10 сообщений</td>
<td style="text-align:center">
<select name="tracker_pos">
<option value="1">Слева</option>
<option value="2">Справа</option>
<option value="0">Не используется</option>
</select>
</td>
<td style="text-align:center">
<select name="tracker_sort">
<option value="4">4</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
</select>
</td>
</tr>
<tr>
<td style="text-align:center">Неизвестно</td>
<td style="text-align:center">
<select name="_pos">
<option value="2">Справа</option>
<option value="1">Слева</option>
<option value="0">Не используется</option>
</select>
</td>
<td style="text-align:center">
<select name="_sort">
<option value=""></option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select>
</td>
</tr>
<tr>
<td style="text-align:center">F.A.Q.</td>
<td style="text-align:center">
<select name="faq_pos">
<option value="0">Не используется</option>
<option value="1">Слева</option>
<option value="2">Справа</option>
</select>
</td>
<td style="text-align:center">
<select name="faq_sort">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
</select>
</td>
</tr>
</table>
</fieldset>
<input type="hidden" name="action" value="main_page">
<input type="submit" value="Сохранить настройки">
</form>