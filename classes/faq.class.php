<?
class faq{
	function add_question($name, $email, $quetion, $av){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
			mysql_selectdb($GLOBALS['db_name']);
			mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
			$query = '
				INSERT INTO `'.$GLOBALS['tbl_prefix'].'faq`
				(`name`, `email`, `question`, `ip`, `date`, `answered`, `available`)
				VALUES
				(\''.$name.'\', \''.$email.'\', \''.$quetion.'\', \''.getenv('REMOTE_ADDR').'\', \''.date('Y-m-d').'\', \'0\', \''.$av.'\')
			';
			if(mysql_query($query))
				return 1;
			else
				return -1;
		}
		else return 0;
	}
	function get_questions($condition = '' , $limit = ''){
		if (mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass'])) {
				mysql_selectdb($GLOBALS['db_name']);
				mysql_query('SET CHARACTER SET \''.$GLOBALS['db_charset'].'\'');
				$query='
						SELECT *
						FROM `'.$GLOBALS['tbl_prefix'].'faq`
						'.$condition.'
						ORDER BY date DESC '.$limit;
				if ($get_faq_res=mysql_query($query)){
					$ret=array();
					$i=1;
					while ($get_faq=mysql_fetch_assoc($get_faq_res)) {
						$get_faq['question'] = str_replace("\r\n", "<br>", $get_faq['question']);
						$get_faq['answer'] = str_replace("\r\n", "<br>", $get_faq['answer']);
						$ret[$i]=$get_faq;
						$i++;
					}
					return $ret;
					
				}
				else { return -1;}
		}
		else return 0;
	}
}
?>
