<?php
mysql_connect('localhost', 'root', 'hello_-s');
mysql_selectdb('cms');
$select_res = mysql_query('select id, timestamp from gallery where 1');
while ($select = mysql_fetch_object($select_res)){
     preg_match('/(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', $select->timestamp, $found);
     //preg_match('/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/', $select->timestamp, $found);
     $time = mktime($found[4], $found[5], $found[6], $found[2], $found[3], $found[1]);
     echo('UPDATE gallery SET timestamp = '.$time.' where id='.$select->id.';<br>');
}
?>