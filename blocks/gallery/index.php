<?php
$filename = 'blocks/'.$directory.'/templates/top.tpl.php';
$file = fopen($filename, "r") or die("Can't open gallery block top template");
$boxlet_content = fread($file, filesize($filename));
fclose($file); 

$user_filter = $usersC->get_filter($_SESSION['user_id']);
$user_filter_arr = $filtersC->parse_filter_string($user_filter);

$ret = base::query('SELECT t.id, t.cid, c.subject, t.file, t.extension, u.nick, c.timest, c.filters FROM threads t INNER JOIN comments c ON t.cid=c.id INNER JOIN users u ON c.uid=u.id WHERE t.approved=true AND t.section=3 ORDER BY t.id DESC LIMIT 3', 'assoc_array', array());

$item_tpl = 'blocks/'.$directory.'/templates/middle.tpl.php';
$tpl_file = fopen($item_tpl, "r") or die("Can't open gallery block middle template");
$tpl_size = filesize($item_tpl);
foreach ($ret as $item)
{
	rewind($tpl_file);
	$boxlet_content .= fread($tpl_file, $tpl_size);
	$boxlet_content = str_replace('[author]', $item['nick'], $boxlet_content);
	if ($messagesC->is_filtered($user_filter_arr, $item['filters']))
	{
		$subject = 'Сообщение отфильтровано в соответствии с вашими настройками фильтрации';
		$img_thumb_link = $img_link = 'themes/'.$theme.'/empty.gif';
        }
        else
	{
		$img_thumb_link = 'images/gallery/thumbs/'.$item['file'].'_small.png';
		$img_link = 'images/gallery/'.$item['file'].'.'.$item['extension'];
		$subject = $item['subject'];
	}
	$boxlet_content = str_replace('[img_thumb_link]', $img_thumb_link, $boxlet_content);
	$boxlet_content = str_replace('[img_link]', $img_link, $boxlet_content);
	$boxlet_content = str_replace('[subject]', $subject, $boxlet_content);
	$boxlet_content = str_replace('[timestamp]', core::to_local_time_zone($item['timest']), $boxlet_content);
	$boxlet_content = str_replace('[link]', 'thread_'.$item['id'].'_page_1', $boxlet_content);
}
fclose($tpl_file);
$boxlet_content = str_replace('[title]', $name, $boxlet_content);
?>