<?php
require 'classes/core.php';
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = $site_name.' - Просмотр неподтвержденных';
$profile_name = $_SESSION['user_name'];
$profile_link = 'profile.php?user='.$_SESSION['user_name'];
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
require 'themes/'.$theme.'/templates/view_all/top.tpl.php';
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
	in_array($usr['banned'], $true_arr) ? $author = '<s>'.$usr['nick'].'</s>' :$author = $usr['nick'];
	$author_profile = '/profile.php?user='.$usr['nick'];
	$timestamp = core::to_local_time_zone($unconfirmed[$i]['timest']);
	$subsection_image = '/themes/'.$theme.'/icons/'.sections::get_subsection_icon($unconfirmed[$i]['subsection']);
	if($unconfirmed[$i]['section']==1)
		require 'themes/'.$theme.'/templates/view_all/news.tpl.php';
	else if($unconfirmed[$i]['section']==2)
		require 'themes/'.$theme.'/templates/view_all/art.tpl.php';
	else if($unconfirmed[$i]['section']==3)
		require 'themes/'.$theme.'/templates/view_all/gallery.tpl.php';
}

require 'themes/'.$theme.'/templates/footer.tpl.php';
?>