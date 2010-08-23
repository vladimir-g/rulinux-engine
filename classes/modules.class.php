<?
class modules{
	function get_module($id = ""){
		$ret=array();
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if (intval($id)>0) {
				$mods_res=mysql_query('
									SELECT `name`, `link`
									FROM `'.$GLOBALS['tbl_prefix'].'modules`
									WHERE `id`='.$id.' AND `active` = 1'
									);
				if (mysql_numrows($mods_res)<=0)
					return -1;
				while ($mods=mysql_fetch_object($mods_res)){
					$ret['name']=$mods->name;
					$ret['link']=$mods->link;
				}
			}
			else {
				if (!empty($id) && $id != 'all') {
					$mods_res=mysql_query('
									SELECT `name`
									FROM `'.$GLOBALS['tbl_prefix'].'modules`
									WHERE `link`=\''.$id.'\' AND `active` = 1
									');
					if (mysql_numrows($mods_res)<=0)
						return -1;
					else
					while ($mods=mysql_fetch_object($mods_res)){
						
						return $mods->name;
					}
				}
				if($id == 'all'){
					$mods_res=mysql_query('
										SELECT `name`, `link`
										FROM `'.$GLOBALS['tbl_prefix'].'modules`
										');
					if (mysql_numrows($mods_res)<=0)
							return -1;
					while ($mods=mysql_fetch_object($mods_res)){
						$ret[$mods->name]=$mods->link;
					}
				}
				if($id == ''){
					$mods_res=mysql_query('
										SELECT `name`, `link`
										FROM `'.$GLOBALS['tbl_prefix'].'modules`
										WHERE `active` = 1
										');
					if (mysql_numrows($mods_res)<=0)
							return -1;
					while ($mods=mysql_fetch_object($mods_res)){
						$ret[$mods->name] = $mods->link;
					}
				}
			
			}
			return $ret;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_module_id($name = "", $link = ""){
		if (!empty($name)) {
			$link='';
		}
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if (!empty($name)) {
				$id_res=mysql_query('
									SELECT `id`
									FROM `'.$GLOBALS['tbl_prefix'].'modules`
									WHERE `name`=\''.$name.'\'
									');
				if (mysql_numrows($id_res)<=0){
					
					return -1;
				}
				while ($id=mysql_fetch_object($id_res)) {
					
					return $id->id;
				}
			}
			elseif (!empty($link)) {
				$id_res=mysql_query('
									SELECT `id`
									FROM `'.$GLOBALS['tbl_prefix'].'modules`
									WHERE `link`=\''.$link.'\'
									');
				if (mysql_numrows($id_res)<=0){
					
					return -1;
				}
				while ($id=mysql_fetch_object($id_res)) {
					return $id->id;
					
				}
			}
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_module_info($id){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$info_res=mysql_query('
								SELECT `descr`, `version`, `comp`, `autor`, `active`
								FROM `'.$GLOBALS['tbl_prefix'].'modules`
								WHERE `id`=\''.$id.'\'
								');
			if (mysql_numrows($info_res)<=0){
				
				return -1;
			}
			$ret=array();
			while ($info=mysql_fetch_object($info_res)) {
				$ret['version']=$info->version;
				$ret['descr']=$info->descr;
				$ret['comp']=$info->comp;
				$ret['autor']=$info->autor;
				$ret['active']=$info->active;
				
				return $ret;
			}
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	
}
?>