<br>
<h2>Укажите личную информацию</h2>
Время сервера: <?=$time?>
<br>
<br>
<form action="register.php" method="post" enctype="multipart/form-data">
<table>
<tr>
<td>Логин:</td>
<td>
<input type="text" name="nick_f" value="<?=$nick?>" disabled="disabled">
<input type="hidden" name="nick" value="<?=$nick?>">
</td>
<td>&nbsp;</td>
</tr>

<td>&nbsp;</td>
</tr>
<tr>
<td>Имя:</td>
<td><input type="text" name="user_name" value=""></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Фамилия:</td>
<td><input type="text" name="user_lastname" value=""></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Пол:</td>
<td>
<select name="gender">
<option value="1" selected>Мужской</option>
<option value="0" >Женский</option>
</select>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>E-mail:</td>
<td>
<input type="text" name="user_email" value="<?=$email?>" disabled="disabled">
<input type="hidden" name="user_email" value="<?=$email?>">
</td>
<td><input type="checkbox" name="showEmail" checked>Показывать</td>
</tr>
<tr>
<td>IM:</td>
<td><input type="text"  name="user_im" value=""></td>
<td><input type="checkbox" name="showIM" checked>Показывать</td>
</tr>
<tr>
<td>Страна:</td>
<td><input type="text" name="user_country" value=""></td>
<td></td>
</tr>
<tr>
<td>Город:</td>
<td><input type="text" name="user_city" value=""></td>
<td></td>
</tr>
<tr>
<td>Выберите время:</td>
<td>
<select name="user-gmt">
<option value="0" selected>Выберите время</option>
<option value="-11">Время сервера -11</option>
<option value="-12">Время сервера -12</option>
<option value="-10">Время сервера -10</option>
<option value="-9">Время сервера -09</option>
<option value="-8">Время сервера -08</option>
<option value="-7">Время сервера -07</option>
<option value="-6">Время сервера -06</option>
<option value="-5">Время сервера -05</option>
<option value="-4">Время сервера -04</option>
<option value="-3">Время сервера -03</option>
<option value="-2">Время сервера -02</option>
<option value="-1">Время сервера -01</option>
<option value="0">Время сервера +00</option>
<option value="+1">Время сервера +01</option>
<option value="+2">Время сервера +02</option>
<option value="+3">Время сервера +03</option>
<option value="+4">Время сервера +04</option>
<option value="+5">Время сервера +05</option>
<option value="+6">Время сервера +06</option>
<option value="+7">Время сервера +07</option>
<option value="+8">Время сервера +08</option>
<option value="+9">Время сервера +09</option>
<option value="+10">Время сервера +10</option>
<option value="+11">Время сервера +11</option>
<option value="+12">Время сервера +12</option>
</select>
</td>
<td></td>
</tr>
</table>
Дополнительно:<br>
<textarea name="user_additional" rows="7" cols="45"></textarea>
<br><br>
<input type="hidden" name="pass" value="<?=$pass?>">
<input type="submit" value="Зарегистрироваться">
<input type="hidden" name="action" value="second_sbm">
</form>