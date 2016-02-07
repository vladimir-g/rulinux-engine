<h1>Добавить материал</h1>
<? if ($is_preview):?>
<h2>Предпросмотр</h2>
<div class="comment">
  <div class="title"><br></div>
  <div class="msg">
    <table cellspacing="0" width="100%">
      <tr>
        <td valign="top"><h2><?=$subject;?></h2>
        <p><?=$preview_comment?></p>
        <div class="sign"><?=$author?> (<a href="<?=$author_profile?>">*</a>) (<?=$timestamp?>)
        <br><?=$useragent;?></div>
      </td>
    </tr>
  </table>
</div>
<? else:?>
<p class="error"><?=$errors['msg'];?></p>
<h2>Добавить сообщение</h2>
<br>Просьба ко всем, добавляющим темы в форум:
<ul>
  <li><b>Прочитайте <a href="/faq">FAQ</a></b>! Возможно, ваш вопрос уже содержится в нашем сборнике ответов на часто задаваемые вопросы.
  <li><b>Пишите в правильный форум!</b> Выберете подходящий по теме вашего вопроса раздел форума, например
  вопросы по администрированию системы нужно задавать в Admin, а
  не в General и т.п.
  <li><b>Пишите осмысленный заголовок</b>. Придумайте осмысленный заголовок теме. Сообщения с бессмысленными загловками ("Помогите!", "Вопрос", ...), как правило, остаются без ответа.
</ul>
<? endif;?>

<form action="<?=$form_link?>" method="post">
<? if (!empty($errors['msg'])):?><p class="error">Форма содержит ошибки</p><? endif;?>
<table border="0">
<tr>
<td style="vertical-align:top;">Заголовок:</td>
<td>
<input type="text" name="subject" value="<?=$subject;?>" style="width:100%">
<p class="error"><?=$errors['subject'];?></p>
</td>
</tr>