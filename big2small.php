<?php
// big2small.php
//Автор: josephson(http://rulinux.net/user_josephson)
if(isset($_GET['pixmap']))
	$pixmap=$_GET['pixmap'];
else exit();
list($w0,$h0,$type,$attr)=getimagesize($pixmap);
$w=$_GET['size'];// Это размер твоего трекера и ширина превьюхи.
$rt=$w/$w0;// Это коэффициент для пересчёта высоты превьюхи.
switch($type)
{
	case IMAGETYPE_JPEG:
		$img=imagecreatefromjpeg($pixmap);
		break;
	case IMAGETYPE_GIF:
		$img=imagecreatefromgif($pixmap);
		break;
	case IMAGETYPE_PNG:
		$img=imagecreatefrompng($pixmap);
		break;
	default:exit();
}
if($rt<1)
{
	// Картинка не поместится в трекер:
        // 1) перерисовываем с сохранением пропорций,
        $h=round($h0*$rt);// высоту надо округлить до целого;
        $img0=imagecreatetruecolor($w,$h);
        imagecopyresized($img0,$img,0,0,0,0,$w,$h,$w0,$h0);
        // 2) показываем превьюху.
        switch($type)
	{
		case IMAGETYPE_JPEG:
			Header("Content-type: image/jpeg");
			imagejpeg($img0);
			break;
		case IMAGETYPE_GIF:
			Header("Content-type: image/gif");
			imagegif($img0);
			break;
		case IMAGETYPE_PNG:
			Header("Content-type: image/png");
			imagepng($img0);
			break;
		default:exit();
	}
}
else
{
	// Картинка поместится в трекер:
        // ничего не делаем, показываем оригинал.
        switch($type)
	{
		case IMAGETYPE_JPEG:
			Header("Content-type: image/jpeg");
			imagejpeg($img);
			break;
		case IMAGETYPE_GIF:
			Header("Content-type: image/gif");
			imagegif($img);
			break;
		case IMAGETYPE_PNG:
			Header("Content-type: image/png");
			imagepng($img);
			break;
		default:exit();
	}
}
imagedestroy($img);
imagedestroy($img0);
?>