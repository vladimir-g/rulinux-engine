<?
class messages{
	function showmsg($title, $text, $type){
		include_once('core.class.php');
		switch ($type) {
			case 'error':$border='#b20000';$bg='#ffe3e3';$image='error.png';log_it($text, 1);break;
			case 'warning':$border='#ae4e00';$bg='#fff1e6';$image='alert.png';log_it($text, 2);break;
			case 'success':$border='#009400';$bg='#edf9ee';$image='success.png';log_it($text, 3);break;
			case 'info':$border='#003c8d';$bg='#e6f1ff';$image='info.png';log_it($text, 4);break;
			default:$border='#000000';$bg='#ffffff';$image='other.png';log_it($text, 5);break;
		}
		echo $msg='<fieldset style="border: 1px solid; border-color:'.$border.'; background-color:'.$bg.'">
<legend style="color:'.$border.'; font-weight:bold; vertical-align:middle">'.$title.'</legend>
<table>
<tr>
<td><img src="design/admin/img/'.$image.'" vspace="right" /></td>
<td><span style="color:'.$border.';">'.$text.'</span></td>
</tr>
</table>
</fieldset><br />';
		switch ($type) {
			case 'success':return true;break;
			case 'info':return true;break;
			case 'warning':return false;break;
			case 'error':return false;break;
			default:return false;break;
		}
	}
}
?>