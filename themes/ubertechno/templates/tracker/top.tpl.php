<h1>Последние сообщения за <?=$hours_count?></h1>
<div class=forum>
<table width="100%" class="message-table">
<thead>
<tr class="tracker_title_tr">
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<th style="padding: 0px;" width = "5%"><span class="tracker_title">Управление</span></th>
<?}?>
<th style="padding: 0px;" width = "12%"><span class="tracker_title">Раздел</span></th>
<th style="padding: 0px;"><span class="tracker_title">Заголовок</span></th>
<th style="padding: 0px;" width = "15%"><span class="tracker_title">Время постинга</span></th>
</tr>
</thead>
<tfoot>
<tr><td colspan='4' align='right'>всего: <?=$msg_count?> &nbsp</td></tr>
</tfoot>
<tbody>
