<h2><a href="<?=$thr_link?>" style="text-decoration:none"><?=$subject?></a></h2>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<div>
<a href="<?=$edit_link?>">Редактировать</a> | 
<a href="<?=$attach_link?>"><?=$attach_text?></a>
</div>
<?}?>
<table cellspacing="0" border="0" width="100%"><tr><td style="vertical-align:top">
<table>
<tr>
  <td style="vertical-align:top">
    <? if (!$filtered):?>
    <a href="<?=$img_link?>"><img src="<?=$img_thumb_link?>" alt="gallery"></a>
    <? endif;?>
  </td>
<td style="vertical-align:top"><?=$comment?><br>
<i><?=$size?></i><br><br>
<? if (!$filtered):?>
>>> <a href="<?=$img_link?>">Просмотр</a>
<? endif;?>
</td>
</tr>
</table>
<table width="100%"><tr><td style="text-align:left"><a href="<?=$thr_link?>"><?=$comments_count?></a>&nbsp;/&nbsp;<a href="<?=$cmnt_link?>">Комментировать</a></td><td style="text-align:right"><?=$author?> (<a href="<?=$author_profile?>">*</a>) (<?=$timestamp?>)</td></tr></table>
<br>
<br>
</td>
</tr>
</table>
<hr>