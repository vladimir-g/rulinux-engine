<br>
<br>
<table width=80% align="center">
<tr>
<td>
<h2>This Site: </h2>
</td>
<td>
<h2>OpenID: </h2>
</td>
</tr>
<tr>
<td>
<form action="login.php" method="post">
<table>
<tr>
<td>Логин: </td>
<td>
<input type="text" name="user">
</td>
</tr>
<tr>
<td>Пароль: </td>
<td>
<input type="password" name="password">
</td>
</tr>
<tr>
<td></td>
<td>
<br>
<input type="hidden" name="auth_system" value="this">
<input  type="submit" name="login" value="отправить">
</td>
</tr>
</table>
</form>
</td>
<td>
<form action="login.php" method="post">
<table>
<tr>
<td>URL: </td>
<td>
<input type="text" name="openid_url">
</td>
</tr>
<tr>
<td></td>
<td>
<input type="hidden" name="openid_action" value="login">
<input type="hidden" name="auth_system" value="openid">
<input  type="submit" name="login" value="отправить">
</td>
</tr>
</table>
</form>
</td>
</tr>
</table>































