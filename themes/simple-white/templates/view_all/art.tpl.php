<h2><a href="<?=$thr_link?>" id="newsheader" style="text-decoration:none"><?=$subject?></a></h2>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<div>
<a href="<?=$aprove_link?>">Подтвердить</a> |  
<a href="<?=$edit_link?>">Редактировать</a>
</div>
<?}?>
<table cellspacing="0" border="0"><tr><td style="vertical-align:top">
<table>
<tr>
<td style="vertical-align:top"><?=$comment?>
<?php if ($is_filtered):?><p><strong>Причины фильтрации: <?=$active_filters;?></strong></p><?php endif;?>
</td>
</tr>
</table>
<p style="font-style:italic"><?=$author?> (<a href="<?=$author_profile?>">*</a>) (<?=$timestamp?>)</p>
<br>
<br>
</td>
</tr>
</table>
<hr>