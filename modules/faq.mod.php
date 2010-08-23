<? if ($inside): ?>
<?
$action = $_GET['action'];

if ($action == ''){
   $action = 'showall';
   echo 'Текущее действие: <strong>Редактирование сообщений</strong>';
}
if (isset($_POST['changed'])){
   $res =
   base::update_field('faq_config', 'value', (int)$_POST['captcha'], '`name` = \'captcha\'') &
   base::update_field('faq_config', 'value', (int)$_POST['email'], '`name` = \'email\'') &
	base::update_field('faq_config', 'value', (int)$_POST['small'], '`name` = \'small\'') &
   base::update_field('faq_config', 'value', (int)$_POST['premod'], '`name` = \'premod\'');
}
switch ($action){
   case 'showall':
      if(isset($_POST['answer'])){
         $res =
         base::erewrite('faq', 'answer', $_POST['answer'], $_POST['qid']) &
         base::erewrite('faq', 'answered', $_SESSION['user_login'], $_POST['qid']);
      }
      if(isset($_POST['answ'])){
         $res =
         base::erewrite('faq', 'answer', $_POST['answ'], $_POST['qid']) &
         base::erewrite('faq', 'answered', $_SESSION['user_login'], $_POST['qid']) &
         base::erewrite('faq', 'name', $_POST['author'], $_POST['qid']) &
         base::erewrite('faq', 'email', $_POST['email'], $_POST['qid']) &
         base::erewrite('faq', 'question', $_POST['question'], $_POST['qid']) &
         base::erewrite('faq', 'date', $_POST['date'], $_POST['qid']) &
         base::erewrite('faq', 'available', (int)$_POST['av'], $_POST['qid']);
      }
?>
<script>
function requestQuestionData(qid, action){
   switch (action){
	case 'answer':
      q = "ajax/faq.ajx.php?r=data&data=author&qid="+qid;
      sendRequest(q, updateAuthor, null, false);
      q = "ajax/faq.ajx.php?r=data&data=question&qid="+qid;
      sendRequest(q, updateQuestion, null, false);
   break;
   case 'edit':
      q = "ajax/faq.ajx.php?r=data&data=name&qid="+qid;
      sendRequest(q, setAuthor, null, false);
      q = "ajax/faq.ajx.php?r=data&data=email&qid="+qid;
      sendRequest(q, setEmail, null, false);
      q = "ajax/faq.ajx.php?r=data&data=question&qid="+qid;
      sendRequest(q, setQuestion, null, false);
      q = "ajax/faq.ajx.php?r=data&data=date&qid="+qid;
      sendRequest(q, setDate, null, false);
      q = "ajax/faq.ajx.php?r=data&data=answer&qid="+qid;
      sendRequest(q, setAnswer, null, false);
      q = "ajax/faq.ajx.php?r=data&data=av&qid="+qid;
      sendRequest(q, setAv, null, false);
   break;
   }
}                    
function updateAuthor(q){
   document.getElementById('author').innerHTML = q;
}
function updateQuestion(q){
   document.getElementById('question').innerHTML = q;
}
function setAuthor(q){
   document.getElementById('author').value = q;
}
function setEmail(q){
   document.getElementById('email').value = q;
}
function setQuestion(q){
   document.getElementById('question').innerHTML = q;
}
function setDate(q){
   document.getElementById('date').value = q;
}
function setAnswer(q){
   document.getElementById('answer').innerHTML = q;
}
function setAv(q){
   if (q > 0)
      document.getElementById('av').innerHTML = '<input type="checkbox" name="av" value="1" checked>';
   else
      document.getElementById('av').innerHTML = '<input type="checkbox" name="av" value="1">';
}
function answerTo(id){
   var winContent = '';
   var hwnd = addWin('center', 'center', '600', '320', 'Ответ на вопрос #' + id, winContent, '#ffffff', '<?=$GLOBALS['icon']?>', true);
   winContent += '<form action="admin.php?mod=faq&action=showall" method="POST"><input type="hidden" name="qid" value="'+id+'"><table width="100%" border="0" cellspadding="1">';
   winContent += '<tbody>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>Автор:</strong>';
   winContent += '</td>';
   winContent += '<td id="author">';
   winContent += '${author}';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>Вопрос:</strong>';
   winContent += '</td>';
   winContent += '<td id="question" style="text-align:justify;vertical-align:top">';
   winContent += '${question}';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>Ответ:</strong>';
   winContent += '</td>';
   winContent += '<td>';
   winContent += '<textarea style="height:200px; width:100%" name="answer"></textarea><br><br>';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '</tbody>';
   winContent += '</table><input type="submit" value="Ответить">&nbsp;<input type="button" value="Закрыть" onclick="destroyWin('+hwnd+')"></form>';
   sendMessage(hwnd, 'content', winContent);
   requestQuestionData(id, 'answer');
}
function editQuestion(id){
   var winContent = '';
   var hwnd = addWin('center', 50, 640, 495, 'Изменение вопроса #' + id, winContent, '#ffffff', '<?=$GLOBALS['icon']?>', true);
   winContent += '<form action="admin.php?mod=faq&action=showall" method="POST"><input type="hidden" name="qid" value="'+id+'"><table width="100%" border="0" cellspadding="1">';
   winContent += '<tbody>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>Автор:</strong>';
   winContent += '</td>';
   winContent += '<td>';
   winContent += '<input type="text" name="author" id="author" value="${author}" style="width:100%">';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>E-mail:</strong>';
   winContent += '</td>';
   winContent += '<td>';
   winContent += '<input type="text" name="email" id="email" value="${email}" style="width:100%">';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>Дата:</strong>';
   winContent += '</td>';
   winContent += '<td>';
   winContent += '<input type="text" name="date" id="date" value="${date}" style="width:100%">';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>Вопрос:</strong>';
   winContent += '</td>';
   winContent += '<td style="text-align:justify;vertical-align:top">';
   winContent += '<textarea style="height:150px; width:100%" id="question" name="question">${question}</textarea>';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top">';
   winContent += '<strong>Ответ:</strong>';
   winContent += '</td>';
   winContent += '<td>';
   winContent += '<textarea style="height:150px; width:100%" name="answ" id="answer">${answer}</textarea><br><br>';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '<tr>';
   winContent += '<td style="vertical-align:top; text-align:right" id="av">';
   winContent += '<input type="checkbox" name="av"></td>';
   winContent += '<td>';
   winContent += '<label for="av"><strong>Вопрос доступен для чтения пользователям</strong></label>';
   winContent += '</td>';
   winContent += '</tr>';
   winContent += '</tbody>';
   winContent += '</table><br><input type="submit" value="Сохранить">&nbsp;<input type="button" value="Закрыть" onclick="destroyWin('+hwnd+')"></form>';
   sendMessage(hwnd, 'content', winContent);
   requestQuestionData(id, 'edit');
}
function delSucceed(result){
   if (result > 0){
      alert('Сообщение успешно удалено. Сейчас будет обновлена страница');
      window.location.reload(true);
   }
}
function deleteQuestion(qid){
   if(confirm("Пожалуйста, подтвердите удаление этого сообщения")){
      q = "ajax/faq.ajx.php?r=del&qid="+qid;
      sendRequest(q, delSucceed, null, false);
   }
   else
      alert('Удаление сообщения было отменено');
}
</script>
<?
      echo '<table width="100%" border="0">';
      echo '<tbody>';
      echo '<tr>';
      echo '<th>';
      if($_GET['filter'] == 'name'){$clr = '#0000ff'; if(!isset($_GET['desc'])) $desc='&desc';} else {$clr = '#000000'; $desc='';}
      echo '<a href="admin.php?mod=faq&action=showall&filter=name'.$desc.'" style="color:'.$clr.'; border-bottom:dashed 1px '.$clr.'">Имя</a>';
      echo '</th>';
      echo '<th>';
      if($_GET['filter'] == 'email'){$clr = '#0000ff'; if(!isset($_GET['desc'])) $desc='&desc';} else {$clr = '#000000'; $desc='';}
      echo '<a href="admin.php?mod=faq&action=showall&filter=email'.$desc.'" style="color:'.$clr.'; border-bottom:dashed 1px '.$clr.'">E-mail</a>';
      echo '</th>';                                               
      echo '<th>';
      if($_GET['filter'] == 'date' ^ !isset($_GET['filter'])){$clr = '#0000ff'; if(!isset($_GET['desc'])) $desc='&desc';} else {$clr = '#000000'; $desc='';}
      echo '<a href="admin.php?mod=faq&action=showall&filter=date'.$desc.'" style="color:'.$clr.'; border-bottom:dashed 1px '.$clr.'">Дата</a>';
      echo '</th>';                                             
      echo '<th>';
      if($_GET['filter'] == 'question'){$clr = '#0000ff'; if(!isset($_GET['desc'])) $desc='&desc';} else {$clr = '#000000'; $desc='';}
      echo '<a href="admin.php?mod=faq&action=showall&filter=question'.$desc.'" style="color:'.$clr.'; border-bottom:dashed 1px '.$clr.'">Вопрос</a>';
      echo '</th>';                                               
      echo '<th>';
      if($_GET['filter'] == 'answer'){$clr = '#0000ff'; if(!isset($_GET['desc'])) $desc='&desc';} else {$clr = '#000000'; $desc='';}
      echo '<a href="admin.php?mod=faq&action=showall&filter=answer'.$desc.'" style="color:'.$clr.'; border-bottom:dashed 1px '.$clr.'">Ответ</a>';
      echo '</th>';                                              
      echo '<th>';
      if($_GET['filter'] == 'answered'){$clr = '#0000ff'; if(!isset($_GET['desc'])) $desc='&desc';} else {$clr = '#000000'; $desc='';}
      echo '<a href="admin.php?mod=faq&action=showall&filter=answered'.$desc.'" style="color:'.$clr.'; border-bottom:dashed 1px '.$clr.'">Ответил</a>';
      echo '</th>';                                                
      echo '<th>';
      echo 'Действия';
      echo '</th>';
      echo '</tr>';
      if(isset($_GET['desc']))
         $desc = ' DESC';
      else
         $desc = '';
      switch($_GET['filter']){
         case 'name':
            $rows = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `available`', 'name'.$desc.', id');
         break;
         case 'email':
            $rows = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `available`', 'email'.$desc.', id');
         break;
         case 'date':
            $rows = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `available`', 'date'.$desc.', id');
         break;
         case 'question':
            $rows = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `available`', 'question'.$desc.', id');
         break;
         case 'answer':
            $rows = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `available`', 'answer'.$desc.', id');
         break;
         case 'answered':
            $rows = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `available`', 'answered'.$desc.', id');
         break;
         default:
            $rows = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `available`', 'date DESC, id');
         break;
      }
      
      foreach ($rows as $row){
         if ((int)$row['answered'] <= 0)
            $st = ' style="background-color: #ffdec3"';
         if ((int)$row['available'] <= 0)
            $st = ' style="background-color: #ddffd4;"';
         if ((int)$row['available'] > 0 && (int)$row['answered'] > 0)
            $st = '';

         echo '<tr class="highlite"'.$st.'>';
         echo '<td>';
         echo $row['name'];
         echo '</td>';
         echo '<td>';
         echo '<a href="mailto:'.$row['email'].'">'.$row['email'].'</a>';
         echo '</td>';
         echo '<td>';
         echo $row['date'];
         echo '</td>';
         echo '<td>';
         echo substr($row['question'], 0, 128).'...';
         echo '</td>';
         echo '<td>';
         echo substr($row['answer'], 0, 128).'...';
         echo '</td>';
         echo '<td>';
         if ($row['answered'] > 0){
            $user = users::get_user_info($row['answered']);
            echo $user['name'];
         }
         echo '</td>';
         echo '<td>';
         if ($row['answered'] <= 0)
            echo '<a href="javascript:editQuestion('.$row['id'].')">Изменить</a><br><a href="javascript:answerTo('.$row['id'].')">Ответить</a><br><a href="javascript:deleteQuestion('.$row['id'].')">Удалить</a>';
         else
            echo '<a href="javascript:editQuestion('.$row['id'].')">Изменить</a><br><a href="javascript:deleteQuestion('.$row['id'].')">Удалить</a>';
         echo '</td>';
         echo '</tr>';
      }
      echo '</tbody>';
      echo '</table>';
      if ((isset($_POST['answer']) || isset($_POST['answ'])) && $res > 0)
         messages::showmsg('Записи', 'Редактирование завершено успешно', 'success');
      elseif((isset($_POST['answer']) || isset($_POST['answ'])) && $res <= 0)
         messages::showmsg('Записи', 'Редактирование завершилось ошибкой. Пожалуйста, обратитесь в техническую поддержку', 'error');
   break;
   case 'unanswered':
      
   break;
   case 'settings':
      echo '<form action="admin.php?mod=faq&action=settings" method="POST">';
      echo '<input type="hidden" name="changed" value="1">';
      echo '<table width="100%" border="0">';
      echo '<tbody>';
      echo '<tr>';
      echo '<th>';
      echo '&nbsp;';
      echo '</th>';
      echo '<th>';
      echo '&nbsp;';
      echo '</th>';
      echo '</tr>';
      echo '<tr class="highlite">';
      echo '<td>';
      echo '<label for="captcha"><div>Использовать систему CAPTCHA<a href="http://captcha.ru/" target="_blank" style="position:relative;top:-5px; font-weight:bolder;">?   </a> для защиты от флуда</div></label>';
      echo '<br></td>';
      echo '<td style="text-align:left;">';
      $checked = base::eread('faq_config', 'value', '', 'name', 'captcha', '') == 1 ? 'checked' : '';
      echo '<input type="checkbox" id="captcha" value="1" name="captcha" '.$checked.'>';
      $checked = '';
      echo '</td>';
      echo '</tr>';
      echo '<tr class="highlite">';
      echo '<td>';
      echo '<label for="email">Обязательно запрашивать e-mail автора вопроса<br><br></label>';
      echo '</td>';
      echo '<td style="text-align:left;">';
      $checked = base::eread('faq_config', 'value', '', 'name', 'email', '') == 1 ? 'checked' : '';
      echo '<input type="checkbox" id="email" value="1" name="email" '.$checked.'>';
      $checked = '';
      echo '</td>';
      echo '</tr>';
      echo '<tr class="highlite">';
      echo '<td>';
      echo '<label for="premod">Премодерация сообщений<br><br></label>';
      echo '</td>';
      echo '<td style="text-align:left;">';
      $checked = base::eread('faq_config', 'value', '', 'name', 'premod', '') == 1 ? 'checked' : '';
      echo '<input type="checkbox" id="premod" value="1" name="premod" '.$checked.'>';
      $checked = '';
      echo '</td>';
      echo '</tr>';
      echo '<tr class="highlite">';
      echo '<td>';
      echo 'Количество вопрос в сайдбаре<br><br></label>';
      echo '</td>';
      echo '<td style="text-align:left;">';
      $small = base::eread('faq_config', 'value', '', 'name', 'small', '');
      echo '<input type="text" id="small" value="'.$small.'" name="small">';
      echo '</td>';
      echo '</tr>';
      echo '</tbody>';
      echo '</table>';
      echo '<br><input type="submit" value="Сохранить настройки">';
      echo '</form>';
      if (isset($_POST['changed']) && $res > 0)
         messages::showmsg('Настройки', 'Настройки модуля FAQ были успешно изменены', 'success');
      elseif(isset($_POST['changed']) && $res <= 0)
         messages::showmsg('Настройки', 'Изменение настрое FAQ завершилось ошибкой. Пожалуйста, обратитесь в техническую поддержку', 'error');
   break;
   default:
      messages::showmsg('Ошибка!', 'Было выбрвно некорректное действие', 'error');
   break;
}
?>
<? endif; ?>