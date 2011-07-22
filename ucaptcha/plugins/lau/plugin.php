<?php
class lau 
{

	function generate_image($canvas)
	{
		global $captcha_font_path,$captcha_img_path;   
		$rand = "X+";
		$a1=rand(0,10);
		$a2=rand(-10,15);
		$rand.=$a1."=".$a2;
		$ans=$a2-$a1;
		for ($i=0;$i<strlen($rand);$i++)
		{
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			imagefttext($canvas, 20, rand (-20,20), 40+20*$i, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $rand[$i]);
		}
		$nme=$this->ucaptcha->get_filename();
		imagepng($canvas);
		//imagepng($canvas,$captcha_img_path."/".$nme.".png");
		$captcha[0]=$nme;
		$captcha[1]=$ans;
		return $captcha;
	}
}
?>