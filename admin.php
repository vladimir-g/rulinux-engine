<?
include('incs/db.inc.php');
require_once('classes/users.class.php');
require_once('classes/auth.class.php');
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link href="design/admin/css/main.css" type="text/css" rel="stylesheet" />
<title>Панель Администратора</title>
<style>
.hiddenMsg{
	position:relative;
	display:none;
}
.shownMsg{
	font-size:13px;
	font-family:Times;
	font-weight:bold;
	color:#000000;
	background-color:#ecedff;
	position:relative;
	display:block;
}
</style>
<?
$icon = 'modules/icons/'.$_GET['mod'].'-small.png';
if (file_exists($icon))
	$icon = 'modules/icons/'.$_GET['mod'].'-small.png';
else
	$icon = 'modules/icons/blank.png';
?>
<script src="js/wins.js">
</script>
<script src="js/ajax.js">
</script>
<script>
function showMsg(message, id){
	document.getElementById(id).innerHTML=message;
	document.getElementById(id).className='shownMsg';
}
function hideMsg(id){
	document.getElementById(id).innerHTML='';
	document.getElementById(id).className='hiddenMsg';
}
</script>
</head>
<body>
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
require_once('classes/config.class.php');
require_once('classes/users.class.php');
require_once('classes/modules.class.php');
require_once('classes/messages.class.php');
require_once('classes/core.class.php');
if ($_SESSION['user_admin']>=1):?>
<?if(isset($_GET['ajax_id'])):?>
<?exit ($_GET['ajax_id'])?>
<?endif;?>
	<table style="border:1px solid; border-color:#0000ff; height:100%; width:100%; border-color:gray; padding:0px;" cellspacing="0" cellpadding="0">
	<tr valign="top">
	<td>
	<table style="width:100%; height:30px; background-image:url(design/admin/img/toolbarbg.png)" cellspacing="0" cellspadding="0">
	<tr>
	<td style="margin:0; padding:0;">
	<img src="design/admin/img/menu.png" alt="Меню" id="menuBtn" title="Меню" border="0" onmouseover="this.src='design/admin/img/menu-hover.png'" onmouseout="if (document.getElementById('main-menu').className != 'menu-shown') this.src='design/admin/img/menu.png'; else this.src='design/admin/img/menu-click.png';"  onmousedown="this.src='design/admin/img/menu-click.png'" onclick="if (document.getElementById('main-menu').className == 'menu-hidden') document.getElementById('main-menu').className='menu-shown'; else document.getElementById('main-menu').className='menu-hidden'">
	</td>
	<td align="right">
	<span class="menulink" style="border:#518cc2 1pt solid; background-color:#3f6d98; padding:2px;">
	<a href="index.php" class="menulink">На сайт</a>
	<span class="menulink">&nbsp;|&nbsp;</span>
	<a href="<?echo $scriptname?>?logout" class="menulink">Выйти</a>
	</span>
	&nbsp;&nbsp;<span class="menulink" style="border:#518cc2 1pt solid; background-color:#3f6d98; padding:2px;"><?=date('H:i')?></span>
	</td>
	<td align="right"><span class="hiddenMsg" style="text-align: left" id="msgSpan"></span></td></tr>
	<tr>
	<td>
		<table class="menu-hidden" id="main-menu">
			<tr class="menu-element"><td><img src="design/admin/img/go-home.png" border="0"></td><td><a href="admin.php" class="menu-element">В начало</a><td></tr>
			<tr style="height:20px;"><td></td><td>Приложения:<td></tr>
