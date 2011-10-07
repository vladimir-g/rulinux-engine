<?php
function str_to_html($string)
{
	$code = array();
	$lang = array();
	$re = '#(\\\\begin)(\[)?(abap|actionscript|actionscript3|ada|apache|applescript|apt_sources|asm|asp|autoit|avisynth|bash|basic4gl|bf|bibtex|blitzbasic|bnf|boo|c|c_mac|caddcl|cadlisp|cfdg|cfm|cil|cmake|cobol|cpp|cpp-qt|csharp|css|d|dcs|delphi|diff|div|dos|dot|eiffel|e-mail|erlang|fo|fortran|freebasic|genero|gettext|glsl|gml|gnuplot|groovy|haskell|hq9plus|html4strict|idl|ini|inno|intercal|io|java|java5|javascript|kixtart|klonec|latex|lisp|locobasic|lolcode|lotusformulas|lotusscript|lscript|lsl2|lua|m68k|make|matlab|mirc|modula3|mpasm|mxml|mysql|nsis|oberon2|objc|ocaml|ocaml-brief|oobas|oracle11|oracle8|pascal|per|perl|php|php-brief|pic16|pixelbender|plsql|povray|powershell|progress|prolog|providex||python|qbasic|rails|rebol|reg|robots|ruby|sas|scala|scheme|scilab|sdlbasic|smalltalk|smarty|sql|tcl|teraterm|text|thinbasic|tsql|typoscript|vb|vbnet|verilog|vhdl|vim|visualfoxpro|visualprolog|whitespace|whois|winbatch|xml|xorg_conf|xpp|z80)?(\])?({highlight})(.*?[^\\\\end{highlight}]?)(\\\\end{highlight})#sim';
	$vh = preg_match_all($re, $string, $match);
	for($i=0;$i<$vh;$i++)
	{
		$lang[$i]=$match[3][$i];
		$with_breaks = mark::highlight(html_entity_decode($match[6][$i], ENT_QUOTES), $match[3][$i], "librarys/geshi/geshi");
		$code[$i] = $with_breaks;
		$string = str_replace($match[0][$i], '⓬'.$i.'⓬', $string);
	}
	$re = '#(\\\\begin\\{math\\})(.*?)(\\\\end\\{math\\})#suim';
	$vh = preg_match_all($re, $string, $match);
	for($i=0;$i<$vh;$i++)
	{
		$with_breaks = mark::make_formula($match[2][$i]);
		$math[$i] = $with_breaks;
		$string = str_replace($match[0][$i], 'ᴥ'.$i.'ᴥ', $string);
	}
	$string = htmlspecialchars($string);
	$string = str_replace('\\\\', '&#92;', $string);
	$string = preg_replace("#\\\\}#sim","&#125;", $string);
	$string = preg_replace("#(\\\\b{)(.*?[^}]?)(})#sim","<b>\$2</b>", $string);
	$string = preg_replace("#(\\\\spoiler{)(.*?[^}]?)(})#sim","<span class=\"spoiler\">\$2</span>", $string);
	$string = preg_replace("#(\\\\i{)(.*?[^}]?)(})#sim","<i>\$2</i>", $string);
	$string = preg_replace("#(\\\\u{)(.*?[^}]?)(})#sim","<u>\$2</u>", $string);
	$string = preg_replace("#(\\\\s{)(.*?[^}]?)(})#sim","<s>\$2</s>", $string);
	$string = preg_replace("#(\\\\sub{)(.*?[^}]?)(})#sim","<sub>\$2</sub>", $string);
	$string = preg_replace("#(\\\\sup{)(.*?[^}]?)(})#sim","<sup>\$2</sup>", $string);
	$string = preg_replace("#\\\\br#sim","<br />", $string);
	$string = str_replace('imgh://', 'http://', $string);
	$string = str_replace('imghs://', 'https://', $string);
	$tags = array
	(
		'list' => '<ul>',
		'num' => '<ol>',
		'quote' => '<div class="quote"><pre>',
	);
	foreach ($tags as $tag => $val)
	{
		if ($tag == 'list')
		{
			$re = '@\\\\(list)({)(.*?)([^\\*]})@sim';
			$vt = preg_match_all($re, $string, $match);
			for($i=0;$i<$vt;$i++)
			{
				$string = preg_replace($re, "$val\$3</ul>", $string, 1);
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
				$string = preg_replace($re, "$val\$3</ol>", $string, 1);
				$with_breaks = str_replace('{*}', '<li>&nbsp;', $match[3][$i]);
				$string = str_replace($match[3][$i], $with_breaks, $string);
			}
		}
		if ($tag == 'quote')
		{
			$re = '@\\\\(quote)({)(.*?[^}]?)(})@sim';
			$vt = preg_match_all($re, $string, $match);
			for($i=0;$i<$vt;$i++)
			{
				$string = preg_replace($re, "$val\$3</pre></div>", $string, 1);
				$with_breaks = preg_replace('#^(\\r\\n)+#', '', $match[3][$i]);
				$with_breaks = preg_replace('/(\\r\\n)+$/', '', $with_breaks);
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
				$string = preg_replace($re, $val1.'$2</p>', $string, 1);
				$with_breaks = str_replace("\\", '<br>', $match[2][$i]);
				$with_breaks = str_replace("\n", ' ', $with_breaks);
				$string = str_replace($match[2][$i], $with_breaks, $string);
			}
		}
	}
	$user_re = "#(\\\\user{)(.*?[^}]?)(})#sim";
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
	$url_re = '#(\\\\url)(\\[)?(.*?[^\\]]?)(\\])?({)(.*?[^}]?)(})#sim';
	$vt = preg_match_all($url_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		if(filter_var($match[6][$i], FILTER_VALIDATE_URL))
		{
			if(empty($match[3][$i]))
				$string = preg_replace($url_re, "<a href=\"\$6\">\$6</a>", $string, 1);
			else
				$string = preg_replace($url_re, "<a href=\"\$6\">\$3</a>", $string, 1);
		}
	}
	$img_re = '#(\\\\img)(\\[?) ?(left|right|middle|top|bottom)? ?(\\])?{(.*?[^}]?)(})#sim';
	$vt = preg_match_all($img_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$imageinfo = getimagesize($match[5][$i]);
		if($imageinfo[0] > 1024)
		{
			if(!empty($match[3][$i]))
				$string = preg_replace($img_re, "<img src=\"\$5\" align=\"$3\" width=\"1024\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
			else
				$string = preg_replace($img_re, "<img src=\"\$5\" width=\"1024\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
		}
		else
		{
			if(!empty($match[3][$i]))
				$string = preg_replace($img_re, "<img src=\"\$5\" align=\"$3\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
			else
				$string = preg_replace($img_re, "<img src=\"\$5\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
		}
	}
	$string = '<p>'.$string.'</p>';
	$string = preg_replace("#(\r\n\r\n|<p>|^)(>|&gt;)(.*?[^\n]?)(\n|$)#sim","\$1<i>>\$3</i><br>", $string);
	$string = str_replace("\r\n\r\n", '<br><br>', $string);
	$string = str_replace("\r\n", ' ', $string);
	$re = "#(⓬)([0-9]+)(⓬)#sim";
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = str_replace('⓬'.$match[2][$i].'⓬','<fieldset><legend>'.$lang[$match[2][$i]].'</legend>'.$code[$match[2][$i]].'</fieldset>',$string);
	}
	$re = "#(ᴥ)([0-9]+)(ᴥ)#suim";
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
		$string = str_replace('ᴥ'.$match[2][$i].'ᴥ', $math[$match[2][$i]], $string);
	return $string;
}
?>