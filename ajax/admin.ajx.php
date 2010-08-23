<?
include('../incs/db.inc.php');
require_once('../classes/auth.class.php');
require_once('../classes/config.class.php');
require_once('../classes/news.class.php');
include('../classes/articles.class.php');
header('Content-Type: text/html; charset='.$db_charset);
?>
<?if ($_SESSION['user_admin']>=1):?>
<?
	$mname=modules::get_module($mod);
	if ($valid=$mname>-1){
		$minfo=modules::get_module_info(modules::get_module_id('', $mod));
		echo '<h1>'.$mname.'</h1>';
		messages::showmsg('Информация о модуле', 'Версия: '.$minfo['version'].'<br />
Совместимость с ядром: '.$minfo['comp'].'<br />
Автор: '.$minfo['autor'].'<br />Описание: '.$minfo['descr'], 'info');
		$m=modules::get_module(modules::get_module_id('', $mod));
		if (file_exists(base::check_setting('modules_dir').'/'.$m['link'].'.mod.php')){
			$inside=true;
			include_once(base::check_setting('modules_dir').'/'.$m['link'].'.mod.php');
		}
		else messages::showmsg('Ошибка подгрузки модуля', 'Невозможно найти файл модуля: вероятно, он был перемещен, удален или переименован, при этом осталась запись в таблице модулей.', 'error');
	}
	else {
		messages::showmsg('Ошибка подгрузки модуля!', 'Искомый модуль не найден', 'error');
	}
?>
<?endif;?>