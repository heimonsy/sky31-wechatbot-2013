<?php
include "common.php";
if( wall_is_login() ) header("Location:index.php");
if( isset($_POST['userName']) )
{
	$userName = $_POST['userName'];
	$pw       = md5($_POST['pw']);
	if( inject_check($userName) ) alert("ÄúÔÚ×öÊ²Ã´ÄØ£¿", "login.php");
	if( wall_login($userName, $pw) ){
	
		$addr = $_SERVER['REMOTE_ADDR'];
		$time = date("Y-m-d H:i:s ", time());
		$host = $_SERVER['REMOTE_HOST'];
		$uage = $_SERVER['HTTP_USER_AGENT'];
		$f = fopen( "log_mp_.log", "a");
		fwrite( $f , "\r\n".$time.",".$addr.",".$host.",".$uage);
		fclose($f);
		
		$_SESSION['admin']='SURPER';
		alert( "µÇÂ¼³É¹¦",  "index.php");
	}else{
		alert( "ÕÊºÅ»òÕßÃÜÂë´íÎó", "login.php");
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>µÇÂ¼</title>
<style>
body { font-family:"Î¢ÈíÑÅºÚ";}
.login_f { height:200px; width:480px; margin:0 auto; margin-top:100px; border:#999 solid 1px;}
.login_f .tit{ height:38px; text-align:center; color:red; border-bottom:#999 solid 1px; line-height:38px; font-size:18px; font-weight:bold; color:#333;}
.input_d { height:40px; width:400px; margin:10px auto; line-height:40px;}
.input_d .l { height:40px; width:50px; text-align:right; float:left; font-size:18px;}
.input_d .r { height:40px; width:342px; float:right;}
.t_i { height:40px; width:342px; border-radius:8px; font-size:18px;}
.sub_d { height:40px; margin:0 auto; width:100px}
.s_i   { height:40px; width:100px;}
</style>
</head>
<body>

<div class="login_f">
	<div class="tit">Î¢ÐÅÇ½µÇÂ¼</div>
    <form action="" method="post" >
    <div class="input_d">
    	<div class="l">ÕÊºÅ</div>
        <div class="r"><input class="t_i" type="text" name="userName" /></div>
    </div>
    <div class="input_d">
    	<div class="l">ÃÜÂë</div>
        <div class="r"><input class="t_i" type="password" name="pw" /></div>
    </div>
    <div class="input_d">
    	<div class="sub_d"><input class="s_i" type="submit" name="submit" value="Ìá½»"></div>
    </div>
    </form>
</div>

</body>
</html>