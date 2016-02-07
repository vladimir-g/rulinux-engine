<h1>Редактировать сообщение</h1>
<form method=POST action="edit-message.php?id=<?=$message_id?>">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="cid" value="<?=$message_id?>">
<table border=0>
<tr>
<td style="vertical-align:top;">
Заглавие:
</td>
<td>
<input type=text name="subject" value="<?=$subj?>" style="width:100%">
</td>
</tr>
<tr>
<td style="vertical-align:top;">
Текст сообщения:
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
<input name="sbm" type=submit value="Изменить">
<input name="msg_uid" type=hidden value="<?=$msg_uid?>">
</td>
</tr>
</table>
</form>
