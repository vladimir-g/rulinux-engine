<?
class pages{
	function get_templates($part, $tpl = 'default'){
		mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']);
		mysql_selectdb($GLOBALS['db_name']);
		if (file_exists('design/'.$tpl.'/templates/'.$part.'.tpl')){
			$tpl_pointer = fopen('design/'.$tpl.'/templates/'.$part.'.tpl', 'r');
			$len = filesize('design/'.$tpl.'/templates/'.$part.'.tpl') == 0 ? 1 : filesize('design/'.$tpl.'/templates/'.$part.'.tpl');
			$ret = fread($tpl_pointer, $len);
			if (1 == get_magic_quotes_gpc())
				$ret = stripslashes($ret);
			fclose($tpl_pointer);
			return $ret;
		}
		else die('Could not find templates, please check template name in your admin console');
	}
	function get_struct($name){
		if (!file_exists('design/'.$name.'/template.tpl'))
			die('Can not open template file');
		$template=file('design/'.$name.'/template.tpl');
		return $template;
	}
	function add_page($title, $content, $menulink, $menuname, $id, $author){
		if ($_SESSION['user_admin']==1){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if ($id==''){
					$query='
							INSERT INTO `'.$GLOBALS['tbl_prefix'].'pages` (`title`, `content`, `author`)
							VALUES (\''.$title.'\', \''.$content.'\', \''.$author.'\')
							';
				}
				else {
					$query='
							INSERT INTO `'.$GLOBALS['tbl_prefix'].'pages` (`id`, `title`, `content`, `author`)
							VALUES ('.$id.',\''.$title.'\', \''.$content.'\', \''.$author.'\')
							';
				}
				if(mysql_query($query))
				{
					if (!empty($menulink) && !empty($menuname))
						pages::add_menu_link($menulink, $menuname);
					 
					return 1;
				}
				else {return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else return -1;
	}
	function get_page($id){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$ret=array('title'=>'Unnamed', 'text'=>'');
			$get_res=mysql_query('
								SELECT `title`, `content`, `author`, `edited_by`
								FROM `'.$GLOBALS['tbl_prefix'].'pages`
								WHERE `id`=\''.$id.'\'
								');
			if (mysql_numrows($get_res)>0) {
				while ($get=mysql_fetch_object($get_res)) {
					$ret['title']=$get->title;
					$ret['text']=$get->content;
					$ret['author']=$get->author;
					$ret['edited_by']=$get->edited_by;
				}
				
				$ret['edited_by']=explode(',', $ret['edited_by']);
				array_pop($ret['edited_by']);
				return $ret;
			}
			else {return -1;}
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_menu($desc = '', $parent){
		require_once('classes/config.class.php');
		$menu_template=pages::get_templates('menu', base::check_setting('template'));
		$active_menu_template=pages::get_templates('menu_active',  base::check_setting('template'));
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			if ($parent){
				$child_res=mysql_query('
						SELECT `parent` parente
						FROM `'.$GLOBALS['tbl_prefix'].'menu`
						WHERE `parent`<>\'0\'
						ORDER BY `id` '.$desc
					);
				$childs = array();
				$n = 0;
				while ($child = mysql_fetch_object($child_res))
					$childs[$n++] = $child->parente;
				$menu_res=mysql_query('
										SELECT `name`, `link`, `id`
										FROM `'.$GLOBALS['tbl_prefix'].'menu`
										WHERE `parent`=\'0\'
										ORDER BY `id` '.$desc
										);
				$i = 0;
				while ($menu=mysql_fetch_object($menu_res)) {
					if ($_GET['id']>0){
						if (('page.php?id='.$_GET['id'])==$menu->link)
							$tmp=$active_menu_template;
						else 
							$tmp=$menu_template;
					}
					else {
						if (($GLOBALS['scriptname'])==$menu->link)
							$tmp=$active_menu_template;
						else
							$tmp=$menu_template;
					}
					$tmp=str_replace('[menu_link]', $menu->link, $tmp);
					$tmp=str_replace('[menu_name]', $menu->name, $tmp);
					$tmp=str_replace('[iterator]', $i, $tmp);
					$found = array_search($menu->id, $childs).'f';
					if ($found == '0f')
						$tmp = str_replace('[submenu]', 'true', $tmp);
					else
						$tmp = str_replace('[submenu]', 'false', $tmp);
					$ret=$ret."\n".$tmp;
					$i++;
				}
			}
			else{
				$menu_template=pages::get_templates('submenu',  base::check_setting('template'));
				$parent_res=mysql_query('
					SELECT `id`
					FROM `'.$GLOBALS['tbl_prefix'].'menu`
					WHERE `parent`=0
					ORDER BY `id` '.$desc
				);
				$m = 0;
				while ($parent = mysql_fetch_object($parent_res)){
					$child_res=mysql_query('
						SELECT `name`, `link`, `parent` parente
						FROM `'.$GLOBALS['tbl_prefix'].'menu`
						WHERE `parent`<>\'0\'
						ORDER BY `id` '.$desc
					);
					while ($child = mysql_fetch_object($child_res)){
						if ($child->parente == $parent->id){
							$buff = $menu_template;
							$buff = str_replace('[iterator]', $m, $buff);
							$buff = str_replace('[menu_name]', $child->name, $buff);
							$buff = str_replace('[menu_link]', $child->link, $buff);
							$buff = str_replace('[submenu]', 'true', $buff);
						}
						else{
							$buff = $menu_template;
							$buff = str_replace('[iterator]', $m, $buff);
							$buff = str_replace('[menu_name]', '', $buff);
							$buff = str_replace('[menu_link]', '', $buff);
							$buff = str_replace('[submenu]', 'false', $buff);
						}
					}
					$tmp .= $buff;
					$buff = '';
					$m++;
				}
				$ret = $tmp;
			}
			
			return $ret;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_links($get = 'name'){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$menu_res=mysql_query('
									SELECT `'.$get.'`, `id`
									FROM `'.$GLOBALS['tbl_prefix'].'menu`
									');
			$ret=array();
			if (mysql_numrows($menu_res)<=0){
				return -1;
			}
			while ($menu=mysql_fetch_object($menu_res)) {
				$ret[$menu->$get]=$menu->id;
			}
			
			return $ret;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_childs($link){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$menu_res=mysql_query('
									SELECT `id`, `name`, `link`
									FROM `'.$GLOBALS['tbl_prefix'].'menu`
									WHERE `parent` = '.$link);
			if (mysql_numrows($menu_res)<=0){
				return -1;
			}
			while ($menu=mysql_fetch_object($menu_res)) {
				$ret = array(
					$menu->id,
					$menu->name,
					$menu->link
				);
			}
			
			return $ret;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_parent($child){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$menu_res=mysql_query('
									SELECT `parent`
									FROM `'.$GLOBALS['tbl_prefix'].'menu`
									WHERE `id` = '.$child);
			if (mysql_numrows($menu_res)<=0){
				return -1;
			}
			while ($menu=mysql_fetch_object($menu_res)) {
				$ret = $menu->parent;
			}
			return $ret;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function get_meta_data($as_array){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$meta_res=mysql_query('
									SELECT `http_equiv`, `content`
									FROM `'.$GLOBALS['tbl_prefix'].'meta`
									');
			$ret=array();
			if (mysql_numrows($meta_res)<=0){
				return -1;
			}
			$i=0;
			while ($meta=mysql_fetch_object($meta_res)){
				if($as_array)
					$ret[$meta->http_equiv]=$meta->content;
				else{
					$ret[$i]='<meta http-equiv="'.$meta->http_equiv.'" content="'.$meta->content.'" />';
					$i++;
				}
			}
			
			return $ret;
		}
		
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function set_meta_data($he, $cont, $id, $add){
		if ($_SESSION['user_admin']==1 || $GLOBALS['install']=='yes'){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$meta_res=mysql_query('
										SELECT `id`
										FROM `'.$GLOBALS['tbl_prefix'].'meta`
										WHERE `id`=\''.$id.'\'
										');
				if (!$add){
					if (mysql_numrows($meta_res)<=0){
						return -1;
					}
					else {
						mysql_query('
									UPDATE `'.$GLOBALS['tbl_prefix'].'meta`
									SET `http_equiv`=\''.$he.'\', `content`=\''.$cont.'\'
									WHERE `id`=\''.$id.'\'
									');
						
						return 1;
					}
				}
				else {
					if (mysql_numrows($meta_res)>0){
						return -1;
					}
					else {
						if(mysql_query('
									INSERT INTO `'.$GLOBALS['tbl_prefix'].'meta` (`http_equiv`, `content`)
									VALUES (\''.$he.'\', \''.$cont.'\')
									')){
						
						return 1;
									}
						else {
							return -1;
						}
					}
				}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else {return -1;}
	}
	function get_pages($filter = 'id', $desc = 'ASC'){
		$filter = $filter == '' ? 'id' : $filter;
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$pages_res=mysql_query('
									SELECT `id`, `title`
									FROM `'.$GLOBALS['tbl_prefix'].'pages`
									ORDER BY '.$filter.' '.$desc);
			$ret=array();
			if (mysql_numrows($pages_res)<=0){
				return -1;
			}
			while ($pages=mysql_fetch_object($pages_res)) {
				$ret[$pages->id]=$pages->title;
			}
			
			return $ret;
		}
		else die('Could not connect to database, please check /incs/db.inc.php');
	}
	function edit_page($id, $title, $content, $tpl = 'default', $edited_by){
		if ($_SESSION['user_admin']==1 || $GLOBALS['install']=='yes'){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if (intval($id)>0 && !empty($title)){
					if(mysql_query('
									UPDATE `'.$GLOBALS['tbl_prefix'].'pages`
									SET `title`=\''.$title.'\', `content`=\''.$content.'\', `edited_by`= CONCAT(`edited_by`, \''.$edited_by.',\')
									WHERE `id`=\''.$id.'\'
									'))
					{echo mysql_error(); return 1;}
					else {return -1;}
				}
				elseif(intval($id)<=0 && !empty($content)) {
					
					$tpl_pointer = fopen('design/'.$tpl.'/templates/'.$id.'.tpl', 'w');
					if($tpl_pointer){
						fwrite($tpl_pointer, $content);
						fclose($tpl_pointer);
						return 1;
					}
					else {return -1;}
					/*if(mysql_query('
									UPDATE `'.$GLOBALS['tbl_prefix'].'template`
									SET `code`=\''.$content.'\'
									WHERE `part`=\''.$id.'\'
									'))
					{return 1;}*/
				}
				else {return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else {return -1;}
	}
	function set_link($link, $name, $id, $parent){
		if ($_SESSION['user_admin']==1){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if(mysql_query('
								UPDATE `'.$GLOBALS['tbl_prefix'].'menu`
								SET `link`=\''.$link.'\', `name`=\''.$name.'\', `parent` = \''.$parent.'\'
								WHERE `id`=\''.$id.'\'
								'))
				{ return 1;}
				else {
								return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else {return -2;}
	}
	function rm_page_link($link){
		if ($_SESSION['user_admin']==1){
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$findid_res=mysql_query('
										SELECT `id`
										FROM `'.$GLOBALS['tbl_prefix'].'menu`
										WHERE `link`=\''.$link.'\'
										');
				if (mysql_numrows($findid_res)<=0)
					{return -1;}
				else 
					while ($findid=mysql_fetch_object($findid_res))
						$id=$findid->id;
				if(mysql_query('
								DELETE FROM `'.$GLOBALS['tbl_prefix'].'menu`
								WHERE `id`=\''.$id.'\'
								'))
				{ return 1;}
				else {return -1;}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else return -1;
	}
	function add_menu_link($link, $name){
		if ($_SESSION['user_admin']==1){
			$valid=pages::get_links();
			$names=array_flip($valid);
			foreach ($valid as $val){
				if ($val==$link){
					$sim='link';
					$value=$link;
					$exist=true;
					break;
				}
				if ($names[$val]==$name){
					$sim='name';
					$value=$name;
					$exist=true;
					break;
				}
			}
			if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				if (!$exist){
					if(mysql_query('
									INSERT INTO `'.$GLOBALS['tbl_prefix'].'menu`(`link`, `name`)
									VALUES(\''.$link.'\', \''.$name.'\')
									'))
					{ return 1;}
					else {return -1;}
				}
				else {
					$id_res=mysql_query('
										SELECT `id`
										FROM `'.$GLOBALS['tbl_prefix'].'menu`
										WHERE `'.$sim.'`=\''.$value.'\'
										');
					while ($idr=mysql_fetch_object($id_res))
						$id=$idr->id;
					if(mysql_query('
									UPDATE `'.$GLOBALS['tbl_prefix'].'menu`
									SET `link`='.$link.', `name`=\''.$name.'\'
									WHERE `id`='.$id
									))
					{ return 1;}
					else {return -1;}
				}
			}
			else die('Could not connect to database, please check /incs/db.inc.php');
		}
		else {return -1;}
	}
}
?>
