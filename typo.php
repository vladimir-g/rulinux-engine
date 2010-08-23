<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd"
    >
<html lang="en">
<head>
    <title>test</title>
</head>
<body>
<?
include('classes/config.class.php');

?>
<form action="" method="POST">
<textarea name="test" style="width:640px; height: 480px;"><?=$_POST['test']?></textarea><br>
<input type="submit" value="test">
</form>
<?
if(isset($_POST['test'])){
	$conf = new base;
	echo $conf->_strToTeX($_POST['test']);
}
?>
</body>
</html>
