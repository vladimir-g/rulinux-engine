<?
class highlightFilter{
    protected $content;
    public function __construct($content)
	{
        $this->setContent($content);
    }

    public function setContent($content)
	{
        $this->content = $content;
    }

    public function render()
	{
        preg_match_all('#\<code class="([^"]*)"\>(.*?)\</code>#is',$this->content,$m);
        $splited = preg_split('#\<code class="[^"]*"\>.*?\</code>#is',$this->content);
        $geshi = new GeSHi;
        $geshi->set_header_type(GESHI_HEADER_DIV);
        $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS,2);
        $geshi->set_line_style('background: #eaeaea;', 'background: #f2f2f2;',true);
        $c=count($splited);
        $result='';
        for($i=0;$i < $c;$i++)
		{
            $result .= $splited[$i];
            $code = $m[2][$i];
            $lang = $m[1][$i];
            $geshi->set_source($code);
            $geshi->set_language($lang);
            $geshi->set_header_content("<span class=\"code-header\">{$lang}</span>");
            if(strlen(trim($code))) $result .= $geshi->parse_code();
        }
        return $result;
    }
}
?>