<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/geshi/geshi.php');
class mark
{
	function get_mark_file($uid)
	{
		$where_arr = array(array("key"=>'id', "value"=>$uid, "oper"=>'='));
		$mark_id = base::select('users', '', 'mark', $where_arr);
		$where_file_arr = array(array("key"=>'id', "value"=>$mark_id[0]['mark'], "oper"=>'='));
		$mark_file = base::select('marks', '', 'file', $where_file_arr);
		return $mark_file[0]['file'];
	}
	function get_mark_info($id)
	{
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$mark_info = base::select('marks', '', '*', $where_arr);
		return $mark_info[0];
	}
	function findFilthyLang($string)
	{
		$where_arr = array(array("key"=>'category', "value"=>'1', "oper"=>'='));
		$sel = base::select('regexps', '', 'reg_exp', $where_arr, 'AND');
		if(!empty($sel))
		{
			for($i=0; $i<count($sel); $i++)
			{
				if (preg_match('#'.$sel[$i]['reg_exp'].'#sim', $string)) 
					return 1;
			}
		}
		return 0;
	}
	function highlight($code, $lang, $path)
	{
		if(empty($lang))
			$lang = 'c';
		$geshi = new GeSHi($code, $lang);
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS,1);
		$code = geshi_highlight($code, $lang, $path, true);
		return $code;
	}
	function get_marks()
	{
		$sel = base::select('marks', '', '*');
		if(!empty($sel))
			return $sel;
		else
			return -1;
	}
}
$mark_file = mark::get_mark_file($_SESSION['user_id']);
include 'mark/'.$mark_file;
?>