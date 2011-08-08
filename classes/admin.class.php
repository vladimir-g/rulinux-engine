<?php
class admin extends object
{
	function unzip($file,$dir='unzip/')
	{
		if(!file_exists($dir))
			mkdir($dir,0777);
		$zip_handle = zip_open($file);
		if (is_resource($zip_handle)) 
		{
			while($zip_entry = zip_read($zip_handle))
			{
				if ($zip_entry) 
				{
					$zip_name=zip_entry_name($zip_entry);
					$zip_size=zip_entry_filesize($zip_entry);
					if(($zip_size==0)&&($zip_name[strlen($zip_name)-1]=='/'))
						mkdir($dir.$zip_name,0775);
					else
					{
						@zip_entry_open($zip_handle, $zip_entry, 'r');
						$fp=@fopen($dir.$zip_name,'wb+');
						@fwrite($fp,zip_entry_read($zip_entry, $zip_size),$zip_size);
						@fclose($fp);
						@chmod($dir.$zip_name,0775);
						@zip_entry_close($zip_entry);
					}
				}
			}
			return true;
		}
		else
		{
			zip_close($zip_handle);
			return false;
		}
	}
	
	function delTree($dir) 
	{
		$files = glob( $dir . '*', GLOB_MARK );
		foreach( $files as $file )
		{
			if( substr( $file, -1 ) == '/' )
				self::delTree( $file );
			else
				unlink( $file );
		}
		if (is_dir($dir)) 
		{
			rmdir($dir);
			return 1;
		}
		else 
			return 0;
	} 
	
	function remove_thread($tid)
	{
		if(!preg_match("/^[0-9]*$/", $tid))
		{
			$re = '/.*message.php\?newsid=([0-9]*).*/';
			if(preg_match($re, $tid, $matches))
			{
				$tid = $matches[1];
			}
			else
				return 0;
		}
		$ret = base::delete('sessions', 'tid', $tid);
		$ret = base::delete('threads', 'id', $tid);
		$ret = base::delete('comments', 'tid', $tid);
		self::log('user with id = '.$_SESSION['user_id'].' removed thread with id = '.$tid);
		return $ret;
	}
	
	function remove_message($cid)
	{
		if(!preg_match("/^[0-9]*$/", $cid))
		{
			$re = '/.*message.php\?newsid=([0-9]*)(&page=[0-9]*)?#([0-9]*)?/';
			if(preg_match($re, $cid, $matches))
			{
				$cid = $matches[3];
			}
			else
				return 0;
		}
		$where_arr = array(array("key"=>'cid', "value"=>$cid, "oper"=>'='));
		$sel = base::select('threads', '', '*', $where_arr, 'AND');
		if(empty($sel))
			$ret = base::delete('comments', 'id', $cid);
		else
		{
			$ret = base::delete('sessions', 'tid', $sel[0]['id']);
			$ret = base::delete('threads', 'id', $sel[0]['id']);
			$ret = base::delete('comments', 'tid', $sel[0]['id']);
		}
		self::log('user with id = '.$_SESSION['user_id'].' removed message with id = '.$cid);
		return $ret;
	}
	
	function get_setting($name)
	{
		$where_arr = array(array("key"=>'name', "value"=>$name, "oper"=>'='));
		$sel = base::select('settings', '', 'value', $where_arr, 'AND');
		return $sel[0]['value'];
	}
	
	function set_setting($name, $value)
	{
		$ret = base::update('settings', 'value', $value, 'name', $name);
		self::log('user with id = '.$_SESSION['user_id'].' changed setting '. $name. ' on '.$value);
		return $ret;
	}
	
	function install_block($filename)
	{
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$is_extr = self::unzip($filename, 'tmp/'.$hash.'/');
		if($is_extr)
		{
			$filename = 'tmp/'.$hash.'/index.block';
			$block = parse_ini_file($filename);
			$ret = rename('tmp/'.$hash.'/', 'blocks/'.$block['directory']);
			self::delTree('tmp/'.$hash.'/');
			if(!$ret)
				return -1;
			$arr = array(array('name', $block['name']), array('description', $block['description']), array('directory', $block['directory']));
			$ret = base::insert('blocks', $arr);
			self::log('user with id = '.$_SESSION['user_id'].' installed block '.$block['name'].' on directory '.$block['directory']);
			return $ret;
			
		}
		else
			return -1;
	}
	
	function remove_block($block_dir)
	{
		if (is_dir('blocks/'.$block_dir)) 
		{
			$ret = base::delete('blocks', 'directory', $block_dir);
			if($ret==1)
			{
				self::delTree($block_dir);
				self::log('user with id = '.$_SESSION['user_id'].' removed block from directory '.$block_dir);
				return 1;
			}
			else
				return -1;
		}
		else
			return -1;
	}

	function install_mark($filename)
	{
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$is_extr = self::unzip($filename, 'tmp/'.$hash.'/');
		if($is_extr)
		{
			$filename = 'tmp/'.$hash.'/index.mark';
			$mark = parse_ini_file($filename);
			$ret = rename('tmp/'.$hash.'/'.$mark['file'], 'classes/mark/'.$mark['file']);
			self::delTree('tmp/'.$hash.'/');
			if(!$ret)
				return -1;
			$arr = array(array('name', $mark['name']), array('description', $mark['description']), array('file', $mark['file']));
			$ret = base::insert('marks', $arr);
			self::log('user with id = '.$_SESSION['user_id'].' installed mark '.$mark['name'].' on file '.$mark['file']);
			return $ret;
			
		}
		else
			return -1;
	}
	
