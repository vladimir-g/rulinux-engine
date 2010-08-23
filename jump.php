<?
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');

//Для новостей:
$newsId = (int)$_GET['newsid'];
//Для форума:
$forumId = (int)$_GET['forum'];
$threadId = (int)$_GET['thread'];
//коммент:
$cid = (int)$_GET['to'];
if($newsId > 0){
    if($cid <= 0)
        header('location: index.php');
}
if($forumId > 0 || $threadId > 0){
    if($forumId <= 0 || $threadId <= 0)
        header('location: index.php');
    elseif($forumId > 0 && $threadId > 0){
        if($cid <= 0)
            header('location: index.php');
    }
}
if($forumId > 0 && $threadId > 0 && $newsId > 0)
    header('location: index.php');
if($forumId <= 0 && $threadId <= 0 && $newsId <= 0)
    header('location: index.php');
$uinfo = users::get_user_info($_SESSION['user_login']);
$comments = $uinfo['comments_on_page'] ? $uinfo['comments_on_page'] : 50;

$elm = -1;
$i = 0;
if($newsId > 0){
    $id_field = 'cid';
    $ids = base::get_fields('comments', 'cid', 'timestamp ASC', 'deleted=0 AND tid='.$newsId);
}
elseif($forumId > 0 && $threadId > 0 && $cid > 0){
    $id_field = 'message_id';
    $ids = base::get_fields('forum_messages', 'message_id', 'posting_date ASC', 'stat=\'opened\' AND forum_id='.$forumId.' AND thread_id='.$threadId);
}
foreach($ids as $id){
    if($id[$id_field] == $cid){
        $elm = $i;
        break;
    }
    else
        $i++;
}
$page = (ceil($elm/$comments)-1);
if(is_int($elm/$comments)) $page++;
if($newsId > 0){
    header('location: message.php?newsid='.$newsId.'&page='.$page.'#'.$cid);
}
elseif($forumId > 0 && $threadId > 0 && $cid > 0){
    header('location: view-message.php?forumid='.$forumId.'&threadid='.$threadId.'&page='.$page.'#'.$cid);
}
//header('location: message.php?newsid='.$newsId.'&page='.$page.'#'.$cid);
?>