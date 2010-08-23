<?
include('../incs/db.inc.php');
require_once('../classes/config.class.php');
require_once('../classes/users.class.php');
require_once('../classes/auth.class.php');
header('Content-Type: text/html; charset='.$db_charset);
?>
<? if ($_SESSION['user_admin']>=1): ?>
<?
if (isset($_GET['getlist'])){
   $groups = users::get_group('all');
	foreach ($groups as $group)
      echo '<option value="'.$group['id'].'">'.$group['name'].'</option>';
   exit();
}
if (isset($_GET['delete'])){
	echo base::del_row('groups', '`gid`='.$_GET['gid']);
	exit();
}
if ($_GET['edit'] == 1){
	if (str_replace(' ', '', $_GET['name']) != '')
		base::update_field('groups', 'name', $_GET['name'], '`gid` = '.$_GET['gid']);
   if ($_GET['gid'] > 1)
      base::update_field('groups', 'rules', bindec($_GET['permissions']), '`gid` = '.$_GET['gid']);
}
elseif(isset($_GET['edit']) && $_GET['edit'] == 0){
   users::add_group(iconv('utf8', $GLOBALS['db_charset'], $_GET['name']), bindec($_GET['permissions']));
}
if (isset($_GET['gid'])){
   $group = users::get_group($_GET['gid']);
   $permissions = decbin($group['rules']);
   $rt = users::$rules_table;
   $permissions = base::divide_string($permissions);
   $AJAXret='
   <legend style="color: #000000; font-weight: bold; vertical-align: middle;">Изменить/создать роль</legend>
   Название: <input type="text" name="rname" id="rname" value="'.$group['name'].'"><br><br>
   Страницы:<br>
   <ul>
   	<input type="checkbox" name="pr" id="pr">Читать<br>
   	<input type="checkbox" name="pa" id="pa">Создавать<br>
   	<input type="checkbox" name="pe" id="pe">Изменять<br>
   	<input type="checkbox" name="pd" id="pd">Удалять<br>
   </ul>
   Пользователи:<br>
   <ul>
   	<input type="checkbox" name="ur" id="ur">Читать<br>
   	<input type="checkbox" name="ua" id="ua">Создавать<br>
   	<input type="checkbox" name="ue" id="ue">Изменять<br>
   	<input type="checkbox" name="ud" id="ud">Удалять<br>
   </ul>
   Новости:<br>
   <ul>
   	<input type="checkbox" name="nr" id="nr">Читать<br>
   	<input type="checkbox" name="na" id="na">Создавать<br>
   	<input type="checkbox" name="ne" id="ne">Изменять<br>
   	<input type="checkbox" name="nd" id="nd">Удалять<br>
   </ul>
   Настройки:<br>
   <ul>
      <input type="checkbox" name="sr" id="sr">Читать<br>
   	<input type="checkbox" name="se" id="se">Изменять<br>
   </ul>';
   $cursor = 0;
   foreach ($rt as $rule){
      if ($permissions[$cursor] == '1')
         $AJAXret = str_replace('name="'.$rule.'"', 'name="'.$rule.'" checked', $AJAXret);
      $cursor++;
   }
}
if (isset($_GET['findusers'])){
   switch ($_GET['by']){
      case 1:
         $AJAXret = users::filter_users('name', $_GET['data']);
      break;
      case 2:
         $AJAXret = users::filter_users('nick', $_GET['data']);
      break;
      case 3:
         $AJAXret = users::filter_users('email', $_GET['data']);
      break;
      case 4:
         $AJAXret = users::filter_users('birthday', $_GET['data']);
      break;
      case 5:;
         if (preg_match('/^М|м|M|m|H|h$/', substr($_GET['data'], 0, 2)))
            $AJAXret = users::filter_users('gender', 'm');
         if (preg_match('/^Ж|ж|F|f|W|w$|D|d/', substr($_GET['data'], 0, 2)))
            $AJAXret = users::filter_users('gender', 'f');
      break;
      case 6:
         $AJAXret = users::filter_users('country', $_GET['data']);
      break;
      case 7:
         $AJAXret = users::filter_users('city', $_GET['data']);
      break;
      case 8:
         $AJAXret = users::filter_users('registered', $_GET['data']);
      break;
      case 9:
         $AJAXret = users::filter_users('last_visit', $_GET['data']);
      break;
      case 10:
         $AJAXret = users::filter_users('id', $_GET['data']);
      break;
   }
}
chdir('..');
$icon = getcwd().'/modules/icons/'.$_GET['mod'].'-small.png';
if (file_exists($icon))
	$icon = 'modules/icons/'.$_GET['mod'].'-small.png';
else
	$icon = 'modules/icons/blank.png';
$buff = '
   <table cellspacing="0" cellspadding="0" width="100%">
      <tr ondblclick="addWin(\'center\', \'center\', 320, 240, \'С притензией на Openbox ^_^\', \'<br><br><br><br><br><br><br><center><br><strong>Эта притензия на WM была создана при помощи JS, который и сгенирировал это окошко!</strong></center>\', true, \''.$icon.'\', false);">
         <th>
            Номер в базе
         </th>
         <th>
            Имя на сайте
         </th>
         <th>
            Роль
         </th>
         <th>
            Имя пользователя
         </th>
         <th>
            E-mail
         </th>
      </tr>
';
if ($AJAXret > 0 && sizeof($AJAXret) >= 1){
   foreach ($AJAXret as $answer){
      $answer['gid'] = users::get_group($answer['gid']);
      $buff .= '
         <tr style="border: 1px solid #000000; height:25px;" class="highlite">
            <td>
               '.$answer['id'].'
            </td>
            <td>
               '.$answer['nick'].'
            </td>
            <td>
               '.$answer['gid']['name'].'
            </td>
            <td>
               '.$answer['name'].'
            </td>
            <td>
               '.$answer['mail'].'
            </td>
         </tr>
      ';
   }
}
$buff .= '</table>';
$AJAXret = $buff;
echo $AJAXret;
?>
<? endif; ?>