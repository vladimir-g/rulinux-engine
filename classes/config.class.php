<?php
class config
{
	function include_database($path='')
	{
		$database = parse_ini_file($path.'config/database.ini', 1);
		$subd = $database['global']['subd'];
		require $path.'classes/base/'.$subd.'.php';
		foreach( $database[$subd] as $key => $value)
		{
			$GLOBALS[$key]=$value;
		}
	}

}
?>