<form action="tracker.php">
<table class=nav><tr>
<td align=left valign=middle>
Последние сообщения за <?=$hours_count?></td>
<td align=right valign=middle>
за последние
<input name="h" onChange="submit();" value="<?=$hours?>">
часа
<input type="submit" value="показать">
</td>
</tr>
</table>
</form>
