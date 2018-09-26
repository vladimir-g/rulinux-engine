<?php

final class templates extends objectbase
{
	private $theme;
	private $filename;
	private $variables;
	private $l_delimiter = '\<!--\{';
	private $r_delimiter = '\}--\>';
	static $baseC = null;
	public function templates()
	{
		self::$baseC = new base;
		$this->variables = array();
	}
	public function set_theme($theme_id)
	{
		$where_arr = array(array("key"=>'id', "value"=>$theme_id, "oper"=>'='));
		$theme = self::$baseC->select('themes', '', '*', $where_arr);
		if (!is_dir('themes/'.$theme[0]['directory']))
			$this->theme = $theme[0];
		else
			$this->theme = 'default';
	}
	public function set_file($file)
	{
		$path = 'themes/default/templates/'.$file;
		if(!empty($this->theme))
		{
			$th_path = 'themes/'.$this->theme.'/templates/'.$file;
			if(file_exists($th_path))
			{
				$path = $th_path;
			}
		}
		$this->filename = $path;
		$this->clear_variables();
	}
	public function assign($key, $value)
	{
		$this->variables[$key]=$value;
	}
	public function clear_variables()
	{
		$this->variables = array();
	}
	public function draw($block)
	{
		$variables = (object)$this->variables;
		$tpl_blocks = $this->parse_template();
		$code = $tpl_blocks[$block];
		eval(' ?>'.$code.'<?php ');
		$this->clear_variables();
	}
	private function set_delimiters($left, $right)
	{
		$this->l_delimiter = $left;
		$this->r_delimiter = $right;
	}
	private function parse_template()
	{
		$ret = array();
		$tpl_val = file_get_contents($this->filename);
		$arr = preg_split('#'.$this->l_delimiter.'#sim', $tpl_val, -1, PREG_SPLIT_NO_EMPTY);
		foreach($arr as $value)
		{
			$tpls_arr = preg_split('#'.$this->r_delimiter.'#sim', $value, -1, PREG_SPLIT_NO_EMPTY);
			$ret[$tpls_arr[0]] = $tpls_arr[1];
		}
		return $ret;
	}
}
