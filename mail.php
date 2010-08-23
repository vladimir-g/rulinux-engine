<?
$pagename=$_SERVER['SCRIPT_NAME'];
$pagename=str_replace(getcwd(), '', $pagename);
if (!empty($_GET)){
	$_VALS=array_flip($_GET);
	$pagename=$pagename.'?';
	$c=0;
	foreach ($_GET as $_VAR){
		$pagename=$pagename.$_VALS[$_VAR].'='.$_VAR;
		$c++;
		if ($c<=(sizeof($_GET)-1))
			$pagename=$pagename.'&';
	}
}
$scriptname=$_SERVER['SCRIPT_NAME'];
$scriptname=str_replace(getcwd(), '', $scriptname);
$pid=intval($_GET['id']);
include('incs/db.inc.php');
require_once('classes/config.class.php');
require_once('classes/pages.class.php');
require_once('classes/auth.class.php');
require_once('classes/users.class.php');
require_once('classes/news.class.php');
require_once('classes/faq.class.php');
require_once('incs/header.inc.php');
echo '<!--content section begin-->';
$err='';
if( isset($phone) )
{
	if( $kontakt =='' )
	{
		$err.='Необходимо указать Контактное лицо.<BR>';
	}
	if( $mail =='' )
	{
		$err.='Необходимо указать Email для связи.<BR>';
	}
	if( $prod =='' )
	{
		$err.='Необходимо указать Название продукции.<BR>';
	}
	if( $phone =='' )
	{
		$err.='Необходимо указать Код города и номер телефона.<BR>';
	}
	if( $err=='')
	{
$headers = 'Content-Type: text/plain;charset=UTF-8; format=flowed'.\n.'MIME-Version: 1.0'.\n.'Content-Transfer-Encoding: 8bit'.\n.'X-Mailer: PHP'.\n;

$message="Получена online заявка:
Название фирмы: ".$firm."
Контактное лицо: ".$kontakt."
Код города и номер телефона: ".$phone."
Email для связи: ".$mail."
Название продукции: ".$prod."
WWW сайта: ".$www."
Происхождение продукции: ".$proish_cntr.";  ".$proish_type."
Страна-производитель: ".$country."
ТН ВЭД: ".$tn_ved."
ОКП: ".$okp."
Срочность проведения работ: ".$crok."
Описание продукции и дополнительная информация: ".$opisanie;
		mail("info@activtest.ru", "Получена оналйн-заявка", $message,
			$headers);
		print "<font color=green>Ваша заявка успешно отправлена</font>";
	}else{
		print "<font color=red>".$err."</font>";
	}
}

echo '<!--content section end-->';
require_once('incs/bottom.inc.php');
?>