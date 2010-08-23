<?
class core{
	private function module_allown($permission, $mod){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
			$query = '
				SELECT `prefix`
				FROM `'.$GLOBALS['tbl_prefix'].'modules`
				WHERE `link` = \''.$mod.'\'';
			$prefix_res = mysql_query($query);
			$query = mysql_fetch_object($prefix_res);
			$prefix = $query->prefix;
			if (substr($permission, ($prefix+1), 1) == '1')
				return true;
			else{
				messages::showmsg('Ошибка безопасности!', 'У вас не достаточно прав для использования этого модуля.', 'error');
				return false;
			}
		}
	}
	function logit($message, $pars, $level) {
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
			$insert='INSERT INTO `'.$GLOBALS['tbl_prefix'].'logs`
					(`level`, `enc_by`, `timestamp`, `params`, `message`)
					VALUES
					('.$level.', \''.$_SESSION['user_name'].'\', \''.date('Y-m-d H:i:s').'\', \''.$pars.'\', \''.$message.'\')
					';
			if(mysql_query($insert))
				return 1;
			else 
				return 0;
		}
		else 
			return 0;
	}
	function load_module($mname, $mod, $permission){
		if (!core::module_allown($permission, $mod)) return false;
		$mname=modules::get_module($mod);
		if ($mname>-1){
			$minfo=modules::get_module_info(modules::get_module_id('', $mod));
			$m=modules::get_module(modules::get_module_id('', $mod));
			echo '<h1><img src="modules/icons/'.$m['link'].'.png" align="left">'.$mname.'</h1>';
			messages::showmsg('Информация о модуле', 'Версия: '.$minfo['version'].'<br />
Совместимость с ядром: '.$minfo['comp'].'<br />
Автор: '.$minfo['autor'].'<br />Описание: '.$minfo['descr'], 'info');
			$links = base::get_field_by_id('modules', 'links', modules::get_module_id('', $mod));
			$tags = base::devide_tags($links);
			if ($links != ''){
				echo '<form action="admin.php" method="GET" id="actForm">';
				echo '<input type="hidden" name="mod" value="'.$mod.'">';
				echo 'Действия: <select name="action" onchange="document.getElementById(\'actForm\').submit()">';
				echo '<option value="0">Перейти к:</option>';
				foreach ($tags as $tag){
					$tag = base::parse_attributes($tag);
					if ($tag['action'] == $_GET['action'])
						$curract = $tag['text'];
					echo '<option value="'.$tag['action'].'">'.$tag['text'].'</option>';
				}
				echo '</select>';
				echo '<input type="submit" value="Перейти">';
				echo '</form>';
				if ($curract != '')
					echo 'Текущее действие: <strong>'.$curract.'</strong><br><br>';
			}
			if (file_exists(base::check_setting('modules_dir').'/'.$m['link'].'.mod.php')){
				$inside=true;
				include_once(base::check_setting('modules_dir').'/'.$m['link'].'.mod.php');
			}
			else messages::showmsg('Ошибка подгрузки модуля', 'Невозможно найти файл модуля: вероятно, он был перемещен, удален или переименован, при этом осталась запись в таблице модулей.', 'error');
		}
		else {
			if ($mod != 'settings' && $mod != 'modules')
				messages::showmsg('Ошибка подгрузки модуля!', 'Искомый модуль не найден или не подключен.', 'error');
		}
	}
	function set_activation($id, $active){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET NAMES \''.$GLOBALS['db_charset'].'\'');
			$query = '
						UPDATE `'.$GLOBALS['tbl_prefix'].'modules`
						SET `active` = '.$active.'
						WHERE `id`='.$id
						;
			if (mysql_query($query))
				return 1;
			else
				return -1;
		}
		else
			return -2;
	}
}
function log_it($message, $level){
	if(!class_exists('base'))
		include_once('config.class.php');
	$pars = "GET:\n";
	foreach ($_GET as $par => $val){
		$pars .= "$par = $val;\n";
	}
	$pars .= "POST:\n";
	foreach ($_POST as $par => $val){
		$pars .= "$par = $val;\n";
	}
	$pars .= "COOKIES:\n";
	foreach ($_COOKIE as $par => $val){
		$pars .= "$par = $val;\n";
	}
	$loglevel = base::check_setting('loglevel');
	if ($loglevel >= $level){
		core::logit($message, $pars, $level);
	}
}
?>