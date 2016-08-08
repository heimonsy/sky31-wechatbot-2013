<?php
session_start();
require_once '../../core/include/common.php';




if(isset($_POST['submit'])) {
	if(!isset($_SESSION['uid'])) exit("ERROR");
	$pw = Encodes::encode(@$_POST['pw']);
	$uid = $_SESSION['uid'];
	$sql = "update `stu_info`  set `cwpw`='{$pw}' where `uid`='{$uid}'";
	$wxdb = MyDB::getWxdb();
	
	$r = $wxdb->query($sql);
	if(!$r){
		ErrorLogs::writeToLog($uid, __FILE__, __LINE__, "更新财务系统密码错误,".mysql_error($wxdb->link), ErrorLogs::FATAL_ERROR);
		echo "绑定失败，服务器出现错误";
		exit();
	}
	else {
		echo "绑定成功~,现在就可以返回微信回复  学费  查询交费情况";
		exit();
	}
	MyDB::close();	
}






if($_GET['oa']){
	$oa = $_GET['oa'];
	//判断oauth是否合法
	if(Oauth::isOauth($oa) && $uid=Oauth::valid($oa) ){
		$bind = Oauth::haveBind($uid);
		if( $bind ){
			$_SESSION['uid']=$uid;
			$_SESSION['haveBind']=true;
			$notice="您已经绑定了信息，再次提交则修改:<br />学号:".Encodes::decode($bind['snum'])."<br />姓名:".Encodes::decode($bind['name'])."<br />昵称:".$bind['nname'];
		}else{
			$_SESSION['uid']=$uid;
			$_SESSION['oa'] =$oa;
			$_SESSION['haveBind'] =false;
			$notice="请输入您的学号和密码<br />您的信息将被加密";
		}

	}else{
		echo "ERROR。非法OAUTH!";
		exit();
	}

}else{
	exit("OA ERROR");
}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>三翼校园</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="stylesheet" href="css/index.css" />
<style>
* { margin:0px; padding:0px;}
#header
{
	height:42px;
	background-color:#69a8d5;
	color:#FFF; line-height:42px; text-align:center; font-size:20px;
	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #69a9d5), color-stop(1, #497fb4)); /* Saf4+, Chrome */

}
#content
{
	height:auto;
	margin-left:10px; margin-right:10px; padding-top:12px; padding-bottom:12px;
}
#notice
{
	height:auto;
	line-height:32px; font-size:18px;
}
.ftit
{
	line-height:20px; font-size:16px;
}

.finp
{
	border-radius:8px;
	height:26px;
	width:auto;
}


.fsub {
	margin-top:10px; margin-left:30px;
	height:32px; width:100px;
	border-radius:2px;
	color: #d9eef7;
	border: solid 1px #0076a3;
	background: #0095cd;
	background: -webkit-gradient(linear, left top, left bottom, from(#00adee), to(#0078a5));
	background: -moz-linear-gradient(top,  #00adee,  #0078a5);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#00adee', endColorstr='#0078a5');
}
.fsub:hover {
	background: #007ead;
	background: -webkit-gradient(linear, left top, left bottom, from(#0095cc), to(#00678e));
	background: -moz-linear-gradient(top,  #0095cc,  #00678e);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#0095cc', endColorstr='#00678e');
}
.fsub:active {
	color: #80bed6;
	background: -webkit-gradient(linear, left top, left bottom, from(#0078a5), to(#00adee));
	background: -moz-linear-gradient(top,  #0078a5,  #00adee);
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='#0078a5', endColorstr='#00adee');
}

#footer
{
	height:52px;
	background-color:#333;
	line-height:52px; color:#FFF; font-size:16px; text-align:center;
	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #404040), color-stop(1, #0c0c0c)); /* Saf4+, Chrome */
}

</style>
</head>

<body>
<div id="header">绑定您的密码
</div>
<div id="content">
<div id="notice">财务管理系统的帐号是学号，初始密码也是学号，如果你已经修改，请在此绑定</div>
        <div id="dfom">
        	<form action="" method="post">
				<span class="ftit">密码：</span><br/>
            	<input class="finp" type="password" name="pw" /><br/>
                <!--<span class="ftit">昵称：</span><br/>
            	<input class="finp" type="text" name="nickName" /><br/>-->
                <input class="fsub" type="submit" name="submit" value="提交" />
            </form>
        </div>
    </div>
    <div id="footer">三翼校园 Copyright © 2004-2013
    </div>
</body>
</html>
