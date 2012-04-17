<p class="error"><?=$errors['msg'];?></p>
<h1>Добавить материал</h1>
<h2>Добавить вопрос в F.A.Q.</h2>
<form action="<?=$add_question?>" method="post">
<? if (!empty($errors['msg'])):?><p class="error">Форма содержит ошибки</p><? endif;?>
<table border="0">
<thead></thead>
<tfoot></tfoot>
<tbody>
<tr>
<td style="vertical-align:top;">Заголовок:</td>
<td>
<input type="text" name="subject" value="<?=$subject;?>" style="width:100%">
<p class="error"><?=$errors['subject'];?></p>
</td>
</tr>
<tr>
<td style="vertical-align:top;">Текст сообщения:</td>
<td>
<textarea name="comment" id="editor" rows="20" cols="80"><?=$comment;?></textarea>
<p class="error"><?=$errors['comment'];?></p>
</td>
</tr>
<tr>
<td colspan="2">
<?=$captcha?>
<p class="error"><?=$errors['captcha'];?></p>
</td>
</tr>
</tbody>
</table>
<input type="submit" name="submit_form" value="Отправить">
&nbsp;
<div style="display:none">Пользователям браузеров без CSS: Поле для проверки, заполнять НЕ НАДО: </div>
<input type="text" name="user_field" style="display:none" width="0"><br>
<p class="error"><?=$errors['user_field'];?></p>
</form>