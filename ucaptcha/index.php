<?php
////error_reporting(E_ALL & E_NOTICE & E_PARSE);
//echo "UCAPTCHA leveled captcha system, level 0 example<br>";
require "ucaptcha.php";
require('../config/db.inc.php');
if($GLOBALS['subd']=='mysql')
	require "../classes/base/mysql.php";
else if ($GLOBALS['subd']=='postgresql')
	require "../classes/base/postgresql.php";
require_once "../classes/users.class.php";
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
	$cpt_level = $_SESSION['user_id'] == 1 ? rand(1,2) : users::get_captcha_level($_SESSION['user_id']);
	$captcha = $cp->gen_image($cpt_level);
	$_SESSION['captcha_keystring'] = $captcha[1];
}
//echo $captcha[0];
//echo "done...";
//
//echo "<br><img src='cpt/".$captcha[0].".png'><br>answer is ".$captcha[1];
?>