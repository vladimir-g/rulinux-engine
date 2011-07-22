<?php
class limiter
{
	function generate_image($canvas)
	{
		global $captcha_font_path,$captcha_img_path;   
		$rand = "lim(";
		$a1=rand(0,10);
		$rand.="$a1 + (1/x))";
		$ans=$a1;
		$hit="x -> ∞";
		for ($i=0;$i<strlen($rand);$i++)
		{
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			imagefttext($canvas, 14, rand (-10,10), 4+14*$i, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $rand[$i]);
		}
		imagefttext($canvas, 15, rand (-5,5), 15, 75, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $hit);
		//$nme=$this->ucaptcha->get_filename();
		imagepng($canvas);
		$captcha[0]=$nme;
		$captcha[1]=$ans;
		return $captcha;
	}
}
?>