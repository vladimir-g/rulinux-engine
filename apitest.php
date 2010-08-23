<?
include('incs/db.inc.php');
include('classes/extapi.class.php');
//extAPI::clearcache();
echo extAPI::sendClientMessage('some test message', 100);
?>