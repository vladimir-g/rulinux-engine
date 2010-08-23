<?
//error_reporting(0);
//include('incs/db.inc.php');
//require_once('classes/config.class.php');
//require_once('classes/modules.class.php');
//$_GET['id'] = base::check_setting('default_page');
//if ((int)$_GET['id']<=0){
//	$mods = modules::get_module('all');
//foreach ($mods as $mod){
//	if ($_GET['id'] == $mod){
//		include($_GET['id'].'.php');
//		break;
//	}
//}
//}
//if ((int)$_GET['id']>0){
//	include('page.php');
//}
include('news.php');
?>