<?php
////error_reporting(E_ALL & E_NOTICE & E_PARSE);
//echo "UCAPTCHA leveled captcha system, level 0 example<br>";
include "ucaptcha.php";
include_once "../incs/db.inc.php";
include_once "../classes/config.class.php";
$cp=new ucaptcha;
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate'); 
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache');
header("Content-Type: image/x-png");
if(isset($_REQUEST[session_name()])){
	session_start();
}

if($_REQUEST[session_name()]){
     $cpt_level = $_SESSION['user_login'] < 1 ? rand(1,2) : base::get_field_by_id('users', 'captcha', $_SESSION['user_login']);
     $captcha = $cp->gen_image($cpt_level);
     $_SESSION['captcha_keystring'] = $captcha[1];
}
//echo $captcha[0];
//echo "done...";
//
//echo "<br><img src='cpt/".$captcha[0].".png'><br>answer is ".$captcha[1];
?>