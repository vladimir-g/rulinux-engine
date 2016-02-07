<br>
<form action="register.php" method="post">
<table>
<tr>
<td>Логин</td>
<td>
<input type="text" name="nick">
</td>
</tr>
<tr>
<td>Пароль</td>
<td>
<input type="password" name="password_1">
</td>
</tr>
<tr>
<td>Подтвердите пароль</td>
<td>
<input type="password" name="password_2">
</td>
</tr>
<tr>
<td>e-mail</td>
<td>
<input type="text" name="e-mail">
</td>
</tr>
</table>
<?=$captcha?>
<p><input type="submit" name="first_smb" value="отправить"></p>
</form>