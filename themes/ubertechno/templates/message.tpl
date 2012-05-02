<!--{article}-->
<div class=messages>
<div class="title">
<span class="msg_resp">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<a href="<?=$thread_move_link?>"><img border="0" src="themes/<?=$theme?>/move.png" alt="[Переместить]"></a>
<a href="<?=$thread_attach_link?>"><img border="0" src="themes/<?=$theme?>/attach.png" alt="[Прикрепить]"></a>
<?}?>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
<?=$message_subject?>
</span>
</div>
<div class="msg" id="msg<?=$message_id?>">
<?=$message_comment?>
<p>
<i><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>) (<?=$message_timestamp?>)<br><?=$changed?><br><?=$message_useragent?></i>
<br><?=$approve?>
</p>
[<a href="<?=$message_add_answer_link?>">Оставить комментарий к статье</a>]<br>
</div>
</div>

<!--{forum}-->
<div class=messages>
<div class="title">
<span class="msg_resp">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<a href="<?=$thread_move_link?>"><img border="0" src="themes/<?=$theme?>/move.png" alt="[Переместить]"></a>
<a href="<?=$thread_attach_link?>"><img border="0" src="themes/<?=$theme?>/attach.png" alt="[Прикрепить]"></a>
<?}?>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
</span>
</div>
<div class="msg" id="msg<?=$message_id?>"><h2 class="nt"><?=$message_subject?></h2>
<?=$message_comment?>
<p>
<i><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>) (<?=$message_timestamp?>)</i>
<br><?=$changed?><br><i><?=$message_useragent?></i>
</p>
[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]<br>
</div>
</div>

<!--{gallery}-->
<div class=messages>
<div class="title">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<a href="<?=$thread_move_link?>"><img border="0" src="themes/<?=$theme?>/move.png" alt="[Переместить]"></a>
<a href="<?=$thread_attach_link?>"><img border="0" src="themes/<?=$theme?>/attach.png" alt="[Прикрепить]"></a>
<?}?>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
</div>
<div class="msg" id="msg<?=$message_id?>"><h2 class="nt"><?=$message_subject?></h2>
<table>
<tr>
<td style="vertical-align:top">
<? if (!$is_filtered):?>
  <a href="images/gallery/<?=$gallery_file_name?>.<?=$gallery_file_extension?>"><img src="images/gallery/thumbs/<?=$gallery_file_name?>_small.png">
<? endif;?>
</td>
<td style="vertical-align:top"><?=$message_comment?>

<? if (!$is_filtered):?>
<br><span style="font-style: italic"><?=$gallery_image_size?>, <?=$gallery_file_size?></span><br><br>
>>> <a href="images/gallery/<?=$gallery_file_name?>.<?=$gallery_file_extension?>">Просмотр</a>
<? endif;?>

</td>
<tr>
</table>
<p>
<i><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>) (<?=$message_timestamp?>)<br><?=$changed?><br><?=$message_useragent?></i>
<br><?=$approve?>
</p>
[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]<br>
</div>
</div>

<!--{message}-->
<div class="messages">
<div class="comment" id="cmm<?=$message_id?>">
<div class="title">
<span class="msg_resp">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
Ответ на: <a href="<?=$message_resp_link?>"><?=$message_resp_title?> </a> от <?=$message_resp_user?>  <?=$message_resp_timestamp?></span>
</div>
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

<!--{nav}-->
<table class=nav>
<tr>
<td align=left valign=middle width="35%">
<table>
<tr valign=middle>
<td align=left valign=top>
<a href="<?=$thread_previous_link?>" rel=prev rev=next><?=$thread_previous_subject?></a>
</td>
</tr>
</table>
</td>
<td>
<table width="100%">
<tr valign=middle align=center>
<td>
<?=$pages?>
</td>
</tr>
</table>
</td>
<td align=left valign=middle width="35%">
<table width="100%">
<tr valign=middle align=right>
<td>
<a href="<?=$thread_next_link?>" rel=prev rev=next><?=$thread_next_subject?></a>
</td>
</tr>
</table>
</td>
</tr>
</table>

<!--{nav_form}-->
<table class="nav">
<tr>
<td align=left valign=middle><a href="<?=$variables->section_link?>"><?=$variables->section_name?></a> - <b><a href="<?=$variables->subsection_link?>"><?=$variables->subsection_name?></a></b></td>
<td align=right>
[<a href="<?=$variables->rss_link?>">RSS</a>]
</td>
</table>

<!--{news}-->
<div class=messages>
<div class="title">
<span class="msg_resp">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<a href="<?=$thread_move_link?>"><img border="0" src="themes/<?=$theme?>/move.png" alt="[Переместить]"></a>
<a href="<?=$thread_attach_link?>"><img border="0" src="themes/<?=$theme?>/attach.png" alt="[Прикрепить]"></a>
<?}?>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
</span>
</div>
<div class="msg" id="msg<?=$message_id?>"><h2 class="nt"><?=$message_subject?></h2>
<?=$message_comment?>
<?=$prooflink?>
<p>
<i><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>) (<?=$message_timestamp?>)<br><?=$changed?><br><?=$message_useragent?></i>
<br><?=$approve?>
</p>
[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]<br>
</div>
</div><br>

<!--{thread_readers}-->
<table class=readers>
<thead>
<tr><td>Этот тред читают <?=$readers_count?>:</td></tr>
</thead>
<tbody>
<tr>
<td>
<?=$readers?>
</td>
</tr>
</tbody>
</table>
<br>