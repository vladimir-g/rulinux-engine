<?php
final class config
{
	function include_database($path='')
	{
		$database = parse_ini_file($path.'config/database.ini', 1);
		$subd = $database['global']['subd'];
		$file = $database[$subd]['file'];
		require $path.'classes/base/'.$file;
		foreach( $database[$subd] as $key => $value)
		{
			$GLOBALS[$key]=$value;
		}
	}

}
?>