<?
$message_id = (int)$_GET['id'];
require 'classes/core.php';
$rss_link='view-rss.php';
if(!empty($_POST['sbm']))
{
	if($_SESSION['user_id'] == 1)
	{
		$legend = 'Действие запрещено';
		$text = 'Вы не можете выставлять фильтры на это сообщение';
		require 'themes/'.$theme.'/templates/fieldset.tpl.php';
		require 'footer.php';
		exit();
	}
	if($_POST['msg_uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
	{
		for($i=1; $i<=$_POST['filters_count']; $i++)
		{
			if(!empty($_POST['filter_'.$i]))
				$str = $str.$i.':1;';
			else
				$str = $str.$i.':0;';
		}
		$val = messages::set_filter($message_id, $str);
		$param_arr = array($message_id);
		$sel = base::query('SELECT tid,md5 FROM comments WHERE id = \'::0::\'','assoc_array', $param_arr);
		$thread_id = $sel[0]['tid'];
		$param_arr = array($thread_id);
		$sel = base::query('SELECT id FROM comments WHERE tid = \'::0::\' AND id>(SELECT min(id) FROM comments WHERE tid=\'::0::\') ORDER BY id','assoc_array', $param_arr);
		for($i=0;$i<count($sel);$i++)
		{
			if($sel[$i]['id']==$message_id)
				$message_number = $i+1;
		}
		$page = ceil($message_number/$uinfo['comments_on_page']);
		die('<meta http-equiv="Refresh" content="0; URL=http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'message.php?newsid='.$thread_id.'&page='.$page.'">');  
	}
}
$title = ' - Установить фильтр на сообщение';
if($_SESSION['user_id'] == 1)
{
	require 'header.php';
	$legend = 'Действие запрещено';
	$text = 'Вы не можете выставлять фильтры на это сообщение';
	require 'themes/'.$theme.'/templates/fieldset.tpl.php';
	require 'footer.php';
	exit();
}
$msg = messages::get_message($message_id);
if($msg['uid'] == $_SESSION['user_id'] || $uinfo['gid']==2 || $uinfo['gid']==3)
{
	require 'header.php';
	require 'themes/'.$theme.'/templates/set_filter/top.tpl.php';
	$filter_str = messages::get_filter($message_id);
	$filtered = filters::parse_filter_string($filter_str);
	$filters_arr = filters::get_filters();
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