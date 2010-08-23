<?
include('incs/db.inc.php');
include('classes/extapi.class.php');

//extAPI::clearcache();
if(isset($_GET['great'])){
   echo extAPI::sendClientMessage('Welcome to RULINUX', 100);
}
elseif(isset($_GET['cmd'])){
   $request = $_GET['cmd'];
   echo extAPI::getClientMessage($request);
}
?>