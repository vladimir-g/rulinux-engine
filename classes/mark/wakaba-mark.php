<?php

function str_to_html($string)
{
 	$code = array();
 	$lang = array();
	$re = '#(``)(@(abap|actionscript|actionscript3|ada|apache|applescript|apt_sources|asm|asp|autoit|avisynth|bash|basic4gl|bf|bibtex|blitzbasic|bnf|boo|c|c_mac|caddcl|cadlisp|cfdg|cfm|cil|cmake|cobol|cpp|cpp-qt|csharp|css|d|dcs|delphi|diff|div|dos|dot|eiffel|e-mail|erlang|fo|fortran|freebasic|genero|gettext|glsl|gml|gnuplot|groovy|haskell|hq9plus|html4strict|idl|ini|inno|intercal|io|java|java5|javascript|kixtart|klonec|latex|lisp|locobasic|lolcode|lotusformulas|lotusscript|lscript|lsl2|lua|m68k|make|matlab|mirc|modula3|mpasm|mxml|mysql|nsis|oberon2|objc|ocaml|ocaml-brief|oobas|oracle11|oracle8|pascal|per|perl|php|php-brief|pic16|pixelbender|plsql|povray|powershell|progress|prolog|providex||python|qbasic|rails|rebol|reg|robots|ruby|sas|scala|scheme|scilab|sdlbasic|smalltalk|smarty|sql|tcl|teraterm|text|thinbasic|tsql|typoscript|vb|vbnet|verilog|vhdl|vim|visualfoxpro|visualprolog|whitespace|whois|winbatch|xml|xorg_conf|xpp|z80)@)?(.*?[^``]?)(``)#sim';
	$vh = preg_match_all($re, $string, $match);
	for($i=0;$i<$vh;$i++)
	{
		$lang[$i]=$match[3][$i];
		$with_breaks = mark::highlight(html_entity_decode($match[4][$i], ENT_QUOTES), $match[3][$i], "geshi/geshi");
		$code[$i] = $with_breaks;
		$string = str_replace($match[0][$i], '⓬'.$i.'⓬', $string);
	}
	$string = htmlspecialchars($string);
 	$string = '<p>'.$string.'</p>';
	$string = preg_replace("#(\\*\\*)(.*?(^\\*\\*)?)(\\*\\*)#sim","<b>\$2</b>", $string);
	$string = preg_replace("#(%%)(.*?(^%%)?)(%%)#sim","<span class=\"spoiler\">\$2</span>", $string);
	$string = preg_replace("#(__)(.*?(^__)?)(__)#sim","<b>\$2</b>", $string);
	$string = preg_replace("#(\\*)([^ ].*?(^\\*)?)(\\*)#sim","<i>\$2</i>", $string);
	$string = preg_replace("#(_)(.*?(^_)?)(_)#sim","<i>\$2</i>", $string);
	$string = preg_replace("#(\\$)(.*?(^\\$)?)(\\$)#sim","<u>\$2</u>", $string);
	$string = preg_replace("#( |&nbsp;|<p>)([A-zА-я0-9<&;>/]*)(\\^W)#suim","\$1<s>\$2</s>", $string);
	$string = preg_replace("@(##)(.*?([^##])?)(##)@sim","<sub>\$2</sub>", $string);
	$string = preg_replace("@(#)(.*?(^#)?)(#)@sim","<sup>\$2</sup>", $string);
	$string = str_replace('imgh://', 'http://', $string);
	$string = str_replace('imghs://', 'https://', $string);
	$string = preg_replace("#(\\* |\\+ |- ){2,}+#", "$1", $string);
	$re = '#(\\* |\\+ |- )(.*?[^((\\r\\n){2,}|</p>)]?)((\\r\\n){2,}|</p>)#sim';
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = preg_replace($re, "<ul><li>&nbsp;$2</ul>", $string, 1);
		$with_breaks = preg_replace('/(\\r\\n)(\\* |\\+ |- )/', '<li>&nbsp;', $match[2][$i]);
		$string = str_replace($match[2][$i], $with_breaks, $string);
	}
	$string = preg_replace("#([0-9]\\. ){2,}+#", "$1", $string);
	$re = '#([0-9]\\. )(.*?[^((\\r\\n){2,}|</p>)]?)((\\r\\n){2,}|</p>)#sim';
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = preg_replace($re, "<ol><li>&nbsp;$2</ol>", $string, 1);
		$with_breaks = preg_replace('/(\\r\\n)([0-9]\\. )/', '<li>&nbsp;', $match[2][$i]);
		$string = str_replace($match[2][$i], $with_breaks, $string);
	}

	$string = preg_replace("#(>>|&gt;&gt;)(.*?(^>>|&gt;&gt;)?)(>>|&gt;&gt;)#sim","<p align=\"right\">\$2</p>", $string);
	$string = preg_replace("#(<<|&lt;&lt;)(.*?(^<<|&lt;&lt;)?)(<<|&lt;&lt;)#sim","<p align=\"left\">\$2</p>", $string);
	$string = preg_replace("#(<>|&lt;&gt;)(.*?(^<>|&lt;&gt;)?)(<>|&lt;&gt;)#sim","<p align=\"center\">\$2</p>", $string);

	$re = '#(`)([^`].*?[^`]?)(`)#sim';
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = preg_replace($re, "<fieldset class=\"quote\"><ol start=\"1\"><li>&nbsp;$2</ol></fieldset>", $string, 1);
		$with_breaks = preg_replace('/^(\\r\\n)+/', '', $match[2][$i]);
		$with_breaks = preg_replace('/(\\r\\n)+$/', '', $with_breaks);
		$with_breaks = preg_replace('/\n/', '<li>&nbsp;', $with_breaks);
		$string = str_replace($match[2][$i], $with_breaks, $string);
	}


	$user_re = "#(\\^)(.*?[^\\^]?)(\\^)#sim";
	$arr = preg_match_all($user_re, $string, $match);
	for($i=0;$i<$arr;$i++)
	{
		$where_arr = array(array("key"=>'nick', "value"=>$match[2][$i], "oper"=>'='));
		$sel = base::select('users', '', '*', $where_arr, 'AND');
		if(!empty($sel))
			$string = preg_replace($user_re, "<b><a href=\"/profile.php?user=\$2\">\$2</a></b>", $string, 1);
		else
			$string = preg_replace($user_re, "\$2", $string, 1);
	}

	$url_re = '#(~)((@)(.*?[^@]?)(@))?(.*?[^~]?)(~)#sim';
	$vt = preg_match_all($url_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		if(filter_var($match[6][$i], FILTER_VALIDATE_URL))
		{
			if(empty($match[4][$i]))
				$string = preg_replace($url_re, "<a href=\"\$6\">\$6</a>", $string, 1);
			else
				$string = preg_replace($url_re, "<a href=\"\$6\">\$4</a>", $string, 1);
		}
	}
	$img_re = '#(~~)((@)(left|right|middle|top|bottom)(@))?(.*?[^~]{2}?)(~~)#sim';
	$vt = preg_match_all($img_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		if(!empty($match[4][$i]))
			$string = preg_replace($img_re, "<img src=\"\$6\" align=\"$4\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
		else
			$string = preg_replace($img_re, "<img src=\"\$6\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
	}
	$string = preg_replace("#(\r\n|<p>|^)(>|&gt;)(.*?[^\n]?)(\n|$)#sim","\$1<i>>\$3</i><br>", $string);
	$string = preg_replace("#(\r\n)+#", '<br>', $string);
	$re = "#(⓬)([0-9]+)(⓬)#sim";
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = str_replace('⓬'.$match[2][$i].'⓬','<fieldset><legend>'.$lang[$match[2][$i]].'</legend>'.$code[$match[2][$i]].'</fieldset>',$string);
	}
	return $string;
}

?>