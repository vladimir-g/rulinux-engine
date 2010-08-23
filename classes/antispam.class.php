<?
class antispam{
   var $dict_file = '724a2acc2811511b04a53a75510b9d47.php';
   var $use = 'db'; # What to use: "files" or "db" for "file" variable $dict_file must be set
   var $session_min_interval = '5';
   var $session_max_interval = '3600';
   var $replacement = '[replaced by engine]';
   var $use_hiddens = true;

   function go($text, $request_array = 'post'){
       switch($request_array){
           case 'post': $request_array = $_POST; break;
           case 'get': $request_array = $_GET; break;
           default: $request_array = $_REQUEST; break;
       }
       if($this->use_hiddens && !empty($request_array['timestamp_as'])){
           switch($this->use){
               case 'files':
                   $time = file_get_contents(getcwd().'/as_sessions/'.$request_array['timestamp_as']);
                   unlink(getcwd().'/as_sessions/'.$request_array['timestamp_as']);
               break;
               case 'db':
                   $query = 'SELECT `time` FROM `as_sessions` WHERE `name` = \''.$request_array['timestamp_as'].'\'';
                   $time_res = mysql_query($query);
                   $time = mysql_fetch_object($time_res);
                   $time = $time->time;
                   mysql_query('DELETE FROM `as_sessions` WHERE `name` = \''.$request_array['timestamp_as'].'\'');
               break;
           }
           if(((time() - $time) < $this->session_min_interval) || ((time() - $time) > $this->session_max_interval))
               return false;
       }
       $data = $this->process_text($text);
       if($data === false){
         return false;
       }

       return $data;
   }

   function load_dict($separator = '|'){ # Loads dictionary with dissallowed words
       $ret = array();
       switch($this->use){
           case 'files':
            $dict = file($_SERVER['DOCUMENT_ROOT'].'/../'.$this->dict_file);
            foreach($dict as $line)
                $ret[] = explode($separator, $line);
            return $ret;
           break;
           case 'db':
            $query = 'SELECT DISTINCT CONCAT(action, words) words FROM as_expressions';
            $expr_res = mysql_query($query);
            while($expr = mysql_fetch_object($expr_res))
                $ret[] = explode($separator, $expr->words);
            return $ret;
           break;
           default:
               return -1;
           break;
       }
   }
   
   function add_hiddens(){ # Adds hidden fields for the anti-bot protection
       $time = time();
       $session_name = md5(time().getenv('REMOTE_ADDR').rand(1, 1000)).'.'.$time;

       switch($this->use){
           case 'files':
               $d = dir(getcwd().'/as_sessions');
               while(false != ($e = $d->read())){ # Clear unused sessions
                   if (preg_match('/.*?\.\d{1,}$/', $e) && filetype(getcwd().'/as_sessions/'.$e) != 'dir'){
                       $fn = explode('.', getcwd().'/as_sessions/'.$e);
                       if(time() - $fn[1] >= $this->session_max_interval)
                        unlink(getcwd().'/as_sessions/'.$e);
                   }
               }
               $id_file = fopen(getcwd().'/as_sessions/'.$session_name, 'w');
               fwrite($id_file, $time);
               fclose($id_file);
           break;
           case 'db':
               mysql_query('DELETE FROM as_sessions WHERE ('.$time.' - timestamp) >= '.$this->session_max_interval); # Clear previous unused sessions
               $query = 'INSERT INTO as_sessions VALUES (null, \''.$session_name.'\', '.$time.')'; # Insert session name into DB
               mysql_query($query);
           break;
           default:
               return -1;
           break;
       }
       echo '<input type="hidden" name="timestamp_as" value="'.$session_name.'" />';
   }

   function process_text($text){
       $dictionary = $this->load_dict();
       foreach($dictionary as $dic){
           $re = '';
           if(preg_match('/[0-9]/', substr($dic[0], 0))){
            $action = substr($dic[0], 0, 1);
            $dic[0] = substr($dic[0], 1);
           }
           foreach($dic as $num => $d){
               $d = preg_quote($d);
               $re .= '('.$d.')';
               if(($num + 1) < sizeof($dic))
                $re .= '.*?';
           }
           if(preg_match('@'.$re.'@si', $text)){
            switch($action){
                case 0:
                    foreach($dic as $d)
                        $text = preg_replace('@'.preg_quote($d).'@i', $this->replacement, $text);
                    return $text;
                break;
                case 1:
                    return false;
                break;
            }
           }
           else
            return $text;
       }
   }
}
?>