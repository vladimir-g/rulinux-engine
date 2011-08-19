<?php
session_start();
if (get_magic_quotes_gpc()) 
{
	function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}
	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}
require_once 'librarys/geshi/geshi.php';
require_once 'librarys/phpmathpublisher/mathpublisher.php';
require_once 'classes/base/base_interface.php';
require_once "classes/config.class.php";
config::include_database();
require_once "classes/object.class.php";
require_once "classes/core.class.php";
$coreC = new core;
$installed = $coreC->is_installed();
if(!$installed)
{
	echo 'Проведите первичную инициализацию. Если вы уже проводили первичную инициализацию, но видите это сообщение по-прежнему, то выствите в файле config/install.ini значение 1 параметру installed.';
	exit;
}
require_once "classes/search.class.php";
$searchC = new search;
require_once "classes/users.class.php";
$usersC = new users;
require_once "classes/auth.class.php";
$authC = new auth;
$uinfo = $usersC->get_user_info($_SESSION['user_id']);
require_once "classes/mark.class.php";
$markC = new mark;
$mark_file = $markC->get_mark_file($_SESSION['user_id']);
require_once 'mark/'.$mark_file;
require_once "classes/filters.class.php";
$filtersC = new filters;
require_once "classes/sections.class.php";
$sectionsC = new sections;
require_once "classes/threads.class.php";
$threadsC = new threads;
require_once "classes/messages.class.php";
$messagesC = new messages;
require_once "classes/faq.class.php";
$faqC = new faq;
require 'classes/rss.class.php';
$rssC = new rss;
?>