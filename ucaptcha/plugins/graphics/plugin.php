<?php
class graphics
{

	//FixMe: bullshit from php.net =)
	function arrow($im, $x1, $y1, $x2, $y2, $alength, $awidth, $color) 
	{
		if( $alength > 1 )
			arrow( $im, $x1, $y1, $x2, $y2, $alength - 1, $awidth - 1, $color );

		$distance = sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2));

		$dx = $x2 + ($x1 - $x2) * $alength / $distance;
		$dy = $y2 + ($y1 - $y2) * $alength / $distance;

		$k = $awidth / $alength;

		$x2o = $x2 - $dx;
		$y2o = $dy - $y2;
		
		$x3 = $y2o * $k + $dx;
		$y3 = $x2o * $k + $dy;

		$x4 = $dx - $y2o * $k;
		$y4 = $dy - $x2o * $k;

		imageline($im, $x1, $y1, $dx, $dy, $color);
		imageline($im, $x3, $y3, $x4, $y4, $color);
		imageline($im, $x3, $y3, $x2, $y2, $color);
		imageline($im, $x2, $y2, $x4, $y4, $color);
	} 

	function generate_image($canvas)
	{
		global $captcha_font_path,$captcha_img_path;
		imagesetthickness($canvas, 1);
		//$rand = preg_replace("/([0-9])/e","chr((\\1+112))",rand(100000,999999));
		$var=rand(0,1);
		$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		$liney=rand(3,6);
		$linex=rand(6,12);
		for ($i=$liney;$i>0;$i--)
		{
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			//	if ($i==0)
			//	{
			//	$this->arrow($canvas,10,$i*60/$liney,200,$i*60/$liney, 5, 5, $color );
			//	}
			//     else
			//     {
			imageline($canvas, 10,$i*60/$liney,200,$i*60/$liney,$color); 
			//    }
	
	
			imagefttext($canvas, 8, rand (-45,45), 3, $i*60/$liney, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $liney-$i);
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		}
		for ($i=0;$i<$linex;$i++)
		{
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			imageline($canvas, 30 + $i*160/$linex,0, 30 + $i*160/$linex, 70,$color); 
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			imagefttext($canvas, 8, rand (-45,45), 30 + $i*160/$linex, 80, $color, $captcha_font_path."/LiberationMono-Bold.ttf", $i);
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		}

		$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		imagesetthickness($canvas, 1);
	
		$x=rand(0,$linex-1);
		$y=rand(0,$liney-1);
		for ($i=-5;$i<5;$i++)
		{
			$xr=rand(0,$linex);
			$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
			$yr=rand(0,$liney);
			imageline($canvas, 30 + $x*160/$linex, ($liney-$y)*60/$liney, 30 + $xr*160/$linex, ($liney-$yr)*60/$liney ,$color); 
		}

		$color = imagecolorallocate($canvas, rand(100,255),rand(100,255) , rand(100,255));
		//echo "!".$x."===".$y."!<br>";
	
	
	
		if ($var)
		{
			imagefttext($canvas, 20, rand (-45,45), 40+20, 50, $color, $captcha_font_path."/LiberationMono-Bold.ttf", "y=?");   
		}
		else
		{
			imagefttext($canvas, 12, rand (-45,45), 100, 70, $color, $captcha_font_path."/LiberationMono-Bold.ttf", "X=?");   
		}
	
		$nme=$this->ucaptcha->get_filename();
		imagepng($canvas);
		//imagepng($canvas,$captcha_img_path."/".$nme.".png");
		$captcha[0]=$nme;
		if ($var)
		{
			$captcha[1]=$y;
			$captcha[2]="Введите координату Y точки, откуда берут начало прямые";
		}
		else
		{
			$captcha[1]=$x;
			$captcha[2]="Введите координату X точки, откуда берут начало прямые";
		}
		return $captcha;
	}
}
?>