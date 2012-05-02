<!--{top}-->
<h1>Список зарегистрированных пользователей</h1>
<br>
<table width="100%" class="message-table">
<thead>
<tr class="msg_table_title_tr">
<th style="padding: 0px;"><span class="msg_table_title">Список пользователей</span></th>
</tr>
</thead>
<tfoot>
<tr><td></td></tr>
</tfoot>
<tbody>

<!--{middle}-->
<tr>
<td>
<img src="<?=$variables->avatar?>"  height="100" align="right" border = "2" vspace="3" hspace="3" alt="user avatar"><b>Ник:</b> <?=$variables->nick?><br>
<b>Группа:</b> <?=$variables->group_name?><br>
<b>Имя:</b> <?=$variables->name?><br>
<b>Город:</b> <?=$variables->city?><br>
<b>Страна:</b> <?=$variables->country?><br>
<b>e-mail:</b> <?=$variables->email?><br>
<b>IM:</b> <?=$variables->im?><br>
<td>
</tr>

<!--{bottom}-->
</tbody>
</table>
<div align=center><p>
<?=$variables->pages?>
<p>
</div>