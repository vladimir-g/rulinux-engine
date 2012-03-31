<tr>
<td style="vertical-align:top;">Адрес файла:</td>
<td>
<input type="file" name="scrot_link" value="" style="width:100%">
<? foreach (array('image_ext', 'image_type', 'image_size', 'image_res', 'image_empty') as $code):?>
<p class="error"><?=$errors[$code];?></p>
<? endforeach;?>
</td>
</tr>
<tr>
<td style="vertical-align:top;">Описание:</td>
<td>
<textarea name="comment" id="editor" rows="20" cols="80"><?=$comment;?></textarea>
<p class="error"><?=$errors['comment'];?></p>
</td>
</tr>
<tr>
<td style="vertical-align:top;">Выберите фильтр:</td>
<td>