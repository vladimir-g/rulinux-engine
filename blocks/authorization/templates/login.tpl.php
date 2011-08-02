<h2>[title]</h2>
<form action="login.php" method="post">
<table>
<tr>
<td>Логин</td>
<td><input type="text" name="user"></td>
</tr>
<tr>
<td>Пароль</td>
<td><input type="password" name="password"></td>
</tr>
</table>
<input  type="submit" name="login" value="Войти">
</form>
<br>
* <a href="profile.php?user=[user]">Профиль</a><br>
* <a href="rules.php">Правила ресурса</a><br><br>
* <a href="view-comments.php">Мои комментарии</a><br>
* <a href="view-comments.php?resp">Ответы на мои комментарии</a>
* <a href="register.php">Регистрация</a>