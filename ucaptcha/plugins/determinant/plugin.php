<?php
class determinant
{

	function generate_image($canvas)
	{
		global $captcha_font_path,$captcha_img_path;   
		$hit = "Считай детерминант!";
		$a=rand(0,9);
		$b=rand(0,9);
		$c=rand(0,9);
		$d=rand(0,9);
		$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		imageline  ( $canvas  , 20  , 10  , 20  , 60  , $color  );
		imageline  ( $canvas  , 160  , 10  , 160  , 60  , $color  );
		$rand=" =?";
		for ($i=0;$i<strlen($rand);$i++)
		{
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			imagefttext($canvas, 20, rand (-45,45), 144+20*$i, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $rand[$i]);
		}
		$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		imagefttext($canvas, 20, rand (-5,5), 40, 20, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $a);
		imagefttext($canvas, 20, rand (-5,5), 40, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $b);

		imagefttext($canvas, 20, rand (-5,5), 120, 20, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $c);
		imagefttext($canvas, 20, rand (-5,5), 120, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $d);

		imagefttext($canvas, 11, rand (-5,5), 15, 75, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $hit);
		$nme=$this->ucaptcha->get_filename();
		//imagepng($canvas,$captcha_img_path."/".$nme.".png");
		imagepng($canvas);
		$ans=($a*$d)-($b*$c);
		$captcha[0]=$nme;
		$captcha[1]=$ans;
		return $captcha;
	}
}
?>