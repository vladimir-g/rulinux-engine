<?
$message_id = (int)$_GET['id'];
require 'classes/core.php';
$title = ' - Показать сообщение';
$rss_link='view-rss.php';
require 'header.php';
echo '<br>';
$msg = $messagesC->get_message($message_id);
$msg_resp = $messagesC->get_message($msg['referer']);
if(!empty($msg_resp))
{
	$message_resp_title = $msg_resp['subject'];
	$message_resp_timestamp = $coreC->to_local_time_zone($msg_resp['timest']);
	$msg_resp_autor = $usersC->get_user_info($msg_resp['uid']);
	$message_resp_user = $msg_resp_autor['nick'];
	$message_resp_link = 'message.php?newsid='.$msg['tid'].'#'.$msg['referer'];
}
$message_subject = $msg['subject'];
$message_comment = $msg['comment'];
$msg_autor = $usersC->get_user_info($msg['uid']);
$coreC->validate_boolean($msg_autor['banned']) ? $message_autor = '<s>'.$msg_autor['nick'].'</s>' :$message_autor = $msg_autor['nick'];
$message_autor_profile_link = '/profile.php?user='.$msg_autor['nick'];
if(!$coreC->validate_boolean($msg['show_ua']))
	$message_useragent = '';
else
	$message_useragent = $msg['useragent'];
$message_timestamp = $coreC->to_local_time_zone($msg['timest']);
$message_add_answer_link = 'comment.php?answerto='.$thread_id.'&cid='.$message_id;
$message_avatar = empty($msg_autor['photo'])? 'themes/'.$theme.'/empty.gif' : 'images/avatars/'.$msg_autor['photo'];
require 'themes/'.$theme.'/templates/message/message.tpl.php';
require 'footer.php';
?>