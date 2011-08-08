<?php
////error_reporting(E_ALL & E_NOTICE & E_PARSE);
//echo "UCAPTCHA leveled captcha system, level 0 example<br>";
require "ucaptcha.php";
require_once "../classes/base/base_interface.php";
require_once "../classes/config.class.php";
config::include_database('../');
require_once "../classes/object.class.php";
require_once "../classes/users.class.php";
$usersC = new users;
$cp=new ucaptcha;
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate'); 
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache');
header("Content-Type: image/x-png");
if(isset($_GET[session_name()]))
{
	session_start();
}
if(!empty($_COOKIE[session_name()]))
{
	$cpt_level = $_SESSION['user_id'] == 1 ? rand(1,2) : $usersC->get_captcha_level($_SESSION['user_id']);
	$captcha = $cp->gen_image($cpt_level);
	$_SESSION['captcha_keystring'] = $captcha[1];
}
?>