</select>
</td>
<td></td>
</tr>
<tr>
<td>Заблокирован:</td>
<td>
<select name="banned">
<option value="1" <?=$banned_y?>>Да</option>
<option value="0" <?=$banned_n?>>Нет</option>
</select>
</td>
<td></td>
</tr>
</table>
</fieldset>
<input type="submit" value="Сохранить настройки">
<input type="hidden" name="action" value="admin">
</form>