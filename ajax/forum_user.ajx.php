<?
include('../incs/db.inc.php');
require_once('../classes/auth.class.php');
require_once('../classes/config.class.php');
require_once('../classes/users.class.php');
header('Content-Type: text/html; charset='.$db_charset);
?>
<? if ($_SESSION['user_admin']>=1): ?>
<?
   if(isset($_GET['purge'])){
      if($_GET['purge'] == 'true'){
         base::del_row('forum_messages', 'forum_id='.$_GET['fid'].' AND thread_id='.$_GET['tid']);
         base::del_row('forum_threads', 'forum_id='.$_GET['fid'].' AND thread_id='.$_GET['tid']);
         echo 'purged';
      }
      else{
         echo base::erewrite('forum_threads', 'stat', 'deleted', $_GET['tid'], 'thread_id');
         echo base::erewrite('forum_threads', 'delete_reason', $_GET['reason'], $_GET['tid'], 'thread_id');
         echo base::erewrite('forum_threads', 'delete_moder', $_SESSION['user_name'], $_GET['tid'], 'thread_id');
         echo 'hidden';
      }
   }
?>
<? endif; ?>