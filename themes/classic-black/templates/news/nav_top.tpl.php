<form action="news.php">
<table class=nav>
<tr>
<td align=left valign=middle>
<a href="/view-section.php?id=<?=$section_id?>"><?=$section_name?></a> - <b><?=$subsection_name?></b>
</td>
<td align=right valign=middle>
[<a href="/add-message.php?fid=<?=$section_id?>">Добавить сообщение</a>]
<select name=id onChange="submit()" title="Быстрый переход">

