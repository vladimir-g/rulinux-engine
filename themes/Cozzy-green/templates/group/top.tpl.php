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
	timer();		
});
</script>

<style>
#trigger {display:block;padding-top:1px;}
#box {display:none}
#box {color:black;border:1px solid #999;padding:5px}
</style>
<div id="jq-wrapper">
<div id="bodyContent" style="padding-bottom:10px">
<a href="#" id="trigger">Рекомендации по разделу</a>
<div id="box">
<?=$recomendations?>
</div>
</div>
</div>
		
<div class=forum>
<table width="100%" class="message-table">
<thead>
<tr>
<th>Управление</th>
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
