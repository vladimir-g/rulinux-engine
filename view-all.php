<?php
require 'classes/core.php';
$title = ' - Просмотр неподтвержденных';
$rss_link='rss';
require 'header.php';
require 'themes/'.$theme.'/templates/view_all/top.tpl.php';
$user_filter = $usersC->get_filter($_SESSION['user_id']);
$user_filter_arr = $filtersC->parse_filter_string($user_filter);
$user_filter_list = $filtersC->get_filter_list($user_filter);
$unconfirmed = $threadsC->get_unconfirmed();
for ($i = 0; $i < count($unconfirmed); $i++)
{
	$thread_id = $unconfirmed[$i]['id'];
	$comment_id = $unconfirmed[$i]['cid'];
	$filter_list = $filtersC->get_filter_list($unconfirmed[$i]['filters']);
	$active_filters = $filtersC->get_active_filters($filter_list, $user_filter_list);
	$is_filtered = $messagesC->is_filtered($user_filter_arr, $unconfirmed[$i]['filters']);
	$subject = $unconfirmed[$i]['subject'];
	$comment = $unconfirmed[$i]['comment'];
	$usr = $usersC->get_user_info($unconfirmed[$i]['uid']);
	$coreC->validate_boolean($usr['banned']) ? $author = '<s>'.$usr['nick'].'</s>' :$author = $usr['nick'];
	$author_profile = 'user_'.$usr['nick'];
	$timestamp = $coreC->to_local_time_zone($unconfirmed[$i]['timest']);
	$aprove_link = 'approve_thread_'.$thread_id;
	$edit_link = 'message_'.$comment_id.':edit';
	$thr_link = 'thread_'.$thread_id.'_page_1';
        switch ($unconfirmed[$i]['section'])
	{
	case NEWS_SECTION_ID:
		$subsection_image = '/themes/'.$theme.'/icons/'.sections::get_subsection_icon($unconfirmed[$i]['subsection']);
		if (!empty($unconfirmed[$i]['prooflink']))
			$prooflink = '>>> <a href="'.$unconfirmed[$i]['prooflink'].'">Подробнее</a>';
		else
			$prooflink = '';
		require 'themes/'.$theme.'/templates/view_all/news.tpl.php';
		break;
	case ARTICLES_SECTION_ID:
		require 'themes/'.$theme.'/templates/view_all/art.tpl.php';
		break;
	case GALLERY_SECTION_ID:
		$img_link = '/images/gallery/'.$unconfirmed[$i]['file'].'.'.$unconfirmed[$i]['extension'];
		$img_thumb_link = '/images/gallery/thumbs/'.$unconfirmed[$i]['file'].'_small.png';
		$size = $unconfirmed[$i]['image_size'].', '.$unconfirmed[$i]['file_size'];
		require 'themes/'.$theme.'/templates/view_all/gallery.tpl.php';
		break;
	}
}
require 'footer.php';
?>