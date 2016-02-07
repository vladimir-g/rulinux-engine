<?php
class viva_la_resistance
{
	function generate_image($canvas)
	{
		global $captcha_font_path,$captcha_img_path;
		$hit = "R=? (Ohms)";
		$a1=rand(0,9);
		$a2=rand(0,9);
		$a3=rand(0,9);
		$m=rand(0,9);
		
		$lcolor[0]=imagecolorallocate($canvas,0,0,0);		// black
		$lcolor[1]=imagecolorallocate($canvas,150,75,0);	// brown
		$lcolor[2]=imagecolorallocate($canvas,255,0,0);		// red
		$lcolor[3]=imagecolorallocate($canvas,255,165,0);	// orange
		$lcolor[4]=imagecolorallocate($canvas,255,255,0);	// yellow
		$lcolor[5]=imagecolorallocate($canvas,0,255,0);		// green
		$lcolor[6]=imagecolorallocate($canvas,0,0,255);		// blue
		$lcolor[7]=imagecolorallocate($canvas,139,0,255);	// violet
		$lcolor[8]=imagecolorallocate($canvas,127,127,127);	// grey
		$lcolor[9]=imagecolorallocate($canvas,255,255,255);	// white
		
		// выводы резистора
		$color = imagecolorallocate($canvas, rand(100,255), rand(100,255), rand(100,255));
		imagefilledrectangle($canvas,20,33,40,37,$color);
		imagefilledrectangle($canvas,140,33,160,37,$color);
		// тело резистора
		$color = imagecolorallocate($canvas, rand(100,255), rand(100,255), rand(100,255));
		imagefilledrectangle($canvas,40,20,140,50,$color);
		
		// линии
		imagefilledrectangle($canvas,55,20,65,50,$lcolor[$a1]);
		imagefilledrectangle($canvas,75,20,85,50,$lcolor[$a2]);
		imagefilledrectangle($canvas,95,20,105,50,$lcolor[$a3]);
		imagefilledrectangle($canvas,115,20,125,50,$lcolor[$m]);
		
		imagefttext($canvas, 11, rand (-5,5), 15, 75, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $hit);
		$nme=$this->ucaptcha->get_filename();
		imagepng($canvas);
                //imagepng($canvas,$captcha_img_path."/".$nme.".png");

		$ans=(($a1*100)+($a2*10)+($a3*1))*pow(10,$m);
		$captcha[0]=$nme;
		$captcha[1]=$ans;
		return $captcha;
	}
}
?>
