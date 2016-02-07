<tr>
<td style="text-align:center"><?=$block_name?></td>
<td style="text-align:center">
<input type="hidden" name="<?=$block_id?>_name" value="<?=$block_name?>">
<select name="<?=$block_id?>_position">
<option value="l" <?=$sel_l?>>Слева</option>
<option value="r"<?=$sel_r?>>Справа</option>
<option value="n" <?=$sel_n?>>Не используется</option>
</select>
</td>
<td style="text-align:center">
<input type="text" name="<?=$block_id?>_sort" value="<?=$sort_val?>">
</td>
</tr>
