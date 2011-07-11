<h2><a href="message.php?newsid=<?=$thread_id?>&page=1" id="newsheader" style="text-decoration:none"><?=$subject?></a></h2>
<div>
<a href="moder.php?action=approve_thread&tid=<?=$thread_id?>">Подтвердить</a> | 
<a href="edit-message.php?id=<?=$comment_id?>">Редактировать</a>
</div>
<table cellspadding="0" cellspacing="0" border="0"><tr><td style="vertical-align:top">
<table>
<tr>
<td style="vertical-align:top"><img src="<?=$subsection_image?>"></td>
<td style="vertical-align:top"><p><p><?=$comment?></p></p>
</td>
<tr>
</table>
<p style="font-style:italic"><?=$author?> (<a href="<?=$author_profile?>">*</a>) (<?=$timestamp?>)</p>
<br>
<br>
</td>
</tr>
</table>
<hr>