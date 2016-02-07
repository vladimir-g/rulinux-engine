<br />
<p class="error"><?=$errors['msg'];?></p>
<div class="messages">
<div class="comment" id="cmm<?=$message_id?>">
<div class=title>
<br></div>
<div class=msg id=msg<?=$message_id?>>
<table cellspacing="0" width="100%">
<tr>
<td valign=top><h2><?=$message_subject?></h2>
<?=$message_comment?>
<div class=sign><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>)(<?=$message_timestamp?>)<br><br><?=$message_useragent?></div>
</td>
</tr>
</table>
</div>
</div>
</div>

<form action="<?=$form_link?>" method="post">
<? if (!empty($errors)):?><p class="error">Форма содержит ошибки</p><? endif;?>
<table border="0">
<tr>
<td style="vertical-align:top;">Тема:</td>
<td>
<input type="text" name="subject" style="width:100%" value="<?=$subj?>">
<p class="error"><?=$errors['subject'];?></p>
</td>
</tr>
<tr>
<td style="vertical-align:top;">
Ваш комментарий:
</td>
<td>
<textarea name="comment" id="comment" rows="20"  cols="80"><?=$comment?></textarea>
<p class="error"><?=$errors['comment'];?></p>
</td>
</tr>
<tr>
<td style="vertical-align:top;">Выберите фильтр: </td>
<td>


