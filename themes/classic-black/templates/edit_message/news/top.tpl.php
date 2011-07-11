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
Категория:
</td>
<td>
<select name="subsection_id" style="width:100%">
