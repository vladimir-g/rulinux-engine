<tr>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<td align="center">
<a href="<?=$filter_link?>"><img border=0 src="themes/<?=$theme?>/filter.png" alt="Удалить"></a>
</td>
<?}?>
<td>
&nbsp<a href="<?=$section_link?>"><?=$section?></a>/<a href="<?=$subsection_link?>"><?=$subsection?></a>&nbsp
</td>
<td>
<a href="<?=$link?>"><?=$subject?></a>(<?=$author?><?=$resp?>)
</td>
<td align='center'><?=$timestamp?></td>
</tr>
