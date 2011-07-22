<?php
class moreless
{

	function generate_image($canvas)
	{
		global $captcha_font_path,$captcha_img_path;
		$a=rand(1,20);
		$b=rand(1,20);
		$c=rand(1,20);
		$d=rand(1,20);
		$rand = "$a/$b ? $c/$d";
		$l=rand(0,1);
		if ($l==0)
		{
			$hit="Что больше?";
			if ($a/$b>$c/$d)
			{
				$ans="$a/$b";
			}
			else
			{
				$ans="$c/$d";
			}
		}
		else
		{	
			$hit="Что меньше?";
			if ($a/$b<$c/$d)
			{
				$ans="$a/$b";
			}
			else
			{
				$ans="$c/$d";
			}
		}
		for ($i=0;$i<strlen($rand);$i++)
		{
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			imagefttext($canvas, 14, rand (-10,10), 24+14*$i, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $rand[$i]);
		}
		imagefttext($canvas, 15, rand (-5,5), 25, 75, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $hit);
		$nme=$this->ucaptcha->get_filename();
		//imagepng($canvas,$captcha_img_path."/".$nme.".png");
		imagepng($canvas);
		$captcha[0]=$nme;
		$captcha[1]=$ans;
		return $captcha;
	}
}
?>