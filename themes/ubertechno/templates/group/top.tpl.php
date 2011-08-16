<h1><?=$section_name?> <?=$subsection_name?></h1><p style="margin-top: 0px">
<em><?=$subsection_description?></em><br><br>

<script type="text/javascript">
var mins = 2;
$(function()
{		
	$("#trigger").click(function(event) 
	{
		event.preventDefault();
		$("#box").slideToggle();
	});

	$("#box a").click(function(event) 
	{
		event.preventDefault();
		$("#box").slideUp();
	});
});
</script>
<div id="jq-wrapper">
<div id="bodyContent" style="padding-bottom:10px">
<a href="#" id="trigger">Рекомендации по разделу</a>
<div id="box">
<?=$recomendations?>
</div>
</div>
</div>
		
<div class=forum>
<table width="100%" class="message-table" cellspacing="0">
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
