<?php
$filename = 'blocks/'.$directory.'/templates/top.tpl.php';
$file = fopen($filename, "r") or die("Can't open file!");
$boxlet_content = fread($file, filesize($filename));
fclose($file); 
$cnt = base::query('SELECT count(*) AS cnt FROM threads WHERE section=3', 'assoc_array', array());
if($cnt[0]['cnt']>2)
$max=3;
else
{
	if($cnt[0]['cnt']>0)
		$max=$cnt[0]['cnt'];
	else
		$max = 0;
}
for($y=0; $y<$max; $y++)
{
	$filename = 'blocks/'.$directory.'/templates/middle.tpl.php';
	$file = fopen($filename, "r") or die("Can't open file!");
	$boxlet_content = $boxlet_content.fread($file, filesize($filename));
	fclose($file); 
	$sel = base::query('SELECT max(id) AS max FROM threads WHERE section = 3','assoc_array');
	if(empty($end))
		$end = $sel[0]['max'];
	$ret = base::query('SELECT t.id, t.cid, c.subject, t.file, t.file_size, t.image_size, t.extension, c.uid, c.timest FROM threads t INNER JOIN comments c ON t.id = c.tid WHERE t.approved=true AND c.id IN (SELECT cid FROM threads WHERE t.section=3) ORDER BY id DESC LIMIT 3', 'assoc_array', array());
	$where_arr = array(array("key"=>'id', "value"=>$ret[$y]['uid'], "oper"=>'='));
	$author = base::select('users', '', 'nick', $where_arr);
	$boxlet_content = str_replace('[author]', $author[0]['nick'], $boxlet_content);
	$boxlet_content = str_replace('[img_thumb_link]', 'images/gallery/thumbs/'.$ret[$y]['file'].'_small.png', $boxlet_content);
	$boxlet_content = str_replace('[img_link]', 'images/gallery/'.$ret[$y]['file'].'.'.$ret[$y]['extension'], $boxlet_content);
	$boxlet_content = str_replace('[subject]', $ret[$y]['subject'], $boxlet_content);
	$timest = core::to_local_time_zone($ret[$y]['timest']);
	$boxlet_content = str_replace('[timestamp]', $timest, $boxlet_content);
	$boxlet_content = str_replace('[link]', 'messfge.php?newsid='.$ret[$y]['id'].'&page=1', $boxlet_content);
}
$boxlet_content = str_replace('[title]', $name, $boxlet_content);
?>