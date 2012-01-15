<h1>Профиль пользователя</h1>
<img src="<?=$photo?>" alt="avatar">
<br>
<table>
<tr>
<td>Nick: </td>
<td><?=$user?></td>
</tr>
<tr>
<td>Имя: </td>
<td><?=$name?></td>
</tr>
<tr>
<td>Фамилия: </td>
<td><?=$lastname?></td>
</tr>
<tr>
<td>Пол: </td>
<td><?=$gender?></td>
</tr>
<tr>
<td>День рождения: </td>
<td><?=$birthday?></td>
</tr>
<tr>
<td>E-mail: </td>
<td><?=$email?></td>
</tr>
<tr>
<td>IM: </td>
<td><?=$im?></td>
</tr>
<tr>
<td>Страна: </td>
<td><?=$country?></td>
</tr>
<tr>
<td>Город: </td>
<td><?=$city?></td>
</tr>
<tr>
<td>Статус: </td>
<td><?=$status?></td>
</tr>
<tr>
<td>Зарегистрирован: </td>
<td><?=$register_date?></td>
</tr>
<tr>
<td>Последний логин: </td>
<td><?=$last_login?></td>
</tr>
</table>
<br>
Дополнительно: 
<br><?=$additional?>
<br>
<table>
<tr>
<td><hr></td>
<td></td>
</tr>
<tr>
<td>Первая созданная тема:</td>
<td><?=$first_topic_date?></td>
</tr>
<tr>
<td>Последняя созданная тема:</td>
<td><?=$last_topic_date?></td>
</tr>
<tr>
<td>Первый комментарий:</td>
<td><?=$first_comment_date?></td>
</tr>
<tr>
<td>Последний комментарий:</td>
<td><?=$last_comment_date?></td>
</tr>
<tr>
<td>Всего <a href="<?=$link?>">комментариев</a>:</td>
<td><?=$comments_count?></td>
</tr>
<tr>
<td>Всего тем:</td>
<td><?=$topics_count?></td>
</tr>
</table>