<h1>Добавить материал</h1>
<h2>Добавить сообщение</h2>
<h2><?=$subject?></h2>
<form action="add-content.php?section=<?=$section_id?>" method="post">
<table cellspadding="0" cellspacing="0" border="0"><tr><td style="vertical-align:top">
<table>
<tr>
<td style="vertical-align:top"><p><p><?=$comment?></p></p>
</td>
<tr>
</table>
<p style="font-style:italic"><?=$author?> (<a href="<?=$author_profile?>">*</a>) (<?=$timestamp?>)</p>
<br>
<br>
</td>
</tr>
<tr>
<td style="vertical-align:top;">Заголовок:</td>
<td>
<input type="text" name="subject" value="<?=$subject?>" style="width:100%">
</td>
</tr>
<tr>
