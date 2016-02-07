<div class="messages" data-user="<?=$message_autor;?>">
<div class="title">
<a href="<?=$thread_this_link?>#msg<?=$message_id?>"><img border="0" src="themes/<?=$theme?>/id.png" alt="[#]"></a>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<a href="<?=$thread_move_link?>"><img border="0" src="themes/<?=$theme?>/move.png" alt="[Переместить]"></a>
<a href="<?=$thread_attach_link?>"><img border="0" src="themes/<?=$theme?>/attach.png" alt="[Прикрепить]"></a>
<?}?>
<a href="<?=$message_set_filter_link?>"><img border="0" src="themes/<?=$theme?>/filter.png" alt="[Добавить метку]"></a>
<a href="<?=$message_edit_link?>"><img border="0" src="themes/<?=$theme?>/edit.png" alt="[Редактировать]"></a>
<?php if (!empty($filter_list)):?><a href="#" data-fblock="#filters-<?=$message_id;?>" class="filter-link">Фильтры</a><?php endif;?>
</div>
<div class="msg" id="msg<?=$message_id?>">
<?php if (!empty($filter_list)):?>
<div id="filters-<?=$message_id;?>" class="filters">
  <ul>
    <?php foreach ($filter_list as $item):?>
    <li><?=$item['name'];?></li>
    <?php endforeach;?>
  </ul>
</div>
<?php endif;?>
<?php if ($is_filtered):?>
<h2 class="nt"><?=FILTERED_HEADING;?></h2>
<?=FILTERED_TEXT;?> <a class="toggle-hidden" data-hidden="#msg-content-<?=$message_id;?>" href="message_<?=$message_id;?>">сюда</a>.
<p><strong>Причины фильтрации: <?=$active_filters;?></strong></p>
<?php endif;?>
<div id="msg-content-<?=$message_id;?>" class="msg-content<?php if ($is_filtered):?> msg-hidden<?php endif;?>">
<a class="toggle-hidden" data-hidden="#msg-content-<?=$message_id;?>" href="#">Скрыть</a>
<h2 class="nt"><?=$message_subject?></h2>
<?=$message_comment?>
<?=$prooflink?>
</div>
<p>
<i><?=$message_autor?>(<a href="<?=$message_autor_profile_link?>">*</a>) (<?=$message_timestamp?>)<br><?=$changed?><br><?=$message_useragent?></i>
<br><?=$approve?>
</p>
[<a href="<?=$message_add_answer_link?>">Ответить на это сообщение</a>]<br>
</div>
</div><br>
