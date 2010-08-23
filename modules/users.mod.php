<?if ($inside):?>
<?
include_once('classes/users.class.php');
$action=$_GET['action'];
if (!isset($action))
	$action = 'users';

$icon = 'modules/icons/'.$_GET['mod'].'-small.png';
if (file_exists($icon))
	$icon = 'modules/icons/'.$_GET['mod'].'-small.png';
else
	$icon = 'modules/icons/blank.png';
?>
<style>
.active{
	border-left:solid #6a6c9b 1px;
	border-top:solid #6a6c9b 1px;
	border-right:solid #6a6c9b 1px;
	border-bottom:none;
	background-color:#ffffff;
}
.inactive{
	border: solid #6a6c9b 1px;
	background-color:#ffffff;
}
</style>
<?
if ($action == 'users'){
	if ((int)$_GET['uid']<=0 && $_GET['action'] != 'addnew'){
		$users=users::get_users(0);
		echo '<table>';
			echo '<tr>';
				echo '<th style="text-align:left">Идентификатор</td>';
				echo '<th style="text-align:left">Логин</td>';
				echo '<th style="text-align:left">Отображаемое имя</td>';
				echo '<th style="text-align:left">Роль</td>';
			echo '</tr>';
			for ($u = 0; $u < sizeof($users); $u++){
				$ugroup = users::get_group($users[$u]['group']);
				echo '<tr>';
				echo '<td><a href = "admin.php?mod=users&act=users&uid='.$users[$u]['id'].'">'.$users[$u]['id'].'</a></td>';
				echo '<td><a href = "admin.php?mod=users&act=users&uid='.$users[$u]['id'].'">'.$users[$u]['login'].'</a></td>';
				echo '<td><a href = "admin.php?mod=users&act=users&uid='.$users[$u]['id'].'">'.$users[$u]['name'].'</a></td>';
				echo '<td><a href = "admin.php?mod=users&act=groups&gid='.$users[$u]['group'].'">'.$ugroup['name'].'</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		elseif ((int)$_GET['uid']>0 || $_GET['action'] == 'addnew'){
			if (isset($_POST['usernick'])) {
				if ($_POST['usernick'] != ''){
					if ((int)$_GET['uid'] > 0){
						foreach ($_POST as $pk => $uv) {
							$field = substr($pk, 4);
							if ($field == 'pass' || $uv == '')
								continue;
							if ($field == 'pass')
								$uv = md5($uv);
							$user_res = users::modify_user_info($field, $uv, (int)$_GET['uid']);
						}
						if ($_POST['mailuser'] == 'on'){
							$sendtext .= 'Здравствуйте, '.$_POST['username']."! \n";
							$sendtext .= 'Администратор сайта http://'.$_SERVER['HTTP_HOST'];
							$sendtext .= 'изменил информацию о вашем пользователе ('.$_POST['usernick'].').';
							mail($_POST['usermail'], 'Ваш новый аккаунт', $sendtext);
						}
					}
				}
			}
			define('uid', $_GET['uid']);
			$user=users::get_user_info(uid);
			?>
			<form action="admin.php?mod=users&act=users&uid=<?=uid?>" method="POST" id="formd1">
			<table>
			<tr>
			<td>Логин</td><td><input type="text" name="usernick" value="<?=$user['nick']?>"></td>
			</tr>
			<tr>
			<td>Пароль <span style="color:#000000">*</span></td>
			<td><input type="password" name="userpass" value=""></td>
			</tr>
			<tr>
			<td>Отображаемое имя</td><td><input type="text" name="username" value="<?=$user['name']?>"></td>
			</tr>
			<tr>
			<td>Страна</td><td><input type="text" name="usercountry" value="<?=$user['country']?>"></td>
			</tr>
			<tr>
			<td>Город</td><td><input type="text" name="usercity" value="<?=$user['city']?>"></td>
			</tr>
			<tr>
			<td>Группа</td>
			</tr>
			<tr>
			<td><hr></td>
			<tr>
			<td>Дата Рождения</td><td><input type="text" name="userbirthday"  id="userbirthday" value="<?=$user['birthday']?>" readonly><input name="pdate" onclick='showCal("Calendar1")' value="..." type="button"></td>
			</tr>
			<tr>
			<td>Пол</td>
			<td>
			<select name="usergender">
			<?
			switch ($user['gender']) {
				case 'm':
					echo 
					'<option value="m">Мужской</option>'.
					'<option value="f">Женский</option>'.
					'<option value="">Не указан...</option>';
					break;
				case 'f':
					echo 
					'<option value="f">Женский</option>';
					'<option value="m">Мужской</option>'.
					'<option value="">Не указан...</option>';
					break;
				default:
					echo 
					'<option value="">Не указан...</option>'.
					'<option value="m">Мужской</option>'.
					'<option value="f">Женский</option>';
					break;
			}
			?>
			</select>
			</td>
			</tr>
			<tr>
			<td>E-mail</td><td><input type="text" name="useremail" value="<?=$user['email']?>"></td>
			</tr>
			<tr>
			<td>Активирован</td>
			<td>
			<select name="userstatus">
			<?
			switch ((int)$user['status']) {
				case 1:
					echo 
					'<option value="1" style="color:#005400">Активирован</option>'.
					'<option value="0">Не активирован</option>';
					break;
				case 0:
					echo 
					'<option value="0" style="color:#ff0000">Не активирован</option>'.
					'<option value="1">Активирован</option>';
					break;
			}
			?>
			</select>
			</td>
			</tr>
			<tr>
			<td>Зарегистрирован</td><td><?=$user['registered']?></td>
			</tr>
			<tr>
			<td>Последний вход</td><td><?=$user['last_visit']?></td>
			</tr>
			</table>
			<input type="checkbox" name="mailuser" /> Оповестить пользователя<br /><br />
			<input type="reset" value="Отменить все изменения">&nbsp;
			<input type="submit" value="Сохранить">
			</form>
			<span style="color:#000000">* - Если данное поле пустое, оно изменено не будет</span><br />
			<?			
			switch ((int)$user_res){
				case 1:
					messages::showmsg('Обновление Информации', 'Обновление информации о пользователе "'.$user['nick'].'" прошло успешно', 'success');
				break;
				case -1:
					messages::showmsg('Обновление Информации', 'Обновление информации о пользователе не удалось. Ошибка соединения с сервером баз данных', 'error');
				break;
				case -2:
					messages::showmsg('Обновление Информации', 'Обновление информации о пользователе не удалось. Проверьте правильность заполнения полей', 'error');
				break;
				case -3:
					messages::showmsg('Обновление Информации', 'Обновление информации о пользователе не удалось. Пожалуйста, обратитесь в техподдержку', 'error');
				break;
				case -4:
					messages::showmsg('Обновление Информации', 'Обновление информации о пользователе не удалось. Такого пользователя не существует', 'error');
				break;
			}
			?>
			<script src="js/calendar.js">
			</script>
			<script language="JavaScript" src="js/cal_conf2.js">
			</script>
			<?
		}
	}
if ($action=='addnew'){
	if (isset($_POST['usernick'])) {
		if ($_POST['usernick'] != ''){
			$user_res = users::add_user(
							$_POST['usernick'], $_POST['userpass'],
							$_POST['username'], $_POST['useremail'],
							$_POST['usercountry'], $_POST['usercity'],
							$_POST['userbirthday'], $_POST['userstatus'],
							$_POST['usergender']
			);
			if ($_POST['mailuser'] == 'on'){
				$sendtext .= 'Здравствуйте, '.$_POST['username']."! \n";
				$sendtext .= 'Администратор сайта http://'.$_SERVER['HTTP_HOST'];
				$sendtext .= 'Добавил для Вас пользователя ('.$_POST['usernick'].").\n";
				$sendtext .= 'Ваш пароль для входа на сайт: '.$_POST['userpass']."\n\n";
				$sendtext .= 'Будем рады видеть Вас на нашем сайте!';
				mail($_POST['usermail'], 'Ваш новый аккаунт', $sendtext);
			}	
		}
	}
	?><form action="admin.php?mod=users&act=addnew" method="POST" id="formd1">
	<script src="js/wins.js">
	</script>
		<script>
			var chars = new Array();
			var pass = "";
			
			for (i = 0; i < 74; i++){
				if ((i+48)>122){
					i--;
					continue;
				}
				chars[i] = String.fromCharCode(i+48);
			}
			
			function showpass(){
				alert('Новый пароль для данного пользователя: <strong>' + pass + '</strong>')
			}
			
			function passgen(field){
				pass = '';
				field = document.getElementById(field);
				field.value = '';
				for (i = 0; i < 8; i++){
					rnd = Math.random();
					num = rnd.toString();
					num = num.substring(3,5);
					num = Math.round(num);
					if (chars[num] == null){
						i--;
						continue;
					}
					pass += chars[num];
				}
				document.getElementById('notice').innerHTML = 'Пароль придуман. <a href="javascript:showpass()">Посмотреть</a>';
				field.value = pass;
			}
		</script>
			<table>
			<tr>
			<td>Логин <span style="color:#000000">*</span></td>
			<td><input type="text" name="usernick"></td>
			</tr>
			<tr>
			<td>Пароль <span style="color:#000000">*</span></td>
			<td><input type="password" name="userpass" id="pass" value="">&nbsp;<input type="button" value="Придумать" onclick="passgen('pass')">&nbsp;<span id="notice" style="color:#1e7705;"></span></td>
			</tr>
			<tr>
			<td>Отображаемое имя <span style="color:#000000">*</span></td>
			<td><input type="text" name="username"></td>
			</tr>
			<tr>
			<td>Страна</td><td><input type="text" name="usercountry"></td>
			</tr>
			<tr>
			<td>Город</td><td><input type="text" name="usercity"></td>
			</tr>
			<tr>
			<td><hr></td>
			<tr>
			<td>Дата Рождения</td><td><input type="text" name="userbirthday" id="userbirthday" readonly><input name="pdate" onclick="showCal('Calendar1')" value="..." type="button"></td>
			</tr>
			<tr>
			<td>Пол</td>
			<td>
			<select name="usergender">
			<option value="">Не указан...</option>
			<option value="m">Мужской</option>
			<option value="f">Женский</option>
			</select>
			</td>
			</tr>
			<tr>
			<td>E-mail <span style="color:#000000">*</span></td>
			<td><input type="text" name="useremail"></td>
			</tr>
			<tr>
			<td>Активирован</td>
			<td>
			<select name="userstatus">
			<?
			switch ((int)$user['status']) {
				case 1:
					echo 
					'<option value="1" style="color:#005400">Активирован</option>'.
					'<option value="0">Не активирован</option>';
					break;
				case 0:
					echo 
					'<option value="0" style="color:#ff0000">Не активирован</option>'.
					'<option value="1">Активирован</option>';
					break;
			}
			?>
			</select>
			</td>
			</tr>
			</table>
			<input type="checkbox" name="mailuser" /> Оповестить пользователя<br /><br />
			<input type="reset" value="Очистить форму">&nbsp;
			<input type="submit" value="Сохранить"><br />
			<span style="color:#000000">* - Обязательные для заполнения поля</span>
			</form>
			<?
			switch ((int)$user_res){
				case 1:
					messages::showmsg('Добавление пользователя', 'Новый пользователь успешно добавлен', 'success');
				break;
				case -1:
					messages::showmsg('Добавление пользователя', 'Обновление информации о пользователе не удалось. Ошибка соединения с сервером баз данных', 'error');
				break;
				case -2:
					messages::showmsg('Добавление пользователя', 'Обновление информации о пользователе не удалось. Такой пользователь уже существует', 'error');
				break;
				case -3:
					messages::showmsg('Добавление пользователя', 'Обновление информации о пользователе не удалось. Пожалуйста, обратитесь в техподдержку', 'error');
				break;
			}
	}
	
if ($action=='groups'){
?>
<script src="js/ajax.js">
</script>
<script>
var permissons = new Array(
	'pr', 'pa', 'pe', 'pd',
	'ur', 'ua', 'ue', 'ud',
	'nr', 'na', 'ne', 'nd',
	'sr', 'se'
);
	
function updateList(items){
	document.getElementById('role').innerHTML = '';
	document.getElementById('role').innerHTML = '<option value="0">---Выберите роль---</option>';
	document.getElementById('role').innerHTML += items;
}
function showGroupData(data){
	document.getElementById('modify_gr').innerHTML = '';
	document.getElementById('modify_gr').innerHTML = data;
}
function showChangedData(data){
	document.getElementById('modify_gr').innerHTML = '';
	document.getElementById('modify_gr').innerHTML = data;
	q = "ajax/users.ajx.php?getlist";
	sendRequest(q, updateList, null, false);
}
function getGroup(gid){
	document.getElementById('edit').value = '1';
	if (gid <= 0) return;
	var q = "ajax/users.ajx.php";
	q = q + "?gid=" + gid;
	sendRequest(q, showGroupData, null, false);
}
function addRole(){
	document.getElementById('rname').value = 'Новая роль';
	document.getElementById('edit').value = '0';
	for (var i = 0; i < permissons.length; i++){
		document.getElementById(permissons[i]).checked = false;
	}
}
function setAttributes(gid){
	var rules = "";
	if (document.getElementById('edit').value == '0'){
		if (document.getElementById('rname').value == ''){alert('Пожалуйста, введите имя роли'); return};
	}
	for (var i = 0; i < permissons.length; i++){
		if (document.getElementById(permissons[i]).checked)
			rules += '1'
		else
			rules += '0'
	}
	
	var q = "ajax/users.ajx.php";
	q = q + "?gid=" + gid + "&edit=" + document.getElementById('edit').value + '&permissions=' + rules + '&name=' + document.getElementById('rname').value;
	sendRequest(q, showChangedData, null, false);
}
function deleteRole (gid){
	if (gid > 1){
		var p = window.prompt('Вы уверены, что хотите удалить эту роль?\r\nВведите (без ковычек): "да, удалить" для подтверждения.');
		if (p == 'да, удалить'){
			q = "ajax/users.ajx.php?delete&gid=" + gid;
			sendRequest(q, updateList, null, false);
			alert('Удаление подтверждено');
		}
		else
			alert('Удаление НЕ БЫЛО подтверждено');
	}
}
</script>
<form action="admin.php?mod=users&act=groups" method="post">
<?
if($_GET['gid'] > 0)
	echo '<input type="hidden" id="edit" value="1">';
else
	echo '<input type="hidden" id="edit" value="0">';
?>
<fieldset style="border: 1px solid #000000; background-color: #ffffff;">
	<legend style="color: #000000; font-weight: bold; vertical-align: middle;">Имеющиеся роли</legend>
		<select name="role" id="role" onChange="getGroup(this.value)">
			<option value="0">---Выберите роль---</option>
			<?
				$groups = users::get_group('all');
				foreach ($groups as $group)
					echo '<option value="'.$group['id'].'">'.$group['name'].'</option>';
			?>
		</select>
		<input type="button" value="Выбрать" onClick="getGroup(document.getElementById('role').value)">
		<input type="button" value="Сохранить" onClick="setAttributes(document.getElementById('role').value)">
		<input type="button" value="Новая" onClick="addRole()">
		<input type="button" style="color:#ff0000; font-weight:bold;" value="Удалить" onClick="deleteRole(document.getElementById('role').value)">
	</legend>
</fieldset>
<fieldset style="border: 1px solid #000000; background-color: #ffffff;" id="modify_gr">
	<?
	if ((int)$_GET['gid']>0):
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
echo $AJAXret;
else:
	?>
<legend style="color: #000000; font-weight: bold; vertical-align: middle;">Изменить/создать роль</legend>
Название: <input type="text" name="rname" id="rname"><br><br>
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
</ul>
<? endif; ?>
</fieldset>
</form>
<?
}

if ($action=='priv'){
	messages::showmsg(
		'Составление приватных списков',
		'Введите информацию, по которой можно однозначно идентифийировать пользователя<br><ul>
		<li>Двойной щелчек на заголовке таблицы вызовет диалог для добавления/удаления столбцов таблицы<br>
		<li>Двойной щелчек на информации о пользователе вызовет диалог действий над пользователями</ul>',
		'null'
		);
?>
<script>
function showFound(answer){
	document.getElementById('foundUsers').innerHTML = answer;
}
function findUser(){
	data = document.getElementById('data').value;
	by = document.getElementById('by').value;
	if (by > 0 && data != ''){
		document.getElementById('foundUsers').innerHTML = '';
		q = "ajax/users.ajx.php?findusers&by=" + by + "&data=" + data + '&mod=users';
		sendRequest(q, showFound, null, false);
	}
	else showMessage('Предупреждение!', 'Недостаточно критериев для поиска', 'warning', '<?=$icon?>')
}
</script>
<form id="ajaxform">
<input type="text" id="data">
<select id="by">
	<option value="0">Идентифицировать по:</option>
	<option value="1">ФИО</option>
	<option value="2">Логину</option>
	<option value="3">E-mail</option>
	<option value="4">Дате Рождения (формат: ГГГГ-ММ-ДД)</option>
	<option value="5">Полу</option>
	<option value="6">Стране</option>
	<option value="7">Городу</option>
	<option value="8">Дате Регистрации (формат: ГГГГ-ММ-ДД)</option>
	<option value="9">Последнему Посещению (формат: ГГГГ-ММ-ДД)</option>
	<option value="10">Номеру в базе</option>
</select>
<input type="button" value="Найти" onclick="findUser()"><br><br></form>
<div id="foundUsers">
</div>
<?
}

if ($action=='settings'){
	echo 'настройки';
}
?>
<?endif;?>
<script src="js/calendar.js">
</script>
<script language="JavaScript">
	addCalendar("Calendar1", "Дата", "userbirthday", "formd1");
</script>
