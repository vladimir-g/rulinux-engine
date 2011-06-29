<br>
<fieldset>
<form action="/moder.php?action=move_thread&tid=<?=$tid?>" method="POST">
<p align="center">
Укажите ссылку на первоисточник:
<input name="link" type="text">
<input name="referer" type="hidden" value="<?=$referer?>">
<input name="section" type="hidden" value="<?=$section?>">
<input name="subsection" type="hidden" value="<?=$subsection?>">
<input name="sbm" type="submit" value="Переместить">
</p>
</form>
</fieldset>