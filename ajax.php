<?
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/auth.class.php');

switch($_POST['task']){
    case 'editNews':
        if($_SESSION['user_admin']>=1)
            editNews((int)$_POST['id']);
        else
            echo 'Permisson denied';
    break;
}

function editNews($nid){
    $baseC = new base();
    echo $baseC->html2Tex($baseC->eread('news', 'text', null, 'id', $nid));
}
?>