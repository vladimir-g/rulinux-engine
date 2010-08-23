<?
include_once('./incs/db.inc.php');
require_once('./classes/config.class.php');
require_once('./classes/users.class.php');
require_once('./classes/pages.class.php');
include_once('./classes/auth.class.php');
include_once('./incs/header.inc.php');
if($_SESSION['user_admin'] < 1){
   echo '<h1>Пока рано</h1>';
   include_once('./incs/bottom.inc.php');
   exit();
}
function find_id($array, $find_key, $find_value){
   foreach($array as $key => $arr){
      if($arr[$find_key] == $find_value)
         return $key;
   }
}
/*$common_ids = base::get_fields('forum_messages', 'message_id', 'posting_date', 'response_to = 7953 AND forum_id=9 AND thread_id=792 AND stat=\'opened\'');
print_r($common_ids);*/
$msg_id = (int)$_GET['msgid'];
$topic = base::get_fields('forum_messages', 'title, forum_id, thread_id, message_text, user_name, posting_date, response_to', '', 'message_id='.$msg_id);
$same_level = base::get_fields('forum_messages', 'message_id', 'posting_date', 'forum_id='.$topic[0]['forum_id'].' AND thread_id='.$topic[0]['thread_id'].' AND response_to='.$topic[0]['response_to']);
$parent_level_id = base::get_fields('forum_messages', 'message_id, response_to', 'posting_date', 'forum_id='.$topic[0]['forum_id'].' AND thread_id='.$topic[0]['thread_id'].' AND message_id='.$topic[0]['response_to']);
$parent_level = base::get_fields('forum_messages', 'message_id', 'posting_date', 'forum_id='.$topic[0]['forum_id'].' AND thread_id='.$topic[0]['thread_id'].' AND response_to='.$parent_level_id[0]['response_to']);
foreach($same_level as $k => $sl){
   $same_level[$k] = $same_level[$k]['message_id'];
}
foreach($parent_level as $k => $pl){
   $parent_level[$k] = $parent_level[$k]['message_id'];
}
print_r($same_level);
print_r($parent_level);
if ((array_search($msg_id, $same_level) + 1) == sizeof($same_level)){
   $next = $parent_level[array_search($parent_level_id[0]['message_id'], $parent_level) + 1];
   $prev = $parent_level[array_search($parent_level_id[0]['message_id'], $parent_level) - 1];
}
echo '<div style="float:left"><a href="watch-thread.php?msgid='.$prev.'"><h1>&larr;</h1></a></div>';
echo '<div style="float:right"><a href="watch-thread.php?msgid='.$next.'"><h1>&rarr;</h1></a></div>';
echo '<div style="width:90%; display:table; text-align:center"><h1>'.$topic[0]['title'].'</h1></div>';
echo '<div style="border:dashed 2px gray; width:97%; display:table; padding-left:20px; padding-right:20px">';
echo '<p style="font-size: 20pt; margin-bottom:0px;margin-top:10px"><strong><em>'.$topic[0]['user_name'].' ('.base::timeTOStDate($topic[0]['posting_date']).'):</em></strong></p>';
echo $topic[0]['message_text'];
echo '</div>';

include_once('./incs/bottom.inc.php');
?>