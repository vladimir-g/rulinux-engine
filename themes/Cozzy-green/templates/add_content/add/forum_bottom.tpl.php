</td>
</tr>
<tr>
<td></td>
<td><?=$captcha?></td>
</tr>
<tr>
<td></td>
<td>
<input type="hidden" name="section" value="<?=$section_id?>">
<input type="submit" name="submit_form" value="Отправить">
<input type="submit" name="submit_form" value="Предпросмотр">
</td>
</tr>
<tr>
<td></td>
<td>
<div style="display:none">Пользователям браузеров без CSS: Поле для проверки, заполнять НЕ НАДО: </div>
<input type="text" name="user_field" style="display:none" value="<?=$user_field?>"><br>
</td>
</tr>
</table>
</form>