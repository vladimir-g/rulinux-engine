<script src="design/<?=$tpl_name?>/js/jquery-1.js" type="text/javascript"></script>
<?
if(isset($_GET['group']) || isset($_GET['forumid']))
	echo '<LINK REL="alternate" TITLE="L.O.R RSS" HREF="view-rss.php?section=forum&newsid='.(int)$_GET['newsid'].'" TYPE="application/rss+xml">';
else
	echo '<LINK REL="alternate" TITLE="L.O.R RSS" HREF="view-rss.php?section=forum" TYPE="application/rss+xml">';
?>
<LINK REL=STYLESHEET TYPE="text/css" HREF="design/<?=$tpl_name?>/css/common.css" TITLE="Normal">
<link rel="top" title="www.lor-ng.org" href="/">
<script src="design/<?=$tpl_name?>/js/lor.js" type="text/javascript">;</script>
<LINK REL=STYLESHEET TYPE="text/css" HREF="design/<?=$tpl_name?>/css/hover.css" TITLE="Normal">
<base href="/">
<LINK REL=STYLESHEET TYPE="text/css" HREF="design/<?=$tpl_name?>/css/main.css" TITLE="Normal">
<LINK REL="shortcut icon" HREF="/favicon.ico" TYPE="image/x-icon">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>
<div class="head">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr background="design/<?=$tpl_name?>/css/images/tile_back.gif">
<td>
<a href="/">
   <img src="design/<?=$tpl_name?>/lor-new.png" border=0 alt="L.O.R. Next Generation"></a>
</td>
<td align=right valign=top>
   <?
   echo $tpl_name;
   ?>
</td>
</tr>
<tr background="design/<?=$tpl_name?>/css/images/tile_sub-lite.gif">
<td align=left>
<a style="text-decoration: none" href="news.php">Новости</a> -
<a style="text-decoration: none" href="view-news.jsp?section=3">Галерея</a> -
<a style="text-decoration: none" href="view-section.php">Форум</a> - 
<a style="text-decoration: none" href="search.php">Поиск</a>
</td>
<td align=left>
<?
   if ($_SESSION['user_login'] != ''):
   {
	?>
	<p align=right>Добро пожаловать <a href="profile.php?user=<?print $_SESSION['user_name'];?>">
	<?echo $_SESSION['user_name'];?></a>
	<?
   }
   elseif ($_SESSION['user_login'] == ''):
   {
	?>
	<a style="text-decoration: none;" href="register.php">Регистрация</a> -
	<a style="text-decoration: none;" href="index.php" onclick="showLoginForm(); return false;">Вход</a>
	<? 
   }
   endif;
   ?>
</td>
</tr>
</table>
</div>