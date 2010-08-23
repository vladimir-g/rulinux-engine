<?
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) 
{
	$miniBB_gzipper_encoding = 'x-gzip';
}
if (strpos(' ' . $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) 
{
	$miniBB_gzipper_encoding = 'gzip';
}
if (isset($miniBB_gzipper_encoding)) 
{
	ob_start();
}
function percent($a, $b) 
{
	$c = $b/$a*100;
	return $c;
}
/**********************************************************************/
if (isset($_GET['remove_blocks'])){
	if($_COOKIE['remove_blocks'] == 1)
		setcookie('remove_blocks', 0, (time()+60*60*24*9999));
	elseif($_COOKIE['remove_blocks'] == 0)
		setcookie('remove_blocks', 1, (time()+60*60*24*9999));
}

if (isset($_GET['info_only'])){
	if($_COOKIE['info_only'] == 1)
		setcookie('info_only', 0, (time()+60*60*24*9999));
	else
		setcookie('info_only', 1, (time()+60*60*24*9999));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ru">
<head>
<?
if($_SESSION['user_login'])
	$info = $usersC->get_user_info($_SESSION['user_login']);
else
	$info = $usersC->get_user_info(0);
$tpl_name =(int)$_SESSION['user_login'] ? $tpl_name= $info['theme'] : (((isset($_COOKIE['lorng_theme']) || isset($_POST['themename'])) && file_exists('design/'.$_COOKIE['lorng_theme'].'/index.theme')) ? $_COOKIE['lorng_theme'] : $baseC->check_setting('template'));
if(defined('GLOBAL_SECTION')){
	switch(GLOBAL_SECTION){
		case 'articles':
			$idfield = 'aid';
		break;
		case 'forum':
			$idfield = 'newsid';
		break;
	}
	if(isset($_GET['newsid']))
		echo '<LINK REL="alternate" TITLE="RULINUX RSS" HREF="view-rss.php?section='.GLOBAL_SECTION.'&group='.(int)$_GET['group'].'&'.$idfield.'='.(int)$_GET['newsid'].'" TYPE="application/rss+xml">';
	else
		echo '<LINK REL="alternate" TITLE="RULINUX RSS" HREF="view-rss.php?section='.GLOBAL_SECTION.'&group='.(int)$_GET['group'].'" TYPE="application/rss+xml">';	
}
else{
	if(isset($_GET['newsid']))
		echo '<LINK REL="alternate" TITLE="RULINUX RSS" HREF="view-rss.php?section=news&newsid='.(int)$_GET['newsid'].'" TYPE="application/rss+xml">';
	else
		echo '<LINK REL="alternate" TITLE="RULINUX RSS" HREF="view-rss.php?section=news" TYPE="application/rss+xml">';
}
$pagename=$_SERVER['SCRIPT_NAME'];
$pagename=str_replace(getcwd(), '', $pagename);
//$meta=$pagesC->get_meta_data(false);
//foreach ($meta as $m)
//	echo "$m\n";
echo '
<link href="design/'.$tpl_name.'/css/main.css" type="text/css" rel="stylesheet" />
<script src="/js/jquery.js" type="text/javascript"></script>
<script src="/js/common.js" type="text/javascript">;</script> 
<LINK REL=STYLESHEET TYPE="text/css" HREF="design/'.$tpl_name.'/css/hover.css" TITLE="Normal">';
	echo '<link href="design/'.$tpl_name.'/css/common.css" type="text/css" rel="stylesheet" />';
/*if (empty($_SESSION['user_login'])){
	$info = array(
		'left_block' => '',
		'right_block' => 'auth:1,gall:2,links:3'
	);
}*/
if($_COOKIE['remove_blocks'] == 1){
	$info['left_block'] = '';
	$info['right_block'] = '';
}
?>
<title>
<?
if(intval($pid)>0){
	$content=$pagesC->get_page($pid);
}
$sitename=$baseC->check_setting('site_name');
echo $sitename.' - ';
echo $content['title'].'</title></head>';
echo '<body>
<!--header section begin-->
';
$uinfo = $info;
if ($uinfo['gid'] == 2) {
	if ($scriptname=='page.php')
		$first='<a href="admin.php?mod=links&eid='.$pid.'" align="right" style="color:#ffffff">Редактировать эту страницу</a>';
	else 
		$first='<a href="admin.php?mod=news&action=edit" align="right" style="color:#ffffff">Добавить новость</a>';
			echo '<div style="background-image:url(design/admin/img/admbg.png); height:23px; text-align:center">
<span style="font-size:12; font-family:Arial; vertical-align:middle; color:#ff0000;">
'.$first.'
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
::: <a href="admin.php?mod=links&eid=header" align="right" style="color:#ffffff">Редактировать верхнюю часть</a> 
::: <a href="admin.php?mod=links&eid=footer" align="right" style="color:#ffffff">Редактировать нижнюю часть</a> 
::: <a href="admin.php" align="right" style="color:#ffffff">В раздел администратора</a> 
::: <a href="'.$scriptname.'?logout" align="right" style="color:#ffffff">Выход</a></span></div>
				';
		}
$header=$pagesC->get_templates('header', $tpl_name);
if (empty($_SESSION['user_login'])){
$loginform='<form action="" method="post">
											<table>
											    <tr>
											        <td>Логин</td>
											        <td><input type="text" name="login"></td>
											    </tr>
												<tr>
											        <td>Пароль</td>
											        <td><input type="password" name="password"></td>
											    </tr>
											</table>
											<input type="submit" value="Войти">
											</form>';
}
else {
	$info=$usersC->get_user_info($_SESSION['user_login']);
	$group = $usersC->get_group($info['gid']);
	$loginform='
	<form action="?logout" method="post">
	Вы вошли как '.$info['nick'].' ('.$group['name'].')
	<br /><br />
	<input type="submit" value="Выйти">
	</form>
	';
}
$searchform='<form action="search.php" method="get">
<input type="text" value="Поиск..." id="keys" name="keys" style="width: 100%;" onfocus="clear_field()" onchange="validate_field()" type="text"><br /><input value="Искать" type="submit">
</form>';
$user_name = empty($_SESSION['user_name']) ? 'anonymous' : $_SESSION['user_name'];
	
if ($_SESSION['user_login'] != ''):
{
	$usr = 'Добро пожаловать <a href="profile.php?user='.$_SESSION['user_name'].'">'.$_SESSION['user_name'].'</a>';
}
elseif ($_SESSION['user_login'] == ''):
{
	$usr = '<a href="/register.php">Регистрация</a> -
	<a href="/index.php" onclick="showLoginForm(); return false;">Вход</a>';
}
endif;
	
$main_head = 'design/'.$tpl_name.'/templates/main_head.tpl';
$file = fopen($main_head,"r");
if(!file)
{
	echo("Ошибка открытия файла");
}
else
{
	$startpage = fread ($file,filesize($main_head));
	$startpage = str_replace("[tpl_name]", $tpl_name, $startpage);
	$startpage = str_replace("[usr_name]", $usr, $startpage);
	fclose($file);
}

$smal_head = 'design/'.$tpl_name.'/templates/smal_head.tpl';
$file = fopen($smal_head,"r");
if(!file)
{
	echo("Ошибка открытия файла");
}
else
{
	$subpage = fread ($file,filesize($smal_head));
	$subpage = str_replace("[tpl_name]", $tpl_name, $subpage);
	$subpage = str_replace("[usr_name]", $usr, $subpage);
	fclose($file);
}
if(class_exists('news')){
$shortnews_res=$newsC->get_news('WHERE `active` = 1 AND `type` = 1', 'LIMIT 0, '.$baseC->eread('news_config', 'value', null, 'name', 'small'));
if (sizeof($shortnews_res)>0){
	if (sizeof($shortnews_res)>=$baseC->eread('news_config', 'value', null, 'name', 'small'))
		$to = $baseC->eread('news_config', 'value', null, 'name', 'small');
	else
		$to = sizeof($shortnews_res);
	for ($i=1; $i<=$to; $i++){
		$shortnews=$shortnews.'<a href="news.php?newsid='.$shortnews_res[$i]['id'].'" id="newsheader">'.$shortnews_res[$i]['title'].' </a><hr>';
		$shortnews=$shortnews.'<p id="newstext">'.$shortnews_res[$i]['desc'].'</p>';
		$shortnews=$shortnews.'<p id="newsbottom">Опубликована: '.$shortnews_res[$i]['timestamp'].'</p><br>';
	}
}
}
if ((int)$_GET['all']>0)
	$checkbox0 = '<input type="checkbox" name="all" id="all" value="1" checked />';
else 
	$checkbox0 = '<input type="checkbox" name="all" id="all" value="1" />';
if ((int)$_GET['mark']>0)
	$checkbox1 = '<input type="checkbox" name="mark" id="mark" value="1" checked />';
else 
	$checkbox1 = '<input type="checkbox" name="mark" id="mark" value="1" />';
if ((int)$_GET['strict']>0)
	$checkbox2 = '<input type="checkbox" name="strict" id="strict" value="1" checked />';
else {
	if (!isset($_GET['keys']))
		$checkbox2 = '<input type="checkbox" name="strict" id="strict" value="1" checked />';
	else
		$checkbox2 = '<input type="checkbox" name="strict" id="strict" value="1" />';
}
$msearchform='<form action="msearch.php" method="get" style="width: 100%;">
<input type="text" value="'.urldecode($_GET['keys']).'" id="keys" name="keys" style="width: 100%;" type="text" /><br /><br />
'.$checkbox2.'<label for="strict"> Строгий поиск</label><br />
'.$checkbox0.'<label for="all"> Искать в тексте ответа</label><br />
'.$checkbox1.'<label for="mark"> Выделять маркером</label>
<br /><br /><input value="Искать" type="submit" />
<input type="reset" value="Очистить" />
</form>
';
if($_COOKIE['remove_blocks'] == 1){
	$info['left_block'] = '';
	$info['right_block'] = '';
}
if (empty($info['left_block']) && !empty($info['right_block'])) $add_style = ' style="margin-right: 245px;"';
if (empty($info['right_block']) && !empty($info['left_block'])) $add_style = ' style="margin-left: 245px;"';
if (!empty($info['left_block']) && !empty($info['right_block'])) $add_style = ' class="newsblog-in2"';
$news_page = '<div class="newsblog2">
  <div'.$add_style.'>

<h1><a href="news.php">Новости</a></h1>
<div class="news">
<div align="right">
[<a href="add-content.php?type=1">Добавить новость</a>]
</div>
';
if (!SUB_PAGE){
	$header=str_replace('[news_page]', $news_page, $header);
	$header=str_replace('[topmenu]', $startpage, $header);
}
else{
	$header=str_replace('[news_page]', '', $header);
	$header=str_replace('[topmenu]', $subpage, $header);
}
$header=str_replace('[news]', $shortnews, $header);
$header=str_replace('[search]', $searchform, $header);
$header=str_replace('[m-search]', $msearchform, $header);
$header=str_replace('[menu]', $pagesC->get_menu('', true), $header);
$header=str_replace('[submenu]', $pagesC->get_menu('', false), $header);
$header=str_replace('[login]', $loginform, $header);
$header=str_replace('[title]', $content['title'], $header);
$header=str_replace('<div class="name2">', '<div class="name2">'.$content['title'], $header);
//if($_COOKIE['info_only'] == 1)
//	$header = preg_replace('/\<img[(\s)(\w)(\W)(\")(\')]{0,}[\>]{1}/', '', $header);
echo $header;
echo '<!--header section end-->';
?>
