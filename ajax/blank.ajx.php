<?
include('../incs/db.inc.php');
require_once('../classes/auth.class.php');
require_once('../classes/config.class.php');
require_once('../classes/users.class.php');
header('Content-Type: text/html; charset='.$db_charset);
?>
<? if ($_SESSION['user_admin']>=1): ?>
<?
   
?>
<? endif; ?>