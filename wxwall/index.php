<?php

session_start();
header("Content-type: text/html; charset=utf-8");
if(isset($_POST['pw'])){
	if($_POST['pw'] == "972551578") {
		$_SESSION['admin']="ADMIN";
		header("Location:subject.php");
	}
}
?>

<form action="" method="post">
	<input type="password" name="pw" value="" />
	<input type="submit" name="submit" value="提交" />
</form>