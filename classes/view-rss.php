<?
require_once('incs/db.inc.php');
require_once('classes/rss.class.php');
require_once('classes/config.class.php');

$template = file_get_contents('subscribe.xml');
$listTemplate = file_get_contents('subscribe-list.xml');
$body = $template;

switch($_GET['section']){
     case 'news':
          if ((int)$_GET['newsid'] > 0)
               $list = rss::recordsGetLast('news', $_GET['newsid']);
          else
               $list = rss::recordsGetLast('news');
          if ((int)$_GET['newsid'] > 0)
               $body = str_replace('${pageTitle}', 'RULINUX.ORG - Новости - '.$list[0]->pageTitle, $body);
          else
               $body = str_replace('${pageTitle}', 'RULINUX.ORG - Новости', $body);
          $body = str_replace('${lastDate}', gmdate('D, d M Y H:i:s O', $list[0]->approve_time), $body);
          $listBody = '';
          foreach ($list as $item){
               $temp = $listTemplate;
               $temp = str_replace('${author}', $item->nick, $temp);
               if ((int)$_GET['newsid'] > 0)
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$_GET['newsid'].'#'.$item->cid, $temp);
               else
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$item->id, $temp);
               $temp = str_replace('${group}', 'news.php?cid='.$item->cid, $temp);
               $temp = str_replace('${title}', $item->title, $temp);
               $time = getdate($item->approve_time);
               $temp = str_replace('${date}', gmdate('D, d M Y H:i:s O', $item->approve_time), $temp);
               $temp = str_replace('${text}', htmlspecialchars($item->text), $temp);
               $listBody .= $temp;
          }
          if (sizeof($list) > 0)
               $body = str_replace('${list}', $listBody, $body);
     break;
     case 'forum':
          if ((int)$_GET['newsid'] > 0)
               $list = rss::recordsGetLast('news', $_GET['newsid']);
          else
               $list = rss::recordsGetLast('forum');
          if ((int)$_GET['newsid'] > 0)
               $body = str_replace('${pageTitle}', 'RULINUX.ORG - Новости - '.$list[0]->pageTitle, $body);
          else
               $body = str_replace('${pageTitle}', 'RULINUX.ORG - Форум', $body);
          $body = str_replace('${lastDate}', gmdate('D, d M Y H:i:s O', $list[0]->posting_date), $body);
          $listBody = '';
          foreach ($list as $item){
               $temp = $listTemplate;
               $temp = str_replace('${author}', $item->nick, $temp);
               if ((int)$_GET['newsid'] > 0)
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$_GET['newsid'].'#'.$item->cid, $temp);
               else
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$item->id, $temp);
               $temp = str_replace('${group}', 'news.php?cid='.$item->cid, $temp);
               $temp = str_replace('${title}', $item->forumName, $temp);
               $time = getdate($item->approve_time);
               $temp = str_replace('${date}', gmdate('D, d M Y H:i:s O', $item->posting_date), $temp);
               $temp = str_replace('${text}', htmlspecialchars($item->text), $temp);
               $listBody .= $temp;
          }
          if (sizeof($list) > 0)
               $body = str_replace('${list}', $listBody, $body);
     break;
}

echo $body;
?>