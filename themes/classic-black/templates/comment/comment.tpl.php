<br />
<div class="messages">
<div class="comment" id="cmm<?=$message_id?>">
<div class=title>
<br></div>
<div class=msg id=<?=$message_id?>>
<table cellspacing="0" cellspadding="0" width=100%>
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

<form action="comment.php?answerto=<?=$thread_id?>&cid=<?=$message_id?>" method="post">
<span style="font-size:10pt">Тема:</span><br>
<input type="text" name="subject" style="width:60%" value="<?=$subj?>"><br>
<span style="font-size:10pt">Ваш комментарий:</span><br>
<textarea name="comment" id="comment" style="height:40%;width:60%;" rows="15"><?=$comment?></textarea><br>
<!-- captcha section -->
<?=$captcha?>
<!-- captcha section end -->
<input type="submit" value="Поместить" name="sbm">
<input type="submit" value="Предпросмотр" name="sbm">
</form>

