<?php
// big2small.php
//Автор: josephson(http://rulinux.net/user_josephson)
if(isset($_GET['pixmap']))
	$pixmap=$_GET['pixmap'];
else exit();

/* Use custom fopen context to prevent problems with streaming content */
if (stripos($pixmap, 'https', 0) === 0)
	$method = 'https';
else
	$method = 'http';
$context = stream_context_create(array($method => array('timeout' => 3.0)));
/* Crate temporary file */
if (false === ($tmp = tempnam(sys_get_temp_dir(), uniqid('rlimg'))))
	exit();
/* Open input and output streams */
if (false === ($in = fopen($pixmap, 'rb', false, $context)) ||
    false === ($out = fopen($tmp, 'wb')))
{
	unlink($tmpfname);
	exit();
}
/* Copy to temp file, max size is 5mb */
stream_copy_to_stream($in, $out, 5242880);

/* Close streams */
fclose($in); fclose($out);

$info = getimagesize($tmp);

unlink($tmp);

if (!$info)
	exit();

list($w0,$h0,$type,$attr) = $info;

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