<h1>Добавить материал</h1>
<? if ($is_preview):?>
<h2>Предпросмотр</h2>
<p>При предпросмотре изображение не отображается</p>
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
<h2>Добавить скриншот</h2>
Требования: <ul>
<li>Ширина x Высота: от 400x400 до 2048x2048 пикселей
<li>Тип: jpeg, gif, png
<li>Размер не более 700 Kb
</ul>
<p>При предпросмотре изображение не отображается</p>
<? endif;?>
<form action="<?=$form_link?>" method="post" enctype="multipart/form-data">
<? if (!empty($errors['msg'])):?><p class="error">Форма содержит ошибки</p><? endif;?>
<table border="0">
<tr>
<td style="vertical-align:top;">Заголовок:</td>
<td>
<input type="text" name="subject" value="<?=$subject;?>" style="width:100%">
<p class="error"><?=$errors['subject'];?></p>
</td>
</tr>