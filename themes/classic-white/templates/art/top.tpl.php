<h1><?=$section_name?> <?=$subsection_name?></h1><p style="margin-top: 0px">
<em><?=$subsection_description?></em><br><br>
<div class=forum>
<table width="100%" class="message-table">
<thead>
<tr>
<?if($uinfo['gid']==2 || $uinfo['gid']==3){?>
<th>Управление</th>
<?}?>
<th width = "70%">Заголовок</th>
<th>Число ответов
<br>всего/день/час</th>
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
