<h1>Последние сообщения за <?=$hours_count?></h1>
<div class=forum>
<table width="100%" class="message-table">
<thead>
<tr>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<th  width = "5%">Управление</th>
<?}?>
<th  width = "12%">Раздел</th>
<th>Заголовок</th>
<th width = "15%">Время постинга</th>
</tr>
</thead>
<tfoot>
<tr><td colspan='4' align='right'>всего: <?=$msg_count?> &nbsp</td></tr>
</tfoot>
<tbody>
