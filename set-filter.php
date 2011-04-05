<?
$message_id = (int)$_GET['id'];
require 'classes/core.php';
if(!empty($_POST['sbm']))
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
	//header('location: message.php?newsid='.$thread_id.'&page='.$page);
}
$user_theme = users::get_user_theme();
$theme = $user_theme['directory'];
$site_name = $_SERVER["HTTP_HOST"];
$title = 'Показать сообщение';
require 'links.php';
require 'themes/'.$theme.'/templates/header.tpl.php';
echo '<br />';
require 'themes/'.$theme.'/templates/set_filter/top.tpl.php';
echo '<br />';
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
echo '<br />';
$filters_count = count($filters_arr);
require 'themes/'.$theme.'/templates/set_filter/bottom.tpl.php';
require 'themes/'.$theme.'/templates/footer.tpl.php';
?>