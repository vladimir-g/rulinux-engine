<div class="boxlet_title_div"><span class="boxlet_title">[title]</span></div>
<div class="boxlet_content">
<form action="login" method="post">
<table width=>
<tr>
<td>Логин: </td>
<td>
<input type="text" class="auth-field" name="user">
</td>
</tr>
<tr>
<td>Пароль: </td>
<td>
<input type="password" class="auth-field" name="password">
</td>
</tr>
<tr>
<td></td>
<td>
<input type="hidden" name="auth_system" value="this">
<input  type="submit" name="login" value="отправить">
</td>
</tr>
</table>
</form>
<br>
<form action="login.php" method="post">
<table>
<tr>
<td>OpenID: </td>
<td>
<input type="text" class="auth-field" name="openid_url">
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
<br>
* <a href="user_[user]">Профиль</a><br>
* <a href="rules">Правила ресурса</a><br><br>
* <a href="comments">Мои комментарии</a><br>
* <a href="replys">Ответы на мои комментарии</a><br>
* <a href="register">Регистрация</a>
</div>
