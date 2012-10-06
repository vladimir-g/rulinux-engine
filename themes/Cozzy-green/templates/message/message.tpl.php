<div class="messages">
<div class="comment" id="cmm<?=$message_id?>">
<div class="title">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
Ответ на: <a href="<?=$message_resp_link?>"><?=$message_resp_title?> </a> от <?=$message_resp_user?>  <?=$message_resp_timestamp?>
<?php if (!empty($filter_list)):?><a href="#" data-fblock="#filters-<?=$message_id;?>" class="filter-link">Фильтры</a><?php endif;?>
<br /></div>
<div class=msg id="msg<?=$message_id?>">
<table cellspacing="0" width="100%">
<tr>
<td valign=top align=center width="160px"><img src="<?=$message_avatar?>" alt="avatar"></td><td valign="top">
<?php if (!empty($filter_list)):?>
<div id="filters-<?=$message_id;?>" class="filters">
  <ul>
    <?php foreach ($filter_list as $item):?>
    <li><?=$item['name'];?></li>
    <?php endforeach;?>
  </ul>
</div>
<?php endif;?>
<h2><?=$message_subject?></h2>
<?=$message_comment?>
<?php if ($is_filtered):?><p><strong>Причины фильтрации: <?=$active_filters;?></strong></p><?php endif;?>
<div class=sign><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>)(<?=$message_timestamp?>)<br><?=$changed?><br><?=$message_useragent?></div>
<div class=reply>[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]</div>
</td>
</tr>
</table>
</div>
</div>
</div>
