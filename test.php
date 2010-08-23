<?php
session_start();
?>
<form action="" method="post">
<p>Enter text shown below:</p>
<p><img src="ucaptcha/index.php?<?=session_name()?>=<?=session_id()?>"></p>
<p><input type="text" name="keystring"></p>
<p><input type="submit" value="Check"></p>
</form>
<?php
//$_SESSION['ucpt'] = '11111111';
//print_r($_SESSION);
if(count($_POST)>0){
	if(isset($_SESSION['captcha_keystring'] ) && $_SESSION['captcha_keystring']  == $_POST['keystring']){
		echo "Correct";
	}else{
		echo "Wrong ".$_SESSION['captcha_keystring'];
	}
}
unset($_SESSION['captcha_keystring']);
?>