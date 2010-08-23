<?
class inspection{
	function rm_java($str){
		$begin_java=strpos($str, '<script');
		$end_java=strpos($str, '</script');
		$end_java_tag=strpos($str, '>', $end_java);
		$ret=null;
		for ($i=0; $i<strlen($str); $i++){
			if ($i==$begin_java){
				$i=$end_java_tag;
				continue;
			}
			$ret=$ret.$str[$i];
		}
		return $ret;
	}
}
echo inspection::rm_java('dchsdvcj<script>hsd</script>cv jkbbjsv');
?>