<form action="<?=$edit_link?>" method="post" enctype="multipart/form-data">
<fieldset>
<legend>Личные данные</legend>
<img src="<?=$avatar?>">
<br>Фотография 
<ul>
<li>минимум: 50x50px;
<li>максимум: 150x150px;
<li>формат: jpeg, gif или png
</ul>
<input type="file" name="user_photo">
<br>
<br>
<table>
<tr>
<td>Имя:</td>
<td><input type="text" name="user_name" value="<?=$name?>"></td>
<td>&nbsp;</td>
</tr>
<td>OpenID:</td>
<td><input type="text" name="openid" value="<?=$openid?>"></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Фамилия:</td>
<td><input type="text" name="user_lastname" value="<?=$lastname?>"></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Пол:</td>
<td>
<select name="gender">
<option value="1" <?=$checkedMale?>>Мужской</option>
<option value="0" <?=$checkedFemale?>>Женский</option>
</select>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>E-mail:</td>
<td><input type="text" name="user_email" value="<?=$email?>"></td>
<td><input type="checkbox" name="showEmail" <?=$show_email_ch?>>Показывать</td>
</tr>
<tr>
<td>IM:</td>
<td><input type="text"  name="user_im" value="<?=$im?>"></td>
<td><input type="checkbox" name="showIM" <?=$show_im_ch?>>Показывать</td>
</tr>
<tr>
<td>Страна:</td>
<td><input type="text" name="user_country" value="<?=$country?>"></td>
<td></td>
</tr>
<tr>
<td>Город:</td>
<td><input type="text" name="user_city" value="<?=$city?>"></td>
<td></td>
</tr>
</table>
Дополнительно:<br>
<textarea name="user_additional" rows="7" cols="45"><?=$additional?></textarea>
</fieldset>
<input type="submit" value="Изменить пользовательскую информацию">
<input type="hidden" name="action" value="info">
</form>
