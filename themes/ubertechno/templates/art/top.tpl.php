<h1><?=$section_name?> <?=$subsection_name?></h1><p style="margin-top: 0px">
<em><?=$subsection_description?></em><br><br>
<div class=forum>
<table width="100%" class="message-table">
<thead>
<tr class="msg_table_title_tr">
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<th style="padding: 0px;"><span class="msg_table_title">Управление</span></th>
<?}?>
<th style="padding: 0px;" width = "70%"><span class="msg_table_title">Заголовок</span></th>
<th style="padding: 0px;"><span class="msg_table_title">Число ответов</span></th>
</tr>
</thead>

<tfoot>
<tr>
<td colspan=2>
<p>
<div style="float: left"></div>
<div style="float: right"></div>
</td>
</tr>
</tfoot>

<tbody>
