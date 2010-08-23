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
               $body = str_replace('${pageTitle}', 'RULINUX.NET - Новости - '.$list[0]->pageTitle, $body);
          else
               $body = str_replace('${pageTitle}', 'RULINUX.NET - Новости', $body);
          $body = str_replace('${lastDate}', gmdate('D, d M Y H:i:s O', $list[0]->approve_time), $body);
          $listBody = '';
          foreach ($list as $item){
               $temp = $listTemplate;
               $temp = str_replace('${author}', $item->by, $temp);
               if ((int)$_GET['newsid'] > 0)
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$_GET['newsid'].'#'.$item->cid, $temp);
               else
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$item->id, $temp);
               $temp = str_replace('${group}', 'news.php?cid='.$item->cid, $temp);
               $temp = str_replace('${guid}', md5(rand(time(), time() + time())), $temp);
               $temp = str_replace('${title}', $item->title, $temp);
               $time = getdate($item->approve_time);
               $temp = str_replace('${date}', gmdate('D, d M Y H:i:s O', $item->approve_time), $temp);
               $temp = str_replace('${text}', $item->text, $temp);
               $listBody .= $temp;
          }
          if (sizeof($list) > 0)
               $body = str_replace('${list}', $listBody, $body);
     break;
     case 'forum':
          if ((int)$_GET['newsid'] > 0)
               $list = rss::recordsGetLast('forum', $_GET['newsid']);
          else
               $list = rss::recordsGetLast('forum');
          $pt = end($list);
          if ((int)$_GET['newsid'] > 0)
               $body = str_replace('${pageTitle}', 'RULINUX.NET - Форум - '.$pt->pageTitle, $body);
          else
               $body = str_replace('${pageTitle}', 'RULINUX.NET - Форум', $body);
          $body = str_replace('${lastDate}', gmdate('D, d M Y H:i:s O', $list[0]->posting_date), $body);
          $listBody = '';
          foreach ($list as $item){
               $temp = $listTemplate;
               $temp = str_replace('${author}', $item->by, $temp);
               if ((int)$_GET['newsid'] > 0)
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$item->tid.'#'.$item->cid, $temp);
               else
                    $temp = str_replace('${msglink}', 'message.php?newsid='.$item->tid, $temp);
               $temp = str_replace('${group}', 'forum-'.$item->rewrite, $temp);
               $temp = str_replace('${guid}', md5(rand(time(), time() + time())), $temp);
               $temp = str_replace('${title}', $item->forumName.' - '.$item->threadName, $temp);
               $time = getdate($item->approve_time);
               $temp = str_replace('${date}', gmdate('D, d M Y H:i:s O', $item->posting_date), $temp);
               $temp = str_replace('${text}', mb_substr($item->text, 0, 512, 'utf-8').(mb_strlen($item->text, 'utf-8') > 512 ? '...' : ''), $temp);
               $listBody .= $temp;
          }
          if (sizeof($list) > 0)
               $body = str_replace('${list}', $listBody, $body);
     break;
     case 'articles':
          $list = rss::recordsGetLast('articles');
          $pt = end($list);
          $body = str_replace('${pageTitle}', 'RULINUX.NET - Статьи - '.$list[0]->forumName, $body);
          $body = str_replace('${lastDate}', gmdate('D, d M Y H:i:s O', $list[0]->posting_date), $body);
          $listBody = '';
          foreach ($list as $item){
               $temp = $listTemplate;
               $temp = str_replace('${author}', $item->by, $temp);
               $temp = str_replace('${msglink}', 'view-article.php?aid='.$item->tid, $temp);
               $temp = str_replace('${group}', 'forum-'.$item->rewrite, $temp);
               $temp = str_replace('${guid}', md5(rand(time(), time() + time())), $temp);
               $temp = str_replace('${title}', $item->forumName.' - '.$item->threadName, $temp);
               $time = getdate($item->approve_time);
               $temp = str_replace('${date}', gmdate('D, d M Y H:i:s O', $item->timestamp), $temp);
               $temp = str_replace('${text}', mb_substr($item->text, 0, 512, 'utf-8').(mb_strlen($item->text, 'utf-8') > 512 ? '...' : ''), $temp);
               $listBody .= $temp;
          }
          if (sizeof($list) > 0)
               $body = str_replace('${list}', $listBody, $body);
     break;
     case 'tracker':
          $list = rss::recordsGetLast('tracker');
          //$pt = end($list);
          $body = str_replace('${pageTitle}', 'RULINUX.NET - Трекер', $body);
          $body = str_replace('${lastDate}', gmdate('D, d M Y H:i:s O', $list[0]->timestamp), $body);
          $listBody = '';
          foreach ($list as $item){
               $temp = $listTemplate;
               $temp = str_replace('${author}', $item->by, $temp);
               $temp = str_replace('${msglink}', 'message.php?newsid='.$item->tid.'&fid='.$item->fid/*.'&page=$pages#'.$item->cid*/, $temp);
               $temp = str_replace('${guid}', md5(rand(time(), time() + time())), $temp);
               $temp = str_replace('${title}', $item->subject, $temp);
               $temp = str_replace('${date}', gmdate('D, d M Y H:i:s O', $item->timestamp), $temp);
               $temp = str_replace('${text}', mb_substr($item->text, 0, 512, 'utf-8').(mb_strlen($item->text, 'utf-8') > 512 ? '...' : ''), $temp);
               $listBody .= $temp;
          }
          if (sizeof($list) > 0)
               $body = str_replace('${list}', $listBody, $body);
     break;
}

echo $body;
?>