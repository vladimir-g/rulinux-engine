<h1>Добавить материал</h1>
<? if ($is_preview):?>
<h2>Предпросмотр</h2>
<h2><?=$subject?></h2>
<table cellspadding="0" cellspacing="0" border="0"><tr><td style="vertical-align:top">
<table>
<tr>
<td style="vertical-align:top"><p><p><?=$preview_comment?></p></p>
</td>
<tr>
</table>
<p style="font-style:italic"><?=$author?> (<a href="<?=$author_profile?>">*</a>) (<?=$timestamp?>)</p>
<br><?=$useragent;?></p>
<? else:?>
<p class="error"><?=$errors['msg'];?></p>
<h2>Добавить статью</h2>
<? endif;?>
<form action="<?=$form_link?>" method="post">
<? if (!empty($errors['msg'])):?><p class="error">Форма содержит ошибки</p><? endif;?>
<table border="0">
<tr>
<td style="vertical-align:top;">Заголовок:</td>
<td>
<input type="text" name="subject" value="<?=$subject;?>" style="width:100%">
<p class="error"><?=$errors['subject'];?></p>
</td>
</tr>


