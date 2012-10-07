<h2 data-user="<?=$autor;?>">
<a href="<?=$thr_link?>" id="newsheader" style="text-decoration:none">
<?php if ($is_filtered):?>
<?=FILTERED_HEADING;?>
<?php else:?>
<?=$subject?>
<?php endif;?>
</a>
</h2>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<div>
<a href="<?=$aprove_link?>">Подтвердить</a> |  
<a href="<?=$edit_link?>">Редактировать</a>
</div>
<?}?>
<table cellspacing="0" border="0"><tr><td style="vertical-align:top">
<table>
<tr>
<td style="vertical-align:top">

<?php if ($is_filtered):?>
<?=FILTERED_TEXT;?> <a class="toggle-hidden" data-hidden="#msg-content-<?=$comment_id;?>" href="message_<?=$comment_id;?>">сюда</a>.
<p><strong>Причины фильтрации: <?=$active_filters;?></strong></p>
<?php endif;?>

<div id="msg-content-<?=$comment_id;?>" class="msg-content<?php if ($is_filtered):?> msg-hidden<?php endif;?>">
<a class="toggle-hidden" data-hidden="#msg-content-<?=$comment_id;?>" href="#">Скрыть</a>
<?php if ($is_filtered):?><h2><a href="<?=$thr_link?>" id="newsheader" style="text-decoration:none"><?=$subject;?></a></h2><?php endif;?>
<?=$comment?>
</div>

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
