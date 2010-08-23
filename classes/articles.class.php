<?
class categories{
	function addcat($name, $pid){
		if ($_SESSION['user_admin']==1){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTERSET \''.$GLOBALS['db_charset'].'\'');
				if (!empty($name) && !empty($pid)){
					$query='
							INSERT INTO `'.$GLOBALS['tbl_prefix'].'categories`
							(`name`, `parent`)
							VALUES (\''.$name.'\', \''.$pid.'\')
							';
				}
				else { return -1;}
				if(mysql_query($query))
				{ return 1;}
				else { return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else return -1;
	}
}
?>