</td>
</tr>
<tr>
<td></td>
<td><?=$captcha?></td>
</tr>
</table>
<input type="hidden" name="section" value="<?=$section_id?>">
<input type="submit" name="submit_form" value="Отправить">
&nbsp;
<input type="submit" name="submit_form" value="Предпросмотр">
<div style="display:none">Пользователям браузеров без CSS: Поле для проверки, заполнять НЕ НАДО: </div>
<input type="text" name="user_field" style="display:none" width="0" value="<?=$user_field?>"><br>
</form>