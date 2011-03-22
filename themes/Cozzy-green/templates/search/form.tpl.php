<br /><h1>Поиск по сайту</h1><br />
<form method=GET action="search.php">
Искать: <input type="text" name="q" size=50 value="<?=$search_string?>"><p>
<select name="include">
<option value="topics" >только темы</option>
<option value="comments" >только комментарии</option>
<option value="all" selected>темы и комментарии</option>
</select>
За:
<select name="date">
<option value="3month" >три месяца</option>
<option value="year" >год</option>
<option value="all" selected>весь период</option>
</select>
<br>
Раздел:
<select name="section">
<option value="1" >новости</option>
<option value="2" >статьи</option>
<option value="3" >галерея</option>
<option value="4" >форум</option>
<option value="0" selected>все</option>
</select>
Пользователь:
<input type="text" name="username" size=20 value="<?=$search_user?>"><p>
<br>
<input type="submit" value="Искать!"><br>
</form>