<?
	if (modules::get_module() > 0){
		$mlinks = modules::get_module();
		$mlinks['Модули'] = 'modules';
		$mlinks['Настройки'] = 'settings';
	}
	else{
		$mlinks = array(
			'Модули' => 'modules',
			'Настройки' => 'settings'
		);
	}
	$mnames = array_flip($mlinks);
	$links = array();
	$i = 0;
	foreach ($mlinks as $ml) {
		if ($ml == 'modules' || $ml == 'settings')
			$links[$i]='<tr class="menu-element"><td><img src="modules/icons/'.$ml.'-small.png" border="0"></td><td><a class="menulink" href="'.$scriptname.'?'.$ml.'">'.$mnames[$ml].'</a></td><td></td></tr>';
		else
			$links[$i]='<tr class="menu-element"><td><img src="modules/icons/'.$ml.'-small.png" border="0"></td><td><a class="menulink" href="'.$scriptname.'?mod='.$ml.'">'.$mnames[$ml].'</a></td><td><img src="design/admin/img/arrow.png"></td></tr>';
		$i++;
	}
	foreach ($links as $lnk) {
		echo $lnk."\n";
	}
	?>
			<tr style="height:20px;"><td></td><td>Действия:<td></tr>
			<tr class="menu-element"><td><img src="design/admin/img/user-desktop.png" border="0"></td><td><a href="index.php" class="menu-element">На сайт</a></td></tr>
			<tr class="menu-element"><td><img src="design/admin/img/quit.png" border="0"></td><td><a href="<?echo $scriptname?>?logout" class="menu-element">Выход</a></td></tr>
		</table>
	</td>
	</tr>
	</table>
	<table style="height:100%;" onclick="document.getElementById('main-menu').className='menu-hidden'; document.getElementById('menuBtn').src='design/admin/img/menu.png'">
	<tr>
	<td>
	<table style="height:100%; background-color:d5d9dc;/*#ecedff;*/">
	<tr valign="top"  style="width:25%"><td align="center">
	<?
	$info=users::get_user_info($_SESSION['user_login']);
	if ($info!=-1)
		echo '<span>Добро пожаловать, '.$info['name'].'</span><br /><img src="design/admin/img/splitter.gif" align="left"><br />';
	else header('location: admin.php?logout');
	?>
	<table align="left">
	<tr style="height:100%;">
	<td style="height:100%;">
	<?
	if (isset($_GET['modules']) && sizeof($_POST) > 0){
		foreach ($_POST as $pkey => $pval){
			if((int)$pkey > 0)
				core::set_activation($pkey, $pval);
		}
	}
	?>
	<span>Использовано места: </span><span class="infotext"></span><br /><br />
	<?
	$space = (100-(intval((diskfreespace($_SERVER['DOCUMENT_ROOT'])/disk_total_space($_SERVER['DOCUMENT_ROOT']))*100)));
	switch ($space){
		case $space < 35:
			$scale_cl = '#6abd1d';
			$scale_bg = 'green';
		break;
		case $space >= 35 && $space < 80:
			$scale_cl = '#d86f1b';
			$scale_bg = 'orange';
		break;
		case $space >= 80:
			$scale_cl = '#cb0101';
			$scale_bg = 'red';
		break;
	}
	?>
	<div style="border: #15508a 1px solid; width: 100%; background-color: #528dc3; color:#ffffff; text-align:center;padding:1pt;"><div style="border: #000000 1px solid; width: <?=$space?>%; background-color: <?=$scale_cl?>; background-image:url(design/admin/img/<?=$scale_bg?>.png); color:#000000; text-align:center; font-weight:bolder;"><?=$space?>%</div></div><br><br />
	<span>Пользователей: </span><span class="infotext"><?echo users::get_users_count()?></span><br />
	<img src="design/admin/img/splitter.gif" align="left"><br />
	<?
	$avmodules = modules::get_module();
	if (@array_search('users', $avmodules) != ''){
		echo '<a href = "admin.php?mod=users&act=users&uid='.$_SESSION['user_login'].'">Ваш Профиль</a><br>';
		echo '<a href = "admin.php?mod=users&act=users&uid='.$_SESSION['user_login'].'">Ваши Сообщения</a> (0/0)<br><br>';
	}
	?>
	Ваши Документы (0/0)<br>
	</td>
	</tr>
	</table>
	</td></tr>
	</table>
	</td>
	<td valign="top" style="width:100%">
	<div id="workflow">
	</div>
	<?
	if ($_SESSION['user_admin']==2){
		messages::showmsg('Внимание!', 'Этот пользоватеть является демонстрационным, у вас нет доступа к изменению настроек системы или содержания страниц!', 'warning');
	}
	$mod=$_GET['mod'];
	if (empty($mod)):
	if (isset($_POST['site_name'])) {
		$_POSTVALS=array_flip($_POST);
		foreach ($_POSTVALS as $val) {
			if (!empty($_POST[$val])){
				$res=base::modify_setting($val, $_POST[$val]);
			}
		}
	}
	?>
	<?if (sizeof($_GET) <= 0):?>
	<table>
	<?

	foreach ($mlinks as $mname => $mlink) {
		echo '<tr>';
		echo '<td style="width:30%"><img src="modules/icons/'.$mlink.'.png"></td>';
		if ($mlink == 'settings' || $mlink == 'modules'){
			echo '<td><a href='.$scriptname.'?'.$mlink.' id="modulename">'.$mname.'</a></td>';
		}
		else {
			echo '<td><a href='.$scriptname.'?mod='.$mlink.' id="modulename">'.$mname.'</a></td>';
		}
		echo '</tr>';
	}
	?>
	</table>
	<? endif; ?>
	<? if (isset($_GET['modules'])): ?>
	<h1><img src="modules/icons/modules.png" align="left">Модули</h1>
	<form action="admin.php?modules" method="POST">
	<table>
	<tr style="background-color:#e9e7e6;">
		<th>№</th>
		<th>Название</th>
		<th>Версия</th>
		<th>Ядро</th>
		<th>Описание</th>
		<th>Включен</th>
	</tr>
	<?
	$modules = modules::get_module('all');
	$i=1;
	foreach ($modules as $modulename => $modulelink){
		$modid = modules::get_module_id($modulename);
		$minfo = modules::get_module_info($modid);
		if ($minfo['active'] == 1)
			$bg = '#c5f9ca';
		else
			$bg = '#ffdec3';
		echo '<tr style="background-color:'.$bg.'">';
		echo '<td style="padding:5px;">';
		echo $i;
		echo '</td>';
		echo '<td style="padding:5px;">';
		echo $modulename;
		echo '</td>';
		echo '<td style="padding:5px;">';
		echo $minfo['version'];
		echo '</td>';
		echo '<td style="padding:5px;">';
		echo $minfo['comp'];
		echo '</td>';
		echo '<td style="padding:5px;">';
		echo $minfo['descr'];
		echo '</td>';
		echo '<td style="padding:5px; text-align:center">';
		if ($minfo['active'] == 1){
			echo '<select name="'.$modid.'" id="'.$modulelink.'">';
			echo '<option value="1">Да</option>';
			echo '<option value="0">Нет</option>';
			echo '</select>';
		}
		else{
			echo '<select name="'.$modid.'" id="'.$modulelink.'">';
			echo '<option value="0">Нет</option>';
			echo '<option value="1">Да</option>';
			echo '</select>';
		}
		echo '</td>';
		echo '</tr>';
		$i++;
	}
	?>
	</table>
	<input type="submit" value="Сохранить изменения">
	</form>
	<? endif; ?>
	<? if (isset($_GET['settings'])): ?>
	<h1><img src="modules/icons/settings.png" align="left">Настройки</h1>
	<a href="admin.php?hide=<?=$_SERVER['QUERY_STRING']?>">Свернуть</a><br>
	<a href="admin.php">Закрыть</a><br><br>
	<form action="<? echo $scriptname ?>" method="POST">
	<table style="width:100%">
	<tr>
	<td>Имя Сайта</td>
	<td align="right"><input type="text" name="site_name" value="<?=base::check_setting('site_name')?>" style="width:100%"></td>
	</tr>
	<tr>
	<td>Имя шаблона стилей</td>
	<td align="right">
	<select name="template" style="width:100%">
	<option value="<?=base::check_setting('template')?>">Предустановленый</option>
	<?
	$tpl_dirs = base::parse_dir('design', '/^([.]{1,2})$|^(admin)$/');
	foreach ($tpl_dirs as $tpl_dir) {
		echo '<option value="'.$tpl_dir.'">'.$tpl_dir.'</option>';
	}
	?>
	</select>
	<!--<input type="text" name="template" value="<?echo base::check_setting('template')?>" style="width:100%">-->
	</td>
	</tr>
	<tr>
	<td>Главный администратор</td>
	<td align="right"><input type="text" name="main_admin" value="<?echo base::check_setting('main_admin')?>" style="width:100%"></td>
	</tr>
	<tr>
	<td>Индексовая страница</td>
	<td align="right"><input type="text" name="default_page" value="<?echo base::check_setting('default_page')?>" style="width:100%"></td>
	</tr>
	<tr>
	<td>Папка модулей</td>
	<td align="right"><input type="text" name="modules_dir" value="<?echo base::check_setting('modules_dir')?>" style="width:100%"></td>
	</tr>
	<tr>
	<td><input type="submit" value="Сохранить">&nbsp;<input type="reset" value="Отменить изменения"></td>
	<td align="right"></td>
	</tr>
	</table>
	<div id="mod">
	<?
	if ($res==1) {
		messages::showmsg('Изменение настроек', 'Настройки сайта успешно изменены и сохранены в базе', 'success');
	}
	else 
	if ($res==-1)
		messages::showmsg('Изменение настроек', 'Изменение настроек сайта завершилось ошибкой! Пожалуйста, свяжитесь с техподдержкой', 'error');
	?>
	</form>
	<?endif;?>
	<?else:?>
	<?
		core::load_module(modules::get_module($mod), $_GET['mod'], $GLOBALS['permissions']);
	?>
	<?endif;?>
	</div>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<?else:?>
	<style>
	.infocus{
		background-color:#f5f6be;
		border:1px dotted #0000ff;
	}
	.outfocus{
		background-color:#ffffff;
		border-color:#6a6c9b;
		border:1px solid;
	}
	</style>
	<table align="center" cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
	<tr valign="middle" align="center">
	<td valign="middle" align="center">
	<span style="color:gray;font-size:12; font-family:Arial">
	Панель администратора<br>
	Пожалуйста, введите Ваш логин и пароль
	</span>
	<form action="<?echo $scriptname?>" method="POST">
	<table style="border:1px solid; border-color:#6a6c9b; height:100px;">
	<tr style="color:gray;font-size:12; font-family:Arial">
	<td>Логин</td>
	<td><input type="text" name="login" id="login" onfocus="this.className='infocus'; document.getElementById('password').className='outfocus'"></td>
	</tr>
	<tr style="color:gray;font-size:12; font-family:Arial">
	<td>Пароль</td>
	<td><input type="password" name="password" id="password" onfocus="this.className='infocus'; document.getElementById('login').className='outfocus'"></td>
	</tr>
	<tr style="color:gray;font-size:12; font-family:Arial" valign="top">
	<td><input type="submit" value="Войти"></td>
	<td>
	<img src="design/admin/img/key.jpg" align="right" />
	</td>
	</tr>
	</table>
	</form>
	</td>
	</tr>
	</table>
<? endif; ?>
<script>
function update_mod(){
	sendRequest('ajax/admin.ajx.php?mod=<?=$_GET['mod']?>',get_mod,null,false)
	setTimeout('update_mod()', 5000);
}
function get_mod(a){
	alert(document.getElementById('mod').innerHTML)
	document.getElementById('mod').innerHTML = a;
}
</script>
</body>
</html>
