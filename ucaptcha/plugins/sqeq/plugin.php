<?php
class sqeq
{
	/* Get non-zero random number for range [$from, $to] */
	function rand_except_zero($from, $to)
	{
		do
			$result = rand($from, $to);
		while ($result == 0);
		return $result;
	}

	/* Format equation to string */
	function format($a, $b, $c)
	{
		$str = '';
		/* $a */
		if (abs($a) == 1) {
			if ($a < 0)
				$str .= '-';
		}
		else {
			$str .= $a;
		}
		$str .= 'X^2';
		/* $b */
		if ($b != 0) {
			if (abs($b) == 1) {
				if ($b > 0)
					$str .= '+';
				else
					$str .= '-';
			}
			else {
				if ($b > 0)
					$str .= '+';
				$str .= $b;
			}
			$str .= 'X';
		}
		/* $c */
		if ($c > 0)
			$str .= '+';
		$str .= $c.'=0';
		return $str;		
	}
	
	function generate_image($canvas)
	{
		/* Algorithm - Vieta's theorem: b = -(x1 + x2), c = x1 * x2 */
		global $captcha_font_path, $captcha_img_path;
		/* Roots */
		$x1 = $this->rand_except_zero(-20, 20);
		$x2 = $this->rand_except_zero(-20, 20);
		/* Coefficients */
		$a = $this->rand_except_zero(-5, 5);
		$b = - ($x1 + $x2) * $a;
		$c = ($x1 * $x2) * $a;
		
		switch (rand(0, 1)) {
		case 0:
			$hit = "max(x1, x2)=?";
			$ans = max($x1, $x2);
			break;
		case 1:
			$hit = "min(x1, x2)=?";
			$ans = min($x1, $x2);
			break;
		}
		/* Format output */
		$eq = $this->format($a, $b, $c);
		for ($i=0; $i < strlen($eq); $i++)
		{
			$color = imagecolorallocate($canvas, rand(100,255), 
						    rand(100,255), rand(100,255));
			imagefttext($canvas, 12, rand (-5,5), 5+12*$i, 50, 
				    $color, $captcha_font_path."/LiberationMono-Bold.ttf", $eq[$i]);
		}
		imagefttext($canvas, 10, rand (-5,5), 20, 75, $color, 
			    $captcha_font_path."/LiberationMono-Bold.ttf", $hit);
		$nme = $this->ucaptcha->get_filename();
		imagepng($canvas);
		$captcha[0] = $nme;
		$captcha[1] = (string)$ans;
		return $captcha;
		}
}
?>