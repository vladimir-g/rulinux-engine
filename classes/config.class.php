<?php
class config
{
	function include_database()
	{
		$database = parse_ini_file('config/database.ini', 1);
		$subd = $database['global']['subd'];
		require 'classes/base/'.$subd.'.php';
		foreach( $database[$subd] as $key => $value)
		{
			$GLOBALS[$key]=$value;
		}
	}

}
?>