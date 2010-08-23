<script src="design/<?=$tpl_name?>/js/jquery-1.js" type="text/javascript"></script>
<LINK REL="alternate" HREF="section-rss.jsp?section=2&amp;group=8404" TYPE="application/rss+xml">
<LINK REL=STYLESHEET TYPE="text/css" HREF="design/<?=$tpl_name?>/css/common.css" TITLE="Normal">
<link rel="top" title="talks.org.ru" href="/">
<script src="design/<?=$tpl_name?>/js/lor.js" type="text/javascript">;</script>
<LINK REL=STYLESHEET TYPE="text/css" HREF="design/<?=$tpl_name?>/css/hover.css" TITLE="Normal">
<base href="/">
<LINK REL=STYLESHEET TYPE="text/css" HREF="design/<?=$tpl_name?>/css/main.css" TITLE="Normal">
<LINK REL="shortcut icon" HREF="/favicon.ico" TYPE="image/x-icon">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td rowspan="2" align=left><a href="/"><img src="design/<?=$tpl_name?>/lor-new.png" width=282 height=60 border=0 alt="L.O.R. Next Generation"></a></td>
<td align="right">
<?
echo $tpl_name;
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
	<a style="text-decoration: none;" href="/" onclick="showLoginForm(); return false;">Вход</a>
	<? 
}
endif;
?>
</td>
</tr>
<tr>
<td align=right valign=bottom>
<a style="text-decoration: none" href="news.php">Новости</a> -
<a style="text-decoration: none" href="view-news.jsp?section=3">Галерея</a> -
<a style="text-decoration: none" href="view-section.php">Форум</a> - 
<a style="text-decoration: none" href="search.php">Поиск</a>
</td>
</tr>
</table>