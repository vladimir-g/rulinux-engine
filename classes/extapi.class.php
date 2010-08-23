<?
class extAPI{
   var $apiVersion = '0.1';
   
   function clearcache(){
      if(mysql_query('DELETE FROM '.$GLOBALS['tbl_prefix'].'external_sessions WHERE auth_time < '.(time() - 3600)))
         echo 'CACHE CREARED';
   }
   function sendClientMessage($message, $type){
      $out_messages = array(
         100 => 'Success'
      );
      $output = '';
      $output .= $type.': '.($out_messages[$type])."\n";
      $output .= "$message\n";
      $output .= 'END';
      return $output;
   }
   function getClientMessage($message, $type){
      $out_messages = array(
         100 => 'Success'
      );
      $output = '';
      $output .= $type.': '.($out_messages[$type])."\n";
      $output .= "$message\n";
      $output .= 'END';
      return $output;
   }
}
?>