	function remove_mark($mark_file, $count)
	{
		if($count>1)
		{
			if (is_file('classes/mark/'.$mark_file)) 
			{
				$ret = base::delete('marks', 'file', $mark_file);
				if($ret==1)
				{
					unlink('classes/mark/'.$mark_file);
					self::log('user with id = '.$_SESSION['user_id'].' removed mark '.$mark_file);
					return 1;
				}
				else
					return -1;
			}
			else
				return -1;
		}
		else
			return -1;
	}
	
	function install_theme($filename)
	{
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$is_extr = self::unzip($filename, 'tmp/'.$hash.'/');
		if($is_extr)
		{
			$filename = 'tmp/'.$hash.'/index.theme';
			$theme = parse_ini_file($filename);
			$ret = rename('tmp/'.$hash.'/', 'themes/'.$theme['directory']);
			self::delTree('tmp/'.$hash.'/');
			if(!$ret)
				return -1;
			$arr = array(array('name', $theme['name']), array('description', $theme['description']), array('directory', $theme['directory']));
			$ret = base::insert('themes', $arr);
			self::log('user with id = '.$_SESSION['user_id'].' installed theme '.$theme['name'].' on directory '.$theme['directory']);
			return $ret;
			
		}
		else
			return -1;
	}
	
	function remove_theme($theme_dir)
	{
		$count = core::get_themes_count();
		if($count>1)
		{
			if (is_dir('themes/'.$theme_dir)) 
			{
				$ret = base::delete('themes', 'directory', $theme_dir);
				if($ret==1)
				{
					self::delTree('themes/'.$theme_dir);
					self::log('user with id = '.$_SESSION['user_id'].' removed theme from directory '.$theme_dir);
					return 1;
				}
				else
					return -1;
			}
			else
				return -1;
		}
		else
			return -1;
	}
	
	function install_filter($filename)
	{
		$hash = md5(gmdate("Y-m-d H:i:s"));
		$is_extr = self::unzip($filename, 'tmp/'.$hash.'/');
		if($is_extr)
		{
			$filename = 'tmp/'.$hash.'/index.filter';
			$filter = parse_ini_file($filename);
			$ret = rename('tmp/'.$hash.'/', 'filters/'.$filter['directory']);
			self::delTree('tmp/'.$hash.'/');
			if(!$ret)
				return -1;
			$arr = array(array('name', $filter['name']), array('text', $filter['description']), array('directory', $filter['directory']), array('class', $filter['class']));
			$ret = base::insert('filters', $arr);
			self::log('user with id = '.$_SESSION['user_id'].' installed theme '.$filter['name'].' on directory '.$filter['directory']);
			return $ret;
			
		}
		else
			return -1;
	}
	
	function remove_filter($filter_dir)
	{
		if (is_dir('filters/'.$filter_dir)) 
		{
			$ret = base::delete('filters', 'directory', $filter_dir);
			if($ret==1)
			{
				self::delTree($filter_dir);
				self::log('user with id = '.$_SESSION['user_id'].' removed filter from directory '.$filter_dir);
				return 1;
			}
			else
				return -1;
		}
		else
			return -1;
	}
	
	function add_subsection($section, $name, $description, $shortfaq='', $rewrite, $icon='')
	{
		if(!empty($icon))
		{
			if(is_file('tmp/'.$icon))
			{
				$themes = core::get_themes();
				for($i=0; $i<count($themes); $i++)
				{
					copy('tmp/'.$icon, 'themes/'.$themes[$i]['directory'].'/icons/'.$icon);
				}
				unlink('tmp/'.$icon);
			}
			else
				return -1;
		}
		$param_arr = array($section);
		$srt = base::query('SELECT max(sort) AS srt FROM subsections WHERE section = \'::0::\'', 'assoc_array', $param_arr);
		$sort = $srt[0]['srt']+1;
		$arr = array(array('section', $section), array('name', $name), array('description', $description), array('shortfaq', $shortfaq), array('rewrite', $rewrite), array('sort', $sort), array('icon', $icon));
		$ret = base::insert('subsections', $arr);
		self::log('user with id = '.$_SESSION['user_id'].' added subsection '.$name.' to section with id = '.$section);
		return $ret;
	}
	
	function remove_subsection($id)
	{
		$ret = base::delete('subsections', 'id', $id);
		self::log('user with id = '.$_SESSION['user_id'].' removed subsection with id =  '.$id);
		return $ret;
	}
	
	function log($text = '')
	{
		$timest = gmdate("Y-m-d H:i:s");
		$text = "\n".$timest.' '.$text;
		$path = $_SERVER['DOCUMENT_ROOT'].'/logs/admin.log';
		$file = fopen($path, "a") or die("Can't open file ($path) to write logs"); 
		if (fwrite($file, $text) === FALSE) 
			return -1;
		fclose($file);
		return 1;
	}
}
?>