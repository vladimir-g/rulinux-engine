<?php
class imeareactsii
{
	function get_random_question()
	{
		$l=rand(0,4);
		switch($l)
		{
			case 0:
				$res[0]='2R-Hal + 2Na = R-R + 2NaHal';
				break;
			case 1:
				$res[0]='Ar-Hal + R-Hal + 2Na = Ar-R + 2NaHal';
				break;
			case 2:
				$res[0]='Ar-NO2 + 6H = Ar-NH2 +2H2O';
				break;
			case 3:
				$res[0]='C2H2 + H2O = CH3CHO';
				break;
			case 4:
				$res[0]='CH4 + HNO3 = CH3NO2 + H2O';
				break;
		}
		$res[1]='Название реакции в род. падеже.';
		return $res;
	}

	function generate_image($canvas)
	{
		global $captcha_font_path,$captcha_img_path;
		$txt=$this->get_random_question();
		$rand=$txt[0];
		$lines=explode("\n",$rand);
		for ($i=0;$i<count($lines);$i++)
		{
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			imagefttext($canvas, 7+rand(0,3), rand (-5,5), 5, 40+$i*10, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $lines[$i]);
		}
		$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		imagefttext($canvas, 8, rand (-5,5), 5, 10+$i*10, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $txt[1]);
		$nme=$this->ucaptcha->get_filename();
		imagepng($canvas);
		//imagepng($canvas,$captcha_img_path."/".$nme.".png");
		$captcha[0]=$nme;
		switch($l)
		{
			case 0:
				$captcha[1]='Вюрца';
				break;
			case 1:
				$captcha[1]='Вюрца-Фиттига';
				break;
			case 2:
				$captcha[1]='Зинина';
				break;
			case 3:
				$captcha[1]='Кучерова';
				break;
			case 4:
				$captcha[1]='Коновалова';
				break;
		}
		return $captcha;	
	}
}
?>
