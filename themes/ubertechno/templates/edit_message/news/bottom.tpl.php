</select>
</td>
</tr>
<tr>
<td style="vertical-align:top;">
Источник (ссылка):
</td>
<td>
<input type=text name="link" value="<?=$link?>" style="width:100%">
</td>
</tr>
<tr>
<td style="vertical-align:top;">
Текст новости:
</td>
<td>
<textarea name="comment" cols=70 rows=20><?=$comment?></textarea>
</td>
</tr>
<tr>
<td style="vertical-align:top;">
Причина редактирования:
</td>
<td>
<input type=text name="reason" value="<?=$reason?>" style="width:100%">
</td>
</tr>
<tr>
<td></td>
<td>
<?=$captcha?>
</td>
</tr>
<tr>
<td></td>
<td>
<input name="tid" type=hidden value="<?=$tid?>">
<input name="section" type=hidden value="<?=$section?>">
<input name="msg_uid" type=hidden value="<?=$msg_uid?>">
<input name="sbm" type=submit value="Изменить">
</td>
</tr>
</table>
</form>