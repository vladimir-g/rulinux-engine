<div class="messages">
<div class="comment" id="cmm<?=$message_id?>">
<table class="title" cellspacing="0" width="100%">
<tr>
<td style="padding-bottom: 0px; padding-top: 0px;" width="72">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
</td>
<td style="padding-bottom: 0px; padding-top: 0px;">
<span class="msg_resp">Ответ на: <a href="<?=$message_resp_link?>"><?=$message_resp_title?> </a> от <?=$message_resp_user?>  <?=$message_resp_timestamp?></span>
</td>
</tr>
</table>
<div class=msg id="msg<?=$message_id?>">
<table cellspacing="0" width="100%">
<tr>
<td valign=top align=center width="160px"><img src="<?=$message_avatar?>" alt="avatar"></td><td valign=top><h2><?=$message_subject?></h2>
<?=$message_comment?>
<div class=sign><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>)(<?=$message_timestamp?>)<br><?=$changed?><br><?=$message_useragent?></div>
<div class=reply>[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]</div>
</td>
</tr>
</table>
</div>
</div>
</div>
