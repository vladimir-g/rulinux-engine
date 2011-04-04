<div class=messages>
<div class="title">
<a href="<?=$thread_this_link?>#<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<a href="<?=$thread_move_link?>"><img border="0" src="themes/<?=$theme?>/move.png" alt="[Переместить]"></a>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$thread_attach_link?>"><img border="0" src="themes/<?=$theme?>/attach.png" alt="[Прикрепить]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
</div>
<div class="msg" id=<?=$message_id?>><h2 class="nt"><?=$message_subject?></h2>
<?=$message_comment?>
<p>
<i><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>) (<?=$message_timestamp?>)<br /><?=$changed?><br /><?=$message_useragent?></i>
<br /><?=$approve?>
</p>
[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]<br>
</div>
</div><br>
