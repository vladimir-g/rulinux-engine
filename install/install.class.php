<?php
class install
{
	function get_db_modules()
	{
		/*очередная заглушка с заделом на будущее*/
		return array(array("name"=>'postgresql', "file"=>'postgresql.php', "sql"=>'postgresql.sql'), array("name"=>'mysql', "file"=>'mysql.php', "sql"=>'mysql.sql'), array("name"=>'mysqli', "file"=>'mysqli.php', "sql"=>'mysql.sql'));
	}
	function set_db_settings($module, $login, $password, $host, $port,  $dbname, $charset='utf8')
	{
		$ini = new TIniFileEx('../config/database.ini');
		$ini->write('global','subd',$module);
		$ini->write($module,'db_user', $login);
		$ini->write($module,'db_pass', $password);
		$ini->write($module,'db_host', $host);
		$ini->write($module,'db_port', $port);
		$ini->write($module,'db_name', $dbname);
		$ini->write($module,'db_charset', $charset);
		$ini->updateFile();
	}
	
	function create_data($bynarys_path, $sql)
	{
		if(empty($bynarys_path))
			$bynarys_path = '/usr/bin';
		$rest = substr($bynarys_path, strlen($bynarys_path)-1, strlen($bynarys_path));
		if($rest == '/')
			$bynarys_path = substr($bynarys_path, 0, strlen($bynarys_path)-1);
		$database = parse_ini_file($path.'../config/database.ini', 1);
		$subd = $database['global']['subd'];
		$login = $database[$subd]['db_user'];
		$password = $database[$subd]['db_pass'];
		$host = $database[$subd]['db_host'];
		$port = $database[$subd]['db_port'];
		$db = $database[$subd]['db_name'];
		$result = 0;
		$file = $_SERVER["DOCUMENT_ROOT"].'/install/sql/'.$sql;
		$dfile = $_SERVER["DOCUMENT_ROOT"].'/install/sql/data.sql';
		if($sql == 'mysql.sql')
		{
			$command = $bynarys_path.'/mysql  -u '.$login.' --password='.$password.' -P '.$port.' -h '.$host.' -D '.$db.' < '.$file;
			$dcommand = $bynarys_path.'/mysql  -u '.$login.' --password='.$password.' -P '.$port.' -h '.$host.' -D '.$db.' < '.$dfile;
		}
		else if($sql == 'postgresql.sql')
		{
			$command = 'PGPASSWORD='.$password.' '.$bynarys_path.'/psql -h '.$host.' -p '.$port.' -U '.$login.' -d '.$db.' -f '.$file;
			$dcommand = 'PGPASSWORD='.$password.' '.$bynarys_path.'/psql -h '.$host.' -p '.$port.' -U '.$login.' -d '.$db.' -f '.$dfile;
		}
		system($command, $result);
		if($result != 0)
			return -1;
		system($dcommand, $dresult);
		if($dresult != 0)
			return -1;
		return 1;
	}
	
	function create_directories()
	{
		if(!is_dir('../images'))
			mkdir('../images',0775);
		if(!is_dir('../images/gallery'))
			mkdir('../images/gallery',0775);
		if(!is_dir('../images/avatars'))
			mkdir('../images/avatars',0775);
		if(!is_dir('../images/formulas'))
			mkdir('../images/formulas',0775);
		if(!is_dir('../tmp'))
			mkdir('../tmp',0775);
		if(!is_dir('../logs'))
			mkdir('../logs',0775);
		return 1;
	}
	
	function set_settings($title, $pass_phrase)
	{
		config::include_database('../');
		$title = htmlspecialchars($title);
		$pass_phrase = htmlspecialchars($pass_phrase);
		$ret = base::update('settings', 'value', $pass_phrase, 'name', 'register_pass_phrase');
		if($ret >0)
		{
			if(!empty($title))
			{
				$ret = base::update('settings', 'value', $title, 'name', 'title');
				if($ret >0)
					return 1;
				else
					return -1;
			}
			else
				return 1;
		}
		else
			return -1;
	}
	
	function finish_installation()
	{
		$ini = new TIniFileEx('../config/install.ini');
		$ini->write('global','installed',true);
		$ini->updateFile();
	}
	function is_installed()
	{
		$file = '../config/install.ini';
		if(!is_file($file))
			return 0;
		$ini = parse_ini_file($file, 1);
		$installed = $ini['global']['installed'];
		if($installed == 1)
			return 1;
		else
			return 0;
	}
	
	function create_root($login, $password)
	{
		config::include_database('../');
		if (!preg_match('/^([a-zA-Z][a-zA-Z0-9\_\-]*){2,}$/', $login))
			return -1;
		$password = md5($password);
		$param_arr = array($login, $password);
		$ret = base::query('INSERT INTO users(gid, nick, password, name, lastname, birthday, gender, email, show_email, im, show_im, country, city, photo, register_date, last_visit, captcha, blocks, additional, raw_additional, news_on_page, comments_on_page, threads_on_page, show_avatars, show_ua, show_resp, theme, gmt, filters, mark, banned, sort_to) VALUES(2, \'::0::\', \'::1::\', \'Администратор сайта\', \'\', \'2000-01-01 00:00:00\', true, \'noemail@rulinux.net\', false, \'nojabber@rulinux.net\', false, \'\', \'\', \'\', \'2000-01-01 00:00:00\', \'2000-01-01 00:00:00\', -1, \'authorization:l:1,links:l:2,gallery:l:3,tracker:l:4,faq:r:1\', \'Администратор сайта добавленный автоматически\', \'Администратор сайта добавленный автоматически\', 10, 50, 30, false, true, false, \'1\', \'+4\', \'\', 1, false, false);', 'assoc_array', $param_arr);
		return $ret;
	}
}
?>