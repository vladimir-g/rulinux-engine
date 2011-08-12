<?php
require 'classes/core.php';
$title = ' - Поиск';
$rss_link='rss';
require 'header.php';
if(!empty($_GET['q']))
{
	$search_user = $_GET['username'];
	$search_string = $_GET['q'];
	if($_GET['filter_search']=='yes')
	{
		$fil_srch_yes_ch = 'checked';
		$fil_srch_no_ch = '';
	}
	else
	{
		$fil_srch_yes_ch = '';
		$fil_srch_no_ch = 'checked';
	}
	if($_GET['search_method']=='or')
	{
		$method = 'or';
		$srch_mthd_and_ch = '';
		$srch_mthd_or_ch = 'checked';
	}
	else
	{
		$method = 'and';
		$srch_mthd_and_ch = 'checked';
		$srch_mthd_or_ch = '';
	}
	require 'themes/'.$theme.'/templates/search/form_top.tpl.php';
	$filters_arr = array();
	$filters = $filtersC->get_filters();
	for($i=0; $i<count($filters);$i++)
	{
		$filter_name = $filters[$i]['name'];
		$filter_id = $filters[$i]['id'];
		if($_GET[$filter_id] == 'yes')
		{
			$filters_arr[$i] = $filter_id.':1';
			$yes_checked = 'checked';
			$no_checked = '';
			$n_m_checked = '';
		}
		else if($_GET[$filter_id] == 'no')
		{
			$filters_arr[$i] = $filter_id.':0';
			$yes_checked = '';
			$no_checked = 'checked';
			$n_m_checked = '';
		}
		else
		{
			$yes_checked = '';
			$no_checked = '';
			$n_m_checked = 'checked';
		}
		require 'themes/'.$theme.'/templates/search/form_middle.tpl.php';
	}
	require 'themes/'.$theme.'/templates/search/form_bottom.tpl.php';
	if($_GET['filter_search'] == 'no')
		$found_msg = $searchC->find($_GET['q'], $_GET['require'], $_GET['date'], $_GET['section'], $_GET['username']);
	else
		$found_msg = $searchC->find_by_filters($_GET['q'], $_GET['require'], $_GET['date'], $_GET['section'], $_GET['username'], $method, $filters_arr);
	if(!empty($found_msg))
	{
		for($i=0; $i<count($found_msg); $i++)
		{
			$msg_id = $found_msg[$i]['id'];
			$message_number = $threadsC->get_msg_number_by_tid($found_msg[$i]['tid'], $msg_id);
			$page = ceil($message_number/$uinfo['comments_on_page']);
			if($page == 0)
				$page = 1;
			$link = 'thread_'.$found_msg[$i]['tid'].'_page_'.$page.'#msg'.$msg_id;
			$subject = $found_msg[$i]['subject'];
			$comment = $found_msg[$i]['comment'];
			$usr = $usersC->get_user_info($found_msg[$i]['uid']);
			$author = $usr['nick'];
			$author_profile = 'user_'.$usr['nick'];
			$timestamp = $coreC->to_local_time_zone($found_msg[$i]['timest']);
			require 'themes/'.$theme.'/templates/search/msg.tpl.php';
		}
	}
}
else
{
	$search_user ='';
	$search_string = '';
	$fil_srch_yes_ch = '';
	$fil_srch_no_ch = 'checked';
	$srch_mthd_and_ch = 'checked';
	$srch_mthd_or_ch = '';
	require 'themes/'.$theme.'/templates/search/form_top.tpl.php';
	$filters = $filtersC->get_filters();
	for($i=0; $i<count($filters);$i++)
	{
		$filter_name = $filters[$i]['name'];
		$filter_id = $filters[$i]['id'];
		$n_m_checked = 'checked';
		require 'themes/'.$theme.'/templates/search/form_middle.tpl.php';
	}
	require 'themes/'.$theme.'/templates/search/form_bottom.tpl.php';
}
require 'footer.php';
?>