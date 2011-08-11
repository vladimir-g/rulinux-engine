<?php
final class faq extends object
{
	static $baseC = null;
	function __construct()
	{
		self::$baseC = new base;
	}
	function add_question($subject, $question)
	{
		$subject = htmlspecialchars($subject);
		$question_raw = $question;
		$question = str_to_html($question);
		$faq_arr = array(
				array('subject', $subject), 
				array('question', $question),
				array('raw_question', $question_raw),
				array('answer', ''), 
				array('raw_answer', ''), 
				array('answered', '0'),
				array('available', '1')
				);
		$ret = self::$baseC->insert('faq', $faq_arr);
		return $ret;
	}
	function response_to_question($id, $answer)
	{
		$id = (int)$id;
		$answer_raw = $answer;
		$answer = str_to_html($answer);
		$answ = self::$baseC->update('faq', 'answer', $answer, 'id', $id);
		$raw_answ = self::$baseC->update('faq', 'raw_answer', $answer_raw, 'id', $id);
		$answd = self::$baseC->update('faq', 'answered', true, 'id', $id);
		if($answ<0)
			return $answ;
		elseif($raw_answ<0)
			return $raw_answ;
		elseif($answd<0)
			return $answd;
		else
			return 1;
	}
	function get_questions()
	{
		$where_arr = array(array("key"=>'available', "value"=>'true', "oper"=>'='));
		$sel = self::$baseC->select('faq', '', '*', $where_arr, '', 'id', 'DESC');
		if(!empty($sel))
			return $sel;
		else
			return -1;
	}
	function get_question($id)
	{
		$where_arr = array(array("key"=>'id', "value"=>$id, "oper"=>'='));
		$sel = self::$baseC->select('faq', '', '*', $where_arr, '', 'id', 'DESC');
		if(!empty($sel))
			return $sel[0];
		else
			return -1;
	}
}
?>