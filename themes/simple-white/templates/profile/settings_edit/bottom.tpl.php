<tr>
<td>Время:</td>
<td>
<select name="user-gmt">
<option value="none" selected>Выберите время</option>
<option value="-11">GMT -11</option>
<option value="-12">GMT -12</option>
<option value="-10">GMT -10</option>
<option value="-9">GMT -09</option>
<option value="-8">GMT -08</option>
<option value="-7">GMT -07</option>
<option value="-6">GMT -06</option>
<option value="-5">GMT -05</option>
<option value="-4">GMT -04</option>
<option value="-3">GMT -03</option>
<option value="-2">GMT -02</option>
<option value="-1">GMT -01</option>
<option value="+0">GMT +00</option>
<option value="+1">GMT +01</option>
<option value="+2">GMT +02</option>
<option value="+3">GMT +03</option>
<option value="+4">GMT +04</option>
<option value="+5">GMT +05</option>
<option value="+6">GMT +06</option>
<option value="+7">GMT +07</option>
<option value="+8">GMT +08</option>
<option value="+9">GMT +09</option>
<option value="+10">GMT +10</option>
<option value="+11">GMT +11</option>
<option value="+12">GMT +12</option>
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