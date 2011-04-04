<form action="profile.php?user=<?=$user?>&edit" method="post">
<fieldset>
<legend>Модераторские настройки</legend>
<table>
<tr>
<td>Заблокирован:</td>
<td>
<select name="banned">
<option value="1" <?=$banned_y?>>Да</option>
<option value="0" <?=$banned_n?>>Нет</option>
</select>
</td>
<td></td>
</tr>
<tr>
<td>Уровень каптчи:</td>
<td>
<select name="captcha">
