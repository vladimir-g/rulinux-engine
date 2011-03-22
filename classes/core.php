<?php
$true_arr = array('t', '1');
$false_arr = array('f', '0');
include('config/db.inc.php');
if($GLOBALS['subd']=='mysql')
	include "classes/base/mysql.php";
else if ($GLOBALS['subd']=='postgresql')
	include "classes/base/postgresql.php";

include "classes/core.class.php";
include "classes/users.class.php";
include "classes/auth.class.php";

session_start();
$uinfo = users::get_user_info($_SESSION['user_id']);


include "classes/mark.class.php";
include "classes/filters.class.php";
include "classes/sections.class.php";
include "classes/threads.class.php";
include "classes/messages.class.php";
//auth_user('moder', 'moder', false);
?>