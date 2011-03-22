<tr>
<td>Время:</td>
<td>
<select name="user-gmt">
<option value="none" selected>Выберите время</option>
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
<tr>
<td>Новостей на странице:</td>
<td><input type="text" name="news_on_page" value="<?=$news_on_page?>" maxlength="3"></td>
<td></td>
</tr>
<tr>
<td>Комментариев на странице:</td>
<td><input type="text" name="comments_on_page" value="<?=$comments_on_page?>" maxlength="4"></td>
<td></td>
</tr>
<tr>
<td>Тредов на странице:</td>
<td><input type="text" name="threads_on_page" value="<?=$threads_on_page?>" maxlength="4"></td>
<td></td>
</tr>
<tr>
<td><label for="showPhotos">Показывать фотографии:</label></td>
<td><input type="checkbox" name="show_photos" value="1" id="showPhotos" <?=$show_photos_ch?>></td>
<td></td>
</tr>
<tr>
<td><label for="showUA">Показывать сигнатуру моего браузера:</label></td>
<td><input type="checkbox" name="show_ua" value="1" id="showUA" <?=$show_ua_ch?>></td>
<td></td>
</tr>
<tr>
<td><label for="sort_to">Сортировать по дате изменения:</label></td>
<td><input type="checkbox" name="sort_to" value="1" id="sort_to" <?=$change_date_sort_ch?>></td>
<td></td>
</tr>
<tr>
<td><label for="show_resp">Показывать автора родительского сообщения в трекере:</label></td>
<td><input type="checkbox" name="show_resp" value="1" id="show_resp" <?=$show_resp_ch?>></td>
<td></td>
</tr>
</table>
</fieldset>
<input type="submit" value="Сохранить настройки">
<input type="hidden" name="action" value="read">
</form>