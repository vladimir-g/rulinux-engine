<?
include('incs/db.inc.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/config.class.php');
$info = users::get_user_info($_SESSION['user_login']);
(int)$_SESSION['user_login'] ? $tpl_name= $info['theme'] : $tpl_name = base::check_setting('template');
?>
<html lang=ru>
<head><title>Удалить</title>
<?
						$header_tpl = 'design/'.$tpl_name.'/templates/head.tpl';
						include($header_tpl);
?>
<form action="<?=$path?>forum.php" method="POST">
<?
if($_GET['action']=='delete_thread')
{
	?>
	<input type="hidden" name="action" value="delete_thread">
	<input type="hidden" name="forumid" value="<?=$_GET['forumid']?>">
	<input type="hidden" name="threadid" value="<?=$_GET['threadid']?>">
	<?
	if(!$_SESSION['user_admin'])
	{
		echo "Вы не можете удалить это сообщение";
		exit();
	}
}
	else if($_GET['action']=='delete')
{
	$thr_id = $_GET['threadid'];
	$forumid = $_GET['forumid'];
	$messageid = $_GET['messageid'];
	$ath = mysql_query("SELECT user_name FROM forum_messages WHERE thread_id = '$thr_id' AND forum_id = '$forumid' AND message_id = '$messageid'");
	$mes = mysql_fetch_array($ath);
	$username = $mes['user_name'];
	if(!$_SESSION['user_admin'] && $username != $_SESSION['user_name'])
	{
		echo "Вы не можете удалить это сообщение";
		exit();
	}
	?>
	<input type="hidden" name="action" value="delete">
	<input type="hidden" name="forumid" value="<?print $_GET['forumid']?>">
	<input type="hidden" name="threadid" value="<?print $_GET['threadid']?>">
	<input type="hidden" name="messageid" value="<?print $_GET['messageid']?>">
	<?
}
else
{
	echo "Неизвестный параметр";
				$tail_tpl = 'design/'.$tpl_name.'/templates/tail.tpl';
				include($tail_tpl);
	exit();
}
?>
<input type="hidden" name="deleter" value="<?=$_SESSION['user_name']?>">
<input type="text" name="purpose" value="<? print $reason?>" style="width:55%">
<br>
<br><input type="submit" value="Удалить">
</form>
<div style="clear: both;"></div>
</div>
<?
				$tail_tpl = 'design/'.$tpl_name.'/templates/tail.tpl';
				include($tail_tpl);
?>