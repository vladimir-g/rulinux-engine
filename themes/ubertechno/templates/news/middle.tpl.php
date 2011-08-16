<h2><a href="<?=$thr_link?>"><?=$subject?></a></h2>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<div>
<a href="<?=$edit_link?>">Редактировать</a> / 
<a href="<?=$attach_link?>"><?=$attach_text?></a>
</div>
<?}?>
<table cellspacing="0" border="0"><tr><td style="vertical-align:top">
<table>
<tr>
<td style="vertical-align:top"><img src="<?=$subsection_image?>" alt="subsection"></td>
<td style="vertical-align:top"><?=$comment?>
</td>
</tr>
</table>
</td>
</tr>
</table>
<table width="100%"><tr><td style="text-align:left"><?=$prooflink?></td><td><a href="<?=$thr_link?>"><?=$comments_count?></a>&nbsp;/&nbsp;<a href="<?=$cmnt_link?>">Комментировать</a></td><td style="text-align:right"><?=$author?> (<a href="<?=$author_profile?>">*</a>) (<?=$timestamp?>)</td></tr></table>
<br>
<br>
<hr>