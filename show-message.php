<?
$message_id = (int)$_GET['id'];
require 'classes/core.php';
$title = ' - Показать сообщение';
$rss_link='rss';
require 'header.php';
echo '<br>';
$msg = $messagesC->get_message($message_id);
$msg_resp = $messagesC->get_message($msg['referer']);
$reply = '';
if(!empty($msg_resp))
{
	$message_resp_title = $msg_resp['subject'];
	$message_resp_timestamp = $coreC->to_local_time_zone($msg_resp['timest']);
	$msg_resp_autor = $usersC->get_user_info($msg_resp['uid']);
	$message_resp_user = $msg_resp_autor['nick'];
	$mess_arr = $threadsC->get_msg_number_by_cid($msg_resp['id']);
	$message_number = $mess_arr[0];
	$resp_page = ceil($message_number/$uinfo['comments_on_page']);
	if($resp_page == 0)
		$resp_page = 1;
	$message_resp_link = 'thread_'.$msg['tid'].'_page_'.$resp_page.'#'.$msg['referer'];
	$reply = 'Ответ на: <a href="'.$message_resp_link.'">'.$message_resp_title.'</a> от '.$message_resp_user.'  '.$message_resp_timestamp;
}
$message_subject = $msg['subject'];
$message_comment = $msg['comment'];
$msg_autor = $usersC->get_user_info($msg['uid']);
$coreC->validate_boolean($msg_autor['banned']) ? $message_autor = '<s>'.$msg_autor['nick'].'</s>' :$message_autor = $msg_autor['nick'];
$message_autor_profile_link = 'user_'.$msg_autor['nick'];
if(!$coreC->validate_boolean($msg['show_ua']))
	$message_useragent = '';
else
	$message_useragent = $msg['useragent'];
$message_timestamp = $coreC->to_local_time_zone($msg['timest']);
$message_add_answer_link = 'new_thread_into_'.$msg['tid'].'_on_'.$message_id;
$message_avatar = empty($msg_autor['photo'])? 'themes/'.$theme.'/empty.gif' : 'images/avatars/'.$msg_autor['photo'];
$message_set_filter_link = 'set_filter_'.$message_id;
$message_edit_link = 'message_'.$message_id.':edit';
require 'themes/'.$theme.'/templates/show_message/show_message.tpl.php';
require 'footer.php';
?>