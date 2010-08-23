<?
$content['title'] = 'Последние комментарии';
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('incs/header.inc.php');
echo '<!--content section begin-->';
echo '<h1>'.$content['title'].'</h1>';
if(preg_match('/^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/', $_GET['user']) || !isset($_GET['user']))
    (int)$uid = isset($_GET['user']) ? ($_GET['user'] == 'anonymous' ? -1 : base::eread('users', 'id', '', 'nick', $_GET['user'])) : $_SESSION['user_login'];
else{
    echo 'Такой пользователь не найден';
    echo '<!--content section end-->';
    require_once('incs/bottom.inc.php');
    die();
}?>
<? if ($uid != 0): ?>
<table width="100%" class="message-table">
<thead>
<tr><th>Раздел</th><th>Группа</th><th>Заглавие темы</th><th>Дата</th></tr>
<tbody>
<?
$from = (int)$_GET['offset'];
$news_comments = users::get_comments_by_uid($uid, 'timestamp', 'DESC', false, $from, 50);
if(sizeof($news_comments) > 0){
    foreach($news_comments as $comment){
        $section = isset($comment['cid']) ? 'Новости' : 'Форум';
        $link = isset($comment['cid']) ? 'message.php?newsid='.$comment['tid'] : 'view-message.php?forumid='.$comment['fid'].'&threadid='.$comment['tid'];
        echo '<tr><td>'.$section.'</td><td>'.$comment['name'].'</td><td><a href="'.$link.'&'.time().'" rev=contents>'.$comment['title'].'</a></td><td>'.base::timeToSTDate($comment['timestamp']).'</td></tr>';
    }
}
$bk = $from <= 0 ? '#' : ($from < 50 ? 'view-comments.php' : 'view-comments.php?offset='.($from-50));
$fw = $from <= 0 ? 'view-comments.php?offset=50' : 'view-comments.php?offset='.($from+50);
?>
</tbody>
<tfoot>
<tr><th><a href="<?=$bk?>"><< Назад</a></th><th></th><th></th><th><a href="<?=$fw?>">Далее >></a></th></tr>
</tfoot>
</table>
<h2>Последние 20 удаленных комментариев</h2>
</table>
<table width="100%" class="message-table">
<thead>
<tr><th>Раздел</th><th>Группа</th><th>Заглавие темы</th><th>Дата</th><th>Удалено</th><th>Причина</th></tr>
<tbody>
<?
$news_comments = users::get_comments_by_uid($uid, 'timestamp', 'DESC', true, 0, 20);
if(sizeof($news_comments) > 0){
    foreach($news_comments as $comment){
        $section = isset($comment['cid']) ? 'Новости' : 'Форум';
        $link = isset($comment['cid']) ? 'message.php?newsid='.$comment['tid'] : 'view-message.php?forumid='.$comment['fid'].'&threadid='.$comment['tid'];
        echo '<tr><td>'.$section.'</td><td>'.$comment['name'].'</td><td><a href="'.$link.'&'.time().'" rev=contents>'.$comment['title'].'</a></td><td>'.base::timeToSTDate($comment['timestamp']).'</td><td>'.$comment['del_by'].'</td><td>'.$comment['deleted_for'].'</td></tr>';
    }
}
?>
</tbody>
</table>
<? endif; ?>
<?
echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>