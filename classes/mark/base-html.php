<?php
function str_to_html($string)
{
	$code = array();
 	$lang = array();
 	$re = '#(<code)([ ]?lang=["]?(abap|actionscript|actionscript3|ada|apache|applescript|apt_sources|asm|asp|autoit|avisynth|bash|basic4gl|bf|bibtex|blitzbasic|bnf|boo|c|c_mac|caddcl|cadlisp|cfdg|cfm|cil|cmake|cobol|cpp|cpp-qt|csharp|css|d|dcs|delphi|diff|div|dos|dot|eiffel|e-mail|erlang|fo|fortran|freebasic|genero|gettext|glsl|gml|gnuplot|groovy|haskell|hq9plus|html4strict|idl|ini|inno|intercal|io|java|java5|javascript|kixtart|klonec|latex|lisp|locobasic|lolcode|lotusformulas|lotusscript|lscript|lsl2|lua|m68k|make|matlab|mirc|modula3|mpasm|mxml|mysql|nsis|oberon2|objc|ocaml|ocaml-brief|oobas|oracle11|oracle8|pascal|per|perl|php|php-brief|pic16|pixelbender|plsql|povray|powershell|progress|prolog|providex||python|qbasic|rails|rebol|reg|robots|ruby|sas|scala|scheme|scilab|sdlbasic|smalltalk|smarty|sql|tcl|teraterm|text|thinbasic|tsql|typoscript|vb|vbnet|verilog|vhdl|vim|visualfoxpro|visualprolog|whitespace|whois|winbatch|xml|xorg_conf|xpp|z80)["]?)?(>)((?!</code>).*?)(</code>)#suim';
	$vh = preg_match_all($re, $string, $match);
	for($i=0;$i<$vh;$i++)
	{
		$lang[$i]=$match[3][$i];
		$with_breaks = mark::highlight(html_entity_decode($match[5][$i], ENT_QUOTES), $match[3][$i], "librarys/geshi/geshi");
		$code[$i] = $with_breaks;
		$string = str_replace($match[0][$i], '⓬'.$i.'⓬', $string);
	}
	$latex = array();
	$latex_re = '#(<latex>)(.*?)(</latex>)#suim';
	$vh = preg_match_all($latex_re, $string, $match);
	for($i=0;$i<$vh;$i++)
	{
		$with_breaks = mark::make_latex($match[2][$i]);
		$latex[$i] = $with_breaks;
		$string = str_replace($match[0][$i], '☣'.$i.'☣', $string);
	}
	$math = array();
	$re = '#(<m>)(.*?)(</m>)#suim';
	$vh = preg_match_all($re, $string, $match);
	for($i=0;$i<$vh;$i++)
	{
		$with_breaks = mark::make_formula($match[2][$i]);
		$math[$i] = $with_breaks;
		$string = str_replace($match[0][$i], 'ᴥ'.$i.'ᴥ', $string);
	}
	$string = htmlspecialchars($string);
	$string = str_replace('\\', '&#92;', $string);
	$string = preg_replace("#(&lt;b&gt;)(.*?[^&lt;/b&gt;]?)(&lt;/b&gt;)#suim","<b>\$2</b>", $string);
	$string = preg_replace("#(&lt;span class=&quot;spoiler&quot;&gt;)((?!&lt;/span&gt).*?)(&lt;/span&gt;)#suim","<span class=\"spoiler\">\$2</span>", $string);
	$string = preg_replace("#(&lt;i&gt;)(.*?[^&lt;/i&gt;]?)(&lt;/i&gt;)#suim","<i>\$2</i>", $string);
	$string = preg_replace("#(&lt;u&gt;)(.*?[^&lt;/u&gt;]?)(&lt;/u&gt;)#suim","<u>\$2</u>", $string);
	$string = preg_replace("#(&lt;s&gt;)(.*?[^&lt;/s&gt;]?)(&lt;/s&gt;)#suim","<s>\$2</s>", $string);
	$string = preg_replace("#(&lt;sub&gt;)(.*?[^&lt;/sub&gt;]?)(&lt;/sub&gt;)#suim","<sub>\$2</sub>", $string);
	$string = preg_replace("#(&lt;sup&gt;)(.*?[^&lt;/sup&gt;]?)(&lt;/sup&gt;)#suim","<sup>\$2</sup>", $string);
	$string = str_replace('imgh://', 'http://', $string);
	$string = str_replace('imghs://', 'https://', $string);
	$string = preg_replace("#(&lt;) ?(br) ?/?(&gt;)#suim","<br>", $string);
	$qoute_re = "#(&lt;q&gt;)(.*?(?!&lt;q))(&lt;/q&gt;)#suim";
	$vt = preg_match_all($qoute_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = preg_replace($qoute_re, "<div class=\"quote\"><pre>\$2</pre></div>", $string, 1);
		$with_breaks = preg_replace('/^(\\r\\n)+/', '', $match[2][$i]);
		$with_breaks = preg_replace('/(\\r\\n)+$/', '', $with_breaks);
		$string = str_replace($match[2][$i], $with_breaks, $string);
	}
	$list_re = "#(&lt;ul&gt;)(.*?(?!ul&gt;))(&lt;/ul&gt;)#suim";
	$vt = preg_match_all($list_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = preg_replace($list_re, "<ul>\$2</ul>", $string, 1);
		$with_breaks = preg_replace('/^(\\r\\n)+/', '', $match[2][$i]);
		$with_breaks = preg_replace('/(\\r\\n)+$/', '', $with_breaks);
		$with_breaks = preg_replace('#&lt;li&gt;#suim', '<li>&nbsp;', $with_breaks);
		$string = str_replace($match[2][$i], $with_breaks, $string);
	}
	$num_re = "#(&lt;ol&gt;)(.*?(?!ol&gt;))(&lt;/ol&gt;)#suim";
	$vt = preg_match_all($num_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = preg_replace($num_re, "<ol start=\"1\">\$2</ol>", $string, 1);
		$with_breaks = preg_replace('/^(\\r\\n)+/', '', $match[2][$i]);
		$with_breaks = preg_replace('/(\\r\\n)+$/', '', $with_breaks);
		$with_breaks = preg_replace('#&lt;li&gt;#suim', '<li>&nbsp;', $with_breaks);
		$string = str_replace($match[2][$i], $with_breaks, $string);
	}
	$string = preg_replace('#(&lt;p align=&quot;)(left|right|center)(&quot;&gt;)(.*?(^/p&gt;)?)(&lt;/p&gt;)#suim',"<p align=\"\$2\">\$4</p>", $string);
	$img_re = '#(&lt;img) ?(align=&quot;)?(left|right|middle|top|bottom)?(&quot;)?(src=&quot;)((?!&quot;).*?)(&quot;&gt;)#suim';
	$vt = preg_match_all($img_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$imageinfo = getimagesize($match[5][$i]);
		if($imageinfo[0] > 1024)
		{
			if(!empty($match[3][$i]))
				$string = preg_replace($img_re, "<img src=\"\$6\" align=\"$3\" width=\"1024\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
			else
				$string = preg_replace($img_re, "<img src=\"\$6\" width=\"1024\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
		}
		else
		{
			if(empty($match[3][$i]))
				$string = preg_replace($img_re, "<img src=\"\$6\" align=\"$3\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
			else
				$string = preg_replace($img_re, "<img src=\"\$6\" alt=\"[путь к изображению некорректен]\" />", $string, 1);
		}
	}
 	$user_re = "#(&lt;span class=&quot;user&quot;&gt;)((?!&lt;/span&gt;).*?)(&lt;/span&gt;)#suim";
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
	$url_re = '#(&lt;a href=&quot;)((?!&quot;).*?)(&quot;&gt;)((?!&lt;/a&gt;).*?)(&lt;/a&gt;)#suim';
	$vt = preg_match_all($url_re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		if(filter_var($match[2][$i], FILTER_VALIDATE_URL))
			$string = preg_replace($url_re, "<a href=\"\$2\">\$4</a>", $string);
	}
 	$string = '<p>'.$string.'</p>';
 	$re = "#(⓬)([0-9]+)(⓬)#suim";
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
	{
		$string = str_replace('⓬'.$match[2][$i].'⓬','<fieldset><legend>'.$lang[$match[2][$i]].'</legend>'.$code[$match[2][$i]].'</fieldset>',$string);
	}
	$re = "#(ᴥ)([0-9]+)(ᴥ)#suim";
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
		$string = str_replace('ᴥ'.$match[2][$i].'ᴥ', $math[$match[2][$i]], $string);
	$re = "#(☣)([0-9]+)(☣)#suim";
	$vt = preg_match_all($re, $string, $match);
	for($i=0;$i<$vt;$i++)
		$string = str_replace('☣'.$match[2][$i].'☣', $latex[$match[2][$i]], $string);
	return $string;
}
?>