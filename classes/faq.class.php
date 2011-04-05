<?php
class faq{

	function add_question($subject, $email, $quetion, $av)
	{
		$subject = htmlspecialchars($subject);
		$question_raw = $question;
		$question = htmlspecialchars($question);
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			$email = htmlspecialchars($email);
		$timestamp = gmdate("y-m-d H:i:s");
		$ip = getenv('REMOTE_ADDR');
		$useragent = htmlspecialchars($useragent);
		$faq_arr = array(
				array('subject', $subject), 
				array('email', $email), 
				array('question', $question),
				array('raw_question', $question_raw),
				array('answer', ''), 
				array('raw_answer', ''), 
				array('timest', $timestamp) , 
				array('useragent', $useragent), 
				array('answered', false), 
				array('available', true)
				);
		$ret = base::insert('faq', $faq_arr);
		return $ret;
	}
	
	function response_to_question($id, $answer)
	{
		$id = (int)$id;
		$answer_raw = $answer;
		$answer = htmlspecialchars($answer);
		$answ = base::update('faq', 'answer', $answer, 'id', $id);
		$raw_answ = base::update('faq', 'raw_answer', $answer_raw, 'id', $id);
		$answd = base::update('faq', 'answered', true, 'id', $id);
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
		$sel = base::select('faq', '', '*', $where_arr, '', 'id', 'DESC');
		if(!empty($sel))
			return $sel;
		else
			return -1;
	}
}
?>
