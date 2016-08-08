<?php
session_start();
require_once '../include/common.php';


if( isset( $_POST['stuNum'] ) )
{
	if( isset( $_SESSION['uid'] ) ){

			$uid      = $_SESSION['uid'];
			$stuNum   = Encodes::encode( $_POST['stuNum'] );
			$pw       = Encodes::encode( $_POST['pw'] );
			//$nickName = $_POST['nickName'];
			$wxdb = MyDB::getWxdb();
			
			$name = PostLogin::getStuName($_POST['stuNum'], $_POST['pw']);
			if( $name==false ){
				$notice = "<font color='red'>学号或者密码错误</font>";
				include 'oauth_1.tpl';
				exit();
			}
			$name = Encodes::encode( $name);
			//echo $name."<br/>";
			if( $_SESSION['haveBind'] )
				$sql = "update `stu_info` set `name`='{$name}',`snum`='{$stuNum}', `pw`='{$pw}' where `uid`='{$uid}'";
			else 
				$sql = "insert into `stu_info` (`uid`, `name`,`snum` ,`pw`) values ('{$uid}', '{$name}', '{$stuNum}', '{$pw}' )";
			
			//echo $sql;
			
			$r = $wxdb->query($sql);
			if( !$r ) die("sql");
			//$sql = "Update `wx_user` set `nname`='{$nickName}' where `uid`='{$uid}'";
			//$r = $wxdb->query($sql);
			//if( !$r ) echo mysql_error();
			$notice= "您的姓名：".Encodes::decode($name)."<br/>恭喜您绑定成功!";
			include 'oauth_2.tpl';
		
	}else
		
		echo "ERROR,非法提交!";
	exit();
}


if($_GET['oa']){
	$oa = $_GET['oa'];
	//判断oauth是否合法
	if( Oauth::isOauth($oa) && $uid=Oauth::valid($oa) ){
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




include 'oauth_1.tpl';
