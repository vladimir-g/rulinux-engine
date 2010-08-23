<?
include_once($_SERVER['DOCUMENT_ROOT'].'/geshi/geshi.php');
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}
class base{
	function findInMultiArray($array, $needle){
		$ret = -1;
		if(sizeof($array)>0){
			foreach($array as $key => $arr){
				if(array_search($needle, $arr))
					return $key;
			}
		}
		return $ret;
	}
	function GetRealIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	function highlight($code, $lang, $path)
	{
		if(empty($lang))
		{
			$lang = 'c';
		}
		$geshi = new GeSHi($code, $lang);
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS,1);
		$code = geshi_highlight($code, $lang, $path, true);
		return $code;
	}

	function findFilthyLang($string)
	{
		$dbcnx = @mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']); 
		if (!$dbcnx) 
		{
			echo ("<P>В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно.</P>");
			$ret = 0;
		}
		mysql_query("SET NAMES utf8");
		$query = "SELECT * FROM `regexps` WHERE category=1";
		$ath = mysql_query($query);
		if($ath)
		{
			while($comments = mysql_fetch_array($ath))
			{
				if (preg_match('/([[:punct:]]|[[:space:]]|[[:cntrl:]]|^)'.$comments['regexp_body'].'([[:punct:]]|[[:space:]]|[[:cntrl:]]|$)/sim', $string)) 
				{
					return 1;
				} 
			}
		}
		else
		{
			echo "<p><b>Error: ".mysql_error()."</b></p>";
			return 0;
		}
		if(!mysql_close($dbcnx))
		{
			echo("Не удалось завершить соединение");
			return 0;
		}
		return 0;
	}
	
	function strToTeX($string)
	{
		//Проинклудим типограф:
		if(!class_exists('Typographus'))
		  include('classes/typograf.class.php');
		//$string = htmlspecialchars($string, ENT_QUOTES);
		$string = strip_tags($string);
		$string = preg_replace('#(\\\\user{)(.*?[^}]?)(})#sim', "<b><a href=\"/profile.php?user=$2\">$2</a></b>", $string);
		/*$re = "#(\\\\href\\[)?((https?|ftp)://\\S+[^\s.,>)\\];'\&quot;!?]*)?(\\]{)(.*?[^}]?)(})#sim";
		$vh = preg_match_all($re, $string, $match);
		for($i=0;$i<$vh;$i++)
		{
			$string = preg_replace($re, '<a href="$2">$5</a>', $string);
			$string = str_replace($match[6][$i], $with_breaks, $string);
		}*/
		//$string = preg_replace("#([^\\\"|>])((https?|ftp)://\S+[^\s.,>)\];'\&quot;!?]*)#", '&nbsp;<a href="\\2">\\2</a>', $string);
		if(preg_match("#\\\\img\[?(left|right|middle)?\]?{(.*?[^}]?)}#sim", $string, $match)){
			$align = $match[1];
			$match[2] = preg_replace('/http(s?)\:\/\//', 'imgh$1://', $match[2]);
			$string = preg_replace("#\\\\img\[?(left|right|middle)?\]?{(.*?[^}]?)}#sim", '<img src="'.$match[2].'" '.($align == '' ? '' : 'align="'.$align.'"').'>', $string);
		}
		$string = preg_replace("#((https?|ftp)://\S+[^\s.,>)\];'\&quot;!?]*)#", '&nbsp;<a href="\\0">\\0</a>', $string);
		$string = str_replace('imgh://', 'http://', $string);
		$string = str_replace('imghs://', 'https://', $string);
		$string = preg_replace("#(\\\\b{)(.*?[^}]?)(})#sim","<b>\$2</b>", $string);
		$string = preg_replace("#(\\\\i{)(.*?[^}]?)(})#sim","<i>\$2</i>", $string);
		$string = preg_replace("#(\\\\u{)(.*?[^}]?)(})#sim","<u>\$2</u>", $string);
		$string = preg_replace("#(\\\\s{)(.*?[^}]?)(})#sim","<s>\$2</s>", $string);
		$string = preg_replace("#(\\\\sub{)(.*?[^}]?)(})#sim","<sub>\$2</sub>", $string);
		$string = preg_replace("#(\\\\sup{)(.*?[^}]?)(})#sim","<sup>\$2</sup>", $string);
		$string = preg_replace("#\\\\br#sim","<br />", $string);

		$tags = array
		(
			'list' => '<ul>',
			'num' => '<ol>',
			'code' => '<fieldset style="border: 1px dashed black; padding:0px;"><ol style="background-color:#3d3d3d;" start="1">',
		);
		foreach ($tags as $tag => $val)
		{
        	
			if ($tag == 'list')
			{
				$re = '@\\\\(list)({)(.*?)([^\\*]})@sim';
				$vt = preg_match_all($re, $string, $match);
				for($i=0;$i<$vt;$i++)
				{
					$string = preg_replace($re, "$val$3</ul>", $string);
					$with_breaks = str_replace('{*}', '<li>&nbsp;', $match[3][$i]);
					$string = str_replace($match[3][$i], $with_breaks, $string);
				}
			}
			if ($tag == 'num')
			{
				$re = '@\\\\(num)({)(.*?)([^\\*]})@sim';
				$vt = preg_match_all($re, $string, $match);
				for($i=0;$i<$vt;$i++)
				{
					$string = preg_replace($re, "$val$3</ol>", $string);
					$with_breaks = str_replace('{*}', '<li>&nbsp;', $match[3][$i]);
					$string = str_replace($match[3][$i], $with_breaks, $string);
				}
			}
			if ($tag == 'code')
			{
				$re = '@\\\\(code)({)(.*?[^}]?)(})@sim';
				$vt = preg_match_all($re, $string, $match);
				for($i=0;$i<$vt;$i++)
				{
					$string = preg_replace($re, "$val$3</ol></fieldset>", $string);
					$with_breaks = preg_replace('/\n/', '<li style="background-color:#000000; padding-left: 5px; color: gray">&nbsp;', $match[3][$i]);
					$string = str_replace($match[3][$i], $with_breaks, $string);
				}
			}
		}
		
		$tags1 = array
		(
        	'center' => '<p align="center">',
			'flushleft' => '<p align="left">',
			'flushright' => '<p align="right">',
		);
		foreach ($tags1 as $tag1 => $val1)
		{
        	$re = '#(\\\\begin{'.$tag1.'})(.*?[^\\\\end{'.$tag1.'}]?)(\\\\end{'.$tag1.'})#sim';
			if ($tag1 == 'center' || $tag1 == 'flushleft' || $tag1 == 'flushright')
			{
				$vh = preg_match_all($re, $string, $match);
				for($i=0;$i<$vh;$i++)
				{
					$string = preg_replace($re, $val1.'$2</p id=\"end{'.$tag1.'}\">', $string);
					$with_breaks = str_replace("\\", '<br id=\"br\">', $match[2][$i]);
					$with_breaks = str_replace("\n", ' ', $with_breaks);
					$string = str_replace($match[2][$i], $with_breaks, $string);
				}
			}
		}
		$string = '<p>'.$string.'</p>';
		$string = preg_replace("#(\r\n\r\n|<p>|^)(>|&gt;)(.*?[^\n]?)(\n|$)#sim","\$1<i>>\$3</i><br>", $string);
		//$string = str_replace("---", '&#8212;', $string);
		//$string = str_replace("--", '&#8211;', $string);
		//$string = str_replace("\-\-\-", '---', $string);
		//$string = str_replace("\-\-", '--', $string);
		$re = '#(\\\\begin)(\[)?(abap|actionscript|actionscript3|ada|apache|applescript|apt_sources|asm|asp|autoit|avisynth|bash|basic4gl|bf|bibtex|blitzbasic|bnf|boo|c|c_mac|caddcl|cadlisp|cfdg|cfm|cil|cmake|cobol|cpp|cpp-qt|csharp|css|d|dcs|delphi|diff|div|dos|dot|eiffel|e-mail|erlang|fo|fortran|freebasic|genero|gettext|glsl|gml|gnuplot|groovy|haskell|hq9plus|html4strict|idl|ini|inno|intercal|io|java|java5|javascript|kixtart|klonec|latex|lisp|locobasic|lolcode|lotusformulas|lotusscript|lscript|lsl2|lua|m68k|make|matlab|mirc|modula3|mpasm|mxml|mysql|nsis|oberon2|objc|ocaml|ocaml-brief|oobas|oracle11|oracle8|pascal|per|perl|php|php-brief|pic16|pixelbender|plsql|povray|powershell|progress|prolog|providex||python|qbasic|rails|rebol|reg|robots|ruby|sas|scala|scheme|scilab|sdlbasic|smalltalk|smarty|sql|tcl|teraterm|text|thinbasic|tsql|typoscript|vb|vbnet|verilog|vhdl|vim|visualfoxpro|visualprolog|whitespace|whois|winbatch|xml|xorg_conf|xpp|z80)?(\])?({highlight})(.*?[^\\\\end{highlight}]?)(\\\\end{highlight})#sim';
		$vh = preg_match_all($re, $string, $match);
		for($i=0;$i<$vh;$i++)
		{
			$string = preg_replace($re, '<fieldset><legend>$3</legend>$6</fieldset>', $string);
			$with_breaks = base::highlight(html_entity_decode($match[6][$i], ENT_QUOTES), $match[3][$i], "geshi/geshi");
			$string = str_replace($match[6][$i], $with_breaks, $string);
		}
		/*preg_match("#(\\includegraphics)(\[)?(width[ ]*=[ ]*)?([0-9]*)?(,[ ]*)?(height[ ]*=[ ]*)?([0-9]*)?(,[ ]*)?(\])?({)(.*)(})#", $string, $img);
		$add='';
		if($img[4])
			$add .= ' width=\"'.$img[4].'\"';
		if($img[7])
			$add .= ' height=\"'.$img[7].'\"';
		$string = preg_replace("#(\\includegraphics(\[)?(.*)?(\]){)(.*)(})#", '<img '.$add.' src=artimages/'.$dir.$img[11].'>', $string);*/
		$string = str_replace("\r\n\r\n", '</p><p>', $string);
		$string = str_replace("\r\n", ' ', $string);
		
		$typo = new Typographus();

		return $string;//iconv('windows-1251', 'utf-8', $typo->process(iconv('utf-8', 'windows-1251', $string)));
	}

	function html2Tex($string)
	{
		//$ret = '';
		$string = str_replace("<br id=\"br\">", " \\\\", $string);
		$string = str_replace("<p align=\"left\">", "\begin{flushleft}", $string);
		$string = str_replace("</p id=\"end{flushleft}\">", "\end{flushleft}", $string);
		$string = str_replace("<p align=\"right\">", "\begin{flushright}", $string);
		$string = str_replace("</p id=\"end{flushright}\">", "\end{flushright}", $string);
		$string = str_replace("<p align=\"center\">", "\begin{center}", $string);
		$string = str_replace("</p id=\"end{center}\">", "\end{center}", $string);
		$string = str_replace("<br>", "\r\n\r\n", $string);
		$string = str_replace("&gt;", ">", $string);
		//$string = str_replace("<p style=\"font-style:italic\">", "\n", $string);
		$string = preg_replace("#(<a href=\")(.*)(\">)#",'',$string);
		$string = str_replace("</a>", "", $string);
		$string = str_replace("<p>", "\n", $string);
		$string = str_replace("</p>", "\n", $string);
		$string = str_replace("<fieldset style=\"border: 1px dashed black; padding:0px;\"><ol style=\"background-color:#3d3d3d;\" start=\"1\">", "\code{", $string);
		$string = str_replace("</ol></fieldset>", "}", $string);
		$string = str_replace("<strong>", "\\b{", $string);
		$string = str_replace("</strong>", "}", $string);
		$string = str_replace("<em>", "\\i{", $string);
		$string = str_replace("</em>", "}", $string);
		$string = str_replace("<u>", "\\u{", $string);
		$string = str_replace("</u>", "}", $string);
		$string = str_replace("<s>", "\\s{", $string);
		$string = str_replace("</s>", "}", $string);
		$string = str_replace("<sub>", "\\sub{", $string);
		$string = str_replace("</sub>", "}", $string);
		$string = str_replace("<sup>", "\\sup{", $string);
		$string = str_replace("</sup>", "}", $string);
		$string = str_replace("<ul>", "\\list{", $string);
		$string = str_replace("<ol>", "\\num{", $string);
		$string = str_replace("<li>", "{*}", $string);
		$string = str_replace("</ul>", "}", $string);
		$string = str_replace("</ol>", "}", $string);
		$string = str_replace("<li style=\"background-color:#000000; padding-left: 5px; color: gray\">&nbsp;", "", $string);
		$string = strip_tags($string);
		return $string;
	}
	/*
	function parseTeXTags($str)
	{
		$tags = array
		(
			'b' => '<strong>',
			'i' => '<em>',
			'u' => '<u>',
			's' => '<s>',
			'sub' => '<sub>',
			'sup' => '<sup>',
        		 'code' => '<fieldset style="border: 1px dashed black; padding:0px;"><ol style="background-color:#3d3d3d;" start="1">',
			'list' => '<ul>',
			'num' => '<ol>',
		);
		foreach ($tags as $tag => $val)
		{
        		 $re = '@\\\('.$tag.')\{(.*?)\}@si';
			if ($tag == 'b')
				$str = preg_replace($re, "$val$2</strong>", $str);
			if ($tag == 'i')
				$str = preg_replace($re, "$val$2</em>", $str);
			if ($tag == 's')
				$str = preg_replace($re, "$val$2</s>", $str);
			if ($tag == 'u')
				$str = preg_replace($re, "$val$2</u>", $str);
			if ($tag == 'sub')
				$str = preg_replace($re, "$val$2</sub>", $str);
			if ($tag == 'sup')
				$str = preg_replace($re, "$val$2</sup>", $str);
			if ($tag == 'code')
			{
				preg_match($re.'U', $str, $match);
				$str = preg_replace($re.'U', "$val$2</ol></fieldset>", $str);
				$with_breaks = preg_replace('/\n/', '<li style="background-color:#000000; padding-left: 5px; color: gray">&nbsp;', $match[2]);
            			$str = str_replace($match[2], $with_breaks, $str);
			}
			if ($tag == 'list')
			{
				preg_match($re.'U', $str, $match);
				$str = preg_replace($re.'U', "$val$2</ul>", $str);
				$with_breaks = str_replace('{*}', '<li>&nbsp;', $match[2]);
            			$str = str_replace($match[2], $with_breaks, $str);
			}
			if ($tag == 'num')
			{
				preg_match($re.'U', $str, $match);
				$str = preg_replace($re.'U', "$val$2</ol>", $str);
				$with_breaks = str_replace('{1}', '<li>&nbsp;', $match[2]);
           			 $str = str_replace($match[2], $with_breaks, $str);
			}
		}
		return $str;
	}
	function strToTeX ($string){
		$string = preg_replace("#(https?|ftp)://\S+[^\s.,>)\];'\&quot;!?]*#", '<a href="\\0">\\0</a>', $string);
		$buff = base::parseTeXTags($string);
		$buff = '<p>'.$buff.'</p>';
		$buff = str_replace("\r\n\r\n>", '</p><p style="font-style:italic">>', $buff);
		$buff = str_replace('<p>>', '<p style="font-style:italic">>', $buff);
		$buff = str_replace('<p>&gt;', '<p style="font-style:italic">&gt;', $buff);
		$buff = str_replace("\r\n\r\n&gt;", '</p><p style="font-style:italic">&gt', $buff);
		$buff = str_replace("\r\n\r\n", '</p><p>', $buff);
		$buff = str_replace("---", '&#8212;', $buff);
		$buff = str_replace("--", '&#8211;', $buff);
		$buff = str_replace('&quot;', '"', $buff);
		return $buff;
	}*/
	
	function nomore ($arr, $vars){
		$before=-1;
		$ai=0;
		$val_arr=array();
		while ($vars[$c]!='') {
			$c++;
		}
		for ($i=0; $i<=$c; $i++) {
			if ($vars[$i]==':') {
				for ($a=($before+1); $a<$i; $a++){
					$element=$element.$vars[$a];
				}
				$val_arr[$ai]=$element;
				$element='';
				$before=$i;
				$ai++;
			}
			foreach ($arr as $key=>$value) {
				if (!key_exists($key, $val_arr)){
					return false;
					break;
				}
			}
			return true;
		}
	}
	function check_setting($sname){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if (!empty($sname)) {
				$get_res=mysql_query('
									SELECT `value`
									FROM `'.$GLOBALS['tbl_prefix'].'settings`
									WHERE `name`=\''.$sname.'\'
									');
				if (mysql_numrows($get_res)>0)
					while ($get=mysql_fetch_object($get_res)){
						return $get->value;
					}
				else {return -1;}
			}
			else {
				$ret=array();
				$get_res=mysql_query('
									SELECT `name`, `value`
									FROM `'.$GLOBALS['tbl_prefix'].'settings`
									');
				if (mysql_numrows($get_res)>0){
					while ($get=mysql_fetch_object($get_res))
						$ret[$get->name]=$get->value;
					
					return $ret;
				}
				else {return -1;}
			}
		}
		else die(mysql_error());
	}
	function erewrite($table, $field, $value, $id, $id_field='id'){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if (!empty($id)){
					$query='
							UPDATE `'.$GLOBALS['tbl_prefix'].$table.'`
							SET `'.$field.'`=\''.$value.'\'
							WHERE `'.$id_field.'`=\''.$id.'\'
							';
				}
				else {
					 return -1;
				}
				if(mysql_query($query))
				{ return 1;}
				else { return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function del_element($element, $id, $id_field='id'){
		if ($_SESSION['user_admin']==1){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$findid_res=mysql_query('
										SELECT `'.$id_field.'`
										FROM `'.$GLOBALS['tbl_prefix'].$element.'`
										WHERE `'.$id_field.'`=\''.$id.'\'
										');
				if (mysql_numrows($findid_res)<=0)
					return -1;
				if(mysql_query('
								DELETE FROM `'.$GLOBALS['tbl_prefix'].$element.'`
								WHERE `'.$id_field.'`=\''.$id.'\'
								'))
				{ return 1;}
				else { return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else return -1;
	}
	function del_row($table, $condition){
		if ($_SESSION['user_admin']==1){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$findid_res=mysql_query('
										SELECT *
										FROM `'.$GLOBALS['tbl_prefix'].$table.'`
										WHERE '.$condition
										);
				//echo mysql_error();
				if (mysql_numrows($findid_res)<=0)
					return -4;
				if(mysql_query('
								DELETE FROM `'.$GLOBALS['tbl_prefix'].$table.'`
								WHERE '.$condition
								))
				{ return 1;}
				else { return -2;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else return -3;
	}
	function get_field_id($table, $field, $value){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$menu_res=mysql_query('
									SELECT `id`
									FROM `'.$GLOBALS['tbl_prefix'].$table.'`
									WHERE `'.$field.'`=\''.$value.'\'
									');
			if (mysql_numrows($menu_res)<=0){
				
				return -1;
			}
			while ($menu=mysql_fetch_object($menu_res)) {
				return $menu->id;
				
			}
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function other_query($query, $returnas = 'array'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$query = str_replace('[prefix]', $GLOBALS['tbl_prefix'], $query);
			if ($ret_res = mysql_query($query)){
				$i = 0;
				switch ($returnas){
					case 'array':
						while ($r = mysql_fetch_array($ret_res)){
							$ret[$i] = $r;
							$i++;
						}
					break;
					case 'assoc_array':
						while ($r = mysql_fetch_assoc($ret_res)){
							$ret[$i] = $r;
							$i++;
						}
					break;
					case 'object':
						while ($r = mysql_fetch_object($ret_res)){
							$ret[$i] = $r;
							$i++;
						}
					break;
				}
				return $ret;
			}
			if(strlen(mysql_error()) > 0){
			   echo '<fieldset><legend>MySQL Error</legend>Error: '.mysql_error().'<br>In query: '.$query.'<br></fieldset>';
			}
		}
	}
	function timeToSTDate($timestamp){
		if((int)$timestamp == 0)
			return '';
		$temp = base::check_setting('gmt');
		$server_gmt = substr($temp, 1) * 3600;
		switch(substr($temp, 0, 1)){
			case '+': $timestamp += $server_gmt; break;
			case '-': $timestamp -= $server_gmt; break;	
		}
		if(class_exists('users')){
			$info = users::get_user_info($_SESSION['user_login']);
			$user_gmt = substr($info['gmt'], 1) * 3600;
			switch(substr($info['gmt'], 0, 1)){
				case '+': $timestamp += $user_gmt; break;
				case '-': $timestamp -= $user_gmt; break;
				default: $user_gmt = substr($info['gmt'], 0) * 3600; $timestamp += $user_gmt; break;
			}
		}
		$time = getdate($timestamp);
		$timestamp = (strlen($time['mday']) < 2 ? '0'.$time['mday'] : $time['mday']);
		$timestamp .= '.'.(strlen($time['mon']) < 2 ? '0'.$time['mon'] : $time['mon']);
		$timestamp .= '.'.$time['year'];
		$timestamp .= '&nbsp;';
		$timestamp .= (strlen($time['hours']) < 2 ? '0'.$time['hours'] : $time['hours']);
		$timestamp .= ':'.(strlen($time['minutes']) < 2 ? '0'.$time['minutes'] : $time['minutes']);
		$timestamp .= ':'.(strlen($time['seconds']) < 2 ? '0'.$time['seconds'] : $time['seconds']);
		return $timestamp;
	}
	function get_field_by_id($table, $field, $id, $id_field = 'id'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$menu_res=mysql_query('
									SELECT `'.$field.'`
									FROM `'.$GLOBALS['tbl_prefix'].$table.'`
									WHERE `'.$id_field.'`=\''.$id.'\'
									');
			if (mysql_numrows($menu_res)<=0){
				
				return -1;
			}
			while ($menu=mysql_fetch_object($menu_res)) {
				return $menu->$field;
				
			}
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function modify_setting($sname, $svalue){
		if($_SESSION['user_admin']==1 || $GLOBALS['install']=='yes'){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if (mysql_query('
								UPDATE `'.$GLOBALS['tbl_prefix'].'settings`
								SET `value`=\''.$svalue.'\'
								WHERE `name`=\''.$sname.'\'
								')){
					
					return 1;
								}
				else{return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		elseif($_SESSION['user_admin']==2) return -1;
	}
	function get_last_index($table, $field){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$c_res=mysql_query('SELECT `'.$field.'`
								FROM `'.$GLOBALS['tbl_prefix'].$table.'`
								');
			while ($c=mysql_fetch_object($c_res))
				$ret=$c->$field;
			return $ret;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function eread($table, $field, $key, $find_by = '', $find_by_value = '', $additional = 'AND 1'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if (empty($find_by) && empty($condition)){
				if (empty($key)){
					$c_res=mysql_query('SELECT '.$field.'
										FROM `'.$GLOBALS['tbl_prefix'].$table.'`
										');
					if (!empty($additional))
						$c_res=mysql_query('SELECT '.$field.'
										FROM `'.$GLOBALS['tbl_prefix'].$table.'`
										WHERE '.$additional);
				}
				else {
					$c_res=mysql_query('SELECT '.$field.', '.$key.'
										FROM `'.$GLOBALS['tbl_prefix'].$table.'`
										');
				}
				if (empty($key) && empty($condition)){
					$i=0;
					while (@$c=mysql_fetch_object($c_res)) {
						$ret[$i]=$c->$field;
						$i++;
					}
				}
				else {
					while ($c=mysql_fetch_object($c_res)) {
						$ret[$c->$key]=$c->$field;
					}
				}
			}
			else {
				if (!empty($find_by_value) || !empty($condition)){
					if (empty($condition)) {
						$c_res=mysql_query('SELECT '.$field.'
										FROM '.$GLOBALS['tbl_prefix'].$table.'
										WHERE '.$find_by.'=\''.$find_by_value.'\'
										 '.$additional);
					}
					else {
						$c_res=mysql_query('SELECT '.$field.'
										FROM '.$GLOBALS['tbl_prefix'].$table.'
										WHERE '.$condition.' '.$additional
										);
					}
					$i = 0;
					while (@$c=mysql_fetch_object($c_res)){
						$ret[$i]=$c->$field;
						$i++;
					}
					if (sizeof($ret) < 2)
						$ret = $ret[0];
				}
				else {return -1;}
			}
			return $ret;
		}
	}
	function get_fields($table, $fields, $order = '', $condition = ''){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$ret = array();
			$i = 0;
			if ($condition != '')
				$query = 'SELECT '.$fields.' FROM `'.$GLOBALS['tbl_prefix'].$table.'` WHERE '.$condition.'';
			else
				$query = 'SELECT '.$fields.' FROM `'.$GLOBALS['tbl_prefix'].$table.'`';
			if ($order != '')
				$query .= ' ORDER BY '.$order;
			$query = mysql_query($query);
			if (mysql_numrows($query) <= 0) return 0;
			else{
				while($q = mysql_fetch_assoc($query)){
					$ret[$i] = $q;
					$i++;
				}
				return $ret;
			}
		}
		else return -1;
	}
	function date_parse($date, $mask){
		for ($pos=0; $pos<strlen($mask); $pos++) {
			switch (strtolower($mask[$pos])) {
				case 'd':
					$days=$pos;
					define('days_at_pos', $pos);
				break;
				case 'm':
					$months=$pos;
					define('months_at_pos', $pos);
				break;
				case 'y':
					$years=$pos;
					define('years_at_pos', $pos);
				break;
			}
		}
		if (strlen($mask)<6)
			return false;
		for ($i=days_at_pos; $i<=$days; $i++)
			$ret['day']=$ret['day'].$date[$i];
		for ($i=months_at_pos; $i<=$months; $i++)
			$ret['month']=$ret['month'].$date[$i];
		for ($i=years_at_pos; $i<=$years; $i++)
			$ret['year']=$ret['year'].$date[$i];
		if (!checkdate($ret['month'], $ret['day'], $ret['year']))
			return true;
		else return $ret;
	}
	function parse_dir($dir, $pattern = '/^*$/'){
		$d = dir($dir);
		$ret = array();
		$i = 0;
		while(false != ($e = $d->read())){
			if (!preg_match($pattern, $e) && filetype(getcwd().'/'.$dir.'/'.$e) == 'dir'){
    			$ret[$i] = $e;
    			$i++;
			}
		}
		return $ret;
	}
	function update_field($table, $field, $value, $condition = '1'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if(mysql_query('
						UPDATE `'.$GLOBALS['tbl_prefix'].$table.'`
						SET `'.$field.'` = \''.$value.'\'
						WHERE '.$condition.'
						')){
				return 1;}
			else {
				echo mysql_error();
				return -2;
			}
		}
		else 
			return -1;
	}
	function divide_string($str){
		$ret = array();
		for ($i=0 ; $i < strlen($str); $i++)
			$ret[$i] = substr($str, $i, 1);
		return $ret;
	}
	function declOfNum($number, $titles){
	    $cases = array (2, 0, 1, 1, 1, 2);
	    return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
	}
	function devide_tags($string){
		$ret = array();
		$validate = array();
		$tag = '';
		$j = 0;
		
		for($i = 0; $i < strlen($string); $i++){
			$buff = substr($string, $i, 1);
	      if ($buff == ']')
	         $validate['i']++;
	      if ($buff == '[')
	         $validate['j']++;
		}
		if ($validate['i'] != $validate['j']) return -1;
		for($i = 0; $i < strlen($string); $i++){
			$buff = substr($string, $i, 1);
	      if ($buff == ']'){
				$ret[$j] = $tag.']';
	         $tag = '';
				$j++;
				continue;
	      }
			$tag .= $buff;
		}
		return $ret;
	}
	function parse_attributes($string){
		$ret = array();
	   $validate = array();

		for($i = 0; $i < strlen($string); $i++){
			$buff = substr($string, $i, 1);
	      if ($buff == '=')
	         $validate['j']++;
	      if ($buff == '"')
	         $validate['k']++;
	      if ($buff == ']')
	         $validate['l']++;
	      if ($buff == '[')
	         $validate['m']++;
		}
	   if ((int)$validate['j'] == 0) return 0;
	   if ((int)$validate['j'] > 0 && ((int)$validate['k']/(int)$validate['j']) != 2) return -1;
	   if ((int)$validate['l'] != (int)$validate['m']) return -2;
	   if (((int)$validate['l'] == (int)$validate['m']) && (((int)$validate['k']/(int)$validate['j']) == 2)){
	      $string = preg_replace('/\s{1,}/', ' ', $string);
	      $ret['~name~'] = substr($string, 1, (strpos($string, ' ') - 1));
	      $string = str_replace(substr($string, 0, strpos($string, ' ')+1), '', $string);
	      for ($i = 0; $i < $validate['j']; $i++){
	         $prop = true;
	         $property = '';
	         $val = false;
	         $value = '';
	         for($j = 0; $j < strlen($string); $j++){
	            $buff = substr($string, $j, 1);
	            if(preg_match('/^[a-zA-Z0-9]$/', $buff) && !$val)
	               $property .= $buff;
	            if($val){
	               $value .= $buff;
	               if (substr($string, $j+1, 1) == '"'){
	                  $ret[$property] = $value;
	                  $string = substr($string, $j+2);
	                  break;
	               }
	               continue;
	            }
	            if(preg_match('/\s/', $buff) && $prop)
	               continue;
	            if(preg_match('/\=/', $buff) && $prop)
	               continue;
	            if(preg_match('/\"/', $buff) && $prop){
	               $prop = false;
	               $val = true;
	               continue;
	            }
	         }
	      }
	      return $ret;
	   }
	}
}
?>
