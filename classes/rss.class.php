<?
class rss{
     function recordsGetLast($table, $id = 0){
          if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
               mysql_selectdb($GLOBALS['db_name']);
               mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
               switch($table){
                    case 'news':
                         if($id > 0)
                              $query = 'SELECT `'.$GLOBALS['db_prefix'].'comments`.`cid`, `'.$GLOBALS['db_prefix'].'comments`.`subject` `title`, `'.$GLOBALS['db_prefix'].'news`.`title` `pageTitle`, `'.$GLOBALS['db_prefix'].'comments`.`comment` `text`, `'.$GLOBALS['db_prefix'].'comments`.`uid`, `comments`.`timestamp` `approve_time`, `'.$GLOBALS['db_prefix'].'users`.`nick`
                                   FROM `'.$GLOBALS['db_prefix'].'comments`, `'.$GLOBALS['db_prefix'].'users`, `'.$GLOBALS['db_prefix'].'news`
                                   WHERE tid = '.$id.'
                                   AND `'.$GLOBALS['db_prefix'].'users`.`id` = `'.$GLOBALS['db_prefix'].'comments`.`uid`
                                   AND `'.$GLOBALS['db_prefix'].'news`.`id` = `'.$GLOBALS['db_prefix'].'comments`.`tid`
                                   ORDER BY `'.$GLOBALS['db_prefix'].'comments`.`timestamp`
                                   DESC LIMIT 0, 20';
                         else
                              $query = 'SELECT * FROM `'.$GLOBALS['db_prefix'].'news` WHERE cid > 0 ORDER BY `ontop` DESC, `approve_time` DESC, `timestamp` DESC LIMIT 0, 10';
                    break;
                    case 'forum':
                         if($id > 0){
                              $query = '
                                   SELECT `comments`.`cid` , `threads`.`tid`, `comments`.`subject` `threadName`, `forums`.`name` forumName, `forums`.`rewrite`, `comments`.`subject` `pageTitle` , `comments`.`comment` `text` , `comments`.`uid` , `comments`.`timestamp` `approve_time` , `users`.`nick` 
                                   FROM `comments` , `users` , `threads` , `forums`
                                   WHERE comments.tid = '.(int)$_GET['newsid'].'
                                   AND `users`.`id` = `comments`.`uid` 
                                   AND `threads`.`tid` = `comments`.`tid`
                                   AND `threads`.`fid` = `forums`.`forum_id`
                                   ORDER BY `comments`.`timestamp` DESC 
                                   LIMIT 0 , 20
                              ';
                         }
                         else{
                              $query = '
                                   SELECT DISTINCT `t`.`fid` , `t`.`tid`, `m`.`subject` `threadName` , `users`.`nick` , `t`.`posting_date` , `f`.`name` `forumName` , `f`.`rewrite` , `m`.`comment` `text` 
                                   FROM `threads` `t` , `forums` `f` , `comments` `m` , `users` 
                                   WHERE `f`.`forum_id` = `t`.`fid` 
                                   AND `m`.`fid` = `t`.`fid` 
                                   AND `m`.`tid` = `t`.`tid` 
                                   AND `m`.parent =0
                                   AND `m`.`fid` = '.(int)$_GET['group'].'
                                   AND `users`.`id` = `t`.`uid` 
                                   ORDER BY `posting_date` DESC 
                                   LIMIT 0 , 10
                              ';
                         }
                    break;
                    case 'articles':
                         $query = '
                              SELECT `articles`.`id` `tid`, `fid`, `title` threadName, `body` `text`, `timestamp`, `users`.`nick`, `forums`.name`forumName`
                              FROM `articles`, `users`, `forums`
                              WHERE `active` = 1
                              AND `users`.`id` = `uid`
                              AND `forums`.`forum_id` = `articles`.`fid`
                              AND `fid` = '.(int)$_GET['group'].'
                              AND `approoved` > 0
                              LIMIT 0, 20
                         ';
                    break;
                    case 'tracker':
                         $query = '
                              SELECT `c`.`timestamp`, `c`.`subject` , `c`.`cid` , `c`.`fid` , `c`.`tid` , `c`.`raw_comment` `text`, `u`.`nick` `by` 
                              FROM `comments` `c` , `users` `u` 
                              WHERE `c`.`timestamp` >= UNIX_TIMESTAMP() - (3600 * 3) 
                              AND `c`.`uid` = `u`.`id`
                         ';
                    break;
               }
               $ret = array();
               $news_res = mysql_query($query);
               while($news = mysql_fetch_object($news_res))
                    $ret[] = $news;
               return $ret;
          }
          else return -1;
     }
}
?>