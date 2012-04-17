<p class="error"><?=$errors['msg'];?></p>
<br />
<div class="messages">
<div class=title>
<br></div>
<div class=msg id=<?=$question_id?>>
<table cellspacing="0" cellspadding="0" width=100%>
<tr>
<td valign=top><h2><?=$question_subject?></h2>
<?=$question_comment?>
</td>
</tr>
</table>
</div>
</div>
<form action="<?=$add_answer?>" method="post">
<table border="0">
<thead></thead>
<tfoot></tfoot>
<tbody>
<tr>
<td style="vertical-align:top;">
Ваш ответ:
</td>
<td>
<textarea name="answer" id="comment" rows="20"  cols="80"><?=$answer;?></textarea>
<p class="error"><?=$errors['answer'];?></p>
</td>
</tr>
<tr>
<td></td>
<td>
<input type="submit" value="Поместить" name="sbm">
</td>
</tr>
</tbody>
</table>
</form>