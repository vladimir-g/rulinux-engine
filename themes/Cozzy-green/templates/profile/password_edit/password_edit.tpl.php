<form action="profile.php?user=<?=$user?>&edit" method="post">
<fieldset>
<legend>Смена пароля</legend>
<table>
<tr>
<td>Старый пароль:</td>
<td><input type="password" name="old_pass" value=""></td>
<td></td>
</tr>
<tr>
<td>Новый пароль:</td>
<td><input type="password" name="new_pass" value=""></td>
<td></td>
</tr>
<tr>
<td>Повторите пароль:</td>
<td><input type="password" name="new_pass_retype" value=""></td>
<td></td>
</tr>
</table>
</fieldset>
<input type="submit" value="Изменить пароль">
<input type="hidden" name="action" value="pass">
</form>
