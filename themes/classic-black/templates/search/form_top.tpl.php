<br><h1>Поиск по сайту</h1><br>
<form method=GET action="search.php">
<table width="60%">
<tr>
<td>
Искать: <input type="text" name="q" size=50 value="<?=$search_string?>">
</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>
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
</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td>
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
</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>
<table>
<tr>
<td>Искать по фильтрам:</td>
<td><input type="radio" name="filter_search" value="yes" <?=$fil_srch_yes_ch?>>Да</td>
<td><input type="radio" name="filter_search" value="no" <?=$fil_srch_no_ch?>>Нет</td>
<td></td>
</tr>
<tr>
<td>Метод поиска:</td>
<td><input type="radio" name="search_method" value="and" <?=$srch_mthd_and_ch?>>И(AND)</td>
<td><input type="radio" name="search_method" value="or" <?=$srch_mthd_or_ch?>>ИЛИ(OR)</td>
<td></td>
</tr>
