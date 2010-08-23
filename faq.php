<?
$pagename=$_SERVER['SCRIPT_NAME'];
$pagename=str_replace(getcwd(), '', $pagename);
if (!empty($_GET)){
	$_VALS=array_flip($_GET);
	$pagename=$pagename.'?';
	$c=0;
	foreach ($_GET as $_VAR){
		$pagename=$pagename.$_VALS[$_VAR].'='.$_VAR;
		$c++;
		if ($c<=(sizeof($_GET)-1))
			$pagename=$pagename.'&';
	}
}
$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$pid=intval($_GET['id']);
$content['title'] = 'FAQ';
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');
require_once('classes/messages.class.php');
require_once('incs/header.inc.php');
echo '<!--content section begin-->';
if (isset($_POST['name']) && isset($_POST['question'])){
   $errors = array();
   foreach($_POST as $key => $value){
      $_POST[$key] = str_replace('<', '&#60', $_POST[$key]);
      $_POST[$key] = str_replace('>', '&#62', $_POST[$key]);
   }
  
   if($_POST['name'] == '')
      $errors[0] = -1;
   else
      $errors[0] = 1;
   if ((int)base::eread('faq_config', 'value', null, 'name', 'email') >= 1){
      if($_POST['email'] == '')
         $errors[1] = -1;
      else
            $errors[1] = 1;
   }
   else
      $errors[1] = 0;
   if($_POST['question'] == '')
      $errors[2] = -1;
   else
      $errors[2] = 1;
   if ((int)base::eread('faq_config', 'value', null, 'name', 'captcha') >= 1){
      if($_POST['captcha'] == '')
         $errors[3] = -1;
      else{
         if($_POST['captcha'] == $_SESSION['captcha_keystring'])
            $errors[3] = 1;
         else
            $errors[3] = -1;
      }
   }
   else
      $errors[3] = 0;
   $log = '';
   for ($i = 0; $i < 4; $i++){
      if ($errors[$i] < 0){
         define('WRONG_FIELDS', true);
         switch($i){
            case 0: $log .= '<li style="color:#b20000">Не введено <strong>Имя</strong>'; break;
            case 1: $log .= '<li style="color:#b20000">Не введен адрес электронной почты (<strong>E-mail</strong>)'; break;
            case 2: $log .= '<li style="color:#b20000">Не введен <strong>Вопрос</strong>'; break;
            case 3: $log .= '<li style="color:#b20000">Неверно введена <strong>Надпись с картинки</strong>'; break;
         }
      }
   }
   if($_POST['signature'] != $_SESSION['signature']){
      $log .= '<li style="color:#b20000">Подпись данной сессии добавления сообщения уже не действительна.';
      define('WRONG_FIELDS', true);
   }
   define('WRONG_FIELDS', false);
   if (WRONG_FIELDS){
      $_GET['add'] = '';
      $log = 'Ваш вопрос не был добавлен по следующим причинам:<ul style="color:#b20000">'.$log.'</ul>';
      messages::showmsg('Ошибка добавления вопроса', $log, 'error');
   }
   else{
      if ((int)base::eread('faq_config', 'value', null, 'name', 'premod') >= 1){
         $_SESSION['captcha_keystring'] = md5(rand());
         if(faq::add_question($_POST['name'], $_POST['email'], $_POST['question'], 0) > 0)
            messages::showmsg('Ваше сообщение было добавлено, но не подтверждено', 'Для того чтобы ваше сообщение оказалось видимым, необходимо решение модератора', 'info');
      }
      else{
         $_SESSION['captcha_keystring'] = md5(rand());
         if(faq::add_question($_POST['name'], $_POST['email'], $_POST['question'], 1) > 0)
            messages::showmsg('Готово', 'Ваше сообщение было успешно добавлено!', 'success');
         else
            messages::showmsg('Ошибка!', 'Сервис временно не доступен. Приносим свои извинения за неудобства', 'error');
      }
   }
$_SESSION['signature'] = md5(rand().time());
}
if (!isset($_GET['add'])){
$questions = base::get_fields('faq', '`name`, `email`, `date`, `question`, `answer`, `answered`, `id`, `ip`', 'date DESC, id', '`available` = 1');
$start = (int)$_GET['pid'];
echo '<h1>FAQ - Вопросы и ответы</h1>';
echo '<div align="right" style="font-weight:bolder; fint-size:15pt;">[<a href="faq.php?add">Добавить вопрос</a>]</div><br>';
$_SESSION['signature'] = md5(rand().time());
   /*echo '<form action="faq.php" method="POST">';
   echo '<input type="hidden" name="signature" value="'.$_SESSION['signature'].'">';
   echo '<table style="width:100%" border="0" cellspacing="0" cellspadding="0">';
   echo '<tbody>';
   echo '<tr class="faq_head" style="background-color:#cfcfcf;">';
   echo '<td style="vertical-align:top">';
   echo 'Имя:';
   echo '</td>';
   echo '<td style="width:75%">';
   echo '<input type="text" name="name" style="width:100%">';
   echo '</td>';
   echo '</tr>';
   if ((int)base::eread('faq_config', 'value', null, 'name', 'email') >= 1){
      echo '<tr class="faq_head" style="background-color:#cfcfcf;">';
      echo '<td style="vertical-align:top">';
      echo 'E-mail:';
      echo '</td>';
      echo '<td style="width:75%">';
      echo '<input type="text" name="email" style="width:100%">';
      echo '</td>';
      echo '</tr>';
   }
   echo '<tr style="background-color:#cfcfcf;font-size:10pt;">';
   echo '<td style="vertical-align:top;font-size:10pt;">';
   echo 'Вопрос:';
   echo '</td>';
   echo '<td style="width:75%">';
   echo '<textarea name="question" style="width:100%" rows="10"></textarea>';
   echo '</td>';
   echo '</tr>';
   if ((int)base::eread('faq_config', 'value', null, 'name', 'captcha') >= 1){
      echo '<tr style="background-color:#cfcfcf">';
      echo '<td style="vertical-align:top;font-size:10pt;">';
      echo 'Введите символы с картинки:';
      echo '</td>';
      echo '<td style="width:75%">';
      echo '<img src="kcaptcha/index.php" align="left">';
      echo '<input type="text" name="captcha" style="height:100%; width:160px; font-size:45px">';
      echo '</td>';
      echo '</tr>';
   }
   echo '<tr style="background-color:#cfcfcf">';
   echo '<td style="vertical-align:top">';
   echo '<input type="submit" value="Задать вопрос">';
   echo '</td>';
   echo '<td style="width:75%">';
   echo '</td>';
   echo '</tr>';
   echo '</tbody>';
   echo '</table>';
   echo '</form>';*/
echo '<table cellspadding="5" cellspacing="0" border="0" style="width:100%; border: 1px solid #000000">';
echo '<tbody>';
for ($i = $start; $i < ($start*10)+10; $i++){
	$questions[$i]['question'] = str_replace("\r\n", "<br>", $questions[$i]['question']);
	$questions[$i]['answer'] = str_replace("\r\n", "<br>", $questions[$i]['answer']);
   echo '<tr class="faq_head">';
   echo '<td style="text-align:justify; vertical-align:top;margin:5">';
   echo 'Автор:';
   echo '</td>';
   echo '<td style="text-align:justify; vertical-align:top">';
   if ($_SESSION['user_admin'] >= 1)
      echo '<a href="mailto:'.$questions[$i]['email'].'" id="otherlinks" style="font-weight:normal;">'.$questions[$i]['name'].'</a> [ <a href="http://www.vline.ru/ip/?ip='.$questions[$i]['ip'].'" style="font-weight:normal;" id="otherlinks">'.$questions[$i]['ip'].'</a> ]';
   else
      echo $questions[$i]['name'];
   echo '</td>';
   echo '</tr>';
   echo '<tr style="font-size:10pt;">';
   echo '<td style="text-align:justify; vertical-align:top">';
   echo 'Вопрос:<br>';
   echo '</td>';
   echo '<td style="text-align:justify; vertical-align:top">';
   echo $questions[$i]['question'];
   echo '</td>';
   echo '</tr>';
   if($questions[$i]['answer'] != ''){
      echo '<tr style="font-size:10pt;">';
      echo '<td style="text-align:justify; vertical-align:top">';
      echo 'Ответ:<br>';
      echo '</td>';
      echo '<td style="text-align:justify; vertical-align:top">';
      echo $questions[$i]['answer'];
      echo '</td>';
      echo '</tr>';
   }
   if ($_SESSION['user_admin'] >= 1)
   echo '<tr style="font-size:10pt;"><td>Действия:</td><td>&nbsp;<a href="admin.php?mod=faq" style="font-weight:normal; color:#000000">Управление FAQ</a>&nbsp;|&nbsp;<a href="admin.php?mod=faq" style="font-weight:normal; color:#000000">Ответить&nbsp;|&nbsp;<a href="admin.php?mod=faq" style="font-weight:normal; color:#000000">Удалить</a></a></td></tr>';
   echo '<tr style="height:25pt;"><td></td><td></td></tr>';
   if (($questions[$i+1]['name']) == '') break;
}
echo '</tbody>';
echo '</table>';
echo '<div align="center" style="font-weight:bolder; fint-size:15pt;">[<a href="faq.php?add">Добавить вопрос</a>]</div>';
$number_of_pages=sizeof($questions)/10;
if (strpos($number_of_pages, '.')>0){
   if ($number_of_pages > 1)
		$number_of_pages = (int)$number_of_pages+1;
}
if ($number_of_pages>=2){
  	echo '<div class="faq_pages" align="center">';
	echo '[<a href="faq.php">1</a>]&nbsp;';
  	$p = 2;
	for ($b=0; $b<$number_of_pages-1; $b++){
		$c=1+10*$p-10;
      echo '[<a href="faq.php?pid='.$c.'">'.$p.'</a>]&nbsp;';
      $p++;
   }
   echo '</div>';
}
}
else{
   $_SESSION['signature'] = md5(rand().time());
   echo '<h1>F.A.Q. - Добавить вопрос</h1>';
   echo '<form action="faq.php" method="POST">';
   echo '<input type="hidden" name="signature" value="'.$_SESSION['signature'].'">';
   echo '<table style="width:100%" border="0" cellspacing="0" cellspadding="0">';
   echo '<tbody>';
   echo '<tr class="faq_head">';
   echo '<td style="vertical-align:top">';
   echo 'Имя:';
   echo '</td>';
   echo '<td style="width:75%">';
   echo '<input type="text" name="name" style="width:100%">';
   echo '</td>';
   echo '</tr>';
   if ((int)base::eread('faq_config', 'value', null, 'name', 'email') >= 1){
      echo '<tr class="faq_head">';
      echo '<td style="vertical-align:top">';
      echo 'E-mail:';
      echo '</td>';
      echo '<td style="width:75%">';
      echo '<input type="text" name="email" style="width:100%">';
      echo '</td>';
      echo '</tr>';
   }
   echo '<tr style="background-color:#d5d5d5;font-size:10pt;">';
   echo '<td style="vertical-align:top;font-size:10pt;">';
   echo 'Вопрос:';
   echo '</td>';
   echo '<td style="width:75%">';
   echo '<textarea name="question" style="width:100%" rows="10"></textarea>';
   echo '</td>';
   echo '</tr>';
   if ((int)base::eread('faq_config', 'value', null, 'name', 'captcha') >= 1){
      echo '<tr style="background-color:#cfcfcf">';
      echo '<td style="vertical-align:top;font-size:10pt;">';
      echo 'Введите символы с картинки:';
      echo '</td>';
      echo '<td style="width:75%">';
      echo '<img src="kcaptcha/index.php" align="left">';
      echo '<input type="text" name="captcha" style="height:100%; width:160px; font-size:45px">';
      echo '</td>';
      echo '</tr>';
   }
   echo '<tr style="background-color:#cfcfcf">';
   echo '<td style="vertical-align:top">';
   echo '<input type="submit" value="Задать вопрос">';
   echo '</td>';
   echo '<td style="width:75%">';
   echo '</td>';
   echo '</tr>';
   echo '</tbody>';
   echo '</table>';
   echo '</form>';
}
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>
