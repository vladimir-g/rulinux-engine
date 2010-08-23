<?
$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$nid=intval($_GET['nid']);
$cid=$_GET['cid'];
include('incs/db.inc.php');
$content=array('title'=>'Ïîèñê');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/search.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('incs/header.inc.php');
$header=pages::get_templates('header');
$footer=pages::get_templates('footer');
if (strpos($header, '[menu]')<0 && strpos($footer, '[menu]')<0)
	pages::get_menu();
if (!empty($_GET['keys']))
	$found=search::find($_GET['keys']);
echo '<!--content section begin-->';

include_once('incs/bottom.inc.php');
?>