<form name="navForm" action="gallery.php">
<table class=nav>
<tr>
<td align=left valign=middle>
<a href="<?=$section_link?>"><?=$section_name?></a> - <b><?=$subsection_name?></b>
</td>
<td align=right valign=middle>
[<a href="<?=$add_link?>">Добавить сообщение</a>]
<select name=id onChange = 'self.location = "<?=$form_link_begin?>"+document.navForm.id[document.navForm.id.selectedIndex].value+"<?=$form_link_end?>"' title="Быстрый переход">

