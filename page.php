<?
$pagename=$_SERVER['SCRIPT_NAME'];
$pagename=str_replace(getcwd(), '', $pagename);
if (!empty($_GET)){
	$_VALS=array_flip($_GET);
	$pagename=$pagename.'?';
	$c=0;
	foreach ($_GET as $_VAR){
		$pagename=$pagename.$_VALS[$_VAR].'='.$_VAR;
		$c++;
		if ($c<=(sizeof($_GET)-1))
			$pagename=$pagename.'&';
	}
}
$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$pid=intval($_GET['id']);
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');

$baseC = new base();
$faqC = new faq();
$pagesC = new pages();
$newsC = new news();
$usersC = new users();

require_once('incs/header.inc.php');
echo '<!--content section begin-->';
//echo '<span class="pagetitle">'.$content['title'].'</span><br>';
if (!empty($_GET['id'])) {
	if ($content!=-1) {
		$txt=$content['text'];
		echo '<div id="content">';
		echo $txt;
		echo '</div>';
	}
	else echo '<div class="error" align="center" style="text-align:center; height:100%;vertical-align:middle"><img src="images/helper.png"><br />Ошибка 404:<br /> 
Запрошенная Вами страница не найдена
</div>';
}
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>