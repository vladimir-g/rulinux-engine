<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<!-- подключаем пространство имён VML для IE -->
<!--[if vml]>
	<xml:namespace ns="urn:schemas-microsoft-com:vml" prefix="v"/>
	<style> v\:* { behavior: url(#default#VML); display: block; } </style>

<![endif]-->
<link rel="stylesheet" href="css/opera.css" type="opera/css" media="screen" />  
<title>Скруглённые углы</title>

<style type="text/css">
	*    { padding: 0; margin: 0; }
	body { background: white; color: black; font: 12px Arial, sans-serif; }

	.rounded { margin: 100px auto; text-align: center; width: 50%; position: relative; padding: 10px; }

/*
Нижеприведённые background-image работает только в Opera 9.50 и представляет собой закодированный алгоритомом base64
код SVG-картинки:

<svg xmlns="http://www.w3.org/2000/svg">
<mask id="mask">
   <rect width="100%" height="100%" rx="10" ry="10" fill="white" stroke="black" stroke-width="2"/>
</mask>
<rect stroke="black" fill="white" stroke-width="4" mask="url(#mask)" width="100%" height="100%" rx="10" ry="10"/>
</svg>


В этой картинке подготавливается бакграунд со скруглёнными углами
*/
	noindex:-o-prefocus, .rounded-svg {
		background-image: url(data:image/svg+xml;base64,cюда нужно положить base64-кодированную картинку);
		border: none !important;
	}

	/* для разных браузеров указываем rounded corner через CSS3-свойство */
	.rounded-css3 {
		border: 1px solid black;
		-moz-border-radius: 1em;     /* mozilla 1.5 */
		-webkit-border-radius: 1em;  /* safari 3 */
		-khtml-border-radius: 1em;   /* Konqueror */
		border-radius: 1em;          /* CSS3 */
	}
</style>

</head>

<body >
	<!--[if vml]><v:roundrect class="rounded" strokecolor="black" strokeweight="1px" arcsize="0.25" ><![endif]-->
	<!--[if !vml]>--><div class="rounded rounded-css3 rounded-svg"><!--<![endif]-->
		<p>Вот эти ребята!</p>
	<!--[if !vml]>--></div><!--<![endif]-->
	<!--[if vml]></v:roundrect><![endif]-->
</body></html>