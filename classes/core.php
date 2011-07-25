<?php
session_start();
require_once "classes/config.class.php";
config::include_database();
require "classes/core.class.php";
require "classes/users.class.php";
require "classes/auth.class.php";
$uinfo = users::get_user_info($_SESSION['user_id']);
require "classes/mark.class.php";
require "classes/filters.class.php";
require "classes/sections.class.php";
require "classes/threads.class.php";
require "classes/messages.class.php";
require "classes/faq.class.php";
?>