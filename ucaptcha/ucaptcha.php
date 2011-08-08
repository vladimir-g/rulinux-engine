<?php
$captcha_plugin_path="./plugins";
$captcha_font_path="./fonts";
$captcha_img_path="./cpt";
$captcha_tpl_path="./images";
require "plugs.dcfg.php";
class ucaptcha
{
	function gen_image($level=0)
	{
		global $captcha_plug, $captcha_plugin_path;
		if (count($captcha_plug[$level])==0)
		{
			$captcha=NULL;
		}
		else
		{
			$pl=rand(0,count($captcha_plug[$level])-1);
			include $captcha_plugin_path."/".$captcha_plug[$level][$pl]."/plugin.php";
			$generator = new $captcha_plug[$level][$pl];
			$generator->ucaptcha=$this;
			$canvas = $this->prepare_canvas();
			$captcha = $generator->generate_image($canvas);
		}
		return $captcha;
	} 
	function db_store_answer($uuid, $answer)
	{
		//echo $uuid ." is ". $answer. "<br>";
		return 0;
	}
	function db_get_answer($uuid)
	{
		return 0;
	}
	function prepare_canvas()
	{
		global $captcha_tpl_path;
		$canvas=imagecreatefrompng($captcha_tpl_path."/lorng_template.png");
		return $canvas;
	}
	function get_filename()
	{
		return md5(microtime()+rand(0,255));
	}
};
?>