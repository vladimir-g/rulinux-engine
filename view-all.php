<?php
include 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.' - Просмотр неподтвержденных';
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
include 'links.php';
include 'themes/'.$theme.'/templates/header.tpl.php';
include 'themes/'.$theme.'/templates/view_all/top.tpl.php';
$unconfirmed = threads::get_unconfirmed();
for($i=0; $i<count($unconfirmed); $i++)
{
	$thread_id = $unconfirmed[$i]['id'];
	$comment_id = $unconfirmed[$i]['cid'];
	$subject = $unconfirmed[$i]['subject'];
	$img_link = '/gallery/'.$unconfirmed[$i]['file'].'.'.$unconfirmed[$i]['extension'];
	$img_thumb_link = '/gallery/thumbs/'.$unconfirmed[$i]['file'].'_small.png';
	$comment = $unconfirmed[$i]['comment'];
	$size = $unconfirmed[$i]['image_size'].', '.$unconfirmed[$i]['file_size'];
	$usr = users::get_user_info($unconfirmed[$i]['uid']);
	$author = $usr['nick'];
	$author_profile = '/profile.php?user='.$usr['nick'];
	$timestamp = $unconfirmed[$i]['timest'];
	$subsection_image = '/themes/'.$theme.'/icons/'.sections::get_subsection_icon($unconfirmed[$i]['subsection']);
	if($unconfirmed[$i]['section']==1)
		include 'themes/'.$theme.'/templates/view_all/news.tpl.php';
	else if($unconfirmed[$i]['section']==2)
		include 'themes/'.$theme.'/templates/view_all/art.tpl.php';
	else if($unconfirmed[$i]['section']==3)
		include 'themes/'.$theme.'/templates/view_all/gallery.tpl.php';
}

include 'themes/'.$theme.'/templates/footer.tpl.php';
?>