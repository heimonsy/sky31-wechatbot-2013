<?php
session_start();
require_once '../../core/include/common.php';


if(isset( $_POST['stuNum'])) {
	if( isset( $_SESSION['uid'] )){
		$uid      = $_SESSION['uid'];
		$stuNum   = addslashes(Encodes::encode( $_POST['stuNum'] ));
		$pw       = addslashes(Encodes::encode( $_POST['pw'] ));
		$wxdb = MyDB::getWxdb();
			
		try{
			$course = Course::getCourseFromXtu($_POST['stuNum'],  $_POST['pw'], $uid);
			if($course) {
				$name = addslashes(Encodes::encode( $course['name']));
				Course::updateCourse($uid, $course['class']);
				if( $_SESSION['haveBind']==true )
					$sql = "update `stu_info` set `name`='{$name}',`snum`='{$stuNum}', `pw`='{$pw}' where `uid`='{$uid}'";
				else
					$sql = "insert into `stu_info` (`uid`, `name`,`snum` ,`pw`) values ('{$uid}', '{$name}', '{$stuNum}', '{$pw}' )";
				$r = $wxdb->query($sql);
				if( !$r ) 
					throw new CURDException("插入或新绑定信息失败", "服务器出错", mysql_error($wxdb->link)."SQL: ".$sql); 
				
				$notice= "您的姓名：".Encodes::decode($name)."<br/>恭喜您绑定成功!<br/>你的课表已经采集，返回微信回复  <span style='color:red;'>课表</span> 查看今日的课表信息";
				include 'oauth_2.tpl';
					
			} else {
				$notice = "<font color='red'>学号或者密码错误</font>";
				include 'oauth_1.tpl';
			}
		}catch (CURDException $e){
			
			ErrorLogs::writeToLog($uid, $e->getFile(), $e->getLine(), "CURD错误：".$e->getMessage(), ErrorLogs::FATAL_ERROR);
			$notice = "<font color='red'>".$e->getMsgToUser()."</font>";
			include 'oauth_1.tpl';
			
		}catch (OtherException $e){
			ErrorLogs::writeToLog($uid, $e->getFile(), $e->getLine(), "其他错误：".$e->getMessage(), ErrorLogs::FATAL_ERROR);
			$notice = "<font color='red'>".$e->getMsgToUser()."</font>";
			include 'oauth_1.tpl';
		}
		exit();
	}else
		echo "ERROR,非法提交!";
	exit();
}


if(isset($_GET['oa'])){
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
			$notice="请输入您的学号和密码<br />";
		}
	}else{
		echo "ERROR。非法OAUTH!";
		exit();
	}
}else{
	exit("OA ERROR");
}




include 'oauth_1.tpl';
