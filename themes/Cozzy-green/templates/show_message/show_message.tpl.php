<div class="messages">
<div class="comment" id="cmm<?=$message_id?>">
<div class=title>
<a href="<?=$thread_this_link?>#<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
<?=$reply?><br></div>
<div class=msg id=<?=$message_id?>>
<table cellspacing="0" cellspadding="0" width="100%">
<tr>
<td valign=top align=center width="160px">
  <? if ($is_gallery):?>
  <a href="<?=$img_link?>"><img src="<?=$img_thumb_link?>" alt="gallery"></a>
  <? elseif (!$is_news):?>
  <img src="<?=$message_avatar?>">
  <? endif;?>
</td>
<td valign=top><h2><?=$message_subject?></h2>
<?=$message_comment?>

<? if ($is_gallery):?>
<i><?=$size?></i><br><br>
>>> <a href="<?=$img_link?>">Просмотр</a>
<? elseif ($is_news):?>
<?=$prooflink;?>
<? endif;?>

<div class=sign><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>)(<?=$message_timestamp?>)<br><?=$changed?><br><?=$message_useragent?></div>
<div class=reply>[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]</div>
</td>
</tr>
</table>
</div>
</div>
</div>
