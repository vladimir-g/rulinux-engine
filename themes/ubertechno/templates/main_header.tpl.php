<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ru">
<head>
<LINK REL="alternate" TITLE="<?=$site_name?> RSS" HREF="<?=$rss_link?>" TYPE="application/rss+xml">
<link href="/css/common.css" type="text/css" rel="stylesheet" />
<link href="themes/<?=$theme?>/css/main.css" type="text/css" rel="stylesheet">
<LINK REL=STYLESHEET TYPE="text/css" HREF="themes/<?=$theme?>/css/hover.css" TITLE="Normal">
<link href="themes/<?=$theme?>/css/common.css" type="text/css" rel="stylesheet">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?=$title?></title></head><body>
<div class="head">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td><a href="<?=$profile_link?>"><?=$profile_name?></a>@<a href="/">RULINUX.NET~#</a></td>
<td align=right valign=top>Last login: <?=$coreC->to_local_time_zone($uinfo['last_visit'])?></td>
</tr>
<tr>
<td align=left><?=$invitation?></td>
<td align=right><a href="<?=$news_link?>">Новости</a> | <a href="<?=$mark_link?>">Разметка</a> | <a href="<?=$users_link?>">Пользователи</a> | <a href="<?=$gallery_link?>">Галерея</a> | <a href="<?=$forum_link?>">Форум</a> | <a href="<?=$articles_link?>">Статьи</a> | <a href="<?=$not_approved_link?>">Неподтвержденное</a> | <a href="<?=$tracker_link?>">Трекер</a> | <a href="<?=$rules_link?>">Правила форума</a> | <a href="<?=$faq_link?>">F.A.Q.</a> | <a href="<?=$links_link?>">Ссылки</a> | <a href="<?=$search_link?>">Поиск</a></td>
</tr>
</table>
</div>
<div style="clear: both;"></div>
<div style="clear: both;"></div>
