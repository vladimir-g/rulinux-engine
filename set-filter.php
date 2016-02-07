<?
$message_id = (int)$_GET['id'];
require 'classes/core.php';
$rss_link='rss';
if(!empty($_POST['sbm']))
{
	if($_POST['msg_uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
	{
		for($i=1; $i<=$_POST['filters_count']; $i++)
		{
			if(!empty($_POST['filter_'.$i]))
				$str = $str.$i.':1;';
			else
				$str = $str.$i.':0;';
		}
		$val = $messagesC->set_filter($message_id, $str);
		$mess_arr = $threadsC->get_msg_number_by_cid($message_id);
		$message_number = $mess_arr[0];
		$thread_id = $mess_arr[1];
		$msg_id = $mess_arr[2];
		$page = ceil($message_number/$uinfo['comments_on_page']);
		require 'header.php';
		$legend = 'Фильтры успешно изменены';
		$text = 'Фильтры успешно изменены<br>Через три секунды вы будете перенаправлены в тред.<br>Если вы не хотите ждать, нажмите <a href="thread_'.$thread_id.'_page_'.$page.'#msg'.$msg_id.'">сюда</a>.';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		die('<meta http-equiv="Refresh" content="3; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'thread_'.$thread_id.'_page_'.$page.'#msg'.$msg_id.'">');  
	}
}
$title = ' - Установить фильтр на сообщение';
$msg = $messagesC->get_message($message_id);
if($_SESSION['user_id'] == 1)
{
	if($msg['session_id'] != session_id())
	{
		require 'header.php';
		$legend = 'Действие запрещено';
		$text = 'Вы не можете выставлять фильтры на это сообщение';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
}
if($msg['uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
{
	require 'header.php';
	require 'themes/'.$theme.'/templates/set_filter/top.tpl.php';
	$filter_str = $messagesC->get_filter($message_id);
	$filtered = $filtersC->parse_filter_string($filter_str);
	$filters_arr = $filtersC->get_filters();
	for($i=0; $i<count($filters_arr);$i++)
	{
		$filterN = $filters_arr[$i]['id'];
		$filter_name = $filters_arr[$i]['name'];
		if($filtered[$i][1]==0)
			$checked_filter = '';
		else
			$checked_filter = 'checked';
		require 'themes/'.$theme.'/templates/set_filter/middle.tpl.php';
	}
	$filters_count = count($filters_arr);
	$msg_uid = $msg['uid'];
	require 'themes/'.$theme.'/templates/set_filter/bottom.tpl.php';
	require 'footer.php';
}
else
{
	require 'header.php';
	$legend = 'Действие запрещено';
	$text = 'Вы не можете выставлять фильтры на это сообщение';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
}
?>