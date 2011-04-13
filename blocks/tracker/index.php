<?php
$filename = 'blocks/'.$directory.'/templates/top.tpl.php';
$file = fopen($filename, "r") or die("Can't open file!");
$boxlet_content = fread($file, filesize($filename));
fclose($file); 
$msg = messages::get_messages_for_tracker(1);;
if(!empty($msg))
{
	for($z=0; $z<count($msg); $z++)
	{
		$filename = 'blocks/'.$directory.'/templates/message.tpl.php';
		$file = fopen($filename, "r") or die("Can't open file!");
		$boxlet_content = $boxlet_content.fread($file, filesize($filename));
		fclose($file); 
		$subj = substr($msg[$z]['subject'], 0, 128);
		$boxlet_content = str_replace('[subject]', $subj, $boxlet_content);
		$comment = substr($msg[$z]['comment'], 0, 255);
		$boxlet_content = str_replace('[comment]', $comment, $boxlet_content);
		$author = users::get_user_info($msg[$z]['uid']);
		$boxlet_content = str_replace('[author]', $author['nick'], $boxlet_content);
		$page = core::get_page_by_tid($msg[$z]['tid'], $msg[$z]['id'], $uinfo['comments_on_page']);
		$link = 'message.php?newsid='.$msg[$z]['tid'].'&page='.$page.'#'.$msg[$z]['id'];
		$boxlet_content = str_replace('[link]', $link, $boxlet_content);
	}
}
$boxlet_content = str_replace('[title]', $name, $boxlet_content);
?